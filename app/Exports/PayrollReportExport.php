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
        // 1. Ambil semua payroll dengan relasi
        $payrolls = Payroll::with([
            'employee.branch',
            'employee.outlet',
            'payrollDetails',  // Untuk ambil allowance dan benefit
        ])
        ->where('payrolls.compani_id', $this->companyId)
        ->where('payrolls.pay_period_start', $this->start)
        ->where('payrolls.pay_period_end', $this->end)
        ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
        ->join('branches', 'employees.branch_id', '=', 'branches.id')
        ->orderBy('branches.name')
        ->orderBy('employees.name')
        ->select('payrolls.*')
        ->get();

        // 2. Kalkulasi untuk setiap payroll
        $payrolls->each(function ($payroll) {
            $details = $payroll->payrollDetails;
            
            // Tunjangan Jabatan (allowance dengan nama "Jabatan")
            $tunjanganJabatan = $details->filter(function ($detail) {
                return $detail->category == 'allowance' && 
                       stripos($detail->name, 'jabatan') !== false;
            })->sum('amount');
            
            // Cast ke float untuk memastikan tidak ter-truncate
            $payroll->tunjangan_jabatan = (float) $tunjanganJabatan;
            
            // === BPJS TK ===
            // BPJS TK Perusahaan (benefit: JKK, JKM, JHT, JP)
            $bpjsTkPerusahaan = $details->filter(function ($detail) {
                return $detail->category == 'benefit' && 
                       (stripos($detail->name, 'JKK') !== false ||
                        stripos($detail->name, 'JKM') !== false ||
                        stripos($detail->name, 'JHT') !== false ||
                        stripos($detail->name, 'JP') !== false);
            })->sum('amount');
            $payroll->bpjs_tk_perusahaan = (float) $bpjsTkPerusahaan;
            
            // BPJS TK Karyawan (deduction: JHT, JP)
            $bpjsTkKaryawan = $details->filter(function ($detail) {
                return $detail->category == 'deduction' && 
                       (stripos($detail->name, 'JHT') !== false ||
                        stripos($detail->name, 'JP') !== false);
            })->sum('amount');
            $payroll->bpjs_tk_karyawan = (float) $bpjsTkKaryawan;
            
            // === BPJS KESEHATAN ===
            // BPJS Kesehatan Perusahaan (benefit: Kesehatan)
            $bpjsKesPerusahaan = $details->filter(function ($detail) {
                return $detail->category == 'benefit' && 
                       stripos($detail->name, 'Kesehatan') !== false;
            })->sum('amount');
            $payroll->bpjs_kes_perusahaan = (float) $bpjsKesPerusahaan;
            
            // BPJS Kesehatan Karyawan (deduction: Kesehatan)
            $bpjsKesKaryawan = $details->filter(function ($detail) {
                return $detail->category == 'deduction' && 
                       stripos($detail->name, 'Kesehatan') !== false;
            })->sum('amount');
            $payroll->bpjs_kes_karyawan = (float) $bpjsKesKaryawan;
            
            // Total BPJS Benefit (untuk Total Gaji + BPJS)
            $bpjsBenefit = $bpjsTkPerusahaan + $bpjsKesPerusahaan;
            $payroll->bpjs_benefit = (float) $bpjsBenefit;
            
            // Total Gaji + BPJS
            $payroll->gaji_plus_bpjs = (float) ($payroll->base_salary + $payroll->bpjs_benefit);
            
            // === INFAQ ===
            // Infaq (deduction dengan nama "Infaq")
            $infaq = $details->filter(function ($detail) {
                return $detail->category == 'deduction' && 
                       stripos($detail->name, 'Infaq') !== false;
            })->sum('amount');
            $payroll->infaq = (float) $infaq;
            
            // === THP (Take Home Pay) ===
            // THP sudah ada di database, tapi kita bisa gunakan net_salary
            $payroll->thp = (float) $payroll->net_salary;
        });

        // 3. Grouping per Branch
        $groupedByBranch = $payrolls->groupBy(function ($payroll) {
            return $payroll->employee->branch_id;
        });

        // 3. Susun data per branch dengan subtotal
        $branches = [];
        
        foreach ($groupedByBranch as $branchId => $payrollsInBranch) {
            $firstPayroll = $payrollsInBranch->first();
            $branchName = $firstPayroll->employee->branch->name;
            
            // Hitung subtotal untuk branch ini
            $subtotal = [
                'count' => $payrollsInBranch->count(),
                'total_gaji' => $payrollsInBranch->sum('base_salary'),
                'total_tunjangan_jabatan' => $payrollsInBranch->sum('tunjangan_jabatan'),
                'total_bpjs_tk_perusahaan' => $payrollsInBranch->sum('bpjs_tk_perusahaan'),
                'total_bpjs_tk_karyawan' => $payrollsInBranch->sum('bpjs_tk_karyawan'),
                'total_bpjs_kes_perusahaan' => $payrollsInBranch->sum('bpjs_kes_perusahaan'),
                'total_bpjs_kes_karyawan' => $payrollsInBranch->sum('bpjs_kes_karyawan'),
                'total_gaji_plus_bpjs' => $payrollsInBranch->sum('gaji_plus_bpjs'),
                'total_thp' => $payrollsInBranch->sum('thp'),
                'total_infaq' => $payrollsInBranch->sum('infaq'),
            ];
            
            $branches[] = [
                'branch_name' => $branchName,
                'payrolls' => $payrollsInBranch,
                'subtotal' => $subtotal,
            ];
        }

        // 4. Grand Total
        $grandTotal = [
            'count' => $payrolls->count(),
            'total_gaji' => $payrolls->sum('base_salary'),
            'total_tunjangan_jabatan' => $payrolls->sum('tunjangan_jabatan'),
            'total_bpjs_tk_perusahaan' => $payrolls->sum('bpjs_tk_perusahaan'),
            'total_bpjs_tk_karyawan' => $payrolls->sum('bpjs_tk_karyawan'),
            'total_bpjs_kes_perusahaan' => $payrolls->sum('bpjs_kes_perusahaan'),
            'total_bpjs_kes_karyawan' => $payrolls->sum('bpjs_kes_karyawan'),
            'total_gaji_plus_bpjs' => $payrolls->sum('gaji_plus_bpjs'),
            'total_thp' => $payrolls->sum('thp'),
            'total_infaq' => $payrolls->sum('infaq'),
        ];

        return view('exports.payrollReport', [
            'branches' => $branches,
            'grandTotal' => $grandTotal,
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