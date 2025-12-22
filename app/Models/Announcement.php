<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'compani_id',
            'content',
        ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }
}
