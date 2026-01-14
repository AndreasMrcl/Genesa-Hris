<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manajemen Lembur</title>
    @include('layout.head')
    <!-- DataTables CSS -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Override DataTables Style */
        .dataTables_wrapper .dataTables_length select { padding-right: 2rem; border-radius: 0.5rem; }
        .dataTables_wrapper .dataTables_filter input { padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #d1d5db; }
        table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb; }
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
                        <i class="fas fa-business-time text-purple-600"></i> Manajemen Lembur
                    </h1>
                    <p class="text-sm text-gray-500">Manajemen data lembur karyawan</p>
                </div>
                <button id="addBtn" class="px-6 py-3 bg-purple-600 text-white rounded-lg shadow-md hover:bg-purple-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Lembur
                </button>
            </div>

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Tanggal</th>
                                <th class="p-4 font-bold">Karyawan</th>
                                <th class="p-4 font-bold text-center">Mulai</th>
                                <th class="p-4 font-bold text-center">Berakhir</th>
                                <th class="p-4 font-bold text-center">Status</th>
                                <th class="p-4 font-bold text-right">Nominal</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Aksi</th>
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
                                        <div class="text-xs text-gray-500">{{ $item->employee->position->name ?? '' }}</div>
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
                                            $statusColor = match($item->status) {
                                                'approved' => 'bg-green-100 text-green-700 border-green-200',
                                                'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                                'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                                default => 'bg-gray-100 text-gray-600'
                                            };
                                        @endphp
                                        <span class="{{ $statusColor }} px-3 py-1 rounded-full text-xs font-bold border uppercase shadow-sm">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-bold text-gray-700">
                                        Rp {{ number_format($item->overtime_pay, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Edit Button --}}
                                            <button class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}"
                                                data-employee="{{ $item->employee_id }}"
                                                data-overtime_date="{{ $item->overtime_date }}"
                                                data-start_time="{{ $item->start_time }}"
                                                data-end_time="{{ $item->end_time }}" 
                                                data-status="{{ $item->status }}"
                                                data-overtime_pay="{{ $item->overtime_pay }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Delete Button --}}
                                            <form method="post" action="{{ route('delovertime', ['id' => $item->id]) }}" class="inline deleteForm">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition" title="Delete">
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
    <div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-business-time text-purple-600"></i> Tambah Lembur
            </h2>

            <form id="addForm" method="post" action="{{ route('postovertime') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('post')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Karyawan</label>
                    <select name="employee_id" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500" required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employee as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="overtime_date" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500" required>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai</label>
                        <input type="time" name="start_time" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Berakhir</label>
                        <input type="time" name="end_time" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nominal Lembur (Rp)</label>
                        <input type="text" name="overtime_pay" class="currency w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500" placeholder="0" required>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-purple-600 text-white font-bold rounded-lg shadow-md hover:bg-purple-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Simpan
                </button>
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
                <i class="fas fa-edit text-blue-600"></i> Edit Lembur
            </h2>

            <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('put')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Karyawan</label>
                    <select id="editEmployee" name="employee_id" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employee as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" id="editOvertime" name="overtime_date" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai</label>
                        <input type="time" id="editStart" name="start_time" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Berakhir</label>
                        <input type="time" id="editEnd" name="end_time" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                        <select id="editStatus" name="status" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nominal Lembur (Rp)</label>
                        <!-- FIX: type="text" class="currency" -->
                        <input type="text" id="editPay" name="overtime_pay" class="currency w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> Perbarui
                </button>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Init DataTable
            new DataTable('#myTable', {
            });

            // === 1. FORMATTER CURRENCY ===
            function formatCurrency(value) {
                let rawValue = value.replace(/\D/g, '');
                if (rawValue === '') return '';
                let numberValue = parseInt(rawValue, 10);
                return numberValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // === 2. EVENT INPUT ===
            $('.currency').on('input', function() {
                let val = $(this).val();
                $(this).val(formatCurrency(val));
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
                $('#editOvertime').val(btn.data('overtime_date'));
                $('#editStart').val(btn.data('start_time'));
                $('#editEnd').val(btn.data('end_time'));
                $('#editStatus').val(btn.data('status'));
                
                // Format Pay
                let rawPay = btn.data('overtime_pay');
                let payStr = String(rawPay).split('.')[0];
                $('#editPay').val(formatCurrency(payStr));

                $('#editForm').attr('action', `/overtime/${btn.data('id')}/update`);
                editModal.removeClass('hidden');
            });

            $('#closeModal').click(() => editModal.addClass('hidden'));
            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
                if (e.target === editModal[0]) editModal.addClass('hidden');
            });

            // === 3. CLEAN INPUT BEFORE SUBMIT ===
            $('form').on('submit', function() {
                $('.currency').each(function() {
                    let cleanVal = $(this).val().replace(/\./g, '');
                    $(this).val(cleanVal);
                });
            });

            // Delete confirmation
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Overtime?',
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