<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Leave Requests</title>
    @include('ess.layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto min-h-screen flex flex-col shadow-lg border-x border-gray-100">

    <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 border-b border-gray-200">
        <div class="p-3 flex items-center justify-between">
            <a href="{{ route('ess-home') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left text-base"></i>
            </a>
            <h1 class="font-bold text-base text-gray-800">Leave Requests</h1>
            <div class="w-9"></div> 
        </div>

        <div class="px-4 pb-4 pt-2">
            <div class="bg-yellow-500 rounded-2xl p-4 shadow-lg shadow-yellow-200 text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                
                <p class="text-[10px] uppercase font-bold text-yellow-100 tracking-wider mb-1">Request Summary</p>
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-3xl font-extrabold">{{ $leaves->where('status', 'approved')->count() }}</h2>
                        <span class="text-xs text-yellow-50 font-medium">Approved</span>
                    </div>
                    <div class="text-right">
                         <p class="text-xs font-bold text-white mb-1">Year {{ date('Y') }}</p>
                         <span class="bg-white/20 text-white px-2 py-0.5 rounded text-[10px] font-bold border border-white/10 backdrop-blur-sm">
                            {{ $leaves->where('status', 'pending')->count() }} Pending
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-3 flex-grow space-y-3 pb-24">
        
        @forelse ($leaves->sortByDesc('created_at') as $item)
            @php
                $startDate = \Carbon\Carbon::parse($item->start_date);
                $endDate = \Carbon\Carbon::parse($item->end_date);
                $duration = $startDate->diffInDays($endDate) + 1;
                
                $statusColor = match($item->status) {
                    'approved' => 'bg-green-100 text-green-700 border-green-200',
                    'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'rejected' => 'bg-red-100 text-red-700 border-red-200',
                    'cancelled' => 'bg-gray-100 text-gray-600 border-gray-200',
                    default => 'bg-gray-100 text-gray-600 border-gray-200'
                };
                
                $statusIcon = match($item->status) {
                    'approved' => 'fa-check-circle',
                    'pending' => 'fa-clock',
                    'rejected' => 'fa-times-circle',
                    'cancelled' => 'fa-ban',
                    default => 'fa-question-circle'
                };

                $typeLabel = str_replace('_', ' ', ucfirst($item->type));
                $typeIcon = match(strtolower($item->type)) {
                    'izin' => 'fa-user-clock',         
                    'sakit' => 'fa-procedures',       
                    'cuti' => 'fa-umbrella-beach',      
                    'meninggalkan_pekerjaan' => 'fa-running', 
                    'tukar_shift' => 'fa-exchange-alt',  t
                    default => 'fa-circle-info'          
                };
            @endphp

            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition relative overflow-hidden group">
                
                <div class="absolute left-0 top-0 bottom-0 w-1.5 
                    {{ $item->status == 'approved' ? 'bg-green-500' : ($item->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                </div>

                <div class="flex gap-3 pl-2">
                    <div class="flex flex-col items-center min-w-[3.5rem]">
                        <div class="w-14 h-14 rounded-xl flex flex-col items-center justify-center border border-gray-200 bg-gray-50">
                            <span class="text-[10px] font-bold uppercase text-gray-400">
                                {{ $startDate->format('M') }}
                            </span>
                            <span class="text-lg font-extrabold text-gray-700 leading-none">
                                {{ $startDate->format('d') }}
                            </span>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 mt-1 uppercase">{{ $startDate->format('D') }}</span>
                    </div>

                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                                    <i class="fas {{ $typeIcon }} text-gray-400 text-xs"></i>
                                    {{ $typeLabel }}
                                </h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                                    Requested: {{ $item->created_at->format('d M Y') }}
                                </p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase border {{ $statusColor }} flex items-center gap-1">
                                <i class="fas {{ $statusIcon }}"></i> {{ $item->status }}
                            </span>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-2 border border-gray-100 mt-2 flex justify-between items-center">
                            <div class="text-xs text-gray-600 font-medium">
                                {{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }}
                            </div>
                            <span class="bg-white px-2 py-0.5 rounded text-[10px] font-bold text-gray-700 border border-gray-200 shadow-sm">
                                {{ $duration }} Days
                            </span>
                        </div>

                        @if($item->note)
                            <div class="mt-2 flex items-start gap-1.5">
                                <i class="fas fa-quote-left text-[8px] text-gray-300 mt-0.5"></i>
                                <p class="text-[11px] text-gray-500 italic leading-tight">{{ $item->note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-[50vh] text-center p-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-300">
                    <i class="fas fa-plane-slash text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700">No Requests</h3>
                <p class="text-xs text-gray-400 mt-1">You haven't submitted any leave requests yet.</p>
            </div>
        @endforelse

    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg md:max-w-sm mx-auto p-4 z-30">
        <button id="addBtn" class="w-full py-3 bg-yellow-500 text-white font-bold rounded-xl shadow-md hover:bg-yellow-600 transition flex items-center justify-center gap-2 transform active:scale-95">
            <i class="fas fa-plus-circle"></i> Request Leave
        </button>
    </div>

    <div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-lg shadow-2xl relative transform transition-all scale-100 h-[85vh] sm:h-auto flex flex-col">

            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <i class="fas fa-plane-departure text-sm"></i>
                    </div>
                    Request Leave
                </h2>
                <button id="closeAddModal" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-400 hover:text-gray-600 shadow-sm transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-grow">
                <form id="addForm" method="post" action="{{ route('req-leave') }}" class="space-y-5">
                    @csrf @method('post')

                    <input type="hidden" name="employee_id" value="{{ Auth::guard('employee')->id() }}">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Start Date</label>
                            <input type="date" name="start_date" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-yellow-500 transition text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">End Date</label>
                            <input type="date" name="end_date" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-yellow-500 transition text-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Request Type</label>
                        <select name="type" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-yellow-500 bg-white text-sm" required>
                            <option value="">-- Select Type --</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="cuti">Cuti</option>
                            <option value="meninggalkan_pekerjaan">Meninggalkan Pekerjaan</option>
                            <option value="tukar_shift">Tukar Shift</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <input type="hidden" name="status" value="pending">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Note</label>
                        <textarea name="note" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-yellow-500 text-sm" placeholder="Please describe your reason / note..." required></textarea>
                    </div>
                </form>
            </div>

            <div class="p-4 border-t border-gray-100 bg-white rounded-b-2xl">
                <button type="submit" form="addForm" class="w-full py-3.5 bg-yellow-500 text-white font-bold rounded-xl shadow-lg hover:bg-yellow-600 transition flex items-center justify-center gap-2 transform active:scale-95">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </div>
        </div>
    </div>

    @include('layout.loading')

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const addModal = $('#addModal');
            
            $('#addBtn').click(function() {
                addModal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            });

            function closeModal() {
                addModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            }

            $('#closeAddModal').click(closeModal);

            $(window).click(function(e) {
                if ($(e.target).is(addModal)) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>