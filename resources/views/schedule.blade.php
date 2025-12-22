<!DOCTYPE html>
<html lang="en">

<head>
    <title>Work Schedule</title>
    @include('layout.head')
    <!-- DataTables CSS -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
    <style>

        .dataTables_wrapper .dataTables_length select { padding-right: 2rem; border-radius: 0.5rem; }
        .dataTables_wrapper .dataTables_filter input { padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #d1d5db; }
        table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb; }

        @media (max-width: 768px) {
            .fc-toolbar.fc-header-toolbar { flex-direction: column; gap: 0.5rem; }
            .fc-toolbar-title { font-size: 1.2rem; text-align: center; }
            .fc-daygrid-day-number { font-size: 0.75rem; }
            .fc-event { font-size: 0.7rem; padding: 2px 3px; }
            #calendar { min-width: 600px; } 
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="far fa-calendar-alt text-indigo-600"></i> Work Schedule
                    </h1>
                    <p class="text-sm text-gray-500">Manage employee shifts and schedules</p>
                </div>

                <div class="w-full md:w-1/3">
                    <form action="{{ route('schedule') }}" method="GET" id="filterForm">
                        <div class="relative">
                            <select name="branch_id" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 pl-10 border focus:ring-2 focus:ring-indigo-500 font-semibold text-gray-700 cursor-pointer">
                                <option value="">-- Select Branch to View --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-building text-gray-400"></i>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="md:flex gap-2 space-y-2 md:space-y-0">
                    <a href="{{ route('shift') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-bold flex items-center gap-2">
                        <i class="fas fa-clock"></i> Manage Master Shifts
                    </a>
                    
                    @if($selectedBranchId)
                        <button id="addBtn" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition font-bold flex items-center gap-2">
                            <i class="fas fa-plus"></i> Assign Schedule
                        </button>
                    @else
                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-bold flex items-center gap-2" title="Select a branch first">
                            <i class="fas fa-plus"></i> Assign Schedule
                        </button>
                    @endif
                </div>
            </div>

            @if($selectedBranchId)

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    
                    <!-- LEFT: SHIFT LIST TABLE -->
                    <div class="xl:col-span-1 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 flex flex-col h-full">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h2 class="font-bold text-gray-700">Recent Assignments</h2>
                        </div>
                        <div class="p-4 overflow-auto flex-grow">
                            <table id="myTable" class="w-full text-left">
                                <thead class="bg-gray-100 text-gray-600 text-xs leading-normal">
                                    <tr>
                                        <th class="p-3 font-bold">Employee</th>
                                        <th class="p-3 font-bold text-center">Date & Shift</th>
                                        <th class="p-3 font-bold text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    @foreach ($schedules as $item)
                                        <tr class="hover:bg-indigo-50 transition duration-150">
                                            <td class="p-3">
                                                <div class="font-bold text-gray-900">{{ $item->employee->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->employee->branch->name }}</div>
                                            </td>
                                            <td class="p-3 text-center">
                                                <div class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</div>
                                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold text-white" style="background-color: {{ $item->shift->color }}">
                                                    {{ $item->shift->name }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    {{-- Edit Button --}}
                                                    <button class="editBtn w-8 h-8 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition"
                                                        data-id="{{ $item->id }}"
                                                        data-employee="{{ $item->employee_id }}"
                                                        data-date="{{ $item->date }}"
                                                        data-shift="{{ $item->shift_id }}"
                                                        title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    {{-- Delete Button --}}
                                                    <form method="post" action="{{ route('delschedule', $item->id) }}" class="inline deleteForm">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="delete-confirm w-8 h-8 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition" title="Delete">
                                                            <i class="fas fa-trash text-xs"></i>
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

                    <!-- RIGHT: CALENDAR -->
                    <div class="xl:col-span-2 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h2 class="font-bold text-gray-700">Calendar View</h2>
                            <div class="flex gap-2">
                                @foreach($shifts as $s)
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $s->color }}"></span> {{ $s->name }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-4 overflow-auto">
                            <!-- Calendar Container -->
                            <div id="calendar" class="rounded-lg min-w-[320px] text-sm"></div>
                        </div>
                    </div>

                </div>
            @else
                <!-- EMPTY STATE -->
                <div class="flex flex-col items-center justify-center h-96 bg-white p-5 rounded-xl shadow-sm border border-gray-100 text-center p-10">
                    <div class="bg-indigo-50 p-4 rounded-full mb-4">
                        <i class="fas fa-building text-4xl text-indigo-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Select a Branch</h3>
                    <p class="text-gray-500 mt-2 max-w-md">Please select a branch from the dropdown above to view schedules and manage shifts for employees.</p>
                </div>
            @endif
        </div>
    </main>

    <!-- ADD MODAL -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-plus-circle text-indigo-600"></i> Assign Schedule
            </h2>

            <form id="addForm" method="post" action="{{ route('postschedule') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                
                <!-- Employee Select -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Employee(s)</label>
                    <select name="employee_ids[]" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" required multiple size="3">
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-500 mt-1">* Hold Ctrl/Cmd to select multiple employees.</p>
                </div>

                <!-- Date & Time Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Shift Template</label>
                    <div class="grid grid-cols-2 gap-3 max-h-40 overflow-y-auto p-1">
                        @foreach($shifts as $shift)
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition border-gray-200 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                <input type="radio" name="shift_id" value="{{ $shift->id }}" class="mr-3 text-indigo-600 focus:ring-indigo-500 border-gray-300" required>
                                <div>
                                    <div class="font-bold text-sm text-gray-800">{{ $shift->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition flex justify-center items-center gap-2">
                        <i class="fas fa-save"></i> Save Shift
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Schedule
            </h2>

            <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('put')
                
                <!-- Employee Select -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Employee</label>
                    <select id="editEmployeeId" name="employee_id" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-gray-100" required>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                    <input type="date" id="editDate" name="date" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-gray-100" readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Shift</label>
                    <select id="editShiftId" name="shift_id" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->name }} ({{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> Update Schedule
                </button>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Init DataTable (Simple Version for side list)
            new DataTable('#myTable', {
            });

            // Modal Logic
            const addModal = $('#addModal');
            const editModal = $('#editModal');

            $('#addBtn').click(() => addModal.removeClass('hidden'));
            $('#closeAddModal').click(() => addModal.addClass('hidden'));
            $('#closeModal').click(() => editModal.addClass('hidden'));
            
            // Close on click outside
            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
                if (e.target === editModal[0]) editModal.addClass('hidden');
            });

            // Edit Button Logic
            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                $('#editEmployeeId').val(btn.data('employee'));
                $('#editDate').val(btn.data('date'));
                $('#editShiftId').val(btn.data('shift'));
                
                $('#editForm').attr('action', `/schedule/${btn.data('id')}/update`); 
                editModal.removeClass('hidden');
            });

            // Delete Confirm
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Shift?',
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

        // FullCalendar Logic
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const events = [
                @foreach($schedules as $s)
                {
                    id: '{{ $s->id }}',
                    title: '{{ $s->employee->name }}',
                    start: '{{ $s->date }}',
                    backgroundColor: '{{ $s->shift->color ?? "#3B82F6" }}',
                    borderColor: 'transparent',
                    extendedProps: {
                        shiftName: '{{ $s->shift->name }}',
                        time: '{{ substr($s->shift->start_time, 0, 5) }} - {{ substr($s->shift->end_time, 0, 5) }}',
                        employeeId: '{{ $s->employee_id }}',
                        shiftId: '{{ $s->shift_id }}'
                    }
                },
                @endforeach
            ];

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                height: 650,
                events: events,
                
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    
                    $('#editEmployeeId').val(props.employeeId);
                    $('#editDate').val(info.event.startStr);
                    $('#editShiftId').val(props.shiftId);
                    
                    $('#editForm').attr('action', `/schedule/${info.event.id}/update`);
                    $('#editModal').removeClass('hidden');
                }
            });

            calendar.render();
        });
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')

</body>
</html>