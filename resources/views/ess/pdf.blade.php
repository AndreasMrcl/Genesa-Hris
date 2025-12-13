<!DOCTYPE html>
<html>
<head>
    <title>Payslip - {{ $payroll->employee->name }}</title>
    <style>
        /* Reset & Font */
        @page { margin: 20px; }
        body { font-family: sans-serif; color: #1f2937; font-size: 12px; line-height: 1.4; }
        
        /* Utility */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .text-red { color: #dc2626; }
        .text-gray { color: #6b7280; }
        .text-indigo { color: #4338ca; }
        .w-full { width: 100%; }
        
        /* Header Layout */
        .header-table { width: 100%; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-cell { width: 100px; vertical-align: middle; }
        .logo-img { width: 80px; height: auto; }
        .title-cell { text-align: center; vertical-align: middle; }
        
        /* Employee Info */
        .info-table { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; }
        .info-value { font-size: 13px; font-weight: bold; color: #111827; }

        /* Earnings & Deductions */
        .columns-container { width: 100%; margin-bottom: 20px; }
        .col-left { width: 48%; float: left; }
        .col-right { width: 48%; float: right; }
        
        .section-title { 
            font-size: 11px; font-weight: bold; color: #9ca3af; 
            text-transform: uppercase; border-bottom: 2px solid #f3f4f6; 
            padding-bottom: 5px; margin-bottom: 10px; 
        }

        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .item-table td { padding: 4px 0; vertical-align: top; }
        .item-name { color: #374151; font-weight: 500; }
        .item-note { font-size: 9px; color: #9ca3af; margin-top: 1px; }
        .item-amount { font-weight: bold; color: #111827; text-align: right; }

        .total-box { 
            background-color: #f9fafb; border-radius: 4px; padding: 8px; 
            border-top: 1px solid #e5e7eb; margin-top: 5px;
        }
        .total-table { width: 100%; }
        .total-label { font-size: 11px; font-weight: bold; color: #4b5563; }
        .total-value { font-size: 12px; font-weight: bold; color: #111827; text-align: right; }

        /* Benefits */
        .benefit-section { margin-top: 20px; border-top: 1px dashed #d1d5db; padding-top: 15px; }
        .benefit-grid { width: 100%; }
        .benefit-grid td { width: 50%; padding: 4px 10px 4px 0; }

        /* Net Pay */
        .net-pay-box { 
            background-color: #eef2ff; border: 1px solid #c7d2fe; 
            border-radius: 6px; padding: 15px; margin-top: 30px; 
        }
        .net-table { width: 100%; }
        .net-title { font-size: 12px; font-weight: bold; color: #3730a3; text-transform: uppercase; }
        .net-subtitle { font-size: 10px; color: #6366f1; }
        .net-amount { font-size: 24px; font-weight: bold; color: #312e81; text-align: right; }

        /* Signature */
        .signature-table { width: 100%; margin-top: 40px; }
        .sig-cell { width: 40%; text-align: center; vertical-align: bottom; }
        .sig-title { font-size: 10px; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 50px; }
        .sig-line { border-top: 1px solid #d1d5db; padding-top: 5px; font-weight: bold; color: #374151; display: inline-block; width: 80%; }

        .clear { clear: both; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('logo.png') }}" class="logo-img" alt="Logo">
            </td>
            <td class="title-cell">
                <div style="font-size: 14px; color: #6b7280; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                    Pengembangan Usaha Sultan Agung
                </div>
                <div style="font-size: 20px; font-weight: 800; text-transform: uppercase; margin: 4px 0;">
                    {{ Auth::user()->compani->company ?? 'COMPANY NAME' }}
                </div>
                <div style="font-size: 11px; color: #4b5563;">
                    {{ Auth::user()->compani->location ?? 'Head Office Location' }}
                </div>
            </td>
            <td class="logo-cell text-right">
                <div style="font-size: 10px; color: #9ca3af;">PAYSLIP</div>
                <div style="font-size: 10px; font-weight: bold;">
                    {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M Y') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="25%">
                <div class="info-label">Employee Name</div>
                <div class="info-value">{{ $payroll->employee->name }}</div>
            </td>
            <td width="25%">
                <div class="info-label">Position</div>
                <div class="info-value">{{ $payroll->employee->position->name ?? '-' }}</div>
            </td>
            <td width="25%">
                <div class="info-label">Period</div>
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('d M') }} - 
                    {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('d M Y') }}
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="info-label">Employee ID</div>
                <div class="info-value">{{ $payroll->employee->nik }}</div>
            </td>
            <td>
                <div class="info-label">Branch</div>
                <div class="info-value">{{ $payroll->employee->branch->name ?? '-' }}</div>
            </td>
            <td>
                <div class="info-label">Payment Method</div>
                <div class="info-value">{{ ucfirst($payroll->payroll_method ?? 'Transfer') }}</div>
            </td>
        </tr>
    </table>

    <!-- Earnings & Deductions -->
    <div class="columns-container">
        
        <!-- EARNINGS -->
        <div class="col-left">
            <div class="section-title">Earnings (Pendapatan)</div>
            <table class="item-table">
                @foreach($payroll->payrollDetails->whereIn('category', ['base', 'allowance']) as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->name }}</div>
                        </td>
                        <td class="item-amount">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </table>
            
            <div class="total-box">
                <table class="total-table">
                    <tr>
                        <td class="total-label">Total Gross Pay</td>
                        <td class="total-value">Rp {{ number_format($payroll->base_salary + $payroll->total_allowances, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- DEDUCTIONS -->
        <div class="col-right">
            <div class="section-title">Deductions (Potongan)</div>
            <table class="item-table">
                @forelse($payroll->payrollDetails->where('category', 'deduction') as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->name }}</div>
                        </td>
                        <td class="item-amount text-red">- Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-gray italic">No deductions</td></tr>
                @endforelse
            </table>

            <div class="total-box">
                <table class="total-table">
                    <tr>
                        <td class="total-label">Total Deductions</td>
                        <td class="total-value text-red">- Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="clear"></div>
    </div>

    <!-- Benefits -->
    @php $benefits = $payroll->benefits;; @endphp
    @if($benefits->count() > 0)
        <div class="benefit-section">
            <div class="section-title" style="border: none; margin-bottom: 5px;">
                Company Paid Benefits (Non-Cash)
            </div>
            <table class="benefit-grid">
                @foreach($benefits->chunk(2) as $chunk)
                    <tr>
                        @foreach($chunk as $item)
                            <td>
                                <table style="width:100%">
                                    <tr>
                                        <td class="text-gray" style="font-size: 11px;">{{ $item->name }}</td>
                                        <td class="text-right" style="font-size: 11px; font-weight: bold;">
                                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        @endforeach
                        @if($chunk->count() == 1) <td></td> @endif
                    </tr>
                @endforeach
            </table>
            <div style="font-size: 9px; color: #9ca3af; margin-top: 5px; font-style: italic;">
                * Benefit ini dibayarkan perusahaan dan tidak mengurangi gaji bersih Anda.
            </div>
        </div>
    @endif

    <!-- Net Pay -->
    <div class="net-pay-box">
        <table class="net-table">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="net-title">Take Home Pay</div>
                    <div class="net-subtitle">Transfer to Bank Account</div>
                </td>
                <td class="net-amount">
                    Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Signature -->
    <table class="signature-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-title">Employee Signature</div>
                <br><br><br>
                <div class="sig-line">{{ $payroll->employee->name }}</div>
            </td>
            <td width="20%"></td>
            <td class="sig-cell">
                <div class="sig-title">Authorized Signature</div>
                <br><br><br>
                <div class="sig-line">HR Manager</div>
            </td>
        </tr>
    </table>

</body>
</html>