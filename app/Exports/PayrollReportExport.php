<?php

namespace App\Exports;

use App\Models\Payroll;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Str;

class PayrollReportExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $companyId;
    protected $start;
    protected $end;

    public function __construct($companyId, $start, $end)
    {
        $this->companyId = $companyId;
        $this->start = $start;
        $this->end = $end;
    }

    public function view(): View
    {
        // 1. Ambil Data
        $payrolls = Payroll::with(['employee.branch', 'employee.outlet', 'employee.position', 'payrollDetails'])
            ->where('payrolls.compani_id', $this->companyId)
            ->where('payrolls.pay_period_start', $this->start)
            ->where('payrolls.pay_period_end', $this->end)
            ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->orderBy('employees.branch_id')
            ->orderBy('employees.outlet_id')
            ->orderBy('employees.name')
            ->select('payrolls.*')
            ->get();

        // 2. KALKULASI DETAIL (Breakdown Komponen)
        $payrolls->each(function($p) {
            $details = $p->payrollDetails;

            // A. Tunjangan (Pajak vs Lainnya)
            $p->allowance_tax = $details->filter(fn($d) => 
                $d->category == 'allowance' && Str::contains($d->name, 'PPh 21')
            )->sum('amount');
            $p->allowance_other = $p->total_allowances - $p->allowance_tax;

            // B. Potongan (BPJS vs Pajak vs Lainnya)
            $p->deduction_bpjs = $details->filter(fn($d) => 
                $d->category == 'deduction' && (
                    Str::contains($d->name, 'BPJS') || 
                    Str::contains($d->name, 'JHT') || 
                    Str::contains($d->name, 'JP') ||
                    Str::contains($d->name, 'Kesehatan')
                )
            )->sum('amount');
            
            $p->deduction_tax = $details->filter(fn($d) => 
                $d->category == 'deduction' && Str::contains($d->name, 'PPh 21')
            )->sum('amount');

            $p->deduction_other = $p->total_deductions - $p->deduction_bpjs - $p->deduction_tax;

            // C. Beban Perusahaan (Benefit Non-Tunai)
            $p->company_bpjs_cost = $details->where('category', 'benefit')->sum('amount');
            $p->total_company_cost = $p->base_salary + $p->total_allowances + $p->company_bpjs_cost;
        });

        // 3. GROUPING
        $allocation = $payrolls->groupBy(function($item) {
            return $item->employee->branch->name ?? 'No Branch';
        })->map(function ($branchItems) {
            
            // Sub-group Outlet
            $outlets = $branchItems->groupBy(function($item) {
                return $item->employee->outlet->name ?? 'Main Outlet';
            })->map(function ($outletItems) {
                return $this->sumData($outletItems);
            });

            return [
                'outlets' => $outlets,
                'summary' => $this->sumData($branchItems)
            ];
        })->sortKeys();

        // 4. Grand Total
        $grandTotal = $this->sumData($payrolls);

        return view('exports.payrollReport', [
            'allocation' => $allocation,
            'grandTotal' => $grandTotal,
            'payrolls' => $payrolls,
            'start' => $this->start,
            'end' => $this->end,
            'companyName' => auth()->user()->compani->name ?? 'Company Name'
        ]);
    }

    private function sumData($collection) {
        return [
            'count' => $collection->count(),
            'base_salary' => $collection->sum('base_salary'),
            'allowance_tax' => $collection->sum('allowance_tax'),
            'allowance_other' => $collection->sum('allowance_other'),
            
            'deduction_bpjs' => $collection->sum('deduction_bpjs'),
            'deduction_tax' => $collection->sum('deduction_tax'),
            'deduction_other' => $collection->sum('deduction_other'),
            
            'net_salary' => $collection->sum('net_salary'),
            'company_bpjs_cost' => $collection->sum('company_bpjs_cost'),
            'total_company_cost' => $collection->sum('total_company_cost'),
        ];
    }

    public function title(): string
    {
        return 'Payroll Analysis';
    }
}