<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payroll History</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

<body class="bg-gray-50">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-money-check-alt text-indigo-600"></i> Payroll History
                    </h1>
                    <p class="text-sm text-gray-500">List of generated payroll periods</p>
                </div>
                <a href="{{ route('createpayroll') }}"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Run Payroll
                </a>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center gap-2"
                    role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Success!</span> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Error!</span> {{ $errors->first() }}
                </div>
            @endif

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold">Period Range</th>
                                <th class="p-4 font-bold ">
                                    <div class="flex justify-center">
                                        Total Branches
                                    </div>  
                                </th>
                                <th class="p-4 font-bold">
                                    <div class="flex justify-center">
                                        Total Expense
                                    </div> 
                                </th>
                                <th class="p-4 font-bold">
                                    <div class="flex justify-center">
                                        Action
                                    </div> 
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @foreach ($batches as $batch)
                                <tr class="cursor-pointer group">
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <a href="{{ route('periodPayrollBranch', ['start' => $batch->pay_period_start, 'end' => $batch->pay_period_end]) }}"
                                                class="text-lg font-bold text-indigo-600 mb-1">
                                                {{ \Carbon\Carbon::parse($batch->pay_period_start)->format('d M Y') }} -
                                                {{ \Carbon\Carbon::parse($batch->pay_period_end)->format('d M Y') }}
                                            </a>
                                            <span class="text-xs text-gray-400">Created:
                                                {{ \Carbon\Carbon::parse($batch->created_at)->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center">
                                            <span class="font-bold text-gray-800 text-base">
                                            {{ $batch->total_branches }}
                                            </span>
                                        </div>  
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="font-bold text-gray-800 text-base">
                                            Rp {{ number_format($batch->total_spent, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">

                                            <!-- TOMBOL EXPORT EXCEL -->
                                            <a href="{{ route('payrollExport', ['start' => $batch->pay_period_start, 'end' => $batch->pay_period_end]) }}"
                                                class="p-2 text-green-600 bg-green-50 hover:bg-green-100 rounded-full transition shadow-sm"
                                                title="Download Excel Rekap">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>

                                            <!-- TOMBOL EXPORT REPORT EXCEL -->
                                            <a href="{{ route('payrollReportExport', ['start' => $batch->pay_period_start, 'end' => $batch->pay_period_end]) }}"
                                                class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-full transition shadow-sm"
                                                title="Download Laporan Lengkap">
                                                <i class="fas fa-chart-pie"></i>
                                            </a>

                                            <!-- TOMBOL DELETE BATCH -->
                                            <form action="{{ route('delpayrollBatch') }}" method="POST"
                                                class="inline-block delete-batch-form">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="start"
                                                    value="{{ $batch->pay_period_start }}">
                                                <input type="hidden" name="end"
                                                    value="{{ $batch->pay_period_end }}">

                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition"
                                                    title="Delete Entire Period">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {});

            $('.delete-confirm').click(function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Payroll Batch?',
                    text: "This will delete ALL data for this period!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete all!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
    @include('sweetalert::alert')
    @include('layout.loading')
</body>

</html>
