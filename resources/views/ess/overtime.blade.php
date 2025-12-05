<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Overtime</title>
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


    <!-- OVERTIME -->
    <div class="p-2">
        <!-- Back Button -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">
            <!-- Header Section -->
            <div>
                <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                    <i class="fas fa-business-time text-purple-600"></i> Overtime Management
                </h1>
                <p class="text-sm text-gray-500">Manage employee overtime records</p>
            </div>

            <!-- Table Section -->
            <div class="overflow-auto">
                <table id="myTable" class="w-full text-left">
                    <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                        <tr>
                            <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                            <th class="p-4 font-bold">Date</th>
                            <th class="p-4 font-bold">Employee</th>
                            <th class="p-4 font-bold text-center">Start</th>
                            <th class="p-4 font-bold text-center">End</th>
                            <th class="p-4 font-bold text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-700 text-sm">
                        @php $no = 1; @endphp
                        @foreach ($overtimes as $item)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="p-4 font-medium">{{ $no++ }}</td>
                                <td class="p-4 font-medium">
                                    {{ \Carbon\Carbon::parse($item->overtime_date)->format('d M Y') }}
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $item->employee->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->employee->position ?? '' }}</div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-mono">
                                        {{ $item->start_time ? \Carbon\Carbon::parse($item->start_time)->format('H:i') : '-' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-mono">
                                        {{ $item->end_time ? \Carbon\Carbon::parse($item->end_time)->format('H:i') : '-' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    @php
                                        $statusColor = match ($item->status) {
                                            'approved' => 'bg-green-100 text-green-700 border-green-200',
                                            'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span
                                        class="{{ $statusColor }} px-3 py-1 rounded-full text-xs font-bold border uppercase shadow-sm">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- FIXED ADD BUTTON -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg md:max-w-sm mx-auto">
        <button id="addBtn"
            class="w-full px-6 py-4 bg-purple-600 text-white font-semibold rounded-none hover:bg-purple-700 transition flex items-center justify-center gap-2 text-lg">
            <i class="fas fa-plus"></i> Request Overtime
        </button>
    </div>

    <!-- ADD MODAL -->
    <div id="addModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-business-time text-purple-600"></i> Add Overtime
            </h2>

            <form id="addForm" method="post" action="{{ route('req-overtime') }}" enctype="multipart/form-data"
                class="space-y-2">
                @csrf
                @method('post')

                <!-- AUTO-FILLED EMPLOYEE -->
                <input type="hidden" name="employee_id" value="{{ Auth::guard('employee')->id() }}">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Employee</label>
                    <input type="text" value="{{ Auth::guard('employee')->user()->name }}"
                        class="w-full rounded-lg bg-gray-100 border-gray-300 shadow-sm p-2.5 border" disabled>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                    <input type="date" name="overtime_date"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Start Time</label>
                    <input type="time" name="start_time"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">End Time</label>
                    <input type="time" name="end_time"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500"
                        required>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-purple-600 text-white font-bold rounded-lg shadow-md hover:bg-purple-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Submit
                </button>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Init DataTable
            new DataTable('#myTable', {});

            // Modal Logic
            const addModal = $('#addModal');

            $('#addBtn').click(() => addModal.removeClass('hidden'));
            $('#closeAddModal').click(() => addModal.addClass('hidden'));

        });
    </script>
</body>

</html>
