<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PayrollExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting, WithEvents, WithCustomStartCell, WithTitle
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

    public function title(): string
    {
        return 'Payroll Report';
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function query()
    {
        return Payroll::query()
            ->with('employee')
            ->where('compani_id', $this->companyId)
            ->where('pay_period_start', $this->start)
            ->where('pay_period_end', $this->end)
            ->orderBy('payroll_method', 'desc')
            ->orderBy('employee_id'); 
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'Metode Payroll',
            'No. Rekening',
            'Jumlah Transfer (IDR)',
            'Bank'
        ];
    }

    public function map($payroll): array
    {
        $this->rowNumber++;

        $method = $payroll->payroll_method ?? 'transfer';

        return [
            $this->rowNumber,
            $payroll->employee->name,
            ucfirst($method),
            $method == 'cash' ? '-' : ($payroll->employee->bank_account_no ? $payroll->employee->bank_account_no : '-'),
            $payroll->net_salary,
            $method == 'cash' ? '-' : ($payroll->employee->bank_name ?? '-'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '4F46E5'], 
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '#,##0', 
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', 'PAYROLL BSI');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells('A3:F3');
                $sheet->setCellValue('A3', ($this->companyId) ? \App\Models\Compani::find($this->companyId)->company : 'All Companies');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $startStr = \Carbon\Carbon::parse($this->start)->format('d M Y');
                $endStr = \Carbon\Carbon::parse($this->end)->format('d M Y');
                
                $sheet->mergeCells('A4:F4');
                $sheet->setCellValue('A4', "Periode: $startStr - $endStr");
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $lastRow = $sheet->getHighestRow();
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];

                $sheet->getStyle('A6:F' . $lastRow)->applyFromArray($styleArray);
                
                $totalRow = $lastRow + 2;
                $sheet->setCellValue('D' . $totalRow, 'TOTAL PAYROLL:');
                $sheet->setCellValue('E' . $totalRow, "=SUM(E7:E$lastRow)");
                $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('D' . $totalRow)->getFont()->setBold(true);
                $sheet->getStyle('E' . $totalRow)->getFont()->setBold(true);
            },
        ];
    }
}