<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OvertimeController extends Controller
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

        $cacheKey = "overtimes_{$userCompany->id}";

        $overtimes = Cache::remember($cacheKey, 180, function () use ($userCompany) {
            return $userCompany->overtimes()->with('employee')->get();
        });

        $employee = $userCompany->employees()->get();

        return view('overtime', compact('overtimes', 'employee'));
    }

    public function store(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required',
            'overtime_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required',
            'overtime_pay' => 'required',
        ]);

        $overtime = Overtime::create([
            'employee_id'     => $data['employee_id'],
            'overtime_date'     => $data['overtime_date'],
            'start_time'     => $data['start_time'],
            'end_time'     => $data['end_time'],
            'status'     => $data['status'],
            'overtime_pay'     => $data['overtime_pay'],
            'compani_id'  => $userCompany->id,
        ]);

        $this->logActivity(
            'Create Overtime',
            "Menambahkan overtime '{$overtime->employee->name}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('overtime'))->with('success', 'Overtime successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required',
            'overtime_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required',
            'overtime_pay' => 'required',
        ]);

        $overtime = Overtime::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->firstOrFail();

        $oldContent = $overtime->employee->name;

        $overtime->update([
            'employee_id'     => $data['employee_id'],
            'overtime_date'     => $data['overtime_date'],
            'start_time'     => $data['start_time'],
            'end_time'     => $data['end_time'],
            'status'     => $data['status'],
            'overtime_pay'     => $data['overtime_pay'],
        ]);

        $this->logActivity(
            'Update Overtime',
            "Mengubah Overtime '{$oldContent}'",
            $userCompany->id
        );

        $this->clearCache($userCompany->id);

        return redirect(route('overtime'))->with('success', 'Overtime successfully updated!');
    }

    public function destroy($id)
    {
        $userCompany = auth()->user()->compani;

        $overtime = Overtime::where('id', $id)
            ->where('compani_id', $userCompany->id)
            ->first();

        if ($overtime) {
            $name = $overtime->employee->name;
            $overtime->delete();

            $this->logActivity(
                'Delete Overtime',
                "{$name}",
                $userCompany->id
            );
        }

        $this->clearCache($userCompany->id);

        return redirect(route('overtime'))->with('success', 'Overtime successfully deleted!');
    }

    private function clearCache($companyId)
    {
        Cache::forget("overtimes_{$companyId}");
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
