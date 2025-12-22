<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allow extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'compani_id',
            'name',
            'type',
            'is_taxable',
        ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function allowEmps()
    {
        return $this->hasMany(AllowEmp::class);
    }
}
