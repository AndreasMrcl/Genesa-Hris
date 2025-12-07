<!DOCTYPE html>
<html lang="en">

<head>
    <title>Attendance Management</title>
    @include('layout.head')
    <!-- DataTables -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Override DataTables Style */
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

            <!-- Header Section -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-clock text-indigo-600"></i> Attendance Recap
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">List of recorded attendance periods</p>
                </div>
                <a href="{{ route('manageattendance') }}" 
                   class="p-2 px-6 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> New Recap
                </a>
            </div>

            <!-- Table Section -->
            <div class="w-full rounded-lg bg-white shadow-md">
                <div class="p-2 overflow-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
                            <tr>
                                <th class="p-4 font-bold">Period Range</th>
                                <th class="p-4 font-bold text-center">Total Employees</th>
                                <th class="p-4 font-bold text-right">Last Updated</th>
                                <th class="p-4 font-bold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700 text-sm">
                            @forelse ($batches as $batch)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <a href="{{ route('manageattendance', ['start' => $batch->period_start, 'end' => $batch->period_end]) }}" 
                                               class="text-lg font-bold text-blue-600 hover:underline mb-1">
                                                {{ \Carbon\Carbon::parse($batch->period_start)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($batch->period_end)->format('d M Y') }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                                            {{ $batch->total_records }} Records
                                        </span>
                                    </td>
                                    <td class="p-4 text-right text-gray-500">
                                        {{ \Carbon\Carbon::parse($batch->last_updated)->diffForHumans() }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <!-- Delete Batch Form -->
                                        <form action="{{ route('delattendance') }}" method="POST" class="inline-block delete-batch-form">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="start" value="{{ $batch->period_start }}">
                                            <input type="hidden" name="end" value="{{ $batch->period_end }}">
                                            
                                            <button type="button" class="delete-confirm p-2 w-9 h-9 text-white bg-red-500 rounded-lg shadow hover:bg-red-600 transition" title="Delete Batch">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-500">
                                        No attendance recap found. Click "New Recap" to start.
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

    <!-- SCRIPTS -->
    <script>
        document.querySelectorAll('.delete-confirm').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Delete this period?',
                    text: "This will delete ALL attendance data for this date range. Cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')

</body>

</html>