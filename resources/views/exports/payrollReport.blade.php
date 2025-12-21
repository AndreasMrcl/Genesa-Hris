<table>
    <!-- SECTION 1: HEADER LAPORAN -->
    <thead>
        <tr>
            <td colspan="8" align="center" style="font-weight: bold; font-size: 16px;">LAPORAN REKAPITULASI PENGGAJIAN (PAYROLL SUMMARY)</td>
        </tr>
        <tr>
            <td colspan="8" align="center" style="font-weight: bold; font-size: 14px;">{{ $companyName }}</td>
        </tr>
        <tr>
            <td colspan="8" align="center">Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td colspan="8"></td> <!-- Spacer -->
        </tr>
    </thead>

    <!-- SECTION 2: EXECUTIVE SUMMARY -->
    <tbody>
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #E2E8F0;">RINGKASAN EKSEKUTIF</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="2">Total Karyawan</td>
            <td align="right">{{ $summary['total_employees'] }} Orang</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="2">Total Gaji Pokok</td>
            <td align="right">{{ number_format($summary['total_base_salary'], 0, ',', '.') }}</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="2">Total Tunjangan</td>
            <td align="right">{{ number_format($summary['total_allowances'], 0, ',', '.') }}</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="2">Total Potongan</td>
            <td align="right" style="color: #FF0000;">({{ number_format($summary['total_deductions'], 0, ',', '.') }})</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">TOTAL GAJI BERSIH</td>
            <td align="right" style="font-weight: bold; background-color: #BBF7D0;">Rp {{ number_format($summary['total_net_salary'], 0, ',', '.') }}</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="8"></td> <!-- Spacer -->
        </tr>

        <!-- SECTION 3: PERFORMA PER CABANG -->
        <tr>
            <td colspan="4" style="font-weight: bold; background-color: #E2E8F0;">ANALISA PER CABANG</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <th style="border: 1px solid #000000; font-weight: bold;">Nama Cabang</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center;">Jml Karyawan</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: right;">Total Gaji Pokok</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: right;">Total Transfer (Net)</th>
            <th colspan="4"></th>
        </tr>
        @foreach($branchPivot as $branch)
        <tr>
            <td style="border: 1px solid #000000;">{{ $branch['name'] }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $branch['count'] }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($branch['total_basic'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($branch['total_net'], 0, ',', '.') }}</td>
            <td colspan="4"></td>
        </tr>
        @endforeach
        <tr>
            <td colspan="8"></td> <!-- Spacer -->
        </tr>

        <!-- SECTION 4: DETAIL TRANSAKSI (TABEL UTAMA) -->
        <tr>
            <td colspan="8" style="font-weight: bold; background-color: #E2E8F0;">DETAIL PENGGAJIAN KARYAWAN</td>
        </tr>
        <tr>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46E5; color: #FFFFFF;">No</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46E5; color: #FFFFFF;">ID Karyawan</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46E5; color: #FFFFFF;">Nama Lengkap</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46E5; color: #FFFFFF;">Jabatan</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46e5; color: #FFFFFF;">Cabang</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46e5; color: #FFFFFF;">Status</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46e5; color: #FFFFFF;">Gaji Pokok</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46e5; color: #FFFFFF;">Total Tunjangan</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46e5; color: #FFFFFF;">Total Potongan</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #4F46e5; color: #FFFFFF;">GAJI BERSIH (NET)</th>
        </tr>
    </tbody>
    <tbody>
        @php $no = 1; @endphp
        @foreach($payrolls as $item)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $no++ }}</td>
            <td style="border: 1px solid #000000; text-align: left;">{{ $item->employee->nik }}</td>
            <td style="border: 1px solid #000000;">{{ $item->employee->name }}</td>
            <td style="border: 1px solid #000000;">{{ $item->employee->position->name ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $item->employee->branch->name ?? '-' }}</td>
            <td style="border: 1px solid #000000;">
                {{ $item->employee->status == 'full_time' ? 'Tetap' : ($item->employee->status == 'part_time' ? 'Kontrak' : 'Harian') }}
            </td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->base_salary, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->total_allowances, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right; color: #FF0000;">({{ number_format($item->total_deductions, 0, ',', '.') }})</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ number_format($item->net_salary, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        
        <!-- ROW TOTAL BAWAH -->
        <tr>
            <td colspan="6" style="border: 1px solid #000000; font-weight: bold; text-align: right;">TOTAL:</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right;">{{ number_format($payrolls->sum('base_salary'), 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right;">{{ number_format($payrolls->sum('total_allowances'), 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; color: #FF0000;">({{ number_format($payrolls->sum('total_deductions'), 0, ',', '.') }})</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #BBF7D0;">{{ number_format($payrolls->sum('net_salary'), 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>