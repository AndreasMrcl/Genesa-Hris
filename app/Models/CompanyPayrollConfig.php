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
        'tax_method',
        'ump_amount',
        'infaq_percent',
        'bpjs_jkk_rate',
        'bpjs_kes_active',
        'bpjs_tk_active',
        'kes_comp_percent',
        'kes_emp_percent',
        'kes_cap_amount',
        'jht_comp_percent',
        'jht_emp_percent',
        'jp_comp_percent',
        'jp_emp_percent',
        'jp_cap_amount',
        'jkm_comp_percent',
    ];
    
    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }
}