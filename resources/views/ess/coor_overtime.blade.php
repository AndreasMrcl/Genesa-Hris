<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Lembur Tim</title>
    @include('ess.layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto min-h-screen flex flex-col shadow-lg border-x border-gray-100">

    @php
        $allOvertimes = $overtimes->flatten();
        $pendingCount = $allOvertimes->where('status', 'pending')->count();
        $totalCount   = $allOvertimes->count();
    @endphp
    
    <!-- HEADER -->
    <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 border-b border-gray-200">
        <div class="p-3 flex items-center justify-between">
            <a href="{{ route('ess-home') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left text-base"></i>
            </a>
            <h1 class="font-bold text-base text-gray-800">Permintaan Lembur Tim</h1>
            <div class="w-9"></div> 
        </div>
        
        <!-- Summary Stats -->
        <div class="px-4 pb-4 pt-2">
            <div class="bg-amber-500 rounded-xl p-3 shadow-lg shadow-amber-200 text-center text-white border border-amber-400">
                <div class="flex items-center justify-center gap-1 mb-0.5">
                    <i class="fas fa-bell text-[10px] text-amber-100 animate-pulse"></i>
                    <p class="text-[10px] uppercase font-bold text-amber-50 tracking-wider">Menunggu Approval</p>
                </div>
                <p class="text-2xl font-extrabold">{{ $pendingCount }}</p>
            </div>
        </div>
    </div>

    <!-- LIST CONTENT -->
    <div class="p-3 flex-grow space-y-4 pb-20">
        
        @forelse ($overtimes as $groupKey => $groupItems)
            @php
                $firstItem = $groupItems->first();
                
                $carbonDate = \Carbon\Carbon::parse($firstItem->overtime_date);
                $startTime  = \Carbon\Carbon::parse($firstItem->start_time);
                $endTime    = \Carbon\Carbon::parse($firstItem->end_time);
                $duration   = $startTime->diff($endTime)->format('%H:%I');
                
                $isToday = $carbonDate->isToday();
                $allApproved = $groupItems->every(fn($i) => $i->status === 'approved');
                $hasPending  = $groupItems->contains(fn($i) => $i->status === 'pending');
                $isLocked    = !$hasPending;

                $cardBorder = $allApproved ? 'border-emerald-200 ring-1 ring-emerald-50' : ($hasPending ? 'border-purple-200' : 'border-gray-200');
                $headerBg   = $allApproved ? 'bg-emerald-50' : 'bg-gray-50';
                $groupEmpIds = $groupItems->pluck('employee_id')->toJson();
            @endphp

            <!-- CARD GROUP -->
            <div class="bg-white rounded-2xl shadow-sm border {{ $cardBorder }} overflow-hidden">
                
                <!-- Card Header -->
                <div class="{{ $headerBg }} p-3 border-b border-gray-100 flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <!-- Date Box -->
                        <div class="flex flex-col items-center justify-center w-12 h-12 bg-white rounded-lg border border-gray-200 shadow-sm">
                            <span class="text-[8px] font-bold uppercase text-gray-400">{{ $carbonDate->translatedFormat('M') }}</span>
                            <span class="text-lg font-extrabold text-gray-800 leading-none">{{ $carbonDate->format('d') }}</span>
                        </div>
                        
                        <!-- Time Info -->
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                {{ $isToday ? 'Hari Ini' : $carbonDate->translatedFormat('l') }}
                                <span class="bg-white border border-gray-200 px-1.5 py-0.5 rounded text-[10px] font-mono font-normal text-gray-500">
                                    {{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }}
                                </span>
                            </h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 rounded">
                                    {{ $duration }} jam
                                </span>
                                <span class="text-[10px] text-gray-400">{{ $groupItems->count() }} Karyawan</span>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    @if(!$isLocked)
                        <div class="flex items-center gap-1">

                            <button class="batchEditBtn w-8 h-8 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-lg shadow-sm hover:bg-indigo-50 transition"
                                data-date="{{ $firstItem->overtime_date }}"
                                data-start="{{ $firstItem->start_time }}"
                                data-end="{{ $firstItem->end_time }}"
                                data-employees="{{ $groupEmpIds }}"
                                title="Edit Kelompok">
                                <i class="fas fa-pencil-alt text-xs"></i>
                            </button>

                            <form action="{{ route('ess-coordinator-overtime-batch-delete') }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <input type="hidden" name="date" value="{{ $firstItem->overtime_date }}">
                                <input type="hidden" name="start" value="{{ $firstItem->start_time }}">
                                <input type="hidden" name="end" value="{{ $firstItem->end_time }}">
                                <button type="button" class="batchDeleteBtn w-8 h-8 flex items-center justify-center bg-white border border-red-100 text-red-500 rounded-lg shadow-sm hover:bg-red-50 transition" title="Batalkan Pengajuan">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded">Selesai</span>
                    @endif
                </div>

                <!-- Card Body: List Employees -->
                <div class="divide-y divide-gray-50">
                    @foreach($groupItems as $item)
                        <div class="p-3 flex justify-between items-center hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <!-- Avatar -->
                                <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs border border-gray-200">
                                    {{ substr($item->employee->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-xs text-gray-700">{{ $item->employee->name }}</p>
                                    @if($item->status == 'approved')
                                        <p class="text-[10px] text-emerald-600 font-bold mt-0.5">Disetujui</p>
                                    @elseif($item->status == 'rejected')
                                        <p class="text-[10px] text-red-500 italic mt-0.5">Ditolak</p>
                                    @else
                                        <p class="text-[10px] text-gray-400 mt-0.5 italic">Menunggu</p>
                                    @endif
                                </div>
                            </div>

                            @php
                                $icon = match($item->status) {
                                    'approved' => 'fa-check-circle text-emerald-500',
                                    'rejected' => 'fa-times-circle text-red-500',
                                    'pending' => 'fa-clock text-amber-400',
                                    default => 'fa-circle text-gray-300'
                                };
                            @endphp
                            <i class="fas {{ $icon }} text-lg opacity-80"></i>
                        </div>
                    @endforeach
                </div>

            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-[50vh] text-center p-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-300">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700">Semua Beres!</h3>
                <p class="text-xs text-gray-400 mt-1">Tidak ada permintaan lembur tertunda.</p>
            </div>
        @endforelse
    </div>

    <!-- BUTTON BATCH -->
    <div class="fixed bottom-0 left-0 w-full md:left-1/2 md:w-full md:max-w-sm md:-translate-x-1/2 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] p-4 z-30">
        <button id="batchBtn" 
            class="w-full py-3.5 bg-purple-600 text-white font-bold rounded-xl shadow-lg shadow-purple-200 hover:bg-purple-700 transition flex items-center justify-center gap-2 transform active:scale-95">
            <i class="fas fa-plus-circle text-lg"></i> Buat Permintaan Lembur
        </button>
    </div>

    <!-- BATCH MODAL -->
    <div id="batchModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-lg shadow-2xl relative transform transition-all scale-100 h-[85vh] sm:h-auto flex flex-col">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clock text-purple-600"></i> Request Lembur
                </h2>
                <button type="button" id="closeBatchModal" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-grow custom-scrollbar">
                <form id="batchForm" action="{{ route('ess-coordinator-overtime-store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Filter Outlet</label>
                        <select id="outletFilter" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-purple-500 text-sm">
                            <option value="">-- Semua Outlet --</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                    </div>
       
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Karyawan</label>
                        <div class="border border-gray-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto p-1 bg-gray-50" id="employeeList">
                            @foreach ($employees as $emp)
                                <label class="flex items-center p-2.5 hover:bg-white rounded-lg cursor-pointer transition border border-transparent hover:border-gray-200 group employee-item" 
                                       data-outlet="{{ $emp->outlet_id ?? $emp->outlet->id ?? '' }}">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mr-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700">{{ $emp->name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $emp->outlet->name ?? '-' }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 italic hidden" id="emptyMsg">Tidak ada karyawan di outlet ini.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal</label>
                        <input type="date" name="overtime_date" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-purple-500 text-sm" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Jam Mulai</label>
                            <input type="time" name="start_time" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-purple-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Jam Selesai</label>
                            <input type="time" name="end_time" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-purple-500 text-sm" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3 bg-purple-600 text-white font-bold rounded-xl shadow-lg hover:bg-purple-700 transition">
                        Ajukan Permintaan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- UPDATE MODAL -->
    <div id="batchEditModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-lg shadow-2xl relative transform transition-all scale-100 h-[85vh] sm:h-auto flex flex-col">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-indigo-600"></i> Edit Kelompok
                </h2>
                <button type="button" id="closeBatchEditModal" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-grow custom-scrollbar">
                <form id="batchEditForm" action="{{ route('ess-coordinator-overtime-batch-update') }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    
                    <input type="hidden" name="original_date" id="be_orig_date">
                    <input type="hidden" name="original_start" id="be_orig_start">
                    <input type="hidden" name="original_end" id="be_orig_end">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Filter Outlet</label>
                        <select id="editOutletFilter" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- Semua Outlet --</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- EMPLOYEE CHECKBOX LIST -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Edit Karyawan</label>
                        <div class="border border-gray-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto p-1 bg-gray-50" id="editEmployeeList">
                            @foreach ($employees as $emp)
                                <label class="flex items-center p-2.5 hover:bg-white rounded-lg cursor-pointer transition border border-transparent hover:border-gray-200 group employee-item" 
                                       data-outlet="{{ $emp->outlet_id ?? $emp->outlet->id ?? '' }}">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="batch-emp-check w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mr-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700">{{ $emp->name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $emp->outlet->name ?? '-' }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1 italic">* Uncheck untuk menghapus karyawan dari kelompok ini.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal</label>
                        <input type="date" name="overtime_date" id="be_date" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Jam Mulai</label>
                            <input type="time" name="start_time" id="be_start" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Jam Selesai</label>
                            <input type="time" name="end_time" id="be_end" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const batchModal = $('#batchModal');
            const batchEditModal = $('#batchEditModal');
            
            // --- BATCH MODAL ---
            $('#batchBtn').click(function() {
                batchModal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
                $('#outletFilter').val('');
                $('#employeeList .employee-item').removeClass('hidden').find('input').prop('checked', false);
            });
            $('#closeBatchModal').click(function() {
                batchModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            });

            $('#outletFilter').change(function() {
                const selectedOutlet = $(this).val();
                let hasVisible = false;
                $('#employeeList .employee-item').each(function() {
                    const empOutlet = $(this).data('outlet');
                    const checkbox = $(this).find('input[type="checkbox"]');
                    if (selectedOutlet === "" || empOutlet == selectedOutlet) {
                        $(this).removeClass('hidden'); hasVisible = true;
                    } else {
                        $(this).addClass('hidden'); checkbox.prop('checked', false);
                    }
                });
                $('#emptyMsg').toggleClass('hidden', hasVisible);
            });

            // --- BATCH EDIT MODAL LOGIC ---
            $('.batchEditBtn').click(function() {
                const btn = $(this);
                
                $('#be_orig_date').val(btn.data('date'));
                $('#be_orig_start').val(btn.data('start'));
                $('#be_orig_end').val(btn.data('end'));

                $('#be_date').val(btn.data('date'));
                $('#be_start').val(btn.data('start'));
                $('#be_end').val(btn.data('end'));

                $('#editOutletFilter').val(''); 
                $('.batch-emp-check').prop('checked', false);
                $('#editEmployeeList .employee-item').removeClass('hidden'); 

                const currentIds = btn.data('employees'); 
                if(Array.isArray(currentIds)) {
                    currentIds.forEach(id => {
                        $(`.batch-emp-check[value="${id}"]`).prop('checked', true);
                    });
                }

                batchEditModal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            });

            $('#closeBatchEditModal').click(function() {
                batchEditModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            });

            $('#editOutletFilter').change(function() {
                const selectedOutlet = $(this).val();
                $('#editEmployeeList .employee-item').each(function() {
                    const empOutlet = $(this).data('outlet');
                    if (selectedOutlet === "" || empOutlet == selectedOutlet) {
                        $(this).removeClass('hidden');
                    } else {
                        $(this).addClass('hidden');
                    }
                });
            });

            $('.batchDeleteBtn').click(function() {
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Batalkan Pengajuan?',
                    text: "Seluruh data dalam kelompok ini akan dihapus.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Kembali'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            $(window).click(function(e) {
                if ($(e.target).is(batchEditModal)) {
                    batchEditModal.addClass('hidden').removeClass('flex');
                    $('body').removeClass('overflow-hidden');
                }
                if ($(e.target).is(batchModal)) {
                    batchModal.addClass('hidden').removeClass('flex');
                    $('body').removeClass('overflow-hidden');
                }
            });
        });
    </script>

    @include('layout.loading')
    @include('sweetalert::alert')
</body>
</html>