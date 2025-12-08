<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollDetail extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'payroll_id',
        'name',
        'category', // Kategori 'benefit' hanya untuk non-cash
        'amount',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
