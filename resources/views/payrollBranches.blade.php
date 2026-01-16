<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payroll Cabang</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            border-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-building text-cyan-600"></i> Daftar Cabang
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Periode: 
                        <span class="font-bold text-gray-700">
                            {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
                        </span>
                    </p>
                </div>
                <a href="{{ route('payroll') }}" 
                   class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition flex items-center gap-2 text-sm">
                   <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg">
                                    <div class="flex items-center justify-center">
                                        Nama Cabang
                                    </div>
                                </th>
                                <th class="p-4 font-bold text-center">
                                    <div class="flex items-center justify-center">
                                        Kategori
                                    </div>
                                </th>
                                <th class="p-4 font-bold text-center">
                                    <div class="flex items-center justify-center">
                                        Karyawan
                                    </div>
                                </th>
                                <th class="p-4 font-bold text-center">
                                    <div class="flex items-center justify-center">
                                        Total BPJS
                                    </div>
                                </th>
                                <th class="p-4 font-bold text-center">
                                    <div class="flex items-center justify-center">
                                        Total Infaq
                                    </div>
                                </th>
                                <th class="p-4 font-bold text-center">
                                    <div class="flex items-center justify-center">
                                        Total Pengeluaran
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @foreach ($branchStats as $stat)
                                <tr class="cursor-pointer" 
                                    onclick="window.location='{{ route('payrollBranchEmployees', ['start' => $start, 'end' => $end, 'branch' => $stat->id]) }}'">
                                    
                                    <td class="p-4">
                                        <div class="font-bold text-lg text-cyan-700 flex items-center justify-center">
                                            {{ $stat->name }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center">
                                            <span class="text-xs uppercase font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded border">
                                                {{ str_replace('_', ' ', $stat->category) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center">
                                            <span class="font-bold text-gray-800 text-normal">
                                                {{ $stat->employee_count }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center">
                                            <span class="text-emerald-600 font-medium">
                                                 Rp {{ number_format($stat->total_bpjs, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center">
                                            <span class="text-orange-600 font-medium">
                                                Rp {{ number_format($stat->total_infaq, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-800 flex items-center justify-center">
                                            Rp {{ number_format($stat->total_expense, 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
                                <td class="p-4 text-center" colspan="2">
                                    <div class="flex items-center justify-center">
                                        TOTAL
                                    </div>
                                </td>
                                <td class="p-4 text-center text-base">
                                    <div class="flex items-center justify-center">
                                        {{ $branchStats->sum('employee_count') }}
                                    </div>
                                </td>
                                <td class="p-4 text-center text-base text-emerald-700">
                                    <div class="flex items-center justify-center">
                                        Rp {{ number_format($branchStats->sum('total_bpjs'), 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="p-4 text-center text-base text-orange-700">
                                    <div class="flex items-center justify-center ">
                                        Rp {{ number_format($branchStats->sum('total_infaq'), 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="p-4 text-center text-base">
                                    <div class="flex items-center justify-center">
                                        Rp {{ number_format($branchStats->sum('total_expense'), 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <p class="text-xs text-gray-400 italic">* Klik pada baris untuk melihat detail karyawan dari cabang tersebut.</p>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {});
        });
    </script>
</body>
</html>