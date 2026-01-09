<?php

namespace App\Exports;

use App\Models\Payroll;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

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

        $payrolls->each(function($p) {
            $companyBenefits = $p->payrollDetails->where('category', 'benefit')->sum('amount');
            
            $p->total_company_cost = $p->base_salary + $p->total_allowances + $companyBenefits;
        });

        $allocation = $payrolls->groupBy(function($item) {
            return $item->employee->branch->name ?? 'No Branch';
        })->map(function ($branchItems) {
            
            $outlets = $branchItems->groupBy(function($item) {
                return $item->employee->outlet->name ?? 'Main Outlet';
            })->map(function ($outletItems) {
                return [
                    'count' => $outletItems->count(),
                    'base_salary' => $outletItems->sum('base_salary'),
                    'total_allowances' => $outletItems->sum('total_allowances'),
                    'net_salary' => $outletItems->sum('net_salary'),
                    'total_company_cost' => $outletItems->sum('total_company_cost'),
                ];
            });

            return [
                'outlets' => $outlets,
                'summary' => [
                    'count' => $branchItems->count(),
                    'base_salary' => $branchItems->sum('base_salary'),
                    'total_allowances' => $branchItems->sum('total_allowances'),
                    'net_salary' => $branchItems->sum('net_salary'),
                    'total_company_cost' => $branchItems->sum('total_company_cost'),
                ]
            ];
        })->sortKeys();

        $grandTotal = [
            'count' => $payrolls->count(),
            'base_salary' => $payrolls->sum('base_salary'),
            'total_allowances' => $payrolls->sum('total_allowances'),
            'net_salary' => $payrolls->sum('net_salary'),
            'total_company_cost' => $payrolls->sum('total_company_cost'),
        ];

        return view('exports.payrollReport', [
            'allocation' => $allocation,
            'grandTotal' => $grandTotal,
            'payrolls' => $payrolls,
            'start' => $this->start,
            'end' => $this->end,
            'companyName' => auth()->user()->compani->name ?? 'Company Name'
        ]);
    }

    public function title(): string
    {
        return 'Payroll Report';
    }
}