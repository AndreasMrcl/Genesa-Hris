<!DOCTYPE html>
<html lang="en">

<head>
    <title>Shift</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    <style>
        /* Make FullCalendar responsive */
        @media (max-width: 768px) {
            .fc-toolbar.fc-header-toolbar {
                flex-direction: column;
                gap: 0.5rem;
            }

            .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
            }

            .fc-toolbar-title {
                font-size: 1.2rem;
                text-align: center;
            }

            /* Shrink calendar content for small screens */
            .fc-daygrid-day-number {
                font-size: 0.75rem;
            }

            .fc-event {
                font-size: 0.7rem;
                padding: 2px 3px;
            }

            .fc-col-header-cell-cushion {
                font-size: 0.7rem;
                padding: 4px;
            }

            /* Allow horizontal scroll if needed */
            #calendar {
                min-width: 600px;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-5 space-y-4">

            <!-- Header & Add Button -->
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <h1 class="font-semibold text-2xl text-black">Shift</h1>
                <button id="addBtn"
                    class="p-2 px-8 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition">
                    Add
                </button>
            </div>

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($shifts as $item)
                                <tr class="border-2">
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->created_at ?? 'N/A' }}</td>
                                    <td>{{ $item->name ?? 'N/A' }}</td>
                                    <td>{{ $item->phone ?? 'N/A' }}</td>
                                    <td>{{ $item->address ?? 'N/A' }}</td>
                                    <td class="flex gap-2">
                                        <button class="editBtn w-full" data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}" data-phone="{{ $item->phone }}"
                                            data-address="{{ $item->address }}">
                                            <span
                                                class="p-2 text-white bg-blue-500 rounded-lg w-full hover:text-gray-300 block text-center shadow transition">Edit</span>
                                        </button>
                                        <form method="post" action="{{ route('delbranch', ['id' => $item->id]) }}"
                                            class="deleteForm w-full">
                                            @csrf
                                            @method('delete')
                                            <button type="button"
                                                class="delete-confirm p-2 text-white bg-red-500 rounded-lg w-full hover:text-gray-300 shadow transition">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Calendar --}}
            <div class="w-full rounded-lg bg-white shadow-md">
                <div class="p-2 overflow-auto">
                    <div id="calendar" class="rounded-lg min-w-[320px]"></div>
                </div>
            </div>

        </div>
    </main>

    <!-- Add Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-white/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div
            class="bg-white rounded-lg p-6 w-full max-w-lg shadow-lg relative transform transition-all duration-300 scale-95">
            <button id="closeAddModal"
                class="absolute top-4 right-4 text-white hover:text-gray-300 bg-red-500 p-1 px-4 rounded-full">
                ✕
            </button>

            <h1 class="text-2xl font-semibold mb-8">Add</h1>

            <form id="addForm" method="post" action="{{ route('postshift') }}" enctype="multipart/form-data"
                class="space-y-3">
                @csrf
                @method('post')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <label class="font-semibold">Employee:</label>
                        <select id="editEmployee" name="employee_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full" required>
                            <option></option>
                            @foreach ($employee as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <button type="submit"
                    class="bg-green-500 text-white p-4 w-full rounded-lg hover:bg-green-600 shadow transition">
                    Submit
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-white/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div
            class="bg-white rounded-lg p-6 w-full max-w-lg shadow-lg relative transform transition-all duration-300 scale-95">
            <button id="closeModal"
                class="absolute top-4 right-4 text-white hover:text-gray-300 bg-red-500 p-1 px-4 rounded-full">✕</button>

            <h1 class="text-2xl font-semibold mb-8">Edit</h1>

            <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-3">
                @csrf
                @method('put')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold">Name:</label>
                        <input type="text" id="editName" name="name" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full">
                    </div>
                    <div>
                        <label class="font-semibold">Phone:</label>
                        <input type="text" id="editPhone" name="phone" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full">
                    </div>
                </div>
                <div>
                    <label class="font-semibold">Address:</label>
                    <input type="text" id="editAddress" name="address" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full">
                </div>
                <button type="submit"
                    class="bg-blue-500 text-white p-4 w-full rounded-lg hover:bg-blue-600 shadow transition">
                    Submit
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        //Table
        $(document).ready(function() {
            new DataTable('#myTable', {});

            // Add modal
            const addModal = $('#addModal');
            $('#addBtn').click(() => addModal.removeClass('hidden'));
            $('#closeAddModal').click(() => addModal.addClass('hidden'));
            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
            });

            // Edit modal
            const editModal = $('#editModal');
            $('.editBtn').click(function() {
                const btn = $(this);
                $('#editName').val(btn.data('name'));
                $('#editPhone').val(btn.data('phone'));
                $('#editAddress').val(btn.data('address'));
                $('#editForm').attr('action', `/branch/update/${btn.data('id')}`);
                editModal.removeClass('hidden');
            });
            $('#closeModal').click(() => editModal.addClass('hidden'));
            $(window).click((e) => {
                if (e.target === editModal[0]) editModal.addClass('hidden');
            });

            // Delete confirmation
            $('.delete-confirm').click(function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Calendar
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const shifts = @json($shifts);

            // Fungsi untuk membuat warna unik dari string
            function stringToColor(str) {
                let hash = 0;
                for (let i = 0; i < str.length; i++) {
                    hash = str.charCodeAt(i) + ((hash << 5) - hash);
                }
                const color = Math.floor(Math.abs(Math.sin(hash) * 16777215) % 16777215).toString(16);
                return '#' + '0'.repeat(6 - color.length) + color;
            }

            const events = shifts.map(shift => {
                const employeeName = shift.employee ? shift.employee.name : 'No employee';
                const color = stringToColor(employeeName);

                return {
                    title: employeeName,
                    start: shift.start_datetime ?? `${shift.start_shift}T${shift.start_time}`,
                    end: shift.end_datetime ?? `${shift.end_shift}T${shift.end_time}`,
                    backgroundColor: color,
                    borderColor: color,
                    extendedProps: {
                        description: shift.description ?? 'No description',
                        branch: shift.branch ? shift.branch.name : '-'
                    }
                };
            });

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events,
                eventClick(info) {
                    const desc = info.event.extendedProps.description || 'No description';
                    const branch = info.event.extendedProps.branch || '-';
                    alert(`Shift: ${info.event.title}\nBranch: ${branch}\nNote: ${desc}`);
                }
            });

            calendar.render();
        });
    </script>

    @include('sweetalert::alert')

</body>

</html>
