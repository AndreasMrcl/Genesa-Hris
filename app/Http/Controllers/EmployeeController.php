<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\ActivityLog;
use App\Models\GlobalPtkp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EmployeeController extends Controller
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

        $cacheKey = "employees_{$userCompany->id}";

        $employees = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->employees()->with('compani', 'branch', 'position')->get();
        });

        $branch = Branch::where('compani_id', $userCompany->id)->select('id', 'name', 'category')->get();

        $positions = $userCompany->positions()->select('id', 'name', 'category', 'base_salary_default')->get();

        $ptkps = $userCompany->globalPtkps()->orderBy('code')->get();

        return view('employee', compact('employees', 'branch', 'positions', 'ptkps'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            // Data Pribadi
            'name' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|min:6',
            'nik' => 'required|numeric',
            'fingerprint_id' => 'required|numeric',
            'phone' => 'required|numeric',
            'address' => 'required|string',
            'ktp' => 'nullable|numeric',

            // Data Pekerjaan
            'position_id' => 'required|exists:positions,id',
            'working_days' => 'required|integer',
            'join_date' => 'required|date',
            'status' => 'required',

            // Data Payroll & Pajak (Baru)
            'base_salary' => 'required|numeric|min:0',
            'payroll_method' => 'required',
            'bank_name' => 'nullable|string',
            'bank_account_no' => 'nullable|numeric',
            'ptkp_status' => 'nullable|string',
            'npwp' => 'nullable|string',
            'bpjs_kesehatan_no' => 'nullable|string',
            'bpjs_ketenagakerjaan_no' => 'nullable|string',

            'participates_bpjs_kes' => 'boolean',
            'participates_bpjs_tk' => 'boolean',
            'participates_bpjs_jp' => 'boolean',

        ]);

        $data['participates_bpjs_kes'] = $request->has('participates_bpjs_kes');
        $data['participates_bpjs_tk'] = $request->has('participates_bpjs_tk');
        $data['participates_bpjs_jp'] = $request->has('participates_bpjs_jp');

        $data['compani_id'] = $userCompany->id;
        $data['password'] = bcrypt($data['password']);

        $employee = Employee::create($data);

        $posName = $employee->position->name ?? '-';

        $this->logActivity(
            'Create Employee',
            "Menambahkan karyawan baru: {$employee->name} (Posisi: {$posName})",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('employee'))->with('success', 'Employee successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        // Validasi Update
        $data = $request->validate([
            'name' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'email' => 'required|email',
            'nik' => 'required|numeric',
            'fingerprint_id' => 'required|numeric',
            'phone' => 'required|numeric',
            'address' => 'required|string',
            'ktp' => 'nullable|numeric',
            'join_date' => 'required|date',
            'status' => 'required',
            'password' => 'nullable|min:6', // Boleh kosong saat update
            'position_id' => 'required|exists:positions,id',
            'working_days' => 'required|integer',

            // Payroll Update
            'base_salary' => 'required|numeric|min:0',
            'payroll_method' => 'required',
            'bank_name' => 'nullable|string',
            'bank_account_no' => 'nullable|numeric',
            'ptkp_status' => 'nullable|string',
            'npwp' => 'nullable|string',
            'bpjs_kesehatan_no' => 'nullable|string',
            'bpjs_ketenagakerjaan_no' => 'nullable|string',

            'participates_bpjs_kes' => 'boolean',
            'participates_bpjs_tk' => 'boolean',
            'participates_bpjs_jp' => 'boolean',
        ]);

        // Hapus password dari array jika kosong (agar tidak ter-update jadi null/kosong)
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $data['compani_id'] = $userCompany->id;

        // Update dengan security check (hanya milik company user)
        $employee = Employee::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $fieldsToTrack = ['name', 'position', 'base_salary', 'status', 'branch_id'];
        $oldData = $employee->only($fieldsToTrack);

        $employee->update($data);

        $changes = [];
        foreach ($fieldsToTrack as $key) {
            if (array_key_exists($key, $data) && $oldData[$key] != $data[$key]) {
                $label = ucfirst(str_replace('_', ' ', $key));
                $changes[] = "$label changed from '{$oldData[$key]}' to '{$data[$key]}'";
            }
        }

        if (!empty($changes)) {
            $desc = "Update Employee {$employee->name}: " . implode(', ', $changes);
        } else {
            $desc = "Update Employee {$employee->name} (Minor details)";
        }

        $this->logActivity('Update Employee', $desc, $userCompany->id);

        $this->clearCache($userCompany->id);

        return redirect(route('employee'))->with('success', 'Employee successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $employee = Employee::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $employee->name;

        $employee->delete();
        
        $this->logActivity(
            'Delete Employee',
            "Menghapus karyawan: {$oldContent}",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('employee'))->with('error', 'Employee not found or access denied.');
    }

    private function clearCache($companyId)
    {
        Cache::forget("employees_{$companyId}");
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

        Cache::forget("activities_{$companyId}");
    }
}