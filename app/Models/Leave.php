<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'compani_id',
        'employee_id',
        'start_date',
        'end_date',
        'type',
        'note',
        'status',
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