<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payroll History</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-black">
                        <i class="fas fa-money-check-alt text-indigo-600"></i> Payroll History
                    </h1>
                    <p class="text-sm text-gray-500">List of generated payroll periods</p>
                </div>
                <a href="{{ route('createpayroll') }}"
                    class="p-2 px-6 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition font-semibold flex items-center gap-2">
                    <span>+</span> Run Payroll
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
                    <table class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-semibold text-gray-700">Period</th>
                                <th class="p-4 font-semibold text-gray-700 text-center">Total Employees</th>
                                <th class="p-4 font-semibold text-gray-700 text-right">Total Expense</th>
                                <th class="p-4 font-semibold text-gray-700 text-center">Status</th>
                                <th class="p-4 font-semibold text-gray-700 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @forelse ($batches as $batch)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <!-- Link ke Level 2 (Detail Periode) -->
                                            <a href="{{ route('periodPayroll', ['start' => $batch->pay_period_start, 'end' => $batch->pay_period_end]) }}"
                                                class="text-lg font-bold text-indigo-600 hover:text-indigo-800 hover:underline mb-1">
                                                {{ \Carbon\Carbon::parse($batch->pay_period_start)->format('d M Y') }} -
                                                {{ \Carbon\Carbon::parse($batch->pay_period_end)->format('d M Y') }}
                                            </a>
                                            <span class="text-xs text-gray-400">Created:
                                                {{ \Carbon\Carbon::parse($batch->created_at)->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                                            {{ $batch->total_employees }} Staff
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="font-bold text-gray-800">
                                            Rp {{ number_format($batch->total_spent, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if ($batch->status == 'paid')
                                            <span
                                                class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full border border-green-200">
                                                Paid
                                            </span>
                                        @else
                                            <span
                                                class="px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full border border-yellow-200">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">

                                            <!-- TOMBOL EXPORT EXCEL (BARU) -->
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
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-500 bg-gray-50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium">No payroll history found</p>
                                            <p class="text-sm mt-1">Click "Run Payroll" to generate your first salary
                                                slip.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-4 px-2">
                        {{ $batches->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // SweetAlert untuk Delete Batch
        document.querySelectorAll('.delete-batch-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delete this period?',
                    text: "This will delete ALL salary slips for this period. This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>