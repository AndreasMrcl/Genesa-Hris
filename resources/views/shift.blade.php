<!DOCTYPE html>
<html lang="en">
<head>
    <title>Master Shifts</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
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
            
            <!-- Header -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-600"></i> Master Shifts
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola template shift (contoh: Pagi, Malam)</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('schedule') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-bold flex items-center gap-2">
                        <i class="far fa-calendar-alt"></i> Ke Kalender
                    </a>
                    <button onclick="openAddModal()" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-bold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Shift Baru

                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center gap-2 border border-green-200">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left border-collapse stripe hover">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="p-4 rounded-tl-lg text-center" width="5%">Warna</th>
                                <th class="p-4">Nama</th>
                                <th class="p-4 text-center">Waktu</th>
                                <th class="p-4 text-center">Durasi</th>
                                <th class="p-4 text-center">Cabang</th>
                                <th class="p-4 text-center rounded-tr-lg" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @foreach($shifts as $shift)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 text-center">
                                    <div class="w-6 h-6 rounded-full mx-auto shadow-sm" style="background-color: {{ $shift->color }};" title="{{ $shift->color }}"></div>
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $shift->name }}</div>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="text-base font-bold text-gray-800">
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                    </div>
                                    @if($shift->is_cross_day)
                                        <span class="text-[10px] text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full font-bold border border-purple-200">
                                            +1 Hari (Lintas Hari)
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-gray-50 text-gray-600 px-2 py-1 rounded border text-xs font-mono">
                                        {{ $shift->duration }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-cyan-50 text-cyan-700 px-3 py-1 rounded-full text-xs font-bold border border-cyan-200">
                                        <i class="fas fa-building mr-1"></i> {{ $shift->branch->name }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <button onclick="editShift({{ $shift }})" class="text-blue-600 hover:text-blue-800 transition p-2 bg-blue-50 rounded-lg hover:bg-blue-100"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('delshift', $shift->id) }}" method="POST" class="inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-800 transition delete-confirm p-2 bg-red-50 rounded-lg hover:bg-red-100"><i class="fas fa-trash"></i></button>
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

    <!-- MODAL (CREATE & EDIT) -->
    <div id="shiftModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl relative transform transition-all scale-100">
            <button onclick="document.getElementById('shiftModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition"><i class="fas fa-times"></i></button>
            
            <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2" id="modalTitle">
                <i class="fas fa-clock text-blue-600"></i> Tambah Master Shift
            </h2>
            
            <form id="shiftForm" method="POST" class="space-y-4">
                @csrf
                <div id="methodField"></div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Shift</label>
                    <input type="text" name="name" id="name" class="w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-blue-500" placeholder="e.g. Morning Shift" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mulai</label>
                        <input type="time" name="start_time" id="start_time" class="w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Akhir</label>
                        <input type="time" name="end_time" id="end_time" class="w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 space-y-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Warna Visual</label>
                    <div class="flex gap-2">
                        <input type="color" name="color" id="color" class="h-10 w-12 rounded cursor-pointer border border-gray-300 p-0" value="#3B82F6">
                        <span class="text-xs text-gray-500 self-center">Ambil warna untuk kalendar</span>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer mt-2 border-t border-gray-200 pt-2">
                        <input type="checkbox" name="is_cross_day" id="is_cross_day" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-700 font-medium">Lintas hari (Berakhir besok)</span>
                    </label>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1 mt-2">Cabang yang Berlaku (Opsional)</label>
                        <select name="branch_id" id="branch_id" class="w-full rounded-lg border-gray-300 p-2 border focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 transition">Save Shift</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {
            });

            const modal = $('#shiftModal');
            
            window.openAddModal = function() {
                $('#shiftForm').attr('action', "{{ route('postshift') }}");
                $('#methodField').html('');
                $('#modalTitle').html('<i class="fas fa-clock text-blue-600"></i> Add Master Shift');
                
                // Reset Form
                $('#name').val('');
                $('#start_time').val('');
                $('#end_time').val('');
                $('#color').val('#3B82F6');
                $('#branch_id').val('');
                $('#is_cross_day').prop('checked', false);
                
                modal.removeClass('hidden');
            }

            window.editShift = function(data) {
                $('#shiftForm').attr('action', `/shift/${data.id}/update`);
                $('#methodField').html('<input type="hidden" name="_method" value="PUT">');
                $('#modalTitle').html('<i class="fas fa-edit text-blue-600"></i> Edit Master Shift');
                
                // Populate
                $('#name').val(data.name);
                $('#start_time').val(data.start_time);
                $('#end_time').val(data.end_time);
                $('#color').val(data.color);
                $('#branch_id').val(data.branch_id);
                $('#is_cross_day').prop('checked', data.is_cross_day == 1);
                
                modal.removeClass('hidden');
            }

            $(window).click((e) => {
                if (e.target === modal[0]) modal.addClass('hidden');
            });

            $('.delete-confirm').click(function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete this shift?',
                    text: "Existing schedules will lose this template.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => { if (result.isConfirmed) form.submit(); });
            });
        });
    </script>
    @include('sweetalert::alert')
    @include('layout.loading')
</body>
</html>