<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;
    protected $fillable =
    [
        'name',
        'compani_id',
        'branch_id',
        'position_id',
        'email',
        'nik',
        'npwp',
        'ktp',
        'bpjs_kesehatan_no',
        'bpjs_ketenagakerjaan_no',
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
}