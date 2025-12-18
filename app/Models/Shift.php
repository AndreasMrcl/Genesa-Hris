<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Shift extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'compani_id',
        'branch_id',
        'name',
        'start_time', 
        'end_time', 
        'is_cross_day', 
        'color'
    ];
    
    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        if ($this->is_cross_day) {
            $end->addDay();
        }
        
        return $start->diffInHours($end) . ' Hours';
    }

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}