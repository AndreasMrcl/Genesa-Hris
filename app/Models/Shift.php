<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'compani_id',
        'branch_id',
        'employee_id',
        'start_shift',
        'end_shift',
        'start_time',
        'end_time',
        'description',
    ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    

}