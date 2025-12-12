<?php

namespace App\Exports;

use App\Models\Payroll;
use App\Models\Branch;
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
        // 1. Ambil Data Payroll dengan Relasi Lengkap
        $payrolls = Payroll::with(['employee.branch', 'employee.position', 'payrollDetails'])
            ->where('compani_id', $this->companyId)
            ->where('pay_period_start', $this->start)
            ->where('pay_period_end', $this->end)
            ->get();

        // 2. Hitung Summary Global (Executive Summary)
        $summary = [
            'total_employees' => $payrolls->count(),
            'total_net_salary' => $payrolls->sum('net_salary'),
            'total_base_salary' => $payrolls->sum('base_salary'),
            'total_allowances' => $payrolls->sum('total_allowances'),
            'total_deductions' => $payrolls->sum('total_deductions'),
            
            // Hitung total pajak & BPJS dari detail
            'total_tax' => $payrolls->flatMap->payrollDetails
                            ->where('category', 'deduction')
                            ->filter(fn($d) => str_contains($d->name, 'PPh 21'))
                            ->sum('amount'),
                            
            'total_bpjs_emp' => $payrolls->flatMap->payrollDetails
                            ->where('category', 'deduction')
                            ->filter(fn($d) => str_contains($d->name, 'BPJS') || str_contains($d->name, 'JHT') || str_contains($d->name, 'Jaminan'))
                            ->sum('amount'),
        ];

        // 3. Grouping per Cabang (Pivot Table)
        $branchPivot = $payrolls->groupBy('employee.branch.name')->map(function ($items, $branchName) {
            return [
                'name' => $branchName ?: 'No Branch',
                'count' => $items->count(),
                'total_basic' => $items->sum('base_salary'),
                'total_net' => $items->sum('net_salary'),
            ];
        });

        return view('exports.payrollReport', [
            'payrolls' => $payrolls,
            'summary' => $summary,
            'branchPivot' => $branchPivot,
            'start' => $this->start,
            'end' => $this->end,
            'companyName' => auth()->user()->compani->name ?? 'Company Name'
        ]);
    }

    public function title(): string
    {
        return 'Laporan Gaji Bulanan';
    }
}