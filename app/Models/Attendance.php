<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'compani_id',
        'employee_id',
        'period_start',
        'period_end',
        'total_present',
        'total_late',
        'total_sick',
        'total_permission',
        'total_permission_letter',
        'total_alpha',
        'total_leave',
        'note'
    ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}