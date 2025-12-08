<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Allow;
use App\Models\AllowEmp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AllowEmpController extends Controller
{
    public function index($employeeId)
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

        $employee = $userCompany->employees()->findOrFail($employeeId);

        $cacheKey = 'allow_emp_' . $employeeId;

        $employeeAllowances = Cache::remember($cacheKey, 60, function () use ($employee) {
            return $employee->allowEmps()->with('allow')->get();
        });

        $allows = $userCompany->allows()->get(); 

        return view('allowEmp', compact('employee', 'employeeAllowances', 'allows'));
    }

    public function store(Request $request, $employeeId)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'allow_id' => 'required',
            'amount' => 'required|min:0',
        ]);

        $employee = Employee::where('id', $employeeId)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $Allow = Allow::where('id', $request->allow_id)
            ->where('compani_id', $userCompany->id)
            ->first();

        if (!$Allow) {
            return back()->withErrors(['msg' => 'Invalid Allowance Data for this Company']);
        }

        AllowEmp::create([
            'employee_id' => $employeeId,
            'allow_id' => $request->allow_id,
            'amount' => $request->amount,
        ]);

        $employeeName = Employee::find($employeeId)->name ?? 'Unknown';
        
        $this->logActivity(
            'Assign Allowance', 
            "Memberikan tunjangan {$Allow->name} kepada {$employeeName} dengan nominal Rp {$request->amount} ", 
            $userCompany->id
        );
        
        Cache::forget('allow_emp_' . $employeeId);

        return back()->with('success', 'Allowance assigned successfully!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'amount' => 'required|min:0',
        ]);

        $assignment = AllowEmp::with(['employee', 'allow'])->findOrFail($id);

        if ($assignment->employee->compani_id !== $userCompany->id) {
            abort(403, 'Unauthorized Action');
        }

        $oldAmount = $assignment->amount;

        $assignment->update([
            'amount' => $request->amount
        ]);

        $this->logActivity(
            'Update Assigned Allowance', 
            "Mengubah nominal {$assignment->allow->name} untuk {$assignment->employee->name} dari Rp {$oldAmount} menjadi Rp {$request->amount}", 
            $userCompany->id
        );

        Cache::forget('allow_emp_' . $assignment->employee_id);

        return back()->with('success', 'Allowance amount updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $assignment = AllowEmp::with(['employee', 'allow'])->findOrFail($id);

        if ($assignment->employee->compani_id !== $userCompany->id) {
            abort(403, 'Unauthorized Action');
        }

        $employeeId = $assignment->employee_id;
        $allowName = $assignment->allow->name;
        $employeeName = $assignment->employee->name;
        
        $assignment->delete();

        $this->logActivity(
            'Remove Assigned Allowance', 
            "Menghapus tunjangan {$allowName} dari {$employeeName}", 
            $userCompany->id
        );

        Cache::forget('allow_emp_' . $employeeId);

        return back()->with('success', 'Allowance removed from employee!');
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