<!DOCTYPE html>
<html>
<head>
    <title>Laporan Lembur - {{ $date }}</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; color: #1f2937; font-size: 12px; line-height: 1.4; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .text-gray { color: #6b7280; }
        .w-full { width: 100%; }
        
        /* Header Layout */
        .header-table { width: 100%; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-cell { width: 100px; vertical-align: middle; }
        .logo-img { width: 80px; height: auto; }
        .title-cell { text-align: center; vertical-align: middle; }
        
        /* Info Area */
        .info-table { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; }
        .info-value { font-size: 13px; font-weight: bold; color: #111827; }

        /* Content Table (List Lembur) */
        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .report-table th { 
            background-color: #f3f4f6; color: #374151; font-weight: bold; 
            text-transform: uppercase; font-size: 10px; padding: 8px; text-align: left; 
            border-bottom: 2px solid #e5e7eb;
        }
        .report-table td { padding: 8px; border-bottom: 1px solid #f3f4f6; color: #1f2937; }
        .report-table tr:last-child td { border-bottom: 2px solid #374151; }

        /* Total Box */
        .net-pay-box { 
            background-color: #eef2ff; border: 1px solid #c7d2fe; 
            border-radius: 6px; padding: 10px; margin-top: 20px; 
            width: 40%; float: right;
        }
        .net-table { width: 100%; }
        .net-title { font-size: 10px; font-weight: bold; color: #3730a3; text-transform: uppercase; }
        .net-amount { font-size: 16px; font-weight: bold; color: #312e81; text-align: right; }

        /* Signature */
        .signature-table { width: 100%; margin-top: 60px; }
        .sig-cell { width: 30%; text-align: center; vertical-align: bottom; }
        .sig-title { font-size: 10px; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 50px; }
        .sig-line { border-top: 1px solid #d1d5db; padding-top: 5px; font-weight: bold; color: #374151; display: inline-block; width: 80%; }

        .clear { clear: both; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <!-- Logo -->
            <td class="logo-cell">
                <img src="{{ public_path('logo.png') }}" class="logo-img" alt="Logo">
            </td>
            <!-- Teks Tengah -->
            <td class="title-cell">
                <div style="font-size: 14px; color: #6b7280; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                    Pengembangan Usaha Sultan Agung
                </div>
                <div style="font-size: 20px; font-weight: 800; text-transform: uppercase; margin: 4px 0;">
                    {{ $company->company ?? 'GENESA HRIS' }}
                </div>
                <div style="font-size: 11px; color: #4b5563;">
                    {{ $company->location ?? 'Head Office Location' }}
                </div>
            </td>
            <!-- Judul Dokumen Kanan -->
            <td class="logo-cell text-right">
                <div style="font-size: 10px; color: #9ca3af;">DOKUMEN</div>
                <div style="font-size: 12px; font-weight: bold; color: #4b5563;">LAPORAN LEMBUR</div>
            </td>
        </tr>
    </table>

    <!-- Info Laporan -->
    <table class="info-table">
        <tr>
            <td width="33%">
                <div class="info-label">Cabang / Area</div>
                <div class="info-value">{{ $coordinator->branch->name ?? '-' }}</div>
            </td>
            <td width="33%">
                <div class="info-label">Tanggal</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</div>
            </td>
            <td width="33%">
                <div class="info-label">Laporan dari</div>
                <div class="info-value">{{ $coordinator->name }}</div>
            </td>
        </tr>

        @if($start_time && $end_time)
        <tr>
            <td colspan="3" style="padding-top: 10px;">
                <div class="info-label">Waktu Lembur</div>
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($start_time)->format('H:i') }} 
                    s/d 
                    {{ \Carbon\Carbon::parse($end_time)->format('H:i') }}
                    <span style="font-weight: normal; color: #666; font-size: 11px; margin-left: 5px;">
                        ({{ \Carbon\Carbon::parse($start_time)->diff(\Carbon\Carbon::parse($end_time))->format('%H:%I') }} Jam)
                    </span>
                </div>
            </td>
        </tr>
        @endif
    </table>

    <!-- Tabel Data Lembur -->
    <table class="report-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="30%">Nama Karyawan</th>
                <th width="20%">Posisi</th>
                <th width="15%" class="text-center">Mulai</th>
                <th width="15%" class="text-center">Selesai</th>
                <th width="15%" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPay = 0; @endphp
            @foreach($overtimes as $index => $item)
            @php
                $totalPay += $item->overtime_pay;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $item->employee->name }}</div>
                    <div style="font-size: 9px; color: #666;">{{ $item->employee->nik }}</div>
                </td>
                <td>{{ $item->employee->position->name ?? '-' }}</td>
                <td >{{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}</td>
                <td >{{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}</td>
                <td class="font-bold">Rp {{ number_format($item->overtime_pay, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="net-pay-box">
        <table class="net-table">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="net-title">Total Pembayaran</div>
                </td>
                <td class="net-amount">
                    Rp {{ number_format($totalPay, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-title">Koordinator</div>
                <br><br><br>
                <div class="sig-line">{{ $coordinator->name }}</div>
            </td>
            <td width="40%"></td>
            <td class="sig-cell">
                <div class="sig-title">Tandatangan (HR/Finance)</div>
                <br><br><br>
                <div class="sig-line">_______________________</div>
            </td>
        </tr>
    </table>

</body>
</html>