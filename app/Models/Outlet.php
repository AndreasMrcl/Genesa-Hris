<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'branch_id',
        'name',
        'phone',
        'address',
        'latitude',     
        'longitude',    
        'gps_radius',    
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
