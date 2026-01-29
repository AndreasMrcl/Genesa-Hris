<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\GpsAttendanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GpsAttendanceController extends Controller
{
    // ESS: Halaman Absensi GPS
    public function index()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $employee = Auth::guard('employee')->user();
        $today = Carbon::today();

        // Cek absensi hari ini
        $todayAttendance = GpsAttendanceLog::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        // Ambil lokasi kerja (prioritas: outlet > branch)
        $workLocation = $employee->outlet ?? $employee->branch;

        // Riwayat 7 hari terakhir
        $recentLogs = GpsAttendanceLog::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [Carbon::now()->subDays(7), Carbon::now()])
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('ess.gpsAttendance', compact('employee', 'todayAttendance', 'workLocation', 'recentLogs'));
    }

    // ESS: Check-In
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:2048',
        ]);

        $employee = Auth::guard('employee')->user();
        $today = Carbon::today();

        // Validasi: Sudah check-in?
        $existing = GpsAttendanceLog::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existing && $existing->check_in_time) {
            return back()->withErrors(['msg' => 'Anda sudah check-in hari ini.']);
        }

        // Validasi: Lokasi kerja sudah diatur?
        $workLocation = $employee->outlet ?? $employee->branch;
        
        if (!$workLocation || !$workLocation->latitude || !$workLocation->longitude) {
            return back()->withErrors(['msg' => 'Lokasi kerja belum diatur. Hubungi Admin/HRD.']);
        }

        // Hitung jarak
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $workLocation->latitude,
            $workLocation->longitude
        );

        $radius = $workLocation->gps_radius ?? 5000;

        // Validasi: Dalam radius?
        if ($distance > $radius) {
            return back()->withErrors([
                'msg' => "Anda berada di luar radius kerja. Jarak: " . $this->formatDistance($distance) . " (Max: " . $this->formatDistance($radius) . ")"
            ]);
        }

        // Upload foto (opsional)
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance-photos', 'public');
        }

        // Tentukan status (late/on-time)
        $schedule = $employee->schedules()->where('date', $today)->first();
        $status = 'present';
        
        if ($schedule && $schedule->shift) {
            $shiftStart = Carbon::parse($schedule->shift->start_time);
            $now = Carbon::now();
            
            // Toleransi 15 menit
            if ($now->greaterThan($shiftStart->addMinutes(15))) {
                $status = 'late';
            }
        }

        // Simpan data
        GpsAttendanceLog::create([
            'employee_id' => $employee->id,
            'compani_id' => $employee->compani_id,
            'attendance_date' => $today,
            'check_in_time' => Carbon::now(),
            'check_in_latitude' => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'check_in_address' => "Lat: {$request->latitude}, Lon: {$request->longitude}",
            'check_in_distance' => $distance,
            'check_in_photo' => $photoPath,
            'status' => $status,
        ]);

        $this->logActivity(
            'GPS Check-In',
            "Check-in GPS oleh {$employee->name} (Status: {$status})",
            $employee->compani_id
        );

        return redirect()->route('ess-gps-attendance')->with('success', 'Check-in berhasil! Status: ' . ucfirst($status));
    }

    // ESS: Check-Out
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:2048',
        ]);

        $employee = Auth::guard('employee')->user();
        $today = Carbon::today();

        $attendance = GpsAttendanceLog::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendance) {
            return back()->withErrors(['msg' => 'Anda belum check-in hari ini.']);
        }

        if ($attendance->check_out_time) {
            return back()->withErrors(['msg' => 'Anda sudah check-out hari ini.']);
        }

        // Validasi lokasi
        $workLocation = $employee->outlet ?? $employee->branch;
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $workLocation->latitude,
            $workLocation->longitude
        );

        $radius = $workLocation->gps_radius ?? 5000;

        if ($distance > $radius) {
            return back()->withErrors([
                'msg' => "Anda berada di luar radius kerja. Jarak: " . $this->formatDistance($distance) . " (Max: " . $this->formatDistance($radius) . ")"
            ]);
        }

        // Upload foto
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance-photos', 'public');
        }

        // Cek early leave
        $schedule = $employee->schedules()->where('date', $today)->first();
        if ($schedule && $schedule->shift) {
            $shiftEnd = Carbon::parse($schedule->shift->end_time);
            $now = Carbon::now();
            
            // Jika pulang 30 menit lebih awal
            if ($now->lessThan($shiftEnd->subMinutes(30))) {
                $attendance->status = 'early_leave';
            }
        }

        $attendance->update([
            'check_out_time' => Carbon::now(),
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_out_address' => "Lat: {$request->latitude}, Lon: {$request->longitude}",
            'check_out_distance' => $distance,
            'check_out_photo' => $photoPath,
        ]);

        $this->logActivity(
            'GPS Check-Out',
            "Check-out GPS oleh {$employee->name}",
            $employee->compani_id
        );

        return redirect()->route('ess-gps-attendance')->with('success', 'Check-out berhasil!');
    }

    // ADMIN: Lihat semua GPS attendance
    public function adminIndex(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $userCompany = Auth::user()->compani;
        $date = $request->get('date', Carbon::today()->toDateString());

        $logs = GpsAttendanceLog::with(['employee.branch', 'employee.outlet', 'employee.position'])
            ->where('compani_id', $userCompany->id)
            ->where('attendance_date', $date)
            ->orderBy('check_in_time')
            ->get();

        return view('gpsAttendanceAdmin', compact('logs', 'date'));
    }

    // Helper: Haversine Distance Formula
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // meters
    }

    // Helper: Format Distance
    private function formatDistance($meters)
    {
        if ($meters < 1000) {
            return round($meters) . ' m';
        }
        return round($meters / 1000, 2) . ' km';
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