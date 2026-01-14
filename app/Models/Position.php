<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'compani_id',
        'is_head',
        'name',
        'category',
        'base_salary_default',
    ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}