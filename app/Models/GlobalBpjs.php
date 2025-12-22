<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalBpjs extends Model
{
    use HasFactory;

    protected $fillable =
        [
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
}
