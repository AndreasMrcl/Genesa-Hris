<?php

namespace App\Exports;

use App\Models\Payroll;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Str;

class PayrollReportExport implements FromView, ShouldAutoSize, WithTitle, WithStyles
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
        // 1. Ambil Data dengan relasi lengkap
        $payrolls = Payroll::with([
            'employee.branch',
            'employee.outlet',
            'employee.position',
            'payrollDetails'
        ])
            ->where('payrolls.compani_id', $this->companyId)
            ->where('payrolls.pay_period_start', $this->start)
            ->where('payrolls.pay_period_end', $this->end)
            ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->orderBy('employees.branch_id')
            ->orderBy('employees.outlet_id')
            ->orderBy('employees.name')
            ->select('payrolls.*')
            ->get();

        // 2. KALKULASI DETAIL (Breakdown Komponen Lebih Detail)
        $payrolls->each(function ($p) {
            $details = $p->payrollDetails;

            // === TUNJANGAN (Breakdown Detail) ===
            // A1. Tunjangan BPJS (yang dibayar perusahaan sebagai benefit)
            $p->allowance_bpjs = $details->filter(
                fn($d) =>
                $d->category == 'benefit' && (
                    Str::contains($d->name, 'BPJS') ||
                    Str::contains($d->name, 'JKK') ||
                    Str::contains($d->name, 'JKM') ||
                    Str::contains($d->name, 'JHT') ||
                    Str::contains($d->name, 'JP') ||
                    Str::contains($d->name, 'Kesehatan')
                )
            )->sum('amount');

            // A2. Tunjangan PPh 21 (Gross Up)
            $p->allowance_tax = $details->filter(
                fn($d) =>
                $d->category == 'benefit' && Str::contains($d->name, 'PPh 21')
            )->sum('amount');

            // A3. Tunjangan Jabatan
            $p->allowance_position = $details->filter(
                fn($d) =>
                $d->category == 'allowance' && (
                    Str::contains(strtolower($d->name), 'jabatan') ||
                    Str::contains(strtolower($d->name), 'posisi')
                )
            )->sum('amount');

            // A4. Tunjangan Lainnya (sisa dari total allowance)
            $p->allowance_other = $p->total_allowances - $p->allowance_tax - $p->allowance_position;

            // === POTONGAN (Breakdown Detail) ===
            // B1. Potongan BPJS Karyawan
            $p->deduction_bpjs = $details->filter(
                fn($d) =>
                $d->category == 'deduction' && (
                    Str::contains($d->name, 'BPJS') ||
                    Str::contains($d->name, 'JHT') ||
                    Str::contains($d->name, 'JP') ||
                    Str::contains($d->name, 'Kesehatan')
                )
            )->sum('amount');

            // B2. Potongan PPh 21
            $p->deduction_tax = $details->filter(
                fn($d) =>
                $d->category == 'deduction' && Str::contains($d->name, 'PPh 21')
            )->sum('amount');

            // B3. Potongan Infaq
            $p->deduction_infaq = $details->filter(
                fn($d) =>
                $d->category == 'deduction' && Str::contains($d->name, 'Infaq')
            )->sum('amount');

            // B4. Potongan Absensi (Alpha, Izin, Terlambat)
            $p->deduction_attendance = $details->filter(
                fn($d) =>
                $d->category == 'deduction' && (
                    Str::contains($d->name, 'Alpha') ||
                    Str::contains($d->name, 'Izin') ||
                    Str::contains($d->name, 'Terlambat')
                )
            )->sum('amount');

            // B5. Potongan Lainnya
            $p->deduction_other = $p->total_deductions - $p->deduction_bpjs - $p->deduction_tax - $p->deduction_infaq - $p->deduction_attendance;

            // === BEBAN PERUSAHAAN ===
            $p->company_bpjs_cost = $details->where('category', 'benefit')->sum('amount');
            $p->total_company_cost = $p->base_salary + $p->total_allowances + $p->company_bpjs_cost;
        });

        // 3. GROUPING per Branch > Outlet
        $allocation = $payrolls->groupBy(function ($item) {
            return $item->employee->branch->name ?? 'No Branch';
        })->map(function ($branchItems) {

            $outlets = $branchItems->groupBy(function ($item) {
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

    private function sumData($collection)
    {
        return [
            'count' => $collection->count(),
            'base_salary' => $collection->sum('base_salary'),

            // Tunjangan Detail
            'allowance_bpjs' => $collection->sum('allowance_bpjs'),
            'allowance_tax' => $collection->sum('allowance_tax'),
            'allowance_position' => $collection->sum('allowance_position'),
            'allowance_other' => $collection->sum('allowance_other'),

            // Potongan Detail
            'deduction_bpjs' => $collection->sum('deduction_bpjs'),
            'deduction_tax' => $collection->sum('deduction_tax'),
            'deduction_infaq' => $collection->sum('deduction_infaq'),
            'deduction_attendance' => $collection->sum('deduction_attendance'),
            'deduction_other' => $collection->sum('deduction_other'),

            // Total
            'net_salary' => $collection->sum('net_salary'),
            'company_bpjs_cost' => $collection->sum('company_bpjs_cost'),
            'total_company_cost' => $collection->sum('total_company_cost'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['size' => 11]],
        ];
    }

    public function title(): string
    {
        return 'Payroll Analysis';
    }
}
