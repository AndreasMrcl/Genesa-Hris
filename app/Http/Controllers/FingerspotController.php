<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FingerspotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        try {
            Log::info('Fingerspot Webhook Hit:', $request->all());

            $payload = $request->all();

            if (!isset($payload['type']) || $payload['type'] !== 'attlog') {
                return response()->json(['status' => 'ignored', 'message' => 'Not attendance log'], 200);
            }

            $rawData = $payload['data'] ?? [];
            if (isset($rawData['pin'])) {
                $rawData = [$rawData];
            }

            $cloudId = $payload['cloud_id'] ?? 'UNKNOWN';

            $count = $this->processLogs($rawData, $cloudId);

            return response()->json(['status' => true, 'message' => "Saved $count logs"], 200);
        } catch (\Exception $e) {
            Log::error('Fingerspot Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error processing data'], 500);
        }
    }

    public function fetchFromApi(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $cloudId = env('FINGERSPOT_CLOUD_ID');
        $apiToken = env('FINGERSPOT_API_TOKEN');

        if (!$cloudId || !$apiToken) {
            return back()->withErrors(['msg' => 'Fingerspot Credentials not set in .env']);
        }
        
        // Ambil Company ID dari Admin yang sedang login
        $currentCompanyId = Auth::user()->compani_id ?? null;

        set_time_limit(300);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $stats = ['processed' => 0, 'new' => 0];

        while ($startDate->lte($endDate)) {
            $currentDateStr = $startDate->format('Y-m-d');
            
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type'  => 'application/json'
                ])->post('https://developer.fingerspot.io/api/get_attlog', [
                    'trans_id'   => (string) rand(100000, 999999),
                    'cloud_id'   => $cloudId,
                    'start_date' => $currentDateStr,
                    'end_date'   => $currentDateStr,
                ]);

                $result = $response->json();

                if (isset($result['success']) && $result['success']) {
                    $rawData = $result['data'] ?? [];
                    
                    // PASSING company_id KE FUNGSI PROCESS
                    $batchStats = $this->processLogs($rawData, $cloudId, $currentCompanyId);
                    
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

    public function processLogs(array $logs, string $deviceSn, $fallbackCompanyId = null)
    {
        $processedCount = 0;
        $newCount = 0;

        // Load mapping Employee
        $employees = Employee::whereNotNull('fingerprint_id')
            ->pluck('id', 'fingerprint_id')
            ->toArray();
        
        // Load mapping Company dari Employee (jika ada)
        $companies = Employee::whereNotNull('fingerprint_id')
             ->pluck('compani_id', 'fingerprint_id')
             ->toArray();

        foreach ($logs as $log) {
            $pin = $log['pin'] ?? null;
            $scanTimeStr = $log['scan'] ?? $log['scan_date'] ?? null;
            $ver = $log['verify'] ?? $log['ver'] ?? null;
            $statusScan = $log['status_scan'] ?? null;

            if (!$pin || !$scanTimeStr) continue;

            $scanTime = date('Y-m-d H:i:s', strtotime($scanTimeStr));

            $employeeId = $employees[$pin] ?? null;
            
            // LOGIKA COMPANY ID:
            // 1. Ambil dari Employee jika PIN cocok
            // 2. Jika tidak cocok, pakai Fallback (dari Admin yg login)
            // 3. Jika masih null, biarkan null
            $companyId = $companies[$pin] ?? $fallbackCompanyId;

            // Simpan Log
            $attendanceLog = AttendanceLog::firstOrCreate(
                [
                    'fingerprint_id' => $pin,
                    'scan_time'      => $scanTime,
                ],
                [
                    'compani_id'        => $companyId, // Pastikan ini terisi
                    'employee_id'       => $employeeId,
                    'device_sn'         => $deviceSn,
                    'verification_mode' => $ver,
                    'scan_status'       => $statusScan,
                    'is_processed'      => false
                ]
            );

            $processedCount++;
            
            if ($attendanceLog->wasRecentlyCreated) {
                $newCount++;
            }
        }

        return ['processed' => $processedCount, 'new' => $newCount];
    }
}
