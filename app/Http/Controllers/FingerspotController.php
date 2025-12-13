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

        $cloudId  = env('FINGERSPOT_CLOUD_ID');
        $apiToken = env('FINGERSPOT_API_TOKEN');

        $currentCompanyId = $userCompany->id;

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        $stats = ['processed' => 0, 'new' => 0];

        $employeeMap = Employee::whereNotNull('fingerprint_id')
            ->get(['fingerprint_id', 'id', 'compani_id'])
            ->keyBy(fn($e) => (string)$e->fingerprint_id);

        while ($startDate->lte($endDate)) {
            $currentDateStr = $startDate->format('Y-m-d');

            try {
                // POST JSON ke API Fingerspot
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                ])->withBody(json_encode([
                    'trans_id' => (string) rand(100000, 999999),
                    'cloud_id' => $cloudId,
                    'start_date' => $currentDateStr,
                    'end_date'   => $currentDateStr,
                ]), 'application/json')->post('https://developer.fingerspot.io/api/get_attlog');

                $result = $response->json();

                Log::info("Fingerspot response for $currentDateStr", $result);

                if (isset($result['success']) && $result['success']) {
                    $rawData = $result['data'] ?? [];
                    $batchStats = $this->processLogs($rawData, $cloudId, $currentCompanyId, $employeeMap);
                    $stats['processed'] += $batchStats['processed'];
                    $stats['new'] += $batchStats['new'];
                } else {
                    Log::warning("Fingerspot Sync Fail for $currentDateStr: " . ($result['message'] ?? 'Unknown'));
                }
            } catch (\Exception $e) {
                Log::error("Fingerspot Connection Error for $currentDateStr: " . $e->getMessage());
            }

            $startDate->addDay();
        }

        if ($stats['processed'] > 0) {
            return back()->with('success', "Sync Completed! Found {$stats['processed']} logs, Saved {$stats['new']} new logs.");
        } else {
            return back()->withErrors(['msg' => "No logs found in cloud for the selected range."]);
        }
    }

    public function processLogs(array $logs, string $deviceSn, $fallbackCompanyId, $employeeMap)
    {
        $processedCount = 0;
        $newCount       = 0;

        foreach ($logs as $log) {
            $pin          = $log['pin'] ?? null;
            $scanTimeStr  = $log['scan_date'] ?? null;
            $ver          = $log['verify'] ?? null;
            $statusScan   = $log['status_scan'] ?? null;

            if (!$pin || !$scanTimeStr) continue;

            $scanTime = date('Y-m-d H:i:s', strtotime($scanTimeStr));

            $employee = $employeeMap[(string)$pin] ?? null;
            if (!$employee) continue; // skip unknown pins

            AttendanceLog::firstOrCreate(
                [
                    'fingerprint_id' => $pin,
                    'scan_time'      => $scanTime,
                ],
                [
                    'compani_id'        => $employee->compani_id,
                    'employee_id'       => $employee->id,
                    'device_sn'         => $deviceSn,
                    'verification_mode' => $ver,
                    'scan_status'       => $statusScan,
                    'is_processed'      => false,
                ]
            );

            $processedCount++;
            if (isset($employeeId)) {
                $newCount++;
            }
        }

        return ['processed' => $processedCount, 'new' => $newCount];
    }
}
