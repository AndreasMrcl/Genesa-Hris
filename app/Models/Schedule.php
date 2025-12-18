<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'compani_id',
        'employee_id',
        'shift_id',
        'date',
    ];

    public function getIsTodayAttribute()
    {
        return Carbon::parse($this->date)->isToday();
    }

    public function getIsPastAttribute()
    {
        return Carbon::parse($this->date)->isPast() && !$this->is_today;
    }

    public function getShiftNameAttribute()
    {
        return $this->shift->name ?? 'OFF';
    }

    public function getShiftColorAttribute()
    {
        return $this->shift->color ?? '#ef4444';
    }

    public function getCardStyleAttribute()
    {
        $border = $this->is_today ? 'border-indigo-500 ring-1 ring-indigo-100' : 'border-transparent';
        $bg = $this->is_past ? 'bg-gray-50 opacity-80' : 'bg-white';
        
        return "{$bg} {$border}";
    }

    public function getDateFormattedAttribute()
    {
        $d = Carbon::parse($this->date);
        return [
            'day_name' => $d->format('D'),
            'day_num'  => $d->format('d'),
            'month'    => $d->format('M'), 
        ];
    }

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}