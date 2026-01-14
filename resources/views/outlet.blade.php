<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manajemen Outlet - {{ $branch->name }}</title>
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
                        <i class="fas fa-store text-cyan-600"></i> {{ $branch->name }} Outlets
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola data outlet untuk cabang {{ $branch->name }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('branch') }}" class="px-5 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Kembali ke Cabang
                    </a>
                    <button id="addBtn" class="px-6 py-3 bg-cyan-600 text-white rounded-lg shadow-md hover:bg-cyan-700 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Outlet
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
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Nama Outlet</th>
                                <th class="p-4 font-bold">Telepon</th>
                                <th class="p-4 font-bold">Alamat</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($outlets as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 text-base">{{ $item->name }}</div>
                                    </td>
                                    <td class="p-4">{{ $item->phone ?? '-' }}</td>
                                    <td class="p-4">{{ $item->address ?? '-' }}</td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <button class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-phone="{{ $item->phone }}"
                                                data-address="{{ $item->address }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form method="post" action="{{ route('deloutlet', ['id' => $item->id]) }}" class="inline deleteForm">
                                                @csrf @method('delete')
                                                <button type="button" class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition" title="Delete">
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
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-store text-cyan-600"></i> Tambah Outlet
            </h2>

            <form id="addForm" method="post" action="{{ route('postoutlet') }}" class="space-y-5">
                @csrf
                <!-- Hidden Branch ID (Karena kita sudah di dalam halaman branch) -->
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Outlet</label>
                    <input type="text" name="name" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="phone" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat</label>
                    <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500"></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-cyan-600 text-white font-bold rounded-lg shadow-md hover:bg-cyan-700 transition">Simpan Outlet</button>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative">
            <button id="closeModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Outlet
            </h2>

            <form id="editForm" method="post" class="space-y-5">
                @csrf @method('put')
                
                <!-- Tidak perlu edit branch_id, karena outlet terikat pada branch parent -->

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Outlet</label>
                    <input type="text" id="editName" name="name" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telepon</label>
                    <input type="text" id="editPhone" name="phone" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat</label>
                    <textarea id="editAddress" name="address" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition">Update Outlet</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {});

            // Modal Toggles
            $('#addBtn').click(() => $('#addModal').removeClass('hidden'));
            $('#closeAddModal').click(() => $('#addModal').addClass('hidden'));
            $('#closeModal').click(() => $('#editModal').addClass('hidden'));
            
            $(window).click((e) => {
                if (e.target === $('#addModal')[0]) $('#addModal').addClass('hidden');
                if (e.target === $('#editModal')[0]) $('#editModal').addClass('hidden');
            });

            // Edit Logic
            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                $('#editName').val(btn.data('name'));
                $('#editPhone').val(btn.data('phone'));
                $('#editAddress').val(btn.data('address'));
                
                // URL Route: /outlet/{id}/update
                $('#editForm').attr('action', `/outlet/${btn.data('id')}/update`);
                $('#editModal').removeClass('hidden');
            });

            // Delete Confirm
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Outlet?',
                    text: "Deleting this outlet might affect employees assigned to it!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => { if (result.isConfirmed) form.submit(); });
            });
        });
    </script>
    @include('sweetalert::alert')
    @include('layout.loading')
</body>
</html>