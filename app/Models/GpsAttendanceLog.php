<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpsAttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'compani_id',
        'attendance_date',
        'check_in_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_address',
        'check_in_distance',
        'check_in_photo',
        'check_out_time',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_address',
        'check_out_distance',
        'check_out_photo',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function getWorkDurationAttribute()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $totalMinutes = $this->check_in_time->diffInMinutes($this->check_out_time);
            $hours = intdiv($totalMinutes, 60);
            $minutes = $totalMinutes % 60;
            return "{$hours}h {$minutes}m";
        }
        return '-';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'present' => 'bg-green-100 text-green-700 border-green-200',
            'late' => 'bg-orange-100 text-orange-700 border-orange-200',
            'early_leave' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'incomplete' => 'bg-red-100 text-red-700 border-red-200',
        ];
        
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
    }

    public function countsAsPresent()
    {
        return in_array($this->status, ['present', 'late', 'early_leave', 'incomplete']);
    }
}