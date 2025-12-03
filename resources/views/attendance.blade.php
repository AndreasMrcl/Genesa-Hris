<!DOCTYPE html>
<html lang="en">

<head>
    <title>Attendance Management</title>
    @include('layout.head')
    <!-- DataTables -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

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
                        <i class="fas fa-clock text-indigo-600"></i> Attendance
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Manage daily attendance records</p>
                </div>
                <button id="addBtn"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Attendance
                </button>
            </div>

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Date</th>
                                <th class="p-4 font-bold">Employee</th>
                                <th class="p-4 font-bold text-center">Clock In</th>
                                <th class="p-4 font-bold text-center">Clock Out</th>
                                <th class="p-4 font-bold text-center">Status</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($attendances as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium">{{ $no++ }}</td>
                                    <td class="p-4 font-medium">
                                        {{ \Carbon\Carbon::parse($item->attendance_date)->format('d M Y') }}
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900">{{ $item->employee->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->employee->position ?? '' }}</div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-mono">
                                            {{ $item->clock_in ? \Carbon\Carbon::parse($item->clock_in)->format('H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-mono">
                                            {{ $item->clock_out ? \Carbon\Carbon::parse($item->clock_out)->format('H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $statusColor = match ($item->status) {
                                                'present' => 'bg-green-100 text-green-700 border-green-200',
                                                'absent' => 'bg-red-100 text-red-700 border-red-200',
                                                'leave' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'sick' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                                'off' => 'bg-gray-100 text-gray-600 border-gray-200',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span
                                            class="{{ $statusColor }} px-3 py-1 rounded-full text-xs font-bold border uppercase shadow-sm">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Edit Button --}}
                                            <button
                                                class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}" data-employee="{{ $item->employee_id }}"
                                                data-attendance_date="{{ $item->attendance_date }}"
                                                data-clock_in="{{ $item->clock_in }}"
                                                data-clock_out="{{ $item->clock_out }}"
                                                data-status="{{ $item->status }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Delete Button --}}
                                            <form method="post"
                                                action="{{ route('delattendance', ['id' => $item->id]) }}"
                                                class="inline deleteForm">
                                                @csrf
                                                @method('delete')
                                                <button type="button"
                                                    class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
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

    <!-- ADD MODAL -->
    <div id="addModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-clock text-indigo-600"></i> Add Attendance
            </h2>

            <form id="addForm" method="post" action="{{ route('postattendance') }}" enctype="multipart/form-data"
                class="space-y-5">
                @csrf
                @method('post')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Employee</label>
                    <select name="employee_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                        required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employee as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                    <input type="date" name="attendance_date"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Clock In</label>
                        <input type="time" name="clock_in"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Clock Out</label>
                        <input type="time" name="clock_out"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                        required>
                        <option value="">-- Select Status --</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="leave">Leave</option>
                        <option value="sick">Sick</option>
                        <option value="off">Off</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Submit
                </button>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Attendance
            </h2>

            <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('put')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Employee</label>
                    <select id="editEmployee" name="employee_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employee as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                    <input type="date" id="editAttendanceDate" name="attendance_date"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Clock In</label>
                        <input type="time" id="editClockIn" name="clock_in"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Clock Out</label>
                        <input type="time" id="editClockOut" name="clock_out"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select id="editStatus" name="status"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="leave">Leave</option>
                        <option value="sick">Sick</option>
                        <option value="off">Off</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> Update
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
            new DataTable('#myTable', {
            });
            // Modal Logic
            const addModal = $('#addModal');
            const editModal = $('#editModal');

            $('#addBtn').click(() => addModal.removeClass('hidden'));
            $('#closeAddModal').click(() => addModal.addClass('hidden'));

            // Edit Logic
            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                $('#editEmployee').val(btn.data('employee'));
                $('#editAttendanceDate').val(btn.data('attendance_date'));
                $('#editClockIn').val(btn.data('clock_in'));
                $('#editClockOut').val(btn.data('clock_out'));
                $('#editStatus').val(btn.data('status'));

                // Update Action URL
                $('#editForm').attr('action', `/attendance/${btn.data('id')}/update`);

                editModal.removeClass('hidden');
            });

            $('#closeModal').click(() => editModal.addClass('hidden'));

            // Close on click outside
            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
                if (e.target === editModal[0]) editModal.addClass('hidden');
            });

            // Delete confirmation
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Attendance?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
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
