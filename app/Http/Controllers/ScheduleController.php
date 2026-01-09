<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Schedule;
use App\Models\Outlet;
use App\Models\Branch;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        $userCompany = Auth::user()->compani;

        if (! $userCompany) {
            return redirect()->route('addcompany');
        }

        $status = $userCompany->status;

        if ($status !== 'Settlement') {
            return redirect()->route('login');
        }

        $branches = Branch::where('compani_id', $userCompany->id)->select('id', 'name')->get();

        $selectedBranchId = $request->get('branch_id');
        $selectedOutletId = $request->get('outlet_id');

        $employees = collect();
        $shifts = collect();
        $schedules = collect();
        $outlets = collect();

        if ($selectedBranchId) {

            $outlets = Outlet::where('branch_id', $selectedBranchId)->orderBy('name')->get();

            $employeeQuery = $userCompany->employees()
                ->with(['position', 'outlet', 'branch'])
                ->where('branch_id', $selectedBranchId)
                ->orderBy('name');

            if ($selectedOutletId) {
                $employeeQuery->where('outlet_id', $selectedOutletId);
            }
            $employees = $employeeQuery->get();

            $shifts = $userCompany->shifts()
                ->where(function($q) use ($selectedBranchId) {
                    $q->whereNull('branch_id')
                      ->orWhere('branch_id', $selectedBranchId);
                })
                ->get();

            $scheduleQuery = $userCompany->schedules()
                ->with(['employee.branch', 'employee.outlet', 'shift']) 
                ->whereHas('employee', function($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });

            if ($selectedOutletId) {
                $scheduleQuery->whereHas('employee', function($q) use ($selectedOutletId) {
                    $q->where('outlet_id', $selectedOutletId);
                });
            }
                
            $schedules = $scheduleQuery->whereBetween('date', [
                    now()->startOfMonth()->subWeek(), 
                    now()->endOfMonth()->addWeek()
                ]) 
                ->latest('date')
                ->get();
        }

        return view('schedule', compact('branches', 'outlets', 'selectedBranchId', 'selectedOutletId', 'employees', 'shifts', 'schedules'));
    }

    public function store(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'branch_id'      => 'required|exists:branches,id',
            'outlet_id'      => 'nullable|exists:outlets,id',
            'employee_ids'   => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'shift_id'       => 'required|exists:shifts,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);

        $shift = $userCompany->shifts()->findOrFail($request->shift_id);
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($request->employee_ids as $empId) {
                foreach ($period as $date) {
                    Schedule::updateOrCreate(
                        [
                            'compani_id' => $userCompany->id,
                            'employee_id' => $empId,
                            'date' => $date->format('Y-m-d'),
                        ],
                        [
                            'shift_id' => $shift->id,
                        ]
                    );
                    $count++;
                }
            }

            DB::commit();

            $this->logActivity('Assign Schedule', "Assign Shift {$shift->name} ke {$count} hari kerja.", $userCompany->id);

            return redirect()->route('schedule', ['branch_id' => $request->branch_id, 'outlet_id' => $request->outlet_id])->with('success', "Schedule updated! $count shifts assigned.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['msg' => 'Error assigning schedule: '.$e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'shift_id'      => 'required|exists:shifts,id',
            'employee_id'   => 'required|exists:employees,id',
        ]);

        $schedule = $userCompany->schedules()->with('employee')->findOrFail($id);

        if ($request->employee_id != $schedule->employee_id) {
            $exists = Schedule::where('compani_id', $userCompany->id)
                ->where('employee_id', $request->employee_id)
                ->where('date', $schedule->date)
                ->exists();

            if ($exists) {
                return back()->withErrors(['msg' => 'Employee replacement already has a schedule on that date.']);
            }
        }

        $schedule->update([
            'shift_id' => $request->shift_id,
            'employee_id' => $request->employee_id,
        ]);

        $this->logActivity('Update Schedule', "Ubah jadwal {$schedule->employee->name} tgl {$schedule->date}", $userCompany->id);

        return redirect()->back()->with('success', 'Schedule updated successfully');
    }

    public function destroy($id)
    {
        $userCompany = Auth::user()->compani;

        $schedule = $userCompany->schedules()->with('employee')->find($id);

        if ($schedule) {
            $name = $schedule->employee->name;
            $date = $schedule->date;
            
            $schedule->delete();
            
            $this->logActivity('Delete Schedule', "Hapus jadwal {$name} tgl {$date}", $userCompany->id);
        }

        return redirect()->back()->with('success', 'Schedule removed successfully');
    }

    private function logActivity($type, $description, $companyId)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'compani_id' => $companyId,
            'activity_type' => $type,
            'description' => $description,
            'created_at' => now(),
        ]);

        Cache::forget("activities_{$companyId}");
    }
}