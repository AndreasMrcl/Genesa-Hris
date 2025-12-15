<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LeaveController extends Controller
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

        $cacheKey = "leaves_{$userCompany->id}";

        $leaves = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->leaves()->with('employee')->get();
        });

        $employee = $userCompany->employees()->get();

        return view('leave', compact('leaves', 'employee'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'type'        => 'required|string',
            'note'      => 'required|string',
            'status'      => 'required|string',
        ]);

        $leave = Leave::create([
            'employee_id'     => $data['employee_id'],
            'start_date'     => $data['start_date'],
            'end_date'     => $data['end_date'],
            'type'     => $data['type'],
            'note'     => $data['note'],
            'status'     => $data['status'],
            'compani_id'  => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Leave',
            "Membuat leave '{$leave->employee->name}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('leave'))->with('success', 'Leave successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required',
            'note' => 'required',
            'status' => 'required',
        ]);

        $leave = Leave::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $leave->employee->name;

        $leave->update([
            'employee_id' => $data['employee_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'type' => $data['type'],
            'note' => $data['note'],
            'status' => $data['status'],
        ]);


        $this->logActivity(
            'Update Leave',
            "Mengubah leave '{$oldContent}' menjadi '{$leave->status}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('leave'))->with('success', 'Leave successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $leave = Leave::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->first();

        $oldContent = $leave->employee->name;

        $leave->delete();

        $this->logActivity(
            'Delete Leave',
            "Menghapus leave '{$oldContent}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('leave'))->with('success', 'Leave successfully deleted!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("leaves_{$companyId}");
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