<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyPayrollConfig extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'compani_id',
        'bpjs_jkk_rate',
        'bpjs_kes_active',
        'bpjs_tk_active',
        'tax_method',
        'ump_amount',
        'infaq_percent',
    ];
    
    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }
}
