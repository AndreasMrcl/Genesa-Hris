<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'compani_id',
            'employee_id',
            'overtime_date',
            'start_time',
            'end_time',
            'status',
            'overtime_pay',
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
