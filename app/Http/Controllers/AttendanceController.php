<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        $page = request()->get('page', 1);

        $cacheTag = 'attendance_batches_' . $userCompany->id; 
        $cacheKey = 'page_' . $page;

        $batches = Cache::tags([$cacheTag])->remember($cacheKey, now()->addMinutes(60), function () use ($userCompany) {
            return $userCompany->attendances()
                ->select(
                    'period_start', 
                    'period_end', 
                    DB::raw('count(*) as total_records'), 
                    DB::raw('max(updated_at) as last_updated')
                )
                ->groupBy('period_start', 'period_end')
                ->latest('last_updated')
                ->paginate(10);
        });

        return view('attendance', compact('batches'));
    }

    public function manage(Request $request)
    {
        $userCompany = Auth::user()->compani;
        
        $start = $request->get('start');
        $end = $request->get('end');
        $employees = [];
        $attendances = [];

        if ($start && $end) {
            $employees = Employee::where('compani_id', $userCompany->id)
                ->orderBy('name')
                ->get();

            $attendances = Attendance::where('compani_id', $userCompany->id)
                ->where('period_start', $start)
                ->where('period_end', $end)
                ->get()
                ->keyBy('employee_id');
        }

        Cache::tags(['attendance_batches_' . $userCompany->id])->flush();

        return view('manageAttendance', compact('start', 'end', 'employees', 'attendances'));
    }

    public function storeBatch(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'data'         => 'required|array',
            'data.*.present' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->data as $empId => $row) {
                Attendance::updateOrCreate(
                    [
                        'compani_id'   => $userCompany->id,
                        'employee_id'  => $empId,
                        'period_start' => $request->period_start,
                        'period_end'   => $request->period_end,
                    ],
                    [
                        'total_present'    => $row['present'] ?? 0,
                        'total_late'       => $row['late'] ?? 0,
                        'total_sick'       => $row['sick'] ?? 0,
                        'total_permission' => $row['permission'] ?? 0,
                        'total_permission_letter' => $row['permission_letter'] ?? 0,
                        'total_alpha'      => $row['alpha'] ?? 0,
                        'total_leave'      => $row['leave'] ?? 0,
                        'note'             => $row['note'] ?? null,
                    ]
                );
            }

            DB::commit();

            $this->logActivity(
                'Update Attendance Batch', 
                "Input/Update rekap absensi periode {$request->period_start} s/d {$request->period_end}", 
                $userCompany->id
            );

            Cache::tags(['attendance_batches_' . $userCompany->id])->flush();

            return redirect()->route('attendance')->with('success', 'Attendance data saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Error saving data: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroyPeriod(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'start' => 'required|date', 
            'end'   => 'required|date',
        ]);

        $deleted = $userCompany->attendances()
            ->where('period_start', $request->start)
            ->where('period_end', $request->end)
            ->delete();

        $this->logActivity(
            'Delete Attendance Batch', 
            "Menghapus rekap absensi periode {$request->start} s/d {$request->end}", 
            $userCompany->id
        );

        Cache::tags(['attendance_batches_' . $userCompany->id])->flush();

        return redirect()->route('attendance')->with('success', 'Attendance data deleted successfully!');
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