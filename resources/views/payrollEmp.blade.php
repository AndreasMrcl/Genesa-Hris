<!DOCTYPE html>
<html lang="en">

<head>
    <title>Period Detail</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-5 space-y-4">

            <!-- Header with Back Button -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800"><i class="fas fa-users text-indigo-600"></i> {{ $branchName }}</h1>
                    <p class="text-sm text-indigo-600 font-semibold mt-1">
                        Employee Payroll for Period: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
                    </p>
                </div>
                <a href="{{ route('periodPayrollBranch', ['start' => $start, 'end' => $end]) }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition flex items-center gap-2 text-sm font-medium">
                    <span>&larr;</span> Back to Branch List
                </a>
            </div>

            <!-- Table -->
            <div class="w-full rounded-lg bg-white shadow-md">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-700">Employee List</h2>
                </div>
                <div class="p-2 overflow-auto">
                    <table id="employeeTable" class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-3 font-semibold text-gray-600 text-sm uppercase">Employee</th>
                                <th class="p-3 font-semibold text-gray-600 text-sm uppercase">Branch</th>
                                <th class="p-3 font-semibold text-gray-600 text-sm uppercase">Base Salary</th>
                                <th class="p-3 font-semibold text-gray-600 text-sm uppercase">Net Salary</th>
                                <th class="p-3 font-semibold text-gray-600 text-sm uppercase text-center">Status</th>
                                <th class="p-3 font-semibold text-gray-600 text-sm uppercase text-center">Method</th>
                                <th class="p-3 font-semibold text-gray-600 text-sm uppercase text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($payrolls as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-3">
                                        <a href="{{ route('showpayroll', $item->id) }}" class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                {{ substr($item->employee->name, 0, 2) }}
                                            </div>
                                           <div>
                                                <div class="font-medium text-gray-900">{{ $item->employee->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->employee->position->name  }}</div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="p-3 text-sm text-gray-600">
                                        {{ $item->employee->branch->name ?? '-' }}
                                    </td>
                                    <td class="p-3 text-sm text-gray-600">
                                        Rp {{ number_format($item->base_salary, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3">
                                        <span class="font-bold text-green-600">
                                            Rp {{ number_format($item->net_salary, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        @if ($item->status == 'paid')
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-50 rounded-full border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Paid
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-50 rounded-full border border-yellow-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-600"></span> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-sm text-center text-gray-600">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs uppercase font-medium text-blue-700 bg-blue-50 rounded-full border border-blue-200">
                                            {{ $item->employee->payroll_method }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <!-- View Slip -->
                                            <a href="{{ route('showpayroll', $item->id) }}"
                                                class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition"
                                                title="View Slip">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <!-- Delete Single -->
                                            <form action="{{ route('delpayroll', $item->id) }}" method="POST"
                                                class="delete-single-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition"
                                                    title="Delete this slip only">
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
            // Aktifkan DataTable untuk sorting & search di level karyawan
            new DataTable('#employeeTable', {
                responsive: true,
                columnDefs: [{
                        orderable: false,
                        targets: -1
                    } // Disable sort kolom Action
                ]
            });

            // Delete Confirmation
            $('.delete-single-form').submit(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delete this slip?',
                    text: "Only this employee's slip will be deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) this.submit();
                });
            });
        });
    </script>
</body>

</html>