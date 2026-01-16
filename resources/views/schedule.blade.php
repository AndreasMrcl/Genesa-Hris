<!DOCTYPE html>
<html lang="en">

<head>
    <title>Jadwal Kerja</title>

    @if (isset($isEss) && $isEss)
        @include('ess.layout.head')
    @else
        @include('layout.head')
    @endif
    <!-- DataTables CSS -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        .dataTables_wrapper .dataTables_length select { padding-right: 2rem; border-radius: 0.5rem; }
        .dataTables_wrapper .dataTables_filter input { padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #d1d5db; }
        table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb; }
        .select2-container .select2-selection--multiple { min-height: 45px; border-color: #d1d5db; border-radius: 0.5rem; }

        /* FullCalendar Customization */
        .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 700; color: #374151; }
        .fc-event { cursor: pointer; border: none; font-size: 0.65rem; padding: 1px 2px; margin-bottom: 1px !important; }
        .fc-daygrid-day-number { font-size: 0.75rem; color: #6b7280; padding: 4px !important; }
        
        /* Mobile Calendar */
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

        .select2-container .select2-selection--multiple {
            min-height: 45px;
            border-color: #d1d5db;
            border-radius: 0.5rem;
        }

        /* FullCalendar Customization */
        .fc-toolbar-title {
            font-size: 1.1rem !important;
            font-weight: 700;
            color: #374151;
        }

        .fc-event {
            cursor: pointer;
            border: none;
            font-size: 0.65rem;
            padding: 1px 2px;
            margin-bottom: 1px !important;
        }

        .fc-daygrid-day-number {
            font-size: 0.75rem;
            color: #6b7280;
            padding: 4px !important;
        }

        /* Mobile Calendar */
        @media (max-width: 768px) {
            .fc-toolbar.fc-header-toolbar {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 0.2rem;
                margin-bottom: 10px !important;
            }

            .fc-toolbar-chunk {
                display: flex;
                align-items: center;
                gap: 2px;
            }

            #calendar {
                font-size: 0.7rem;
            }

            .fc-view-harness {
                height: auto !important;
            }
        }
    </style>
</head>

<body class="{{ isset($isEss) && $isEss ? 'bg-gray-50 font-sans w-full md:max-w-sm mx-auto min-h-screen flex flex-col shadow-lg border-x border-gray-100' : 'bg-gray-50 font-sans' }}">

    @if (!isset($isEss) || !$isEss)
        {{-- ADMIN LAYOUT --}}
        @include('layout.sidebar')
        <main class="md:ml-64 xl:ml-72 2xl:ml-72">
            @include('layout.navbar')
            <div class="p-6 space-y-6">
            @else
                {{-- ESS LAYOUT --}}
                <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 border-b border-gray-200">
                    <div class="p-3 flex items-center justify-between">
                        <a href="{{ route('ess-home') }}"
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                            <i class="fas fa-arrow-left text-base"></i>
                        </a>
                        <h1 class="font-bold text-base text-gray-800">Jadwal Tim</h1>
                        <div class="w-9"></div>
                    </div>
                </div>
                <div class="p-3 space-y-4 flex-grow pb-20">
    @endif

    <div class="{{ isset($isEss) && $isEss ? 'flex flex-col gap-3' : 'bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4' }}">
        <!-- Kiri: Judul -->
        @if (!isset($isEss) || !$isEss)
            <div class="w-full md:w-auto">
                <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                    <i class="far fa-calendar-alt text-indigo-600"></i> Jadwal Kerja
                </h1>
                <p class="text-sm text-gray-500">Manajemen jadwal kerja</p>
            </div>
        @endif

        <!-- Tengah: Filter Branch -->
        <div class="{{ isset($isEss) && $isEss ? 'w-full' : 'w-full md:w-1/3' }}">
            @if (!isset($isEss) || !$isEss)
                <form action="{{ route('schedule') }}" method="GET" id="filterForm">
                    <div class="relative">
                        <select name="branch_id" onchange="this.form.submit()"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 pl-10 border focus:ring-2 focus:ring-indigo-500 font-semibold text-gray-700 cursor-pointer">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-building text-gray-400"></i>
                        </div>
                    </div>
                    @if (isset($outlets) && $outlets->count() > 0)
                        <div class="relative mt-2">
                            <select name="outlet_id" onchange="this.form.submit()"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 pl-9 border focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 cursor-pointer bg-gray-50">
                                <option value="">All Outlets</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}"
                                        {{ $selectedOutletId == $outlet->id ? 'selected' : '' }}>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-store text-gray-400"></i>
                            </div>
                        </div>
                    @endif
                </form>
            @else
                @if (isset($outlets) && $outlets->count() > 0)
                    <form action="{{ route('ess-coordinator-schedule') }}" method="GET">
                        <div class="relative">
                            <select name="outlet_id" onchange="this.form.submit()"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 pl-9 border focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 cursor-pointer bg-white text-sm">
                                <option value="">All My Outlets</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}"
                                        {{ $selectedOutletId == $outlet->id ? 'selected' : '' }}>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-store text-gray-400"></i>
                            </div>
                        </div>
                    </form>
                @endif
            @endif
        </div>

        @if (!isset($isEss) || !$isEss)
            <div class="flex gap-2 w-full md:w-auto justify-end">
                <a href="{{ route('shift') }}"
                    class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-bold flex items-center gap-2 text-sm whitespace-nowrap">
                    <i class="fas fa-clock"></i> Master Shifts
                </a>

                @if ($selectedBranchId)
                    <button id="addBtn"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition font-bold flex items-center gap-2 whitespace-nowrap">
                        <i class="fas fa-plus"></i> Atur Jadwal
                    </button>
                @else
                    <button disabled
                        class="px-5 py-2.5 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-bold flex items-center gap-2 whitespace-nowrap"
                        title="Select a branch first">
                        <i class="fas fa-plus"></i> Atur Jadwal
                    </button>
                @endif
            </div>
        @endif
    </div>

    @if ($selectedBranchId)
        <div class="{{ isset($isEss) && $isEss ? 'flex flex-col gap-4' : 'grid grid-cols-1 xl:grid-cols-3 gap-6' }}">
            <!-- RIGHT: CALENDAR -->
            <div class="{{ isset($isEss) && $isEss ? 'order-1' : 'xl:col-span-2 order-2' }} bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-3 border-b border-gray-100 bg-gray-50 flex flex-wrap justify-between items-center gap-2">
                    <h2 class="font-bold text-gray-700 text-sm">Kalender</h2>
                    <div class="flex flex-wrap gap-1 justify-end">
                        @foreach ($shifts as $s)
                            <div class="flex items-center gap-1 text-[10px] bg-white px-1.5 py-0.5 rounded border">
                                <span class="w-2 h-2 rounded-full"
                                    style="background-color: {{ $s->color }}"></span> {{ $s->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="p-2 overflow-hidden">
                    <div id="calendar" class="rounded-lg w-full"></div>
                </div>
            </div>

            <!-- LEFT: SCHEDULE LIST TABLE -->
            <div class="{{ isset($isEss) && $isEss ? 'order-2' : 'xl:col-span-1 order-1' }} bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 flex flex-col h-full">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-bold text-gray-700 text-sm">Susunan Jadwal</h2>
                </div>
                <div class="p-4 overflow-auto flex-grow">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-600 text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="p-3 font-bold border-b border-gray-100">Karyawan</th>
                                <th class="p-3 font-bold border-b border-gray-100 text-center">Info</th>
                                <th class="p-3 font-bold border-b border-gray-100 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-xs divide-y divide-gray-100">
                            @foreach ($schedules as $item)
                                <tr class="hover:bg-indigo-50 transition">
                                    <td class="p-3">
                                        <div class="font-bold text-gray-900 text-xs">
                                            {{ $item->employee->name ?? 'Unknown' }}</div>
                                        @if (isset($isEss) && $isEss)
                                        @else
                                            <div class="text-[9px] text-gray-400 mt-0.5">
                                                {{ $item->employee->branch->name ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="font-medium text-gray-800">
                                            {{ \Carbon\Carbon::parse($item->date)->format('d M') }}</div>
                                        <span
                                            class="inline-block mt-0.5 px-1.5 py-px rounded text-[9px] font-bold text-white"
                                            style="background-color: {{ $item->shift->color ?? '#ccc' }}">
                                            {{ $item->shift->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex justify-center items-center gap-1">
                                            {{-- Edit --}}
                                            <button
                                                class="editBtn w-7 h-7 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded hover:bg-indigo-100 transition"
                                                data-id="{{ $item->id }}" data-employee="{{ $item->employee_id }}"
                                                data-date="{{ $item->date }}" data-shift="{{ $item->shift_id }}">
                                                <i class="fas fa-edit text-[10px]"></i>
                                            </button>

                                            {{-- Delete --}}
                                            @php
                                                $deleteRoute =
                                                    isset($isEss) && $isEss
                                                        ? route('ess-coordinator-schedule-destroy', $item->id)
                                                        : route('delschedule', $item->id);
                                            @endphp
                                            <form method="post" action="{{ $deleteRoute }}"
                                                class="inline deleteForm">
                                                @csrf @method('DELETE')
                                                <button type="button"
                                                    class="delete-confirm w-7 h-7 flex items-center justify-center bg-red-50 text-red-600 rounded hover:bg-red-100 transition">
                                                    <i class="fas fa-trash text-[10px]"></i>
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
    @else
        <!-- EMPTY STATE -->
        <div
            class="flex flex-col items-center justify-center h-80 bg-white rounded-xl shadow-sm border border-dashed border-gray-300 text-center p-6">
            <div class="bg-indigo-50 p-3 rounded-full mb-3">
                <i class="fas fa-building text-3xl text-indigo-400"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">
                {{ isset($isEss) && $isEss ? 'Tidak ada data jadwal' : 'Pilih Cabang' }}
            </h3>
            <p class="text-gray-500 mt-1 text-xs max-w-xs">
                {{ isset($isEss) && $isEss
                    ? 'Tidak ada data jadwal untuk cabang/outlet Anda.'
                    : 'Silakan pilih cabang untuk melihat jadwal.' }}
            </p>
        </div>
    @endif

    @if (!isset($isEss) || !$isEss)
        </div>
        </main>
    @else
        </div>

        <!-- Floating Action Button -->
        @if ($selectedBranchId)
            <div
                class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg md:max-w-sm mx-auto p-4 z-30">
                <button id="addBtnESS"
                    class="w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition flex items-center justify-center gap-2 transform active:scale-95">
                    <i class="fas fa-plus-circle"></i>  Atur Jadwal Kerja
                </button>
            </div>
        @endif
    @endif

    <!-- MODAL ADD (ASSIGN) -->
    @if ($selectedBranchId)
        <div id="addModal"
            class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
            <div
                class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-lg shadow-2xl relative transform transition-all scale-100 h-[85vh] sm:h-auto flex flex-col">

                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-indigo-600"></i> Atur Jadwal Kerja
                    </h2>
                    <button id="closeAddModal"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-400 hover:text-gray-600 shadow-sm border border-gray-100 transition"><i
                            class="fas fa-times"></i></button>
                </div>

                <div class="p-6 overflow-y-auto flex-grow custom-scrollbar">
                    <form id="addForm" method="post"
                        action="{{ isset($isEss) && $isEss ? route('ess-coordinator-schedule-store') : route('postschedule') }}"
                        class="space-y-5">
                        @csrf

                        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                        <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}">

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-semibold text-gray-700">Pilih Karyawan</label>

                                <div class="flex items-center gap-2">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-1 text-xs text-gray-600 font-bold">Semua</span>
                                    </label>
                                </div>
                            </div>

                            <div class="relative mb-2">
                                <input type="text" id="employeeSearch" placeholder="Cari nama..."
                                    class="w-full rounded-lg border-gray-300 p-2 pl-8 text-xs focus:ring-2 focus:ring-indigo-500">
                                <i class="fas fa-search absolute left-2.5 top-2.5 text-gray-400 text-xs"></i>
                            </div>

                            <div
                                class="border border-gray-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto custom-scrollbar bg-gray-50 p-1">
                                @foreach ($employees as $emp)
                                    <label
                                        class="employee-item flex items-center p-2.5 hover:bg-white rounded-lg cursor-pointer transition border border-transparent hover:border-gray-200 group">
                                        <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}"
                                            class="emp-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mr-3">
                                        <div>
                                            <div class="text-sm font-bold text-gray-700 group-hover:text-indigo-700">
                                                {{ $emp->name }}</div>
                                            <div class="text-[10px] text-gray-500 flex items-center gap-1">
                                                @if (isset($isEss) && $isEss)
                                                    {{ $emp->position->name ?? '-' }}
                                                @else
                                                    <i class="fas fa-store text-[9px]"></i>
                                                    {{ $emp->outlet->name ?? ($emp->position->name ?? '-') }}
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach

                                @if ($employees->isEmpty())
                                    <p class="text-center text-xs text-gray-400 py-4">Tidak ada karyawan ditemukan.</p>
                                @endif
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 text-right">
                                Dipilih: <span id="selectedCount" class="font-bold text-indigo-600">0</span>
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Dari tanggal</label>
                                <input type="date" name="start_date"
                                    class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Sampai tanggal</label>
                                <input type="date" name="end_date"
                                    class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm"
                                    required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Shift
                                Template</label>
                            <div class="grid grid-cols-2 gap-3 max-h-40 overflow-y-auto p-1 custom-scrollbar">
                                @foreach ($shifts as $shift)
                                    <label
                                        class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition border-gray-200 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                        <input type="radio" name="shift_id" value="{{ $shift->id }}"
                                            class="mr-3 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                            required>
                                        <div>
                                            <div class="font-bold text-sm text-gray-800">{{ $shift->name }}</div>
                                            <div class="text-[10px] text-gray-500">
                                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-4 border-t border-gray-100 bg-white rounded-b-2xl">
                    <button type="submit" form="addForm"
                        class="w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition flex items-center justify-center gap-2 transform active:scale-95">
                        <i class="fas fa-check"></i> Assign
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL EDIT -->
    <div id="editModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div
            class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2"><i
                        class="fas fa-edit text-blue-600"></i> Edit Schedule</h2>
                <button id="closeModal"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-400 hover:text-gray-600 shadow-sm border border-gray-100 transition"><i
                        class="fas fa-times"></i></button>
            </div>
            <div class="p-6">
                <form id="editForm" method="post" class="space-y-5">
                    @csrf @method('put')
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Karyawan</label>
                        <select id="editEmployeeId" name="employee_id"
                            class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-blue-500 text-sm">
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal</label>
                        <input type="date" id="editDate" name="date"
                            class="w-full rounded-xl border-gray-300 shadow-sm p-3 border bg-gray-50 text-gray-500 text-sm"
                            readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Shift</label>
                        <select id="editShiftId" name="shift_id"
                            class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-blue-500 text-sm"
                            required>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}
                                    ({{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="w-full py-3.5 bg-blue-600 text-white font-bold rounded-xl shadow-md hover:bg-blue-700 transition flex justify-center items-center gap-2 transform active:scale-95">
                        <i class="fas fa-save"></i> Perbarui
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {

            new DataTable('#myTable', {});

            @if (isset($isEss) && $isEss)
                const baseUrl = "/coordinator/schedule";
            @else
                const baseUrl = "/schedule";
            @endif

            const addModal = $('#addModal');
            const editModal = $('#editModal');

            $('#addBtn, #addBtnESS').click(() => {
                addModal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            });
            $('#closeAddModal').click(() => {
                addModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            });

            $('#closeModal').click(() => {
                editModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            });

            $(window).click((e) => {
                if ($(e.target).is(addModal)) {
                    addModal.addClass('hidden').removeClass('flex');
                    $('body').removeClass('overflow-hidden');
                }
                if ($(e.target).is(editModal)) {
                    editModal.addClass('hidden').removeClass('flex');
                    $('body').removeClass('overflow-hidden');
                }
            });

            $('#selectAll').change(function() {
                const isChecked = $(this).prop('checked');
                $('.emp-checkbox:visible').prop('checked', isChecked);
                updateCount();
            });

            $('#employeeSearch').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $(".employee-item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
                $('#selectAll').prop('checked', false);
            });

            $(document).on('change', '.emp-checkbox', function() {
                updateCount();
                if (!$(this).prop('checked')) {
                    $('#selectAll').prop('checked', false);
                }
            });

            function updateCount() {
                const count = $('.emp-checkbox:checked').length;
                $('#selectedCount').text(count);
            }

            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                $('#editEmployeeId').val(btn.data('employee'));
                $('#editDate').val(btn.data('date'));
                $('#editShiftId').val(btn.data('shift'));

                let actionUrl = `${baseUrl}/${btn.data('id')}`;

                $('#editForm').attr('action', actionUrl);
                editModal.removeClass('hidden');
            });

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

        @if ($selectedBranchId)
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');

                @if (isset($isEss) && $isEss)
                    const calBaseUrl = "/coordinator/schedule";
                @else
                    const calBaseUrl = "/schedule";
                @endif

                const events = [
                    @foreach ($schedules as $s)
                        {
                            id: '{{ $s->id }}',
                            title: '{{ $s->employee->name }}',
                            start: '{{ $s->date }}',
                            backgroundColor: '{{ $s->shift->color ?? '#3B82F6' }}',
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

                        $('#editForm').attr('action', `${calBaseUrl}/${info.event.id}`);
                        $('#editModal').removeClass('hidden');
                    }
                });

                calendar.render();
            });
        @endif
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')

</body>

</html>
