<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\ActivityLog;
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

        $cacheKey = 'employees_' . $userCompany->id;

        $employees = Cache::remember($cacheKey, 60, function () use ($userCompany) {
            return $userCompany->employees()->with('compani', 'branch')->get();
        });

        $branch = Branch::where('compani_id', $userCompany->id)->select('id', 'name')->get();

        return view('employee', compact('employees', 'branch'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        // Validasi mencakup semua field baru dari View & Model
        $data = $request->validate([
            // Data Pribadi
            'name' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'email' => 'required|email|unique:employees,email', // Sebaiknya unik
            'password' => 'required|min:6',
            'nik' => 'required|numeric',
            'phone' => 'required|numeric',
            'address' => 'required|string',
            'ktp' => 'nullable|numeric',
            
            // Data Pekerjaan
            'position' => 'required|string',
            'join_date' => 'required|date',
            'status' => 'required|in:full_time,part_time',
            
            // Data Payroll & Pajak (Baru)
            'base_salary' => 'required|numeric|min:0',
            'bank_name' => 'required|string',
            'bank_account_no' => 'required|string',
            'ptkp_status' => 'nullable|string',
            'npwp' => 'nullable|string',
            'bpjs_kesehatan_no' => 'nullable|string',
            'bpjs_ketenagakerjaan_no' => 'nullable|string',
            
            // Checkbox BPJS (0 atau 1)
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

        $this->logActivity(
            'Create Employee', 
            "Menambahkan karyawan baru: {$employee->name} (Posisi: {$employee->position})", 
            $userCompany->id
        );

        Cache::forget('employees_' . $userCompany->id);

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
            'phone' => 'required|numeric',
            'address' => 'required|string',
            'ktp' => 'nullable|numeric',
            'position' => 'required|string',
            'join_date' => 'required|date',
            'status' => 'required|in:full_time,part_time',
            'password' => 'nullable|min:6', // Boleh kosong saat update
            
            // Payroll Update
            'base_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string',
            'bank_account_no' => 'nullable|string',
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

        $data['participates_bpjs_kes'] = $request->has('participates_bpjs_kes');
        $data['participates_bpjs_tk'] = $request->has('participates_bpjs_tk');
        $data['participates_bpjs_jp'] = $request->has('participates_bpjs_jp');

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

        Cache::forget('employees_' . $userCompany->id);

        return redirect(route('employee'))->with('success', 'Employee successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $employee = Employee::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->first();

        if ($employee) {
            $name = $employee->name;
            $employee->delete();
            $this->logActivity('Delete Employee', "Menghapus karyawan: {$name}", $userCompany->id);

            Cache::forget('employees_' . $userCompany->id);
            return redirect(route('employee'))->with('success', 'Employee Berhasil Dihapus!');
        }

        return redirect(route('employee'))->with('error', 'Employee not found or access denied.');
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