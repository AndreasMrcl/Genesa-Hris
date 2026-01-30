<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\GpsAttendanceLog;
use App\Models\Schedule;
use App\Models\Leave;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceProcessorService
{
    public function generateAttendanceRecap($companyId, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $employees = Employee::where('compani_id', $companyId)
            ->get();
        
        $results = [];
        
        foreach ($employees as $employee) {

            $hasGpsLogs = GpsAttendanceLog::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$start, $end])
                ->exists();
            
            $hasFingerspotLogs = AttendanceLog::where('employee_id', $employee->id)
                ->whereBetween(DB::raw('DATE(scan_time)'), [$start->toDateString(), $end->toDateString()])
                ->exists();
            
            $source = 'manual';
            if ($hasGpsLogs && $hasFingerspotLogs) {
                $source = 'mixed';
            } elseif ($hasGpsLogs) {
                $source = 'gps';
            } elseif ($hasFingerspotLogs) {
                $source = 'fingerspot';
            }
            
            $attendanceData = $this->calculateAttendance($employee, $start, $end, $source);
            
            $results[] = [
                'employee' => $employee,
                'source' => $source,
                'data' => $attendanceData,
            ];
        }
        
        return $results;
    }
    
    private function calculateAttendance($employee, $start, $end, $source)
    {
        $period = \Carbon\CarbonPeriod::create($start, $end);
        
        $totalPresent = 0;
        $totalLate = 0;
        $totalAlpha = 0;
        $totalPermission = 0;
        $totalSick = 0;
        $totalLeave = 0;
        
        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            
            $schedule = Schedule::where('employee_id', $employee->id)
                ->where('date', $dateStr)
                ->first();
            
            if (!$schedule || !$schedule->shift) {
                continue;
            }
            
            $gpsLog = GpsAttendanceLog::where('employee_id', $employee->id)
                ->where('attendance_date', $dateStr)
                ->first();
            
            $fingerspotLog = AttendanceLog::where('employee_id', $employee->id)
                ->whereDate('scan_time', $dateStr)
                ->orderBy('scan_time', 'asc')
                ->first();
            
            if ($gpsLog && $gpsLog->check_in_time) {
                $totalPresent++;
                
                if ($gpsLog->status == 'late') {
                    $totalLate++;
                }
            } elseif ($fingerspotLog) {
                $totalPresent++;
                
                $shiftStart = Carbon::parse($schedule->shift->start_time);
                $checkInTime = Carbon::parse($fingerspotLog->scan_time);
                
                if ($checkInTime->greaterThan($shiftStart->addMinutes(15))) {
                    $totalLate++;
                }
            } else {
                $totalAlpha++;
            }
        }
        
        $leaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->where('start_date', '<=', $start)
                         ->where('end_date', '>=', $end);
                  });
            })
            ->get();
        
        foreach ($leaves as $leave) {
            $leavePeriod = \Carbon\CarbonPeriod::create($leave->start_date, $leave->end_date);
            
            foreach ($leavePeriod as $leaveDate) {
                if ($leaveDate->between($start, $end)) {
                    $hasSchedule = Schedule::where('employee_id', $employee->id)
                        ->where('date', $leaveDate->toDateString())
                        ->whereHas('shift')
                        ->exists();
                    
                    if ($hasSchedule) {
                        if ($leave->type == 'cuti') {
                            $totalLeave++;
                        } elseif ($leave->type == 'sakit') {
                            $totalSick++;
                        } elseif ($leave->type == 'izin') {
                            $totalPermission++;
                        }
                        
                        if ($totalAlpha > 0) {
                            $totalAlpha--;
                        }
                    }
                }
            }
        }
        
        return [
            'total_present' => $totalPresent,
            'total_late' => $totalLate,
            'total_alpha' => $totalAlpha,
            'total_permission' => $totalPermission,
            'total_sick' => $totalSick,
            'total_leave' => $totalLeave,
        ];
    }

    public function saveAttendanceRecap($companyId, $startDate, $endDate, $results)
    {
        DB::beginTransaction();
        try {
            foreach ($results as $result) {
                Attendance::updateOrCreate(
                    [
                        'compani_id' => $companyId,
                        'employee_id' => $result['employee']->id,
                        'period_start' => $startDate,
                        'period_end' => $endDate,
                    ],
                    [
                        'source' => $result['source'],
                        'total_present' => $result['data']['total_present'],
                        'total_late' => $result['data']['total_late'],
                        'total_alpha' => $result['data']['total_alpha'],
                        'total_permission' => $result['data']['total_permission'],
                        'total_sick' => $result['data']['total_sick'],
                        'total_leave' => $result['data']['total_leave'],
                    ]
                );
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}