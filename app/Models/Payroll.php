<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use stdClass;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'compani_id',
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'base_salary',
        'total_allowances',
        'total_deductions',
        'net_salary',
        'status',
        'payment_date',
    ];

    public function compani()
    {
        return $this->belongsTo(Compani::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function getBenefitsAttribute()
    {
        $allBenefits = $this->payrollDetails->where('category', 'benefit');

        $tkComponents = ['JKK', 'JKM', 'JHT', 'JP'];

        $bpjsTkItems = $allBenefits->whereIn('name', $tkComponents);
        $otherItems  = $allBenefits->whereNotIn('name', $tkComponents);

        $finalBenefits = $otherItems->values();

        $totalTk = $bpjsTkItems->sum('amount');

        if ($totalTk > 0) {
            $dummy = new stdClass();
            $dummy->name = 'Tunj. BPJS TK';
            $dummy->amount = $totalTk;
            $dummy->category = 'benefit';

            $finalBenefits->push($dummy);
        }

        return $finalBenefits;
    }
}
