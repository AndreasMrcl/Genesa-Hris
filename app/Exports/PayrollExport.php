<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Agar lebar kolom otomatis
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PayrollExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $companyId;
    protected $start;
    protected $end;

    private $rowNumber = 0;

    public function __construct($companyId, $start, $end)
    {
        $this->companyId = $companyId;
        $this->start = $start;
        $this->end = $end;
    }

    public function query()
    {
        return Payroll::query()
            ->with('employee')
            ->where('compani_id', $this->companyId)
            ->where('pay_period_start', $this->start)
            ->where('pay_period_end', $this->end);
    }

    // 1. Header Tabel
    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'No. Rekening', 
            'Jumlah Transfer (IDR)',
            'Bank'
        ];
    }

    public function map($payroll): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $payroll->employee->name,
            
            // Tanda kutip satu (') memaksa Excel membaca sebagai Teks
            // agar nomor rekening tidak berubah jadi format scientific (1.2E+10)
            $payroll->employee->bank_account_no ? "" . $payroll->employee->bank_account_no : '-',
            
            $payroll->net_salary,
            
            $payroll->employee->bank_name ?? '-',
        ];
    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Header) Bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '#,##0',
        ];
    }
}