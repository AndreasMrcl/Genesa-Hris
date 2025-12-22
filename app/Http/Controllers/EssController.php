<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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