<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AttendanceController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $userCompany = Auth::user()->compani;

        if (!$userCompany) {
            return redirect()->route('addcompany');
        }

        $status = $userCompany->status;

        if ($status !== 'Settlement') {
            return redirect()->route('login');
        }

        $cacheKey = 'attendances_' . $userCompany->id;

        $attendances = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userCompany) {
            return $userCompany->attendances()->with('employee')->get();
        });

        $employee = $userCompany->employees()->get();

        return view('attendance', compact('attendances', 'employee'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required',
            'attendance_date' => 'required',
            'clock_in' => 'required',
            'clock_out' => 'required',
            'status' => 'required',
        ]);

        $data['compani_id'] = $userCompany->id;

        $atten = Attendance::create($data);

        $this->logActivity('Create Allowance', "Menambahkan attendance baru: {$atten->employee->name} ({$atten->status})", $userCompany->id);

        Cache::forget('attendances_' . $userCompany->id);

        return redirect(route('attendance'))->with('success', 'Attendance successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'employee_id' => 'required',
            'attendance_date' => 'required',
            'clock_in' => 'required',
            'clock_out' => 'required',
            'status' => 'required',
        ]);

        $attendance = Attendance::findOrFail($id);

        $oldEmployee = $attendance->employee->name;
        $oldStatus   = $attendance->status;

        $data = $request->only(['employee_id', 'attendance_date', 'clock_in', 'clock_out', 'status']);
        $data['compani_id'] = $userCompany->id;

        $attendance->update($data);

        // LOG UPDATE
        $this->logActivity(
            'Update Attendance',
            "Mengubah attendance {$oldEmployee} dari status {$oldStatus} menjadi {$request->status}",
            $userCompany->id
        );

        Cache::forget('attendances_' . $userCompany->id);

        return redirect(route('attendance'))->with('success', 'Attendance successfully updated!');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        $employeeName = $attendance->employee->name;
        $status       = $attendance->status;
        $companyId    = $attendance->compani_id;

        $attendance->delete();

        // LOG DELETE
        $this->logActivity(
            'Delete Attendance',
            "Menghapus attendance {$employeeName} ({$status})",
            $companyId
        );

        Cache::forget('attendances_' . $companyId);

        return redirect(route('attendance'))->with('success', 'Attendance successfully deleted!');
    }

    private function logActivity($type, $description, $companyId)
    {
        ActivityLog::create([
            'user_id'       => Auth::id(),
            'compani_id'    => $companyId,
            'activity_type' => $type,
            'description'   => $description,
            'created_at'    => now(),
        ]);

        Cache::tags(['activities_' . $companyId])->flush();
    }
}
