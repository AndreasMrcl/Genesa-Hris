<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalPtkp extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'compani_id',
        'code',
        'amount',
        'ter_category',
    ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }
}