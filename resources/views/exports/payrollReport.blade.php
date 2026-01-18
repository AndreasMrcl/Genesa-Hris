<table>
    <thead>
        <tr>
            <th colspan="20" style="font-weight: bold; font-size: 16px; height: 30px; vertical-align: middle; text-align: center; background-color: #1e40af; color: #ffffff;">
                LAPORAN DETAIL GAJI DAN ANALISA BEBAN PERUSAHAAN
            </th>
        </tr>
        <tr>
            <th colspan="20" style="font-weight: bold; font-size: 13px; text-align: center; background-color: #3b82f6; color: #ffffff;">
                {{ $companyName }}
            </th>
        </tr>
        <tr>
            <th colspan="20" style="font-size: 11px; text-align: center; background-color: #60a5fa; color: #ffffff;">
                Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
            </th>
        </tr>
        <tr><th colspan="20"></th></tr>
    </thead>

    <tbody>
        <!-- ============================================ -->
        <!-- SECTION 1: RINGKASAN ALOKASI BIAYA -->
        <!-- ============================================ -->
        <tr>
            <td colspan="20" style="font-weight: bold; background-color: #7c3aed; color: #ffffff; padding: 8px; font-size: 12px;">
                I. RINGKASAN ALOKASI BIAYA PER LOKASI (COST CENTER SUMMARY)
            </td>
        </tr>
        
        <!-- Header Table 1 -->
        <tr>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #e2e8f0; text-align: center; vertical-align: middle;">Lokasi</th>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #e2e8f0; text-align: center; vertical-align: middle;">Jumlah<br>Karyawan</th>
            
            <th colspan="5" style="border: 1px solid #000; font-weight: bold; background-color: #dbeafe; text-align: center;">KOMPONEN PENDAPATAN (Income)</th>
            <th colspan="6" style="border: 1px solid #000; font-weight: bold; background-color: #fee2e2; text-align: center;">POTONGAN KARYAWAN (Deductions)</th>
            
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #d1fae5; text-align: center; vertical-align: middle;">TAKE HOME<br>PAY (THP)</th>
            
            <th colspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #fef3c7; text-align: center;">BEBAN PERUSAHAAN</th>
            
            <th colspan="4" style="border: none;"></th>
        </tr>
        <tr>
            <!-- Sub-header Pendapatan (5 cols) -->
            <th style="border: 1px solid #000; font-weight: bold; background-color: #eff6ff; text-align: center; font-size: 9px;">Gaji Pokok</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #eff6ff; text-align: center; font-size: 9px;">Tunj. Jabatan</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #eff6ff; text-align: center; font-size: 9px;">Tunj. PPh21</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #eff6ff; text-align: center; font-size: 9px;">Tunj. Lain</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #bfdbfe; text-align: center; font-size: 9px;">Total Income</th>
            
            <!-- Sub-header Potongan (6 cols) -->
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">Pot. BPJS</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">Pot. PPh21</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">Pot. Infaq</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">Pot. Absensi</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">Pot. Lain</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fecaca; text-align: center; font-size: 9px;">Total Pot.</th>
            
            <!-- Sub-header Beban (2 cols) -->
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef9c3; text-align: center; font-size: 9px;">BPJS Kantor</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef9c3; text-align: center; font-size: 9px;">Total Beban</th>
            
            <th colspan="4" style="border: none;"></th>
        </tr>

        @foreach($allocation as $branchName => $data)
            <!-- Branch Header -->
            <tr>
                <td colspan="14" style="border: 1px solid #000; font-weight: bold; background-color: #f3f4f6; padding: 5px;">
                    {{ $branchName }}
                </td>
                <td colspan="6" style="border: none;"></td>
            </tr>
            
            <!-- Outlets -->
            @foreach($data['outlets'] as $outletName => $d)
            <tr>
                <td style="border: 1px solid #000; padding-left: 15px;">└ {{ $outletName }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $d['count'] }}</td>
                
                <!-- Income -->
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($d['base_salary'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($d['allowance_position'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($d['allowance_tax'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($d['allowance_other'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right; font-weight: bold; background-color: #dbeafe;">
                    {{ number_format($d['base_salary'] + $d['allowance_position'] + $d['allowance_tax'] + $d['allowance_other'], 0, ',', '.') }}
                </td>
                
                <!-- Deductions -->
                <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($d['deduction_bpjs'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($d['deduction_tax'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($d['deduction_infaq'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($d['deduction_attendance'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($d['deduction_other'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right; font-weight: bold; color: #dc2626; background-color: #fee2e2;">
                    {{ number_format($d['deduction_bpjs'] + $d['deduction_tax'] + $d['deduction_infaq'] + $d['deduction_attendance'] + $d['deduction_other'], 0, ',', '.') }}
                </td>
                
                <!-- THP -->
                <td style="border: 1px solid #000; text-align: right; font-weight: bold; background-color: #d1fae5;">
                    {{ number_format($d['net_salary'], 0, ',', '.') }}
                </td>
                
                <!-- Company Cost -->
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($d['company_bpjs_cost'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right; font-weight: bold; background-color: #fef3c7;">
                    {{ number_format($d['total_company_cost'], 0, ',', '.') }}
                </td>
                
                <td colspan="4" style="border: none;"></td>
            </tr>
            @endforeach

            <!-- Subtotal Branch -->
            <tr>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb; padding-right: 10px;">
                    SUBTOTAL {{ strtoupper($branchName) }}:
                </td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #e5e7eb;">{{ $data['summary']['count'] }}</td>
                
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['base_salary'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['allowance_position'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['allowance_tax'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['allowance_other'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #bfdbfe;">
                    {{ number_format($data['summary']['base_salary'] + $data['summary']['allowance_position'] + $data['summary']['allowance_tax'] + $data['summary']['allowance_other'], 0, ',', '.') }}
                </td>
                
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['deduction_bpjs'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['deduction_tax'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['deduction_infaq'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['deduction_attendance'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['deduction_other'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #fecaca;">
                    {{ number_format($data['summary']['deduction_bpjs'] + $data['summary']['deduction_tax'] + $data['summary']['deduction_infaq'] + $data['summary']['deduction_attendance'] + $data['summary']['deduction_other'], 0, ',', '.') }}
                </td>
                
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #a7f3d0;">{{ number_format($data['summary']['net_salary'], 0, ',', '.') }}</td>
                
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ number_format($data['summary']['company_bpjs_cost'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #fde68a;">{{ number_format($data['summary']['total_company_cost'], 0, ',', '.') }}</td>
                
                <td colspan="4" style="border: none;"></td>
            </tr>
            <tr><td colspan="20" style="height: 5px;"></td></tr>
        @endforeach

        <!-- GRAND TOTAL -->
        <tr style="height: 35px;">
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white; padding-right: 10px;">
                GRAND TOTAL:
            </td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: center; background-color: #7c3aed; color: white;">{{ $grandTotal['count'] }}</td>
            
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['base_salary'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['allowance_position'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['allowance_tax'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['allowance_other'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #6366f1; color: white;">
                {{ number_format($grandTotal['base_salary'] + $grandTotal['allowance_position'] + $grandTotal['allowance_tax'] + $grandTotal['allowance_other'], 0, ',', '.') }}
            </td>
            
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['deduction_bpjs'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['deduction_tax'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['deduction_infaq'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['deduction_attendance'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['deduction_other'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #ef4444; color: white;">
                {{ number_format($grandTotal['deduction_bpjs'] + $grandTotal['deduction_tax'] + $grandTotal['deduction_infaq'] + $grandTotal['deduction_attendance'] + $grandTotal['deduction_other'], 0, ',', '.') }}
            </td>
            
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #10b981; color: white;">{{ number_format($grandTotal['net_salary'], 0, ',', '.') }}</td>
            
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #7c3aed; color: white;">{{ number_format($grandTotal['company_bpjs_cost'], 0, ',', '.') }}</td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; background-color: #f59e0b; color: white;">{{ number_format($grandTotal['total_company_cost'], 0, ',', '.') }}</td>
            
            <td colspan="4" style="border: none;"></td>
        </tr>
    </tbody>
    
    <tr><td colspan="20" style="height: 20px;"></td></tr>

    <!-- ============================================ -->
    <!-- SECTION 2: DETAIL PER KARYAWAN -->
    <!-- ============================================ -->
    <tbody>
        <tr>
            <td colspan="20" style="font-weight: bold; background-color: #7c3aed; color: #ffffff; padding: 8px; font-size: 12px;">
                II. RINCIAN PER KARYAWAN (INDIVIDUAL PAYROLL DETAIL)
            </td>
        </tr>
        
        <!-- Header Table 2 -->
        <tr>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #e2e8f0; text-align: center; vertical-align: middle;">No</th>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #e2e8f0; text-align: center; vertical-align: middle;">NIK</th>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #e2e8f0; text-align: center; vertical-align: middle;">Nama Karyawan</th>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #e2e8f0; text-align: center; vertical-align: middle;">Jabatan</th>
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #e2e8f0; text-align: center; vertical-align: middle;">Outlet</th>
            
            <th colspan="5" style="border: 1px solid #000; font-weight: bold; background-color: #dbeafe; text-align: center;">PENDAPATAN</th>
            <th colspan="6" style="border: 1px solid #000; font-weight: bold; background-color: #fee2e2; text-align: center;">POTONGAN</th>
            
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #d1fae5; text-align: center; vertical-align: middle;">THP<br>(Net Salary)</th>
            
            <th colspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #fef3c7; text-align: center;">BEBAN PERUSAHAAN</th>
            
            <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #e2e8f0; text-align: center; vertical-align: middle;">Metode<br>Gaji</th>
        </tr>
        <tr>
            <!-- Pendapatan (5 cols) -->
            <th style="border: 1px solid #000; font-weight: bold; background-color: #eff6ff; text-align: center; font-size: 9px;">Gaji Pokok</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #eff6ff; text-align: center; font-size: 9px;">Tunj. Jabatan</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #eff6ff; text-align: center; font-size: 9px;">Tunj. PPh21</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #eff6ff; text-align: center; font-size: 9px;">Tunj. Lain</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #bfdbfe; text-align: center; font-size: 9px;">Total</th>
            
            <!-- Potongan (6 cols) -->
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">BPJS</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">PPh21</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">Infaq</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">Absensi</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef2f2; text-align: center; font-size: 9px;">Lainnya</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fecaca; text-align: center; font-size: 9px;">Total</th>
            
            <!-- Beban (2 cols) -->
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef9c3; text-align: center; font-size: 9px;">BPJS Kantor</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fef9c3; text-align: center; font-size: 9px;">Total Beban</th>
        </tr>

        @php $no = 1; @endphp
        @foreach($payrolls as $item)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $no++ }}</td>
            <td style="border: 1px solid #000; text-align: left;">{{ $item->employee->nik ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $item->employee->name }}</td>
            <td style="border: 1px solid #000;">{{ $item->employee->position->name ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $item->employee->outlet->name ?? '-' }}</td>
            
            <!-- Pendapatan -->
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($item->base_salary, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($item->allowance_position, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($item->allowance_tax, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($item->allowance_other, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right; background-color: #eff6ff; font-weight: bold;">
                {{ number_format($item->base_salary + $item->total_allowances, 0, ',', '.') }}
            </td>

            <!-- Potongan -->
            <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($item->deduction_bpjs, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($item->deduction_tax, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($item->deduction_infaq, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($item->deduction_attendance, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right; color: #dc2626;">{{ number_format($item->deduction_other, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right; background-color: #fef2f2; font-weight: bold; color: #dc2626;">
                {{ number_format($item->total_deductions, 0, ',', '.') }}
            </td>
            
            <!-- THP -->
            <td style="border: 1px solid #000; text-align: right; font-weight: bold; background-color: #ecfdf5; color: #059669;">
                {{ number_format($item->net_salary, 0, ',', '.') }}
            </td>

            <!-- Beban Perusahaan -->
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($item->company_bpjs_cost, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right; font-weight: bold; background-color: #fef9c3;">
                {{ number_format($item->total_company_cost, 0, ',', '.') }}
            </td>
            
            <!-- Metode -->
            <td style="border: 1px solid #000; text-align: center;">{{ strtoupper($item->payroll_method) }}</td>
        </tr>
        @endforeach

        <!-- Footer Keterangan -->
        <tr><td colspan="20" style="height: 10px;"></td></tr>
        <tr>
            <td colspan="20" style="font-size: 9px; font-style: italic;">
                <strong>Keterangan:</strong><br>
                • THP (Take Home Pay) = Total Pendapatan - Total Potongan<br>
                • Total Beban Perusahaan = Gaji Pokok + Total Tunjangan + BPJS Ditanggung Perusahaan<br>
                • BPJS Kantor meliputi: JKK, JKM, JHT (Perusahaan), JP (Perusahaan), Kesehatan (Perusahaan)
            </td>
        </tr>
    </tbody>
</table>