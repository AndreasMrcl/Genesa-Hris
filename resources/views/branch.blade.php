<!DOCTYPE html>
<html lang="en">

<head>
    <title>Branch Management</title>
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
                        <i class="fas fa-building text-cyan-600"></i> Branch Management</h1>
                    <p class="text-sm text-gray-500">Manage company locations and categories</p>
                </div>
                <button id="addBtn" class="px-6 py-3 bg-cyan-600 text-white rounded-lg shadow-md hover:bg-cyan-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Branch
                </button>
            </div>

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Name</th>
                                <th class="p-4 font-bold">Category</th>
                                <th class="p-4 font-bold">Contact</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($branches as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium">{{ $no++ }}</td>
                                    <td class="p-4 space-y-1">
                                        <div class="font-bold text-gray-900 text-base">{{ $item->name }}</div>
                                        <div class="text-xs text-gray-400">Created: {{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="bg-cyan-100 text-cyan-800 text-xs px-3 py-1 rounded-full font-bold border border-cyan-200 uppercase">
                                            {{ str_replace('_', ' ', $item->category ?? 'General') }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs space-y-1">
                                        <div class="flex items-center gap-2"><i class="fas fa-phone text-gray-400 w-4"></i> {{ $item->phone }}</div>
                                        <div class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-gray-400 w-4"></i> {{ \Illuminate\Support\Str::limit($item->address, 30) }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <button class="editBtn w-10 h-10 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-phone="{{ $item->phone }}"
                                                data-address="{{ $item->address }}"
                                                data-category="{{ $item->category }}"
                                                title="Edit">
                                                <i class="fas fa-edit text-lg"></i>
                                            </button>

                                            <form method="post" action="{{ route('delbranch', ['id' => $item->id]) }}" class="inline deleteForm">
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
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-building text-cyan-600"></i> Add Branch
            </h2>

            <form id="addForm" method="post" action="{{ route('postbranch') }}" class="space-y-5">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500" required>
                            <option value="general">General</option>
                            <option value="food_beverage">Food & Beverage</option>
                            <option value="retail">Retail</option>
                            <option value="hospitality">Hospitality</option>
                            <option value="education">Education</option>
                            <option value="creative">Creative</option>
                            <option value="health_beauty">Health & Beauty</option>
                            <option value="logistics">Logistics</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                        <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500" required></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-cyan-600 text-white font-bold rounded-lg shadow-md hover:bg-cyan-700 transition">Save Branch</button>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative">
            <button id="closeModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Branch
            </h2>

            <form id="editForm" method="post" class="space-y-5">
                @csrf @method('put')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                        <input type="text" id="editName" name="name" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                        <select id="editCategory" name="category" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                            <option value="general">General</option>
                            <option value="food_beverage">Food & Beverage</option>
                            <option value="retail">Retail</option>
                            <option value="hospitality">Hospitality</option>
                            <option value="education">Education</option>
                            <option value="creative">Creative</option>
                            <option value="health_beauty">Health & Beauty</option>
                            <option value="logistics">Logistics</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                        <input type="text" id="editPhone" name="phone" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                        <textarea id="editAddress" name="address" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition">Update Branch</button>
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

            // Modal Toggles
            $('#addBtn').click(() => $('#addModal').removeClass('hidden'));
            $('#closeAddModal').click(() => $('#addModal').addClass('hidden'));
            
            // Edit Logic
            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                $('#editName').val(btn.data('name'));
                $('#editPhone').val(btn.data('phone'));
                $('#editAddress').val(btn.data('address'));
                $('#editCategory').val(btn.data('category'));
                $('#editForm').attr('action', `/branch/${btn.data('id')}/update`);
                $('#editModal').removeClass('hidden');
            });
            $('#closeModal').click(() => $('#editModal').addClass('hidden'));

            // Delete Confirm
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Branch?',
                    text: "You won't be able to revert this!",
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