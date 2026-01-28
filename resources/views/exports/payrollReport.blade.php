<table>
    <thead>
        <tr>
            <th colspan="5" style="font-weight: bold; font-size: 14px; text-align: center; background-color: #1e40af; color: #ffffff;">
                LAPORAN PENGGAJIAN KARYAWAN
            </th>
        </tr>
        <tr>
            <th colspan="5" style="font-weight: bold; font-size: 12px; text-align: center; background-color: #3b82f6; color: #ffffff;">
                {{ $companyName }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="font-size: 10px; text-align: center; background-color: #60a5fa; color: #ffffff;">
                Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
            </th>
        </tr>
        <tr><th colspan="5"></th></tr>

        <tr>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #e5e7eb; text-align: center;">NAMA</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #e5e7eb; text-align: center;">UNIT</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #e5e7eb; text-align: center;">GAJI</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #dbeafe; text-align: center;">TUNJANGAN JABATAN</th>
            <th style="border: 1px solid #000; font-weight: bold; background-color: #a7f3d0; text-align: center;">TOTAL GAJI + BPJS</th>
        </tr>
    </thead>

    <tbody>
        @foreach($branches as $branch)
            {{-- Branch Header --}}
            <tr>
                <td colspan="5" style="border: 1px solid #000; font-weight: bold; background-color: #f3f4f6; padding: 5px;">
                    {{ strtoupper($branch['branch_name']) }}
                </td>
            </tr>

            {{-- Employees in this branch --}}
            @foreach($branch['payrolls'] as $payroll)
            <tr>
                <td style="border: 1px solid #000; padding-left: 5px;">
                    {{ $payroll->employee->name }}
                </td>
                <td style="border: 1px solid #000; text-align: center;">
                    {{ $payroll->employee->outlet->name ?? '-' }}
                </td>
                <td style="border: 1px solid #000; text-align: right; padding-right: 5px;">
                    {{ number_format($payroll->base_salary, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #000; text-align: right; padding-right: 5px; background-color: #eff6ff; mso-number-format:'\@';">
                    @if($payroll->tunjangan_jabatan > 0)
                        &#8203;{{ number_format($payroll->tunjangan_jabatan, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td style="border: 1px solid #000; text-align: right; padding-right: 5px; font-weight: bold; background-color: #d1fae5;">
                    {{ number_format($payroll->gaji_plus_bpjs, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach

            {{-- Subtotal per Branch --}}
            <tr>
                <td colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: right; padding-right: 10px; background-color: #e5e7eb;">
                    SUBTOTAL ({{ $branch['subtotal']['count'] }} karyawan):
                </td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; padding-right: 5px; background-color: #e5e7eb;">
                    &#8203;{{ number_format($branch['subtotal']['total_gaji'], 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; padding-right: 5px; background-color: #e5e7eb; mso-number-format:'\@';">
                    &#8203;{{ number_format($branch['subtotal']['total_tunjangan_jabatan'], 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: right; padding-right: 5px; background-color: #a7f3d0;">
                    &#8203;{{ number_format($branch['subtotal']['total_gaji_plus_bpjs'], 0, ',', '.') }}
                </td>
            </tr>

            {{-- Spacing --}}
            <tr><td colspan="5" style="height: 5px;"></td></tr>
        @endforeach

        {{-- Grand Total --}}
        <tr>
            <td colspan="2" style="border: 2px solid #000; font-weight: bold; text-align: right; padding-right: 10px; background-color: #6366f1; color: white;">
                TOTAL ({{ $grandTotal['count'] }} karyawan):
            </td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; padding-right: 5px; background-color: #6366f1; color: white;">
                &#8203;{{ number_format($grandTotal['total_gaji'], 0, ',', '.') }}
            </td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; padding-right: 5px; background-color: #6366f1; color: white; mso-number-format:'\@';">
                &#8203;{{ number_format($grandTotal['total_tunjangan_jabatan'], 0, ',', '.') }}
            </td>
            <td style="border: 2px solid #000; font-weight: bold; text-align: right; padding-right: 5px; background-color: #10b981; color: white;">
                &#8203;{{ number_format($grandTotal['total_gaji_plus_bpjs'], 0, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>