<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compani extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'user_id',
        'name',
        'no_telpon',
        'ktp',
        'atas_nama',
        'bank',
        'no_rek',
        'company',
        'status',
        'location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
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

    public function allows()
    {
        return $this->hasMany(Allow::class);
    }

    public function deducts()
    {
        return $this->hasMany(Deduct::class);
    }

    public function companyPayrollConfig()
    {
        return $this->hasOne(CompanyPayrollConfig::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
