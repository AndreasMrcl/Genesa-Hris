<!DOCTYPE html>
<html lang="en">

<head>
    <title>Shift Management</title>
    @include('layout.head')
    <!-- DataTables CSS -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    
    <style>
        /* DataTables Override */
        .dataTables_wrapper .dataTables_length select { padding-right: 2rem; border-radius: 0.5rem; }
        .dataTables_wrapper .dataTables_filter input { padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #d1d5db; }
        table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb; }

        /* FullCalendar Responsive Tweaks */
        @media (max-width: 768px) {
            .fc-toolbar.fc-header-toolbar { flex-direction: column; gap: 0.5rem; }
            .fc-toolbar-title { font-size: 1.2rem; text-align: center; }
            .fc-daygrid-day-number { font-size: 0.75rem; }
            .fc-event { font-size: 0.7rem; padding: 2px 3px; }
            #calendar { min-width: 600px; } /* Force horizontal scroll on very small screens */
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
                        <i class="fas fa-calendar-alt text-indigo-600"></i> Shift Management
                    </h1>
                    <p class="text-sm text-gray-500">Manage employee shifts and schedules</p>
                </div>
                <button id="addBtn" class="px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Shift
                </button>
            </div>

            <!-- Grid Layout: Table & Calendar -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                
                <!-- LEFT: SHIFT LIST TABLE (Col-Span 1) -->
                <div class="xl:col-span-1 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 flex flex-col h-full">
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <h2 class="font-bold text-gray-700">Shift List</h2>
                    </div>
                    <div class="p-4 overflow-auto flex-grow">
                        <table id="myTable" class="w-full text-left">
                            <thead class="bg-gray-100 text-gray-600 text-xs leading-normal">
                                <tr>
                                    <th class="p-3 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                    <th class="p-3 font-bold">Employee</th>
                                    <th class="p-3 font-bold text-center rounded-tr-lg">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @php $no = 1; @endphp
                                @foreach ($shifts as $item)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="p-3 font-medium">{{ $no++ }}</td>
                                        <td class="p-3">
                                            <div class="font-bold text-gray-900">{{ $item->employee->name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="flex justify-center items-center gap-2">
                                                {{-- Edit Button --}}
                                                <button class="editBtn w-8 h-8 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition"
                                                    data-id="{{ $item->id }}"
                                                    data-employee="{{ $item->employee_id }}"
                                                    title="Edit">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>

                                                {{-- Delete Button --}}
                                                <form method="post" action="{{ route('delshift', ['id' => $item->id]) }}" class="inline deleteForm">
                                                    @csrf @method('delete')
                                                    <button type="button" class="delete-confirm w-8 h-8 flex items-center justify-center bg-gray-500 text-white rounded-lg shadow hover:bg-red-600 transition" title="Delete">
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

                <!-- RIGHT: CALENDAR (Col-Span 2) -->
                <div class="xl:col-span-2 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h2 class="font-bold text-gray-700">Calendar View</h2>
                    </div>
                    <div class="p-4 overflow-auto">
                        <!-- Calendar Container -->
                        <div id="calendar" class="rounded-lg min-w-[320px] text-sm"></div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <!-- ADD MODAL -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-clock text-indigo-600"></i> Add New Shift
            </h2>

            <form id="addForm" method="post" action="{{ route('postshift') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('post')
                
                <!-- Employee Select -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Employee</label>
                    <select name="employee_id" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 transition" required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employee as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
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
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Start Time</label>
                        <input type="time" name="start_time" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">End Time</label>
                        <input type="time" name="end_time" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>

                <!-- Description (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Note / Description</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"></textarea>
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
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative">
            <button id="closeModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Shift
            </h2>

            <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('put')
                
                <!-- Employee Select -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Employee</label>
                    <select id="editEmployeeId" name="employee_id" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employee as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                        <i class="fas fa-check"></i> Update Shift
                    </button>
                </div>
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
            
            // Edit Button Logic
            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                $('#editEmployeeId').val(btn.data('employee'));
                // Populate field lain...
                
                $('#editForm').attr('action', `/shift/${btn.data('id')}/update`); 
                editModal.removeClass('hidden');
            });
            
            $('#closeModal').click(() => editModal.addClass('hidden'));

            // Close on click outside
            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
                if (e.target === editModal[0]) editModal.addClass('hidden');
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

            const shifts = @json($shifts); 

            function stringToColor(str) {
                let hash = 0;
                for (let i = 0; i < str.length; i++) {
                    hash = str.charCodeAt(i) + ((hash << 5) - hash);
                }
                const h = Math.abs(hash) % 360;
                return `hsl(${h}, 70%, 60%)`;
            }

            const events = shifts.map(shift => {

                const employeeName = shift.employee ? shift.employee.name : 'Unknown';
                const color = stringToColor(employeeName);
                
                let startStr = shift.start_datetime; 
                let endStr = shift.end_datetime;

                if (!startStr && shift.date && shift.start_time) {
                     startStr = `${shift.date}T${shift.start_time}`;
                     endStr = `${shift.date}T${shift.end_time}`; // Asumsi shift 1 hari
                }

                return {
                    title: employeeName,
                    start: startStr, 
                    end: endStr,
                    backgroundColor: color,
                    borderColor: color,
                    textColor: '#ffffff', 
                    extendedProps: {
                        description: shift.description ?? '-',
                        branch: shift.branch ? shift.branch.name : '-'
                    }
                };
            });

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto', // Agar tinggi menyesuaikan konten
                events: events,
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    Swal.fire({
                        title: info.event.title,
                        html: `
                            <div class="text-left">
                                <p><strong>Branch:</strong> ${props.branch}</p>
                                <p><strong>Time:</strong> ${info.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${info.event.end ? info.event.end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '?'}</p>
                                <p><strong>Note:</strong> ${props.description}</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'Close'
                    });
                }
            });

            calendar.render();
        });
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')

</body>
</html>