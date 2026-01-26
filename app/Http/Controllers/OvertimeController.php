<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Overtime;
use App\Models\Branch;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class OvertimeController extends Controller
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

        $cacheKey = "overtimes_{$userCompany->id}";

        $overtimes = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            $data = $userCompany->overtimes()
                ->with(['employee.branch', 'employee.position'])
                ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
                ->orderBy('created_at', 'desc')
                ->get();

            return $data->groupBy(function ($item) {
                return $item->overtime_date . '|' . $item->start_time . '|' . $item->end_time;
            });
        });

        $employee = $userCompany->employees()
            ->with(['branch', 'outlet', 'position'])
            ->orderBy('name')
            ->get();
        
        $branches = Branch::where('compani_id', $userCompany->id)
            ->orderBy('name')
            ->get();
            
        $branchIds = $branches->pluck('id');
        $outlets = Outlet::whereIn('branch_id', $branchIds)
            ->orderBy('name')
            ->get();

        return view('overtime', compact('overtimes', 'employee', 'branches', 'outlets'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'employee_ids'   => 'required|array',           
            'employee_ids.*' => 'exists:employees,id',     
            'overtime_date'  => 'required|date',
            'start_time'     => 'required',
            'end_time'       => 'required',
            'status'         => 'required|in:pending,approved,rejected',
            'overtime_pay'   => 'required_if:status,approved|nullable|numeric|min:0',
        ]);

        $count = 0;

        foreach ($data['employee_ids'] as $empId) {
            Overtime::create([
                'employee_id'   => $empId,
                'overtime_date' => $data['overtime_date'],
                'start_time'    => $data['start_time'],
                'end_time'      => $data['end_time'],
                'status'        => $data['status'],
                'overtime_pay'  => $data['overtime_pay'] ?? 0,
                'compani_id'    => $userCompany->id,
            ]);
            $count++;
        }

        $this->logActivity(
            'Create Overtime',
            "Admin membuat data lembur (Batch) untuk {$count} karyawan",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('overtime'))->with('success', "Berhasil menambahkan {$count} data lembur!");
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;
        
        $overtime = Overtime::where('id', $id)->where('compani_id', $userCompany->id)->firstOrFail();

        $data = $request->validate([
            'status'       => 'required|in:pending,approved,rejected',
            'overtime_pay' => 'nullable|numeric|min:0',
        ]);

        $overtime->update([
            'status'       => $data['status'],
            'overtime_pay' => $data['overtime_pay'] ?? 0,
        ]);

        $this->logActivity('Update Overtime', "Mengubah status {$overtime->employee->name} -> {$data['status']}", $userCompany->id);
        $this->clearCache($userCompany->id);

        return redirect(route('overtime'))->with('success', 'Status berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;
        $overtime = Overtime::where('id', $id)->where('compani_id', $userCompany->id)->first();

        if ($overtime) {
            $overtime->delete();
            $this->logActivity('Delete Overtime', "Menghapus data lembur {$overtime->employee->name}", $userCompany->id);
        }

        $this->clearCache($userCompany->id);
        return redirect(route('overtime'))->with('success', 'Data berhasil dihapus!');
    }

    public function batchUpdate(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'original_date'  => 'required|date',
            'original_start' => 'required',
            'original_end'   => 'required',
            'overtime_date'  => 'required|date',
            'start_time'     => 'required',
            'end_time'       => 'required',
            'employee_ids'   => 'array',
        ]);

        $newEmployeeIds = $request->employee_ids ?? [];

        $existingRecords = Overtime::where('compani_id', $userCompany->id)
            ->where('overtime_date', $request->original_date)
            ->where('start_time', $request->original_start)
            ->where('end_time', $request->original_end)
            ->get();

        $existingEmployeeIds = $existingRecords->pluck('employee_id')->toArray();

        DB::beginTransaction();
        try {
            foreach ($existingRecords as $record) {
                if (in_array($record->employee_id, $newEmployeeIds)) {
                    $record->update([
                        'overtime_date' => $request->overtime_date,
                        'start_time'    => $request->start_time,
                        'end_time'      => $request->end_time,
                    ]);
                } else {                
                    $record->delete();
                }
            }

            $toCreate = array_diff($newEmployeeIds, $existingEmployeeIds);
            foreach ($toCreate as $empId) {
                Overtime::create([
                    'compani_id'    => $userCompany->id,
                    'employee_id'   => $empId,
                    'overtime_date' => $request->overtime_date,
                    'start_time'    => $request->start_time,
                    'end_time'      => $request->end_time,
                    'status'        => 'pending', 
                    'overtime_pay'  => 0
                ]);
            }

            DB::commit();
            $this->logActivity('Batch Update', "Updated group {$request->original_date}", $userCompany->id);
            $this->clearCache($userCompany->id);

            return redirect(route('overtime'))->with('success', 'Kelompok lembur berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Batch update failed: ' . $e->getMessage()]);
        }
    }

    public function batchDelete(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'date'  => 'required|date',
            'start' => 'required',
            'end'   => 'required',
        ]);

        $deleted = Overtime::where('compani_id', $userCompany->id)
            ->where('overtime_date', $request->date)
            ->where('start_time', $request->start)
            ->where('end_time', $request->end)
            ->delete();

        $this->logActivity('Batch Delete', "Deleted group {$request->date} ($deleted rows)", $userCompany->id);
        $this->clearCache($userCompany->id);

        return redirect(route('overtime'))->with('success', "Kelompok lembur berhasil dihapus ($deleted data).");
    }

    public function printReport(Request $request)
    {
        $userCompany = Auth::user()->compani;

        $date = $request->query('date');
        $startTime = $request->query('start_time');
        $endTime = $request->query('end_time');
        $branchId = $request->query('branch_id');
        $employeeId = $request->query('employee_id');

        if (!$date) {
            return back()->withErrors(['msg' => 'Silakan pilih tanggal terlebih dahulu untuk mencetak laporan.']);
        }

        $query = Overtime::with(['employee.position', 'employee.branch'])
            ->where('compani_id', $userCompany->id)
            ->whereDate('overtime_date', $date);

        if ($startTime && $endTime) {
            $query->where('start_time', $startTime)
                ->where('end_time', $endTime);
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($branchId) {
            $query->whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $overtimes = $query->orderBy('employee_id')->get();

        if ($overtimes->isEmpty()) {
            return back()->withErrors(['msg' => 'Tidak ada data lembur yang ditemukan pada kriteria tersebut.']);
        }

        $pdf = Pdf::loadView('ess.overtime_report', [
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'overtimes' => $overtimes,
            'coordinator' => Auth::user(),
            'company' => $userCompany
        ]);

        $pdf->setPaper('A4', 'portrait');

        $fileName = 'Lembur_' . $date;
        if ($employeeId) {
            $fileName .= '_' . ($overtimes->first()->employee->name ?? 'Employee');
        } elseif ($branchId) {
            $fileName .= '_Branch-' . $branchId;
        }
        $fileName .= '.pdf';

        return $pdf->stream($fileName);
    }

    private function clearCache($companyId)
    {
        Cache::forget("overtimes_{$companyId}");
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
