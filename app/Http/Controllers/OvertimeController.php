<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Overtime;
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

        $cacheKey = 'overtimes_' . $userCompany->id;

        $overtimes = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userCompany) {
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

        $data['compani_id'] = $userCompany->id;

        Overtime::create($data);

        Cache::forget('overtimes');

        return redirect(route('overtime'))->with('success', 'Overtime successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userCompany = auth()->user()->compani;

        $request->validate([
            'employee_id' => 'required',
            'overtime_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required',
            'overtime_pay' => 'required',
        ]);

        $data = $request->only(['employee_id', 'overtime_date', 'start_time', 'end_time', 'total_hours', 'reason', 'status', 'overtime_pay']);

        $data['compani_id'] = $userCompany->id;

        Overtime::where('id', $id)->update($data);

        Cache::forget('overtimes');

        return redirect(route('overtime'))->with('success', 'Overtime successfully updated!');
    }

    public function destroy($id)
    {
        Overtime::destroy($id);

        Cache::forget('overtimes_' . $userCompany->id);

        return redirect(route('overtime'))->with('success', 'Overtime successfully deleted!');
    }

 
}
