<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Deduct;
use App\Models\DeductEmp;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DeductEmpController extends Controller
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

        $employee = $userCompany->employees()->findOrFail( $employeeId);

        $cacheKey = "deduct_emp_{$employeeId}";

        $employeeDeductions = Cache::remember($cacheKey, 180, function () use ($employeeId) {
            return DeductEmp::with('deduct')
                ->where('employee_id', $employeeId)
                ->get();
        });

        $deducts = $userCompany->deducts()->get();

        return view('deductEmp', compact('employee', 'employeeDeductions', 'deducts'));
    }

    public function store(Request $request, $employeeId)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'deduct_id' => 'required',
            'amount' => 'required|min:0',
        ]);

        $employee = $userCompany->employees()
            ->where('id', $employeeId)
            ->firstOrFail();

        $Deduct = $userCompany->deducts()
            ->where('id', $request->deduct_id)
            ->first();

        if (! $Deduct) {
            return back()->withErrors(['msg' => 'Invalid Deduction Data for this Company']);
        }

        DeductEmp::create([
            'employee_id' => $employeeId,
            'deduct_id' => $request->deduct_id,
            'amount' => $request->amount,
        ]);

        $employeeName = Employee::find($employeeId)->name ?? 'Unknown';

        $this->logActivity(
            'Assign Deduction',
            "Memberikan potongan {$Deduct->name} kepada {$employeeName} dengan nominal Rp {$request->amount} ",
            $userCompany->id
        );

        $this->clearCache($employeeId);

        return back()->with('success', 'Deduction assigned successfully!');
    }

    public function update(Request $request, $employeeId)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'amount' => 'required|min:0',
        ]);

        $assignment = DeductEmp::with(['employee', 'deduct'])->findOrFail($employeeId);

        if ($assignment->employee->compani_id !== $userCompany->id) {
            abort(403, 'Unauthorized Action');
        }

        $oldAmount = $assignment->amount;

        $assignment->update([
            'amount' => $request->amount,
        ]);

        $this->logActivity(
            'Update Assigned Deduction',
            "Mengubah nominal {$assignment->deduct->name} untuk {$assignment->employee->name} dari Rp {$oldAmount} menjadi Rp {$request->amount}",
            $userCompany->id
        );

        $this->clearCache($employeeId);

        return back()->with('success', 'Deduction amount updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $assignment = DeductEmp::with(['employee', 'deduct'])->findOrFail($id);

        if ($assignment->employee->compani_id !== $userCompany->id) {
            abort(403, 'Unauthorized Action');
        }

        $employeeId = $assignment->employee_id;
        $deductName = $assignment->deduct->name;
        $employeeName = $assignment->employee->name;

        $assignment->delete();

        $this->logActivity(
            'Remove Assigned Deduction',
            "Menghapus potongan {$deductName} dari {$employeeName}",
            $userCompany->id
        );

        $this->clearCache($employeeId);

        return back()->with('success', 'Deduction removed from employee!');
    }

    private function clearCache($employeeId)
    {
        Cache::forget("deduct_emp_{$employeeId}");
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
