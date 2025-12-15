<!DOCTYPE html>
<html lang="en">

<head>
    <title>Master Deductions</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800">
                        <i class="fas fa-file-invoice-dollar text-rose-600"></i> Master Deductions</h1>
                    <p class="text-sm text-gray-500">Define reduction types for employees</p>
                </div>
                <button id="addBtn" class="px-6 py-3 bg-rose-500 text-white rounded-lg shadow-md hover:bg-rose-600 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Deduction
                </button>
            </div>

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Deduction Name</th>
                                <th class="p-4 font-bold">Type</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($deductions as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium">{{ $no++ }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name }}</td>
                                    <td class="p-4">
                                        @if($item->type == 'fixed')
                                            <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-bold border border-blue-200">FIXED (Monthly)</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full font-bold border border-gray-200">ONE TIME</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <button class="editBtn w-10 h-10 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-type="{{ $item->type }}"
                                                title="Edit">
                                                <i class="fas fa-edit text-lg"></i>
                                            </button>

                                            <form method="post" action="{{ route('deldeduction', $item->id) }}" class="inline deleteForm">
                                                @csrf @method('delete')
                                                <button type="button" class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition" title="Delete">
                                                    <i class="fas fa-trash text-lg"></i>
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
        <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl relative">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-minus-circle text-rose-500"></i> Add Deduction
            </h2>

            <form action="{{ route('postdeduction') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-rose-500" placeholder="e.g. Kasbon" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-rose-500" required>
                        <option value="fixed">FIXED (Tetap per Bulan)</option>
                        <option value="one_time">ONE TIME (Sekali Saja)</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-rose-500 text-white font-bold rounded-lg shadow-md hover:bg-rose-600 transition">Save Deduction</button>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl relative">
            <button id="closeEditModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Deduction
            </h2>

            <form id="editForm" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                    <input type="text" id="editName" name="name" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Type</label>
                    <select id="editType" name="type" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                        <option value="fixed">FIXED</option>
                        <option value="one_time">ONE TIME</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition">Update Deduction</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {
            });

            $('#addBtn').click(() => $('#addModal').removeClass('hidden'));
            $('#closeAddModal').click(() => $('#addModal').addClass('hidden'));
            
            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                $('#editName').val(btn.data('name'));
                $('#editType').val(btn.data('type'));
                $('#editForm').attr('action', `/deduction/${btn.data('id')}/update`);
                $('#editModal').removeClass('hidden');
            });
            $('#closeEditModal').click(() => $('#editModal').addClass('hidden'));

            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Deduction?',
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