<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Payroll</title>
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    @include('ess.layout.head')
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto">

    <!-- HEADER / BACK BUTTON -->
    <div class="p-2">
        <a href="{{ route('ess-home') }}"
            class="inline-flex items-center gap-2 px-6 py-2 bg-white text-gray-700 rounded-xl text-3xl">
            <span>&larr;</span>
        </a>
    </div>


    <!-- PAYROLL -->
    <div class="p-2">
        <!-- Back Button -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">

                        <!-- Header Section -->
            <div>
                    <h1 class="font-semibold text-2xl text-black">
                        <i class="fas fa-money-check-alt text-indigo-600"></i> Payroll History
                    </h1>
                    <p class="text-sm text-gray-500">List of generated payroll periods</p>
            </div>

            <!-- Table Section -->
            <div class="overflow-auto">
                <table id="employeeTable" class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
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
                            @php $no = 1; @endphp
                            @foreach ($payrolls as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 font-medium">{{ $no++ }}</td>
                                    <td class="p-3">
                                        <a href="{{ route('showpayroll', $item->id) }}" class="flex items-center gap-3">
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
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
        </div>
    </div>


    @include('layout.loading')

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Init DataTable
            new DataTable('#myTable', {});
        });
    </script>
</body>

</html>
