<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowEmp extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'employee_id',
            'allow_id',
            'amount',
        ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function allow()
    {
        return $this->belongsTo(Allow::class);
    }
}
