<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Overtime;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EssController extends Controller
{
    public function home()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $employee = Auth::guard('employee')->user();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->latest()
            ->first();

        return view('ess.home', compact('employee', 'attendance'));
    }

    public function attendance()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $attendances = Auth::guard('employee')->user()->attendances;

        return view('ess.attendance', compact('attendances'));
    }

    public function leave()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $leaves = Auth::guard('employee')->user()->leaves;

        return view('ess.leave', compact('leaves'));
    }

    public function reqLeave(Request $request)
    {

        $userCompany = Auth::guard('employee')->user()->compani;

        $data = $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required',
            'reason' => 'required',
            'status' => 'required',
        ]);

        $data['compani_id'] = $userCompany->id;

        Leave::create($data);

        Cache::forget('leaves');

        return redirect(route('ess-leave'))->with('success', 'Leave successfully created!');
    }


    public function overtime()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $overtimes = Auth::guard('employee')->user()->overtimes;

        return view('ess.overtime', compact('overtimes'));
    }

    public function reqOvertime(Request $request)
    {

        $userCompany = Auth::guard('employee')->user()->compani;

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

        return redirect(route('ess-leave'))->with('success', 'Leave successfully created!');
    }


    public function note()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $notes = Auth::guard('employee')->user()->notes;

        return view('ess.note', compact('notes'));
    }

    public function payroll()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        return view('ess.payroll');
    }

    public function organization()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        return view('ess.organization');
    }

    public function absen()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        return view('ess.absen');
    }

    public function profil()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect('/');
        }

        $employee = Auth::guard('employee')->user();


        return view('ess.profil', compact('employee'));
    }
}
