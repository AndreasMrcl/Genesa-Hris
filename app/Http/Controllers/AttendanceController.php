<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\GpsAttendanceLog;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\AttendanceProcessorService;

class AttendanceController extends Controller
{
    public function index()
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

        $page = request()->get('page', 1);

        $cacheKey = "attendance_batches_{$userCompany->id}";

        $batches = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->attendances()
                ->select(
                    'period_start',
                    'period_end',
                    DB::raw('count(*) as total_records'),
                    DB::raw('max(updated_at) as last_updated')
                )
                ->groupBy('period_start', 'period_end')
                ->latest('last_updated')
                ->get();
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

        $gpsData = [];
        $fingerspotData = [];
        $leaveData = [];
        $detectionStats = [
            'total_gps' => 0,
            'total_fingerspot' => 0,
            'total_leaves' => 0,
        ];

        if ($start && $end) {
            $employees = $userCompany->employees()
                ->with(['position', 'branch', 'outlet'])
                ->orderBy('name')
                ->get();

            $attendances = $userCompany->attendances()
                ->where('period_start', $start)
                ->where('period_end', $end)
                ->get()
                ->keyBy('employee_id');

            $employeeIds = $employees->pluck('id')->toArray();

            $gpsLogs = GpsAttendanceLog::whereIn('employee_id', $employeeIds)
                ->whereBetween('attendance_date', [$start, $end])
                ->whereNotNull('check_in_time')
                ->select('employee_id', 'attendance_date', 'status')
                ->get();

            foreach ($gpsLogs as $log) {
                if (!isset($gpsData[$log->employee_id])) {
                    $gpsData[$log->employee_id] = [
                        'count' => 0,
                        'late_count' => 0,
                        'dates' => [],
                    ];
                }

                if (!in_array($log->attendance_date, $gpsData[$log->employee_id]['dates'])) {
                    $gpsData[$log->employee_id]['dates'][] = $log->attendance_date;
                    $gpsData[$log->employee_id]['count']++;

                    if ($log->status == 'late') {
                        $gpsData[$log->employee_id]['late_count']++;
                    }
                }
            }

            $detectionStats['total_gps'] = $gpsLogs->count();

            $fingerspotLogs = AttendanceLog::whereIn('employee_id', $employeeIds)
                ->whereBetween(DB::raw('DATE(scan_time)'), [$start, $end])
                ->select('employee_id', DB::raw('DATE(scan_time) as scan_date'))
                ->distinct()
                ->get();

            foreach ($fingerspotLogs as $log) {
                if (!isset($fingerspotData[$log->employee_id])) {
                    $fingerspotData[$log->employee_id] = [
                        'count' => 0,
                        'dates' => [],
                    ];
                }

                if (!in_array($log->scan_date, $fingerspotData[$log->employee_id]['dates'])) {
                    $fingerspotData[$log->employee_id]['dates'][] = $log->scan_date;
                    $fingerspotData[$log->employee_id]['count']++;
                }
            }

            $detectionStats['total_fingerspot'] = $fingerspotLogs->count();

            $leaves = Leave::whereIn('employee_id', $employeeIds)
                ->where('status', 'approved')
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                })
                ->get();

            foreach ($leaves as $leave) {
                if (!isset($leaveData[$leave->employee_id])) {
                    $leaveData[$leave->employee_id] = [
                        'sick' => 0,
                        'permission' => 0,
                        'leave' => 0,
                    ];
                }

                $leaveStart = Carbon::parse($leave->start_date);
                $leaveEnd = Carbon::parse($leave->end_date);
                $periodStart = Carbon::parse($start);
                $periodEnd = Carbon::parse($end);

                $actualStart = $leaveStart->greaterThan($periodStart) ? $leaveStart : $periodStart;
                $actualEnd = $leaveEnd->lessThan($periodEnd) ? $leaveEnd : $periodEnd;

                $leaveDays = $actualStart->diffInDays($actualEnd) + 1;

                if ($leave->type == 'sakit') {
                    $leaveData[$leave->employee_id]['sick'] += $leaveDays;
                } elseif ($leave->type == 'izin') {
                    $leaveData[$leave->employee_id]['permission'] += $leaveDays;
                } elseif ($leave->type == 'cuti') {
                    $leaveData[$leave->employee_id]['leave'] += $leaveDays;
                }
            }

            $detectionStats['total_leaves'] = $leaves->count();

            foreach ($employees as $emp) {
                $empId = $emp->id;

                $gpsCount = isset($gpsData[$empId]) ? $gpsData[$empId]['count'] : 0;
                $gpsLateCount = isset($gpsData[$empId]) ? $gpsData[$empId]['late_count'] : 0;

                $fingerspotCount = isset($fingerspotData[$empId]) ? $fingerspotData[$empId]['count'] : 0;

                $autoPresent = max($gpsCount, $fingerspotCount);

                $source = '';
                if ($gpsCount > 0 && $fingerspotCount > 0) {
                    $source = 'mixed';
                } elseif ($gpsCount > 0) {
                    $source = 'gps';
                } elseif ($fingerspotCount > 0) {
                    $source = 'fingerspot';
                }

                $emp->auto_present = $autoPresent;
                $emp->gps_count = $gpsCount;
                $emp->gps_late_count = $gpsLateCount;
                $emp->fingerspot_count = $fingerspotCount;
                $emp->detection_source = $source;

                $emp->auto_sick = isset($leaveData[$empId]) ? $leaveData[$empId]['sick'] : 0;
                $emp->auto_permission = isset($leaveData[$empId]) ? $leaveData[$empId]['permission'] : 0;
                $emp->auto_leave = isset($leaveData[$empId]) ? $leaveData[$empId]['leave'] : 0;
            }
        }

        return view('manageAttendance', compact(
            'start',
            'end',
            'employees',
            'attendances',
            'detectionStats'
        ));
    }

    public function storeBatch(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'data' => 'required|array',
            'data.*.present' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->data as $empId => $row) {
                $hasGps = GpsAttendanceLog::where('employee_id', $empId)
                    ->whereBetween('attendance_date', [$request->period_start, $request->period_end])
                    ->whereNotNull('check_in_time')
                    ->exists();

                $hasFingerspot = AttendanceLog::where('employee_id', $empId)
                    ->whereBetween(DB::raw('DATE(scan_time)'), [$request->period_start, $request->period_end])
                    ->exists();

                $source = 'manual';
                if ($hasGps && $hasFingerspot) {
                    $source = 'mixed';
                } elseif ($hasGps) {
                    $source = 'gps';
                } elseif ($hasFingerspot) {
                    $source = 'fingerspot';
                }

                Attendance::updateOrCreate(
                    [
                        'compani_id' => $userCompany->id,
                        'employee_id' => $empId,
                        'period_start' => $request->period_start,
                        'period_end' => $request->period_end,
                    ],
                    [
                        'source' => $source,
                        'total_present' => $row['present'] ?? 0,
                        'total_sick' => $row['sick'] ?? 0,
                        'total_permission' => $row['permission'] ?? 0,
                        'total_alpha' => $row['alpha'] ?? 0,
                        'total_leave' => $row['leave'] ?? 0,
                        'note' => $row['note'] ?? null,
                    ]
                );
            }

            DB::commit();

            $this->logActivity(
                'Update Attendance Batch',
                "Input/Update rekap absensi periode {$request->period_start} s/d {$request->period_end}",
                $userCompany->id
            );

            $this->clearCache($userCompany->id);

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
            'end' => 'required|date',
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

        $this->clearCache($userCompany->id);

        return redirect()->route('attendance')->with('success', 'Attendance data deleted successfully!');
    }

    public function autoGenerate(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = $request->start_date;
        $end = $request->end_date;

        try {
            $processor = new AttendanceProcessorService();

            $results = $processor->generateAttendanceRecap($userCompany->id, $start, $end);

            $processor->saveAttendanceRecap($userCompany->id, $start, $end, $results);

            $this->logActivity(
                'Auto-Generate Attendance',
                "Generate rekap absensi otomatis periode {$start} s/d {$end} dari Fingerspot & GPS",
                $userCompany->id
            );

            $this->clearCache($userCompany->id);

            return redirect()->route('attendance')->with('success', 'Attendance auto-generated successfully from Fingerspot & GPS data!');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function clearCache($companyId)
    {
        Cache::forget("attendance_batches_{$companyId}");
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
