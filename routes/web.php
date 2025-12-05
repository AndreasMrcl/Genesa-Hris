<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EssController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AllowController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DeductController;
use App\Http\Controllers\CompaniController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AllowEmpController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\DeductEmpController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CompanyPayrollConfigController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/signin', [AuthController::class, 'signin'])->name('signin');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::fallback(function () {
    return view('errors.404');
});

Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/setting', [PageController::class, 'setting'])->name('setting');
    Route::get('/profile', [PageController::class, 'profile'])->name('profile');

    //COMPANY
    Route::get('/company', [CompaniController::class, 'index'])->name('company');
    Route::get('/addcompany', [CompaniController::class, 'create'])->name('addcompany');
    Route::post('/postcompany', [CompaniController::class, 'store'])->name('postcompany');
    Route::delete('/company/{id}/delete', [CompaniController::class, 'destroy'])->name('delcompany');

    //BRANCH
    Route::get('/branch', [BranchController::class, 'index'])->name('branch');
    Route::post('/postbranch', [BranchController::class, 'store'])->name('postbranch');
    Route::get('/branch/{id}/show', [BranchController::class, 'show'])->name('detailbranch');
    Route::put('/branch/{id}/update', [BranchController::class, 'update'])->name('updatebranch');
    Route::delete('/branch/{id}/delete', [BranchController::class, 'destroy'])->name('delbranch');

    //EMPLOYEE
    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee');
    Route::post('/postemployee', [EmployeeController::class, 'store'])->name('postemployee');
    Route::put('/employee/{id}/update', [EmployeeController::class, 'update'])->name('updateemployee');
    Route::delete('/employee/{id}/delete', [EmployeeController::class, 'destroy'])->name('delemployee');

    //LEAVE
    Route::get('/leave', [LeaveController::class, 'index'])->name('leave');
    Route::post('/postleave', [LeaveController::class, 'store'])->name('postleave');
    Route::put('/leave/{id}/update', [LeaveController::class, 'update'])->name('updateleave');
    Route::delete('/leave/{id}/delete', [LeaveController::class, 'destroy'])->name('delleave');

    //OVERTIME
    Route::get('/overtime', [OvertimeController::class, 'index'])->name('overtime');
    Route::get('/addovertime', [OvertimeController::class, 'create'])->name('addovertime');
    Route::post('/postovertime', [OvertimeController::class, 'store'])->name('postovertime');
    Route::get('/editovertime/{id}', [OvertimeController::class, 'edit'])->name('editovertime');
    Route::put('/overtime/{id}/update', [OvertimeController::class, 'update'])->name('updateovertime');
    Route::delete('/overtime/{id}/delete', [OvertimeController::class, 'destroy'])->name('delovertime');

    //NOTE
    Route::get('/note', [NoteController::class, 'index'])->name('note');
    Route::post('/postnote', [NoteController::class, 'store'])->name('postnote');
    Route::put('/note/{id}/update', [NoteController::class, 'update'])->name('updatenote');
    Route::delete('/note/{id}/delete', [NoteController::class, 'destroy'])->name('delnote');

     //ATTENDANCE
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/attendance/manage', [AttendanceController::class, 'manage'])->name('manageattendance');
    Route::post('/attendance/store', [AttendanceController::class, 'storeBatch'])->name('postattendance');
    Route::delete('/attendance/batch-delete', [AttendanceController::class, 'destroyPeriod'])->name('delattendance');

    //SHIFT
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift');
    Route::post('/postshift', [ShiftController::class, 'store'])->name('postshift');
    Route::put('/shift/{id}/update', [ShiftController::class, 'update'])->name('updateshift');
    Route::delete('/shift/{id}/delete', [ShiftController::class, 'destroy'])->name('delshift');

    //PAYROLL
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll');
    Route::post('/postpayroll', [PayrollController::class, 'store'])->name('postpayroll');
    Route::put('/payroll/{id}/update', [PayrollController::class, 'update'])->name('updatepayroll');
    Route::delete('/payroll/{id}/delete', [PayrollController::class, 'destroy'])->name('delpayroll');

    //NOTE
    Route::get('/note', [NoteController::class, 'index'])->name('note');
    Route::post('/postnote', [NoteController::class, 'store'])->name('postnote');
    Route::put('/note/{id}/update', [NoteController::class, 'update'])->name('updatenote');
    Route::delete('/note/{id}/delete', [NoteController::class, 'destroy'])->name('delnote');

    //ALLOWANCE
    Route::get('/allowance', [AllowController::class, 'index'])->name('allowance');
    Route::post('/postallowance', [AllowController::class, 'store'])->name('postallowance');
    Route::put('/allowance/{id}/update', [AllowController::class, 'update'])->name('updateallowance');
    Route::delete('/allowance/{id}/delete', [AllowController::class, 'destroy'])->name('delallowance');

    //DEDUCTION
    Route::get('/deduction', [DeductController::class, 'index'])->name('deduction');
    Route::post('/postdeduction', [DeductController::class, 'store'])->name('postdeduction');
    Route::put('/deduction/{id}/update', [DeductController::class, 'update'])->name('updatededuction');
    Route::delete('/deduction/{id}/delete', [DeductController::class, 'destroy'])->name('deldeduction');

    //ALLOWANCE EMPLOYEE
    Route::get('/employees/{id}/allowances', [AllowEmpController::class, 'index'])->name('allowanceEmp');
    Route::post('/employees/{id}/allowances', [AllowEmpController::class, 'store'])->name('postallowanceEmp');
    Route::put('/allowance-employee/{id}/update', [AllowEmpController::class, 'update'])->name('updateallowanceEmp');
    Route::delete('/allowance-employee/{id}/delete', [AllowEmpController::class, 'destroy'])->name('delallowanceEmp');

    //DEDUCTION EMPLOYEE
    Route::get('/employees/{id}/deductions', [DeductEmpController::class, 'index'])->name('deductionEmp');
    Route::post('/employees/{id}/deductions', [DeductEmpController::class, 'store'])->name('postdeductionEmp');
    Route::put('/deduction-employee/{id}/update', [DeductEmpController::class, 'update'])->name('updatedeductionEmp');
    Route::delete('/deduction-employee/{id}/delete', [DeductEmpController::class, 'destroy'])->name('deldeductionEmp');

    // PAYROLL EXPORT EXCEL
    Route::get('/payrolls/export', [PayrollController::class, 'exportExcel'])->name('payrollExport');

    //PAYROLL
    Route::get('/payrolls', [PayrollController::class, 'index'])->name('payroll');
    Route::get('/payrolls/period/{start}/{end}', [PayrollController::class, 'period'])->name('periodPayroll');
    Route::get('/payrolls/create', [PayrollController::class, 'create'])->name('createpayroll');
    Route::post('/payrolls', [PayrollController::class, 'store'])->name('postpayroll');
    Route::get('/payrolls/{id}', [PayrollController::class, 'show'])->name('showpayroll');
    Route::delete('/payrolls/batch-delete', [PayrollController::class, 'destroyPeriod'])->name('delpayrollBatch');
    Route::delete('/payrolls/{id}', [PayrollController::class, 'destroy'])->name(name: 'delpayroll');

    //ACTIVITY LOG
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activityLog');

    Route::get('/companyconfig', [CompanyPayrollConfigController::class, 'index'])->name('companyconfig');
    Route::put('/companyconfig/update', [CompanyPayrollConfigController::class, 'update'])->name('updatecompanyconfig');
});

Route::middleware('auth:employee')->group(function () {
    Route::get('/ess-home', [EssController::class, 'home'])->name('ess-home');

    Route::get('/ess-attendance', [EssController::class, 'attendance'])->name('ess-attendance');

    Route::get('/ess-leave', [EssController::class, 'leave'])->name('ess-leave');
    Route::post('/req-leave', [EssController::class, 'reqLeave'])->name('req-leave');

    Route::get('/ess-overtime', [EssController::class, 'overtime'])->name('ess-overtime');
    Route::post('/req-overtime', [EssController::class, 'reqOvertime'])->name('req-overtime');

    Route::get('/ess-note', [EssController::class, 'note'])->name('ess-note');

    Route::get('/ess-payroll', [EssController::class, 'payroll'])->name('ess-payroll');

    Route::get('/ess-organization', [EssController::class, 'organization'])->name('ess-organization');

    Route::get('/ess-absen', [EssController::class, 'absen'])->name('ess-absen');

    Route::get('/ess-profil', [EssController::class, 'profil'])->name('ess-profil');
});
