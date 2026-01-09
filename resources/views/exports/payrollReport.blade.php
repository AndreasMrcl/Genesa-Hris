<table>
    <!-- HEADER -->
    <thead>
        <tr>
            <td colspan="7" align="center" style="font-weight: bold; font-size: 16px; height: 30px; vertical-align: middle;">
                LAPORAN ALOKASI BIAYA GAJI (PAYROLL COST ALLOCATION)
            </td>
        </tr>
        <tr>
            <td colspan="7" align="center" style="font-weight: bold; font-size: 12px;">{{ $companyName }}</td>
        </tr>
        <tr>
            <td colspan="7" align="center">Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</td>
        </tr>
        <tr><td colspan="7"></td></tr>
    </thead>

    <!-- TABEL 1: ALOKASI PER CABANG & OUTLET -->
    <tbody>
        <tr>
            <td colspan="7" style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF; border: 1px solid #000000;">
                I. RINGKASAN ALOKASI PER CABANG (COST CENTER)
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0; text-align: center;">Lokasi (Cabang / Outlet)</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0; text-align: center;">Jml Pegawai</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0; text-align: center;">Total Gaji Pokok</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0; text-align: center;">Total Tunjangan</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0; text-align: center;">Total Transfer (Net)</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0; text-align: center;">Total Beban (Cost)</th>
            <th style="border: 1px solid #000000;"></th>
        </tr>

        @foreach($allocation as $branchName => $data)
            <!-- NAMA CABANG (HEADER) -->
            <tr>
                <td colspan="7" style="border: 1px solid #000000; font-weight: bold; background-color: #F3F4F6;">
                    {{ $branchName }}
                </td>
            </tr>
            
            <!-- LIST OUTLET DI DALAM CABANG -->
            @foreach($data['outlets'] as $outletName => $outletData)
            <tr>
                <td style="border: 1px solid #000000; padding-left: 20px;">  {{ $outletName }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $outletData['count'] }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($outletData['base_salary'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($outletData['total_allowances'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($outletData['net_salary'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($outletData['total_company_cost'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000;"></td>
            </tr>
            @endforeach

            <!-- SUBTOTAL CABANG -->
            <tr>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #F3F4F6;">Subtotal {{ $branchName }}:</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #F3F4F6;">{{ $data['summary']['count'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #F3F4F6;">{{ number_format($data['summary']['base_salary'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #F3F4F6;">{{ number_format($data['summary']['total_allowances'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #F3F4F6;">{{ number_format($data['summary']['net_salary'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #F3F4F6;">{{ number_format($data['summary']['total_company_cost'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000;"></td>
            </tr>
            <tr><td colspan="7" style="border-left: 1px solid #000000; border-right: 1px solid #000000;"></td></tr>
        @endforeach

        <!-- GRAND TOTAL -->
        <tr>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #BBF7D0; font-size: 12px;">GRAND TOTAL:</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #BBF7D0; font-size: 12px;">{{ $grandTotal['count'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #BBF7D0; font-size: 12px;">{{ number_format($grandTotal['base_salary'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #BBF7D0; font-size: 12px;">{{ number_format($grandTotal['total_allowances'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #BBF7D0; font-size: 12px;">{{ number_format($grandTotal['net_salary'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #BBF7D0; font-size: 12px;">{{ number_format($grandTotal['total_company_cost'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000;"></td>
        </tr>
    </tbody>

    <tr><td colspan="7"></td></tr>
    <tr><td colspan="7"></td></tr>

    <!-- TABEL 2: DETAIL KARYAWAN -->
    <tbody>
        <tr>
            <td colspan="10" style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF; border: 1px solid #000000;">
                II. RINCIAN GAJI PER KARYAWAN (DETAIL)
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #E2E8F0;">No</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Nama Karyawan</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Cabang</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Outlet</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Gaji Pokok</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Tunjangan</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Potongan</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Take Home Pay</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Total Company Cost</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Metode</th>
        </tr>
        
        @php $no = 1; @endphp
        @foreach($payrolls as $item)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $no++ }}</td>
            <td style="border: 1px solid #000000;">{{ $item->employee->name }}</td>
            <td style="border: 1px solid #000000;">{{ $item->employee->branch->name ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $item->employee->outlet->name ?? '-' }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->base_salary, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->total_allowances, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right; color: red;">({{ number_format($item->total_deductions, 0, ',', '.') }})</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ number_format($item->net_salary, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->total_company_cost, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ ucfirst($item->payroll_method) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>