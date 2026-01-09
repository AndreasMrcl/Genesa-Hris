<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable =
        [
            'name',
            'compani_id',
            'branch_id',
            'outlet_id',
            'position_id',
            'email',
            'nik',
            'fingerprint_id',
            'npwp',
            'ktp',
            'bpjs_kesehatan_no',
            'bpjs_ketenagakerjaan_no',
            'ptkp_status',
            'phone',
            'address',
            'base_salary',
            'working_days',
            'payroll_method',
            'bank_name',
            'bank_account_no',
            'participates_bpjs_kes',
            'participates_bpjs_tk',
            'participates_bpjs_jp',
            'join_date',
            'status',
            'password',
        ];

    protected $hidden = [
        'password',
    ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function allowEmps()
    {
        return $this->hasMany(AllowEmp::class);
    }

    public function deductEmps()
    {
        return $this->hasMany(DeductEmp::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
