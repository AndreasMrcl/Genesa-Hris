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
        // Ratakan dulu collectionnya untuk menghitung statistik global
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
                    <p class="text-[10px] uppercase font-bold text-amber-50 tracking-wider">Jumlah Pending</p>
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
                
                // Cek Status Group
                $allApproved = $groupItems->every(fn($i) => $i->status === 'approved');
                $hasPending  = $groupItems->contains(fn($i) => $i->status === 'pending');
                
                // Visualisasi Border Kartu
                $cardBorder = $allApproved ? 'border-emerald-200 ring-1 ring-emerald-50' : ($hasPending ? 'border-purple-200' : 'border-gray-200');
                $headerBg   = $allApproved ? 'bg-emerald-50' : 'bg-gray-50';
            @endphp

            <!-- CARD GROUP -->
            <div class="bg-white rounded-2xl shadow-sm border {{ $cardBorder }} overflow-hidden">
                
                <!-- Card Header: TANGGAL & WAKTU -->
                <div class="{{ $headerBg }} p-3 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <!-- Date Box -->
                        <div class="flex flex-col items-center justify-center w-12 h-12 bg-white rounded-lg border border-gray-200 shadow-sm">
                            <span class="text-[8px] font-bold uppercase text-gray-400">{{ $carbonDate->format('M') }}</span>
                            <span class="text-lg font-extrabold text-gray-800 leading-none">{{ $carbonDate->format('d') }}</span>
                        </div>
                        
                        <!-- Time Info -->
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                {{ $isToday ? 'Hari Ini' : $carbonDate->format('l') }}
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

                    <!-- Print Button (Jika Approved Semua) -->
                    @if($allApproved)
                        <a href="{{ route('ess-coordinator-overtime-print', [
                                'date' => $firstItem->overtime_date, 
                                'start_time' => $firstItem->start_time, 
                                'end_time' => $firstItem->end_time
                           ]) }}" 
                           target="_blank" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 transition text-[10px] font-bold">
                            <i class="fas fa-print"></i> PDF
                        </a>
                    @elseif($hasPending)
                         <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded text-[10px] font-bold border border-amber-200">Review</span>
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
                                    @if($item->overtime_pay > 0)
                                        <p class="text-[10px] text-emerald-600 font-bold mt-0.5">
                                            Rp {{ number_format($item->overtime_pay, 0, ',', '.') }}
                                        </p>
                                    @else
                                        <p class="text-[10px] text-gray-400 mt-0.5 italic">Pending</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Action -->
                            @if($item->status == 'pending')
                                <button class="editBtn w-8 h-8 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-lg shadow-sm hover:bg-indigo-50 transition"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->employee->name }}"
                                    data-status="{{ $item->status }}"
                                    data-pay="{{ $item->overtime_pay }}">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                            @else
                                @php
                                    $icon = $item->status == 'approved' ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-red-500';
                                @endphp
                                <i class="fas {{ $icon }} text-lg"></i>
                            @endif
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
            <i class="fas fa-layer-group text-lg"></i> Input Kelompok
        </button>
    </div>

    <!-- BATCH MODAL -->
    <div id="batchModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-lg shadow-2xl relative transform transition-all scale-100 h-[85vh] sm:h-auto flex flex-col">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clock text-purple-600"></i> Kelompok Lembur
                </h2>
                <button type="button" id="closeBatchModal" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-grow custom-scrollbar">
                <form id="batchForm" action="{{ route('ess-coordinator-overtime-store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- EMPLOYEE CHECKBOX LIST -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Karyawan</label>
                        <div class="border border-gray-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto p-1 bg-gray-50">
                            @foreach ($employees as $emp)
                                <label class="flex items-center p-2.5 hover:bg-white rounded-lg cursor-pointer transition border border-transparent hover:border-gray-200 group">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mr-3">
                                    <span class="text-sm font-bold text-gray-700">{{ $emp->name }}</span>
                                </label>
                            @endforeach
                        </div>
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

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Est. Upah Lembur (Rp)</label>
                        <input type="text" name="overtime_pay" class="currency w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-purple-500 text-sm" placeholder="0">
                        <p class="text-[10px] text-gray-400 mt-1">* Opsional. Nominal ini akan diterapkan ke semua karyawan.</p>
                    </div>

                    <button type="submit" class="w-full py-3 bg-purple-600 text-white font-bold rounded-xl shadow-lg hover:bg-purple-700 transition">
                        Simpan & Setujui Otomatis
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- UPDATE STATUS MODAL -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-sm shadow-2xl relative transform transition-all scale-100">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Perbarui Lembur</h2>
                <button type="button" id="closeEditModal" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6">
                <!-- FORM -->
                <form id="editForm" method="post" action="">
                    @csrf @method('put')
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Karyawan</label>
                        <input type="text" id="empName" class="w-full rounded-xl bg-gray-50 border-gray-200 text-gray-500 text-sm font-bold" disabled>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Atur Status</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="approved" class="peer sr-only" required>
                                <div class="peer-checked:bg-emerald-100 peer-checked:text-emerald-700 peer-checked:border-emerald-300 border border-gray-200 rounded-xl p-3 text-center transition hover:bg-gray-50">
                                    <i class="fas fa-check-circle block text-xl mb-1"></i>
                                    <span class="text-xs font-bold">Setuju</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="rejected" class="peer sr-only" required>
                                <div class="peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-300 border border-gray-200 rounded-xl p-3 text-center transition hover:bg-gray-50">
                                    <i class="fas fa-times-circle block text-xl mb-1"></i>
                                    <span class="text-xs font-bold">Tolak</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Overtime Pay (Rp)</label>
                        <input type="text" id="editPay" name="overtime_pay" class="currency w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-purple-500 text-sm font-bold text-gray-700" placeholder="0">
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition">
                        Perbarui Status
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // FORMAT CURRENCY
            function formatCurrency(value) {
                let rawValue = String(value).replace(/\D/g, '');
                if (rawValue === '') return '';
                let numberValue = parseInt(rawValue, 10);
                return numberValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('.currency').on('input', function() {
                $(this).val(formatCurrency($(this).val()));
            });

            $('form').on('submit', function() {
                $(this).find('.currency').each(function() {
                    let cleanVal = $(this).val().replace(/\./g, '');
                    $(this).val(cleanVal);
                });
            });

            const batchModal = $('#batchModal');
            const editModal = $('#editModal');
            
            // --- BATCH MODAL ---
            $('#batchBtn').click(function() {
                batchModal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            });
            $('#closeBatchModal').click(function() {
                batchModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            });

            // --- EDIT MODAL ---
            $('.editBtn').click(function() {
                const btn = $(this);
                const id = btn.attr('data-id');
                
                $('#empName').val(btn.attr('data-name'));
                
                let rawPay = btn.attr('data-pay');
                if (rawPay) {
                    let payStr = String(rawPay).split('.')[0];
                    $('#editPay').val(formatCurrency(payStr));
                } else {
                    $('#editPay').val('');
                }

                $('input[name="status"]').prop('checked', false);
                let currentStatus = btn.attr('data-status');

                if(currentStatus !== 'pending') {
                    $(`input[name="status"][value="${currentStatus}"]`).prop('checked', true);
                }
                
                $('#editForm').attr('action', `/coordinator/overtime/${id}`);
                
                editModal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            });

            $('#closeEditModal').click(function() {
                editModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            });

            $(window).click(function(e) {
                if ($(e.target).is(editModal)) {
                    editModal.addClass('hidden').removeClass('flex');
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