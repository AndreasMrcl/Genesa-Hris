<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FingerspotController extends Controller
{

    // public function handleWebhook(Request $request)

    // {
    //     try {
    //         Log::info('Fingerspot Webhook Hit:', $request->all());

    //         $payload = $request->all();

    //         if (!isset($payload['type']) || $payload['type'] !== 'attlog') {
    //             return response()->json(['status' => 'ignored', 'message' => 'Not attendance log'], 200);
    //         }

    //         $rawData = $payload['data'] ?? [];
    //         if (isset($rawData['pin'])) {
    //             $rawData = [$rawData];
    //         }

    //         $cloudId = $payload['cloud_id'] ?? 'UNKNOWN';

    //         $count = $this->processLogs($rawData, $cloudId);

    //         return response()->json(['status' => true, 'message' => "Saved $count logs"], 200);
    //     } catch (\Exception $e) {
    //         Log::error('Fingerspot Webhook Error: ' . $e->getMessage());
    //         return response()->json(['status' => false, 'message' => 'Error processing data'], 500);
    //     }
    // }


    public function fetchFromApi(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $cloudIds = [
            env('FINGERSPOT_CLOUD_ID_1'),
            env('FINGERSPOT_CLOUD_ID_2'),
        ];
        $apiToken = env('FINGERSPOT_API_TOKEN');

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        $employeeMap = Employee::whereNotNull('fingerprint_id')
            ->get(['fingerprint_id', 'id', 'compani_id'])
            ->keyBy(fn($e) => (string)$e->fingerprint_id);

        $totalSaved = 0;
        $allLogs = [];

        while ($startDate->lte($endDate)) {
            $currentDateStr = $startDate->format('Y-m-d');
            $logsPerEmployee = [];

            foreach ($cloudIds as $cloudId) {
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiToken])
                    ->post('https://developer.fingerspot.io/api/get_attlog', [
                        'trans_id'   => (string) rand(100000, 999999),
                        'cloud_id'   => $cloudId,
                        'start_date' => $currentDateStr,
                        'end_date'   => $currentDateStr,
                    ]);

                $result = $response->json();
                $rawData = $result['data'] ?? [];

                foreach ($rawData as $log) {
                    $pin = $log['pin'] ?? null;
                    $scanTimeStr = $log['scan_date'] ?? null;
                    if (!$pin || !$scanTimeStr) continue;

                    $employee = $employeeMap[(string)$pin] ?? null;
                    if (!$employee) continue;

                    $dateOnly = date('Y-m-d', strtotime($scanTimeStr));
                    $scanTime = date('Y-m-d H:i:s', strtotime($scanTimeStr));

                    $key = $employee->id . '|' . $dateOnly;
                    if (!isset($logsPerEmployee[$key]) || $scanTime < $logsPerEmployee[$key]['scan_time']) {
                        $logsPerEmployee[$key] = [
                            'compani_id'        => $employee->compani_id ?? $userCompany->id,
                            'employee_id'       => $employee->id,
                            'fingerprint_id'    => $pin,
                            'device_sn'         => $cloudId,
                            'scan_time'         => $scanTime,
                            'verification_mode' => $log['verify'] ?? null,
                            'scan_status'       => $log['status_scan'] ?? null,
                            'is_processed'      => false,
                        ];
                    }
                }
            }

            // Save to DB and collect saved logs
            foreach ($logsPerEmployee as $attendanceData) {
                $exists = AttendanceLog::where('employee_id', $attendanceData['employee_id'])
                    ->whereDate('scan_time', date('Y-m-d', strtotime($attendanceData['scan_time'])))
                    ->exists();

                if (!$exists) {
                    AttendanceLog::create($attendanceData);
                    $totalSaved++;
                    $allLogs[] = $attendanceData;
                }
            }

            $startDate->addDay();
        }

        return redirect()->back()->with('success', "Fetched and saved $totalSaved attendance logs.");
    }
}
