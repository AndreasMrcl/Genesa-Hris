<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'compani_id',
            'name',
            'address',
            'phone',
            'category',
        ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }
}
