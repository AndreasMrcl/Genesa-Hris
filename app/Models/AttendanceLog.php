<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AttendanceLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'compani_id',
        'employee_id',
        'fingerprint_id',
        'device_sn',
        'scan_time',
        'verification_mode',
        'scan_status',
        'is_processed'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }
}
