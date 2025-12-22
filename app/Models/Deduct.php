<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduct extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'compani_id',
            'name',
            'type',
        ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function deductEmps()
    {
        return $this->hasMany(DeductEmp::class);
    }
}
