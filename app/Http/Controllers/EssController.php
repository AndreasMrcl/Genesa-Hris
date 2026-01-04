<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\Shift;
use App\Models\Schedule;
use App\Models\Branch;
use App\Models\Outlet;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EssController extends Controller
{
    public function home()
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $employee = Auth::guard('employee')->user();

        $compani = $employee->compani;

        $announcements = $compani->announcements;

        $attendance = Attendance::where('employee_id', $employee->id)
            ->latest()
            ->first();

        return view('ess.home', compact('employee', 'compani', 'announcements', 'attendance'));
    }

    public function schedule()
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $schedules = Auth::guard('employee')->user()
            ->schedules()
            ->with('shift')
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date', 'asc')
            ->get();

        $totalHours = $schedules->reduce(function ($carry, $item) {
            if ($item->shift) {
                $start = Carbon::parse($item->shift->start_time);
                $end = Carbon::parse($item->shift->end_time);

                if ($item->shift->is_cross_day) {
                    $end->addDay();
                }

                return $carry + $start->diffInHours($end);
            }

            return $carry;
        }, 0);

        $nextShiftText = '-';
        $nextItem = $schedules->first();

        if ($nextItem) {
            $nextDate = Carbon::parse($nextItem->date);

            if ($nextDate->isToday()) {
                $dayStr = 'Today';
            } elseif ($nextDate->isTomorrow()) {
                $dayStr = 'Tomorrow';
            } else {
                $dayStr = $nextDate->format('d M');
            }

            $timeStr = $nextItem->shift
                ? Carbon::parse($nextItem->shift->start_time)->format('H:i')
                : '(Off)';

            $nextShiftText = "$dayStr, $timeStr";
        }

        return view('ess.schedule', compact('schedules', 'totalHours', 'nextShiftText'));
    }

    public function attendance()
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $attendances = Auth::guard('employee')
            ->user()
            ->attendances()
            ->latest('period_start')
            ->get();

        return view('ess.attendance', compact('attendances'));
    }

    public function leave()
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $leaves = Auth::guard('employee')->user()->leaves;

        return view('ess.leave', compact('leaves'));
    }

    public function reqLeave(Request $request)
    {

        $userCompany = Auth::guard('employee')->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required',
            'note' => 'required',
        ]);

        $leave = Leave::create([
            'employee_id' => $data['employee_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'type' => $data['type'],
            'note' => $data['note'],
            'compani_id' => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Leave',
            "Membuat leave '{$leave->employee->name}'",
            $userCompany->id
        );

        Cache::forget("leaves_{$userCompany->id}");

        return redirect(route('ess-leave'));
    }

    public function overtime()
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $overtimes = Auth::guard('employee')->user()->overtimes;

        return view('ess.overtime', compact('overtimes'));
    }

    public function reqOvertime(Request $request)
    {

        $userCompany = Auth::guard('employee')->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required',
            'overtime_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $overtime = Overtime::create([
            'employee_id' => $data['employee_id'],
            'overtime_date' => $data['overtime_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'compani_id' => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Overtime',
            "Menambahkan overtime '{$overtime->employee->name}'",
            $userCompany->id
        );

        Cache::forget("overtimes_{$userCompany->id}");

        return redirect(route('ess-overtime'));
    }

    public function note()
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $notes = Auth::guard('employee')->user()->notes;

        return view('ess.note', compact('notes'));
    }

    public function payroll()
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $payrolls = Auth::guard('employee')->user()->payrolls;

        return view('ess.payroll', compact('payrolls'));
    }

    public function downloadPdf($id)
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $payroll = Payroll::with(['employee', 'payrollDetails'])->findOrFail($id);

        if($payroll->employee_id != Auth::guard('employee')->id()) abort(403);

        $pdf = Pdf::loadView('ess.pdf', compact('payroll'));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Payslip-' . $payroll->employee->name . '-' . $payroll->pay_period_end . '.pdf');
    }

    public function profil()
    {
        if (! Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $employee = Auth::guard('employee')->user();

        $compani = $employee->compani;

        $announcements = $compani->announcements;

        return view('ess.profil', compact('employee', 'compani', 'announcements'));
    }

    private function checkCoordinator() 
    {
        $user = Auth::guard('employee')->user();

        if (!$user || !$user->position->is_head) { 
             abort(403, 'Access Denied. Coordinator only.');
        }
        return $user;
    }

    public function coordinatorSchedule(Request $request)
    {
        $coordinator = $this->checkCoordinator();

        $branchId = $coordinator->branch_id;
        $userCompany = $coordinator->compani;

        $selectedOutletId = $request->get('outlet_id');

        $employeesQuery = Employee::where('compani_id', $userCompany->id)
            ->where('branch_id', $branchId)
            ->orderBy('name');
            
        if ($selectedOutletId) {
            $employeesQuery->where('outlet_id', $selectedOutletId);
        }
        $employees = $employeesQuery->get();

        $shifts = Shift::where('compani_id', $userCompany->id)
            ->where(function($q) use ($branchId) {
                $q->whereNull('branch_id')->orWhere('branch_id', $branchId);
            })->get();

        $outlets = Outlet::where('branch_id', $branchId)->get();

        $schedulesQuery = Schedule::with(['employee', 'shift'])
            ->where('compani_id', $userCompany->id)
            ->whereHas('employee', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
            
        if ($selectedOutletId) {
            $schedulesQuery->whereHas('employee', function($q) use ($selectedOutletId) {
                $q->where('outlet_id', $selectedOutletId);
            });
        }

        $schedules = $schedulesQuery->whereBetween('date', [now()->startOfMonth()->subWeek(), now()->endOfMonth()->addWeek()])
            ->get();
            
        $branches = Branch::where('id', $branchId)->get(); 
        $selectedBranchId = $branchId; 

        $isEss = true; 

        return view('schedule', compact(
            'branches', 'outlets', 'selectedBranchId', 'selectedOutletId', 
            'employees', 'shifts', 'schedules', 'isEss'
        ));
    }

    public function coordinatorStoreSchedule(Request $request) 
    {
        $coordinator = $this->checkCoordinator();
        $userCompany = $coordinator->compani;

        $request->validate([
            'employee_ids'   => 'required|array',
            'employee_ids.*' => 'exists:employees,id', 
            'shift_id'       => 'required|exists:shifts,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);
        $shift = Shift::find($request->shift_id);
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($request->employee_ids as $empId) {

                $targetEmp = Employee::find($empId);

                if($targetEmp->branch_id != $coordinator->branch_id) continue;

                foreach ($period as $date) {
                    Schedule::updateOrCreate(
                        ['compani_id' => $userCompany->id, 'employee_id' => $empId, 'date' => $date->format('Y-m-d')],
                        ['shift_id' => $shift->id]
                    );
                    $count++;
                }
            }
            DB::commit();

            $this->logActivity('Assign Schedule (Coord)', "Assign Shift {$shift->name} ke {$count} hari.", $userCompany->id);

            return redirect()->route('ess-coordinator-schedule', ['outlet_id' => $request->outlet_id])
                ->with('success', "Schedule updated! $count shifts assigned.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function coordinatorUpdateSchedule(Request $request, $id)
    {
        $coordinator = $this->checkCoordinator();
        $userCompany = $coordinator->compani;

        $request->validate([
            'shift_id'    => 'required|exists:shifts,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $schedule = Schedule::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->with('employee')
            ->firstOrFail();

        if ($schedule->employee->branch_id != $coordinator->branch_id) {
             abort(403, 'Anda tidak memiliki akses untuk mengedit jadwal karyawan cabang lain.');
        }

        if ($request->employee_id != $schedule->employee_id) {
            $newEmp = Employee::find($request->employee_id);
            if ($newEmp->branch_id != $coordinator->branch_id) {
                return back()->withErrors(['msg' => 'Karyawan pengganti harus dari cabang yang sama.']);
            }

            $exists = Schedule::where('compani_id', $userCompany->id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $schedule->date)
                ->exists();
            if ($exists) {
                return back()->withErrors(['msg' => 'Karyawan pengganti sudah memiliki jadwal di tanggal tersebut.']);
            }
        }

        $schedule->update([
            'shift_id'    => $request->shift_id,
            'employee_id' => $request->employee_id
        ]);

        $this->logActivity('Update Schedule (Coord)', "Ubah jadwal {$schedule->employee->name}", $userCompany->id);

        return redirect()->back()->with('success', 'Schedule updated successfully');
    }

    public function coordinatorDestroySchedule($id)
    {
        $coordinator = $this->checkCoordinator();
        $userCompany = $coordinator->compani;

        $schedule = Schedule::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->with('employee')
            ->first();

        if ($schedule) {
            if ($schedule->employee->branch_id != $coordinator->branch_id) {
                abort(403, 'Akses Ditolak.');
            }

            $name = $schedule->employee->name;
            $date = $schedule->date;
            $schedule->delete();

            $this->logActivity('Delete Schedule (Coord)', "Hapus jadwal {$name} tgl {$date}", $userCompany->id);
        }

        return redirect()->back()->with('success', 'Schedule removed successfully');
    }

    private function logActivity($type, $description, $companyId)
    {
        ActivityLog::create([
            'employee_id' => Auth::guard('employee')->id(),
            'compani_id' => $companyId,
            'activity_type' => $type,
            'description' => $description,
            'created_at' => now(),
        ]);

        Cache::forget("activities_{$companyId}");
    }
}