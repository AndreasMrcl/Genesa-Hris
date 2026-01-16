<table>
    <thead>
        <tr>
            <th colspan="15" align="center" style="font-weight: bold; font-size: 16px; height: 30px; vertical-align: middle;">
                LAPORAN DETAIL GAJI & ANALISA BEBAN (PAYROLL COST ANALYSIS)
            </th>
        </tr>
        <tr>
            <th colspan="15" align="center" style="font-weight: bold; font-size: 12px;">{{ $companyName }}</th>
        </tr>
        <tr>
            <th colspan="15" align="center">Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</th>
        </tr>
        <tr><th colspan="15"></th></tr>
    </thead>

    <tbody>
        <!-- SECTION 1 -->
        <tr>
            <td colspan="15" style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF; border: 1px solid #000000;">
                I. RINGKASAN ALOKASI BIAYA (COST CENTER SUMMARY)
            </td>
        </tr>
        <tr>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0; text-align: center; vertical-align: middle;">Lokasi</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0; text-align: center; vertical-align: middle;">Jml Org</th>
            
            <th colspan="3" style="border: 1px solid #000000; font-weight: bold; background-color: #dbeafe; text-align: center;">Komponen Pendapatan</th>
            <th colspan="3" style="border: 1px solid #000000; font-weight: bold; background-color: #fee2e2; text-align: center;">Potongan Karyawan</th>
            
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; background-color: #d1fae5; text-align: center; vertical-align: middle;">THP (Transfer)</th>
            
            <th colspan="2" style="border: 1px solid #000000; font-weight: bold; background-color: #f3f4f6; text-align: center;">Beban Perusahaan</th>
            
            <!-- Spacer untuk konsistensi 15 kolom -->
            <th colspan="4" style="border: none;"></th> 
        </tr>
        <tr>
            <!-- Sub Header (10 Cols) -->
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #eff6ff; text-align: center;">Gaji Pokok</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #eff6ff; text-align: center;">Tunj. Pajak</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #eff6ff; text-align: center;">Tunj. Lain</th>
            
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #fef2f2; text-align: center;">BPJS (Kary)</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #fef2f2; text-align: center;">PPh 21</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #fef2f2; text-align: center;">Lainnya</th>
            
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #f9fafb; text-align: center;">BPJS (Kantor)</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #f9fafb; text-align: center;">TOTAL BEBAN</th>
            
            <th colspan="4" style="border: none;"></th>
        </tr>

        @foreach($allocation as $branchName => $data)
            <tr>
                <td colspan="11" style="border: 1px solid #000000; font-weight: bold; background-color: #F3F4F6;">{{ $branchName }}</td>
                <td colspan="4" style="border: none;"></td>
            </tr>
            
            @foreach($data['outlets'] as $outletName => $d)
            <tr>
                <td style="border: 1px solid #000000; padding-left: 20px;"> - {{ $outletName }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $d['count'] }}</td>
                
                <td style="border: 1px solid #000000; text-align: right;">{{ $d['base_salary'] }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $d['allowance_tax'] }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $d['allowance_other'] }}</td>
                
                <td style="border: 1px solid #000000; text-align: right; color: red;">{{ $d['deduction_bpjs'] }}</td>
                <td style="border: 1px solid #000000; text-align: right; color: red;">{{ $d['deduction_tax'] }}</td>
                <td style="border: 1px solid #000000; text-align: right; color: red;">{{ $d['deduction_other'] }}</td>
                
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $d['net_salary'] }}</td>
                
                <td style="border: 1px solid #000000; text-align: right;">{{ $d['company_bpjs_cost'] }}</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold; background-color: #f3f4f6;">{{ $d['total_company_cost'] }}</td>
                <td colspan="4" style="border: none;"></td>
            </tr>
            @endforeach

            <!-- SUBTOTAL -->
            <tr>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #e5e7eb;">Total {{ $branchName }}:</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #e5e7eb;">{{ $data['summary']['count'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ $data['summary']['base_salary'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ $data['summary']['allowance_tax'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ $data['summary']['allowance_other'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; color: red; background-color: #e5e7eb;">{{ $data['summary']['deduction_bpjs'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; color: red; background-color: #e5e7eb;">{{ $data['summary']['deduction_tax'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; color: red; background-color: #e5e7eb;">{{ $data['summary']['deduction_other'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #d1fae5;">{{ $data['summary']['net_salary'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ $data['summary']['company_bpjs_cost'] }}</td>
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ $data['summary']['total_company_cost'] }}</td>
                <td colspan="4" style="border: none;"></td>
            </tr>
            <tr><td colspan="15"></td></tr>
        @endforeach

        <!-- GRAND TOTAL -->
        <tr style="height: 30px;">
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">GRAND TOTAL:</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #4F46E5; color: white;">{{ $grandTotal['count'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">{{ $grandTotal['base_salary'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">{{ $grandTotal['allowance_tax'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">{{ $grandTotal['allowance_other'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">{{ $grandTotal['deduction_bpjs'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">{{ $grandTotal['deduction_tax'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">{{ $grandTotal['deduction_other'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #059669; color: white;">{{ $grandTotal['net_salary'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">{{ $grandTotal['company_bpjs_cost'] }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #4F46E5; color: white;">{{ $grandTotal['total_company_cost'] }}</td>
            <td colspan="4" style="border: none;"></td>
        </tr>
    </tbody>
    
    <tr><td colspan="15"></td></tr>
    <tr><td colspan="15"></td></tr>

    <!-- TABEL 2: DETAIL INDIVIDUAL (15 Kolom) -->
    <tbody>
        <tr>
            <td colspan="15" style="font-weight: bold; background-color: #4F46E5; color: #FFFFFF; border: 1px solid #000000;">
                II. RINCIAN PER KARYAWAN (INDIVIDUAL DETAIL)
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">No</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Nama</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #E2E8F0;">Outlet</th>
            
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #dbeafe;">Gaji Pokok</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #dbeafe;">Tunj. Pajak</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #dbeafe;">Tunj. Lain</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #dbeafe;">Bruto</th> 

            <th style="border: 1px solid #000000; font-weight: bold; background-color: #fee2e2;">Pot. BPJS</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #fee2e2;">Pot. PPh21</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #fee2e2;">Pot. Lain</th>
            
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #d1fae5;">THP (Net)</th>
            
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #f3f4f6;">BPJS Kantor</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #f3f4f6;">Total Beban</th>
            <th style="border: 1px solid #000000; font-weight: bold; background-color: #f3f4f6;">Metode</th>
        </tr>

        @php $no = 1; @endphp
        @foreach($payrolls as $item)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $no++ }}</td>
            <td style="border: 1px solid #000000;">{{ $item->employee->name }}</td>
            <td style="border: 1px solid #000000;">{{ $item->employee->outlet->name ?? '-' }}</td>
            
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->base_salary, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->allowance_tax, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->allowance_other, 0, ',', '.') }}</td>
            <!-- Hitung Bruto di View (Gaji + Tunjangan) -->
            <td style="border: 1px solid #000000; text-align: right; background-color: #eff6ff;">
                {{ number_format($item->base_salary + $item->total_allowances, 0, ',', '.') }}
            </td>

            <td style="border: 1px solid #000000; text-align: right; color: red;">{{ number_format($item->deduction_bpjs, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right; color: red;">{{ number_format($item->deduction_tax, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right; color: red;">{{ number_format($item->deduction_other, 0, ',', '.') }}</td>
            
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; background-color: #ecfdf5;">
                {{ number_format($item->net_salary, 0, ',', '.') }}
            </td>

            <td style="border: 1px solid #000000; text-align: right;">{{ number_format($item->company_bpjs_cost, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ number_format($item->total_company_cost, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ ucfirst($item->payroll_method) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>