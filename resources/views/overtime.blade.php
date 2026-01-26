<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manajemen Lembur</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <style>
        .dataTables_wrapper .dataTables_length select { padding-right: 2rem; border-radius: 0.5rem; }
        .dataTables_wrapper .dataTables_filter input { padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #d1d5db; }
        table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb; }
        .employee-list-item:hover { background-color: #f9fafb; }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header -->
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-business-time text-purple-600"></i> Manajemen Lembur
                    </h1>
                    <p class="text-sm text-gray-500">Data lembur dikelompokkan berdasarkan jadwal.</p>
                </div>
                <button id="addBtn" class="px-6 py-3 bg-purple-600 text-white rounded-lg shadow-md hover:bg-purple-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Lembur
                </button>
            </div>

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold" width="5%">No</th>
                                <th class="p-4 font-bold" width="20%">
                                    <div class="flex items-center justify-center">
                                        Jadwal
                                    </div>
                                </th>
                                <th class="p-4 font-bold">
                                    <div class="flex items-center justify-center">
                                        Daftar Karyawan
                                    </div>
                                </th>
                                <th class="p-4 font-bold" width="15%">
                                    <div class="flex items-center justify-center">
                                        Aksi
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($overtimes as $key => $group)
                                @php 
                                    $header = $group->first(); 
                                    $totalNominal = $group->sum('overtime_pay');
                                    $groupEmpIds = $group->pluck('employee_id')->toJson();
                                    $groupBranchId = $header->employee->branch_id ?? '';
                                    $groupOutletId = $header->employee->outlet_id ?? ($header->employee->outlet->id ?? '');
                                @endphp
                                <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                                    <td class="p-4 font-medium">
                                        <div class="flex items-center justify-center">
                                            {{ $no++ }}
                                        </div>
                                    </td>
                                    
                                    <!-- Jadwal -->
                                    <td class="p-4 ">
                                        <div class="font-bold text-gray-800 text-base">
                                            {{ \Carbon\Carbon::parse($header->overtime_date)->format('d M Y') }}
                                        </div>
                                        <div class="mt-1 flex items-center gap-2 text-xs text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded w-fit">
                                            <i class="far fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($header->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($header->end_time)->format('H:i') }}
                                        </div>
                                        <div class="mt-2 text-xs font-semibold text-gray-600">
                                            Total: Rp {{ number_format($totalNominal, 0, ',', '.') }}
                                        </div>
                                        @if($header->employee->branch)
                                            <div class="mt-1 text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded w-fit font-bold uppercase">
                                                {{ $header->employee->branch->name }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Daftar Karyawan -->
                                    <td class="p-4 ">
                                        <div class="flex flex-col gap-2">
                                            @foreach($group as $item)
                                                <div class="employee-list-item flex justify-between items-center p-2 rounded border border-gray-100 bg-white shadow-sm gap-4">
                                                    <div class="min-w-[150px]">
                                                        <div class="font-bold text-gray-800">{{ $item->employee->name }}</div>
                                                        <div class="text-[10px] text-gray-500">{{ $item->employee->position->name ?? '-' }}</div>
                                                    </div>
                                                    
                                                    <div class="flex-grow flex items-center justify-end gap-3">
                                                        @if($item->status == 'pending')
                                                            <span class="bg-yellow-100 text-yellow-700 border-yellow-200 px-2 py-0.5 rounded text-[10px] font-bold border uppercase">{{ $item->status }}</span>
                                                            <form action="{{ url('/overtime/' . $item->id . '/update') }}" method="POST" class="quick-action-form flex items-center gap-2">
                                                                @csrf @method('PUT')
                                                                <input type="hidden" name="status" class="status-input"> 
                                                                <div class="relative">
                                                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                                                    <input type="text" name="overtime_pay" class="currency w-28 pl-6 pr-2 py-1 text-xs border border-gray-300 rounded" placeholder="0" required>
                                                                </div>
                                                                <button type="button" class="btn-approve bg-green-100 text-green-600 w-7 h-7 rounded hover:bg-green-200" title="Setujui"><i class="fas fa-check text-xs"></i></button>
                                                                <button type="button" class="btn-reject bg-red-100 text-red-600 w-7 h-7 rounded hover:bg-red-200" title="Tolak"><i class="fas fa-times text-xs"></i></button>
                                                            </form>
                                                        @else
                                                            @php
                                                                $statusColor = match($item->status) {
                                                                    'approved' => 'bg-green-100 text-green-700 border-green-200',
                                                                    'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                                                    default => 'bg-gray-100 text-gray-600'
                                                                };
                                                            @endphp
                                                            <span class="{{ $statusColor }} px-2 py-0.5 rounded text-[10px] font-bold border uppercase">{{ $item->status }}</span>
                                                            <span class="text-xs text-gray-600 font-mono">Rp {{ number_format($item->overtime_pay, 0, ',', '.') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Aksi Group -->
                                    <td class="p-4 text-center">
                                        <div class="flex h-12 gap-2">
                                            
                                            <!-- Export PDF -->
                                            <a href="{{ route('printovertimereport', [
                                                    'date' => $header->overtime_date,
                                                    'start_time' => $header->start_time,
                                                    'end_time' => $header->end_time,
                                                    'branch_id' => $header->employee->branch_id ?? null
                                                ]) }}" 
                                               target="_blank"
                                               class="w-full py-1.5 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition text-xs font-bold flex items-center justify-center gap-1">
                                                <i class="fas fa-print"></i> PDF
                                            </a>

                                            <!-- Edit -->
                                            <button class="batchEditBtn w-full py-1.5 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200 transition text-xs font-bold flex items-center justify-center gap-1"
                                                data-date="{{ $header->overtime_date }}"
                                                data-start="{{ $header->start_time }}"
                                                data-end="{{ $header->end_time }}"
                                                data-employees="{{ $groupEmpIds }}"
                                                data-branch="{{ $groupBranchId }}"
                                                data-outlet="{{ $groupOutletId }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <!-- Delete -->
                                            <form action="{{ route('delovertimebatch') }}" method="POST" class="w-full">
                                                @csrf @method('DELETE')
                                                <input type="hidden" name="date" value="{{ $header->overtime_date }}">
                                                <input type="hidden" name="start" value="{{ $header->start_time }}">
                                                <input type="hidden" name="end" value="{{ $header->end_time }}">
                                                <button type="button" class="batchDeleteBtn w-full py-1.5 h-12 bg-red-100 text-red-700 rounded hover:bg-red-200 transition text-xs font-bold flex items-center justify-center gap-1">
                                                    <i class="fas fa-trash"></i> Hapus
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

    <!-- ADD MODAL (BATCH) -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2"><i class="fas fa-plus-circle text-purple-600"></i> Tambah Lembur</h2>
            <form id="addForm" method="post" action="{{ route('postovertime') }}" class="space-y-5">
                @csrf @method('post')

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-3 border-b border-gray-200 pb-1">Filter Karyawan</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Cabang</label>
                            <select id="addBranchFilter" class="w-full rounded-lg border-gray-300 shadow-sm p-2 text-sm focus:ring-purple-500">
                                <option value="">-- Semua Cabang --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Outlet</label>
                            <select id="addOutletFilter" class="w-full rounded-lg border-gray-300 shadow-sm p-2 text-sm focus:ring-purple-500">
                                <option value="">-- Pilih Outlet --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Karyawan</label>
                    <div id="addEmployeeList" class="border border-gray-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto p-1 bg-gray-50">
                        @foreach ($employee as $emp)
                            <label class="flex items-center p-2.5 hover:bg-white rounded-lg cursor-pointer transition border border-transparent hover:border-gray-200 group employee-checkbox-item"
                                   data-branch="{{ $emp->branch_id }}"
                                   data-outlet="{{ $emp->outlet_id ?? $emp->outlet->id ?? '' }}">
                                <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mr-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">{{ $emp->name }}</span>
                                    <span class="text-[10px] text-gray-400 flex gap-1">
                                        <span>{{ $emp->branch->name ?? '-' }}</span>
                                        @if($emp->outlet) <span class="text-gray-300">•</span> <span>{{ $emp->outlet->name ?? '-' }}</span> @endif
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-1 italic">* Menampilkan karyawan sesuai filter di atas.</p>
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nominal (Rp)</label>
                        <input type="text" name="overtime_pay" class="currency w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-purple-500" placeholder="0">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-purple-600 text-white font-bold rounded-lg shadow-md hover:bg-purple-700 transition">Simpan</button>
            </form>
        </div>
    </div>

    <!-- BATCH EDIT MODAL -->
    <div id="batchEditModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeBatchEditModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2"><i class="fas fa-edit text-blue-600"></i> Edit Kelompok Lembur</h2>
            
            <form id="batchEditForm" method="post" action="{{ route('updateovertimebatch') }}" class="space-y-5">
                @csrf @method('PUT')
                
                <input type="hidden" name="original_date" id="be_orig_date">
                <input type="hidden" name="original_start" id="be_orig_start">
                <input type="hidden" name="original_end" id="be_orig_end">

                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-4">
                    <label class="block text-xs font-bold text-blue-500 uppercase mb-3 border-b border-blue-200 pb-1">Filter Karyawan</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Cabang</label>
                            <select id="editBranchFilter" class="w-full rounded-lg border-gray-300 shadow-sm p-2 text-sm focus:ring-blue-500">
                                <option value="">-- Semua Cabang --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Outlet</label>
                            <select id="editOutletFilter" class="w-full rounded-lg border-gray-300 shadow-sm p-2 text-sm focus:ring-blue-500">
                                <option value="">-- Pilih Outlet --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Edit Daftar Karyawan</label>
                    <div id="editEmployeeList" class="border border-gray-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto p-1 bg-gray-50">
                        @foreach ($employee as $emp)
                            <label class="flex items-center p-2.5 hover:bg-white rounded-lg cursor-pointer transition border border-transparent hover:border-gray-200 group employee-checkbox-item"
                                   data-branch="{{ $emp->branch_id }}"
                                   data-outlet="{{ $emp->outlet_id ?? $emp->outlet->id ?? '' }}">
                                <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="batch-emp-check w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mr-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">{{ $emp->name }}</span>
                                    <span class="text-[10px] text-gray-400 flex gap-1">
                                        <span>{{ $emp->branch->name ?? '-' }}</span>
                                        @if($emp->outlet) <span class="text-gray-300">•</span> <span>{{ $emp->outlet->name ?? '-' }}</span> @endif
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1 italic">* Uncheck untuk menghapus karyawan dari grup ini.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="overtime_date" id="be_date" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai</label>
                        <input type="time" name="start_time" id="be_start" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Berakhir</label>
                        <input type="time" name="end_time" id="be_end" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {});

            function formatCurrency(v) { return v.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
            $(document).on('input', '.currency', function() { $(this).val(formatCurrency($(this).val())); });

            const allOutlets = @json($outlets);

            function updateOutletDropdown(branchId, targetSelectId, selectedOutletId = null) {
                const targetSelect = $(targetSelectId);
                targetSelect.empty().append('<option value="">-- Semua Outlet --</option>');
                
                if(branchId) {
                    const filtered = allOutlets.filter(o => o.branch_id == branchId);
                    
                    filtered.forEach(o => {
                        const isSelected = (selectedOutletId && selectedOutletId == o.id) ? 'selected' : '';
                        targetSelect.append(`<option value="${o.id}" ${isSelected}>${o.name}</option>`);
                    });
                }
            }

            function filterEmployees(containerId) {
                var prefix = (containerId === '#addEmployeeList') ? '#add' : '#edit';
                var branchId = $(prefix + 'BranchFilter').val();
                var outletId = $(prefix + 'OutletFilter').val();

                var items = $(containerId).find('.employee-checkbox-item');
                
                items.each(function() {
                    const item = $(this);
                    const empBranch = item.data('branch');
                    const empOutlet = item.data('outlet');
                    const checkbox = item.find('input[type="checkbox"]');

                    var matchBranch = (branchId === "" || empBranch == branchId);
                    var matchOutlet = (outletId === "" || empOutlet == outletId);

                    if (matchBranch && matchOutlet) {
                        item.removeClass('hidden');
                    } else {
                        item.addClass('hidden');
                        if(containerId === '#addEmployeeList') {
                            checkbox.prop('checked', false);
                        }
                    }
                });
            }

            $('#addBranchFilter').change(function() {
                let bId = $(this).val();
                updateOutletDropdown(bId, '#addOutletFilter');
                filterEmployees('#addEmployeeList');
            });
            $('#addOutletFilter').change(function() { filterEmployees('#addEmployeeList'); });

            $('#editBranchFilter').change(function() {
                let bId = $(this).val();
                updateOutletDropdown(bId, '#editOutletFilter');
                filterEmployees('#editEmployeeList');
            });
            $('#editOutletFilter').change(function() { filterEmployees('#editEmployeeList'); });

            $(document).on('click', '.btn-approve, .btn-reject', function() {
                const isApprove = $(this).hasClass('btn-approve');
                const form = $(this).closest('form');
                const nom = form.find('.currency').val();

                if(isApprove && (nom == '' || nom == '0')) return Swal.fire('Error', 'Isi nominal lembur!', 'error');

                Swal.fire({
                    title: isApprove ? 'Setujui?' : 'Tolak?',
                    text: isApprove ? `Nominal: Rp ${nom}` : 'Nominal akan dianggap 0.',
                    icon: isApprove ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isApprove ? '#10B981' : '#EF4444',
                    confirmButtonText: 'Ya'
                }).then((res) => {
                    if (res.isConfirmed) {
                        form.find('.status-input').val(isApprove ? 'approved' : 'rejected');
                        form.find('.currency').val(nom.replace(/\./g, ''));
                        form.submit();
                    }
                });
            });

            $(document).on('click', '.batchDeleteBtn', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus Kelompok?',
                    text: "Seluruh data karyawan pada jadwal ini akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus Semua!'
                }).then((res) => { if(res.isConfirmed) form.submit(); });
            });

            const beModal = $('#batchEditModal');
            $(document).on('click', '.batchEditBtn', function() {
                const btn = $(this);
                const groupBranchId = btn.data('branch'); 
                const groupOutletId = btn.data('outlet');

                $('#be_orig_date').val(btn.data('date')); $('#be_orig_start').val(btn.data('start')); $('#be_orig_end').val(btn.data('end'));
                $('#be_date').val(btn.data('date')); $('#be_start').val(btn.data('start')); $('#be_end').val(btn.data('end'));

                $('#editBranchFilter').val(groupBranchId);
                updateOutletDropdown(groupBranchId, '#editOutletFilter', groupOutletId);
                
                filterEmployees('#editEmployeeList'); 

                $('.batch-emp-check').prop('checked', false);
                const currentIds = btn.data('employees'); 
                if(Array.isArray(currentIds)) { currentIds.forEach(id => { $(`.batch-emp-check[value="${id}"]`).prop('checked', true); }); }
                beModal.removeClass('hidden');
            });
            $('#closeBatchEditModal').click(() => beModal.addClass('hidden'));
            
            const addModal = $('#addModal');
            $('#addBtn').click(() => { 
                addModal.removeClass('hidden'); 
                $('#addBranchFilter').val(''); 
                updateOutletDropdown('', '#addOutletFilter');
                $('#addOutletFilter').val(''); 
                filterEmployees('#addEmployeeList');
            });
            $('#closeAddModal').click(() => addModal.addClass('hidden'));

            $(window).click((e) => {
                    if (e.target === addModal[0]) addModal.addClass('hidden');
                    if (e.target === beModal[0]) beModal.addClass('hidden');
                });

            // Clean currency on Submit
            $('form').on('submit', function() {
                $(this).find('.currency').each(function() { $(this).val($(this).val().replace(/\./g, '')); });
            });
        });
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')
</body>
</html>