<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Team Leaves</title>
    @include('ess.layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto min-h-screen flex flex-col shadow-lg border-x border-gray-100">

    <!-- HEADER -->
    <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 border-b border-gray-200">
        <div class="p-3 flex items-center justify-between">
            <a href="{{ route('ess-home') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left text-base"></i>
            </a>
            <h1 class="font-bold text-base text-gray-800">Team Leave Requests</h1>
            <div class="w-9"></div> 
        </div>
        
        <!-- Summary Stats -->
        <div class="px-4 pb-4 pt-2">
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-amber-50 rounded-xl p-3 border border-amber-100 text-center">
                    <p class="text-[10px] uppercase font-bold text-amber-400 tracking-wider">Pending</p>
                    <p class="text-xl font-extrabold text-amber-600">{{ $leaves->where('status', 'pending')->count() }}</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-center">
                    <p class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">Approved (This Year)</p>
                    <p class="text-xl font-extrabold text-emerald-600">{{ $leaves->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- LIST CONTENT -->
    <div class="p-3 flex-grow space-y-3 pb-20">
        @forelse ($leaves as $item)
            @php
                $startDate = \Carbon\Carbon::parse($item->start_date);
                $duration = $startDate->diffInDays(\Carbon\Carbon::parse($item->end_date)) + 1;
                
                $statusColor = match($item->status) {
                    'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'rejected' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-gray-100 text-gray-600'
                };
            @endphp

            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition group relative overflow-hidden">
                <!-- Status Strip -->
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $item->status == 'pending' ? 'bg-amber-500' : ($item->status == 'approved' ? 'bg-emerald-500' : 'bg-red-500') }}"></div>
                
                <div class="pl-2">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <!-- Avatar -->
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-sm">
                                {{ substr($item->employee->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm">{{ $item->employee->name }}</h3>
                                <p class="text-[10px] text-gray-500">{{ $item->employee->position->name ?? 'Staff' }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $statusColor }}">
                            {{ $item->status }}
                        </span>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 mt-3">
                        <div class="flex justify-between items-center text-xs mb-2">
                            <div class="text-gray-500 font-medium">Type</div>
                            <div class="font-bold text-gray-700 uppercase">{{ str_replace('_', ' ', $item->type) }}</div>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <div class="text-gray-500 font-medium">Date</div>
                            <div class="font-bold text-gray-700">
                                {{ $startDate->format('d M') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                                <span class="ml-1 text-gray-400 font-normal">({{ $duration }} Days)</span>
                            </div>
                        </div>
                        @if($item->note)
                            <div class="mt-2 pt-2 border-t border-gray-200 text-[11px] text-gray-500 italic">
                                "{{ $item->note }}"
                            </div>
                        @endif
                    </div>

                    <div class="mt-3 flex justify-end">
                        @if($item->status == 'pending')
                            <button class="editBtn px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg shadow-sm hover:bg-indigo-700 transition flex items-center gap-2"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->employee->name }}"
                                data-status="{{ $item->status }}">
                                <i class="fas fa-edit"></i> Update Status
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-[50vh] text-center p-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-300">
                    <i class="fas fa-check-double text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700">All Clear!</h3>
                <p class="text-xs text-gray-400 mt-1">No pending leave requests from your team.</p>
            </div>
        @endforelse
    </div>

    <!-- BUTTON INPUT -->
    <div class="fixed bottom-0 left-0 w-full md:left-1/2 md:w-full md:max-w-sm md:-translate-x-1/2 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] p-4 z-30">
        <button id="addBtn" 
            class="w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-300 hover:bg-indigo-700 transition flex items-center justify-center gap-2 transform active:scale-95">
            <i class="fas fa-user-edit text-lg"></i> Input Leave
        </button>
    </div>

    <div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-lg shadow-2xl relative transform transition-all scale-100 sm:h-auto flex flex-col">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-edit text-indigo-600"></i> Input Leave
                </h2>
                <button id="closeAddModal" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-grow">
                <form action="{{ route('ess-coordinator-leave-store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- SELECT EMPLOYEE -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Select Employee</label>
                        <select name="employee_id" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm" required>
                            <option value="">-- Choose Employee --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Start Date</label>
                            <input type="date" name="start_date" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">End Date</label>
                            <input type="date" name="end_date" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Type</label>
                        <select name="type" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 bg-white text-sm" required>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="cuti">Cuti</option>
                            <option value="meninggalkan_pekerjaan">Meninggalkan Pekerjaan</option>
                            <option value="tukar_shift">Tukar Shift</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Note</label>
                        <textarea name="note" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Reason..." required></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition">
                        Submit & Auto-Approve
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- UPDATE STATUS MODAL -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-sm shadow-2xl relative transform transition-all scale-100">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Update Request</h2>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6">
                <form id="editForm" method="post" action="">
                    @csrf @method('put')
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Employee</label>
                        <input type="text" id="empName" class="w-full rounded-xl bg-gray-50 border-gray-200 text-gray-500 text-sm font-bold" disabled>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Set Status</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="approved" class="peer sr-only">
                                <div class="peer-checked:bg-emerald-100 peer-checked:text-emerald-700 peer-checked:border-emerald-300 border border-gray-200 rounded-xl p-3 text-center transition hover:bg-gray-50">
                                    <i class="fas fa-check-circle block text-xl mb-1"></i>
                                    <span class="text-xs font-bold">Approve</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="rejected" class="peer sr-only">
                                <div class="peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-300 border border-gray-200 rounded-xl p-3 text-center transition hover:bg-gray-50">
                                    <i class="fas fa-times-circle block text-xl mb-1"></i>
                                    <span class="text-xs font-bold">Reject</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const addModal = $('#addModal');
            const editModal = $('#editModal');
            
            $('#addBtn').click(function() {
                addModal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            });
            
            $('#closeAddModal').click(function() {
                addModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            });
            
            $('.editBtn').click(function() {
                const btn = $(this);
                const id = btn.data('id');
                
                $('#empName').val(btn.data('name'));
                $('input[name="status"]').prop('checked', false);
                $(`input[name="status"][value="${btn.data('status')}"]`).prop('checked', true);
                
                $('#editForm').attr('action', `/coordinator/leave/${id}`);
                
                editModal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            });

            $('#closeModal').click(function() {
                editModal.addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            });

            $(window).click(function(e) {
                if ($(e.target).is(editModal)) {
                    editModal.addClass('hidden').removeClass('flex');
                    $('body').removeClass('overflow-hidden');
                }
                if ($(e.target).is(addModal)) {
                    addModal.addClass('hidden').removeClass('flex');
                    $('body').removeClass('overflow-hidden');
                }
            });
        });
    </script>

    @include('layout.loading')
    @include('sweetalert::alert')

</body>
</html>