<!DOCTYPE html>
<html lang="en">

<head>
    <title>Tetapkan Tunjangan</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-5 space-y-4">

            <!-- Header & Back Button -->
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-semibold text-2xl text-black">Tetapkan Tunjangan</h1>
                    <p class="text-gray-600 text-sm mt-1">Karyawan: <span
                            class="font-bold text-indigo-700">{{ $employee->name }}</span> ({{ $employee->position->name }})
                    </p>
                </div>
                <a href="{{ route('employee') }}"
                    class="p-2 px-6 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition flex items-center gap-2">
                    <span>&larr;</span> Kembali
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Add Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2 text-gray-800">Tambahkan Penugasan Baru</h2>

                        <form action="{{ route('postallowanceEmp', $employee->id) }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="space-y-2">
                                <label class="font-semibold text-black">Jenis Tunjangan:</label>
                                <select name="allow_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2.5 rounded-lg w-full focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">-- Pilih Tunjangan --</option>
                                    @foreach ($allows as $allow)
                                        <option value="{{ $allow->id }}">{{ $allow->name }} ({{ $allow->type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="font-semibold text-black">Jumlah (Rp):</label>
                                <input type="text" name="amount"
                                    class="currency bg-gray-50 border border-gray-300 text-gray-900 p-2.5 rounded-lg w-full focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0" min="0" required>
                            </div>

                            <button type="submit"
                                class="w-full bg-green-500 text-white p-3 rounded-lg hover:bg-green-600 shadow transition font-semibold mt-4">
                                Tetapkan Tunjangan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column: List Table -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2 text-gray-800">Tunjangan Saat Ini</h2>
                        <div class="overflow-x-auto">
                            <table id="allowTable"
                                class="w-full text-left border-collapse bg-gray-50 rounded-lg overflow-hidden">
                                <thead class="bg-gray-200 text-gray-700 uppercase text-xs leading-normal">
                                    <tr>
                                        <th class="py-3 px-4">Nama Tunjangan</th>
                                        <th class="py-3 px-4">Jenis</th>
                                        <th class="py-3 px-4">Jumlah</th>
                                        <th class="py-3 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm font-light bg-white">
                                    @foreach ($employeeAllowances as $item)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                            <td class="py-3 px-4 font-medium text-gray-800">{{ $item->allow->name }}
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($item->allow->type == 'fixed')
                                                    <span
                                                        class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">Tetap</span>
                                                @elseif($item->allow->type == 'daily')
                                                    <span
                                                        class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold">Harian</span>
                                                @else
                                                    <span
                                                        class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold">Satu Kali</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 font-bold text-gray-800">Rp
                                                {{ number_format($item->amount, 0, ',', '.') }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <!-- Edit Button -->
                                                    <button
                                                        class="editAmountBtn p-2 bg-yellow-400 text-white rounded-lg shadow hover:bg-yellow-500 transition"
                                                        data-id="{{ $item->id }}"
                                                        data-name="{{ $item->allow->name }}"
                                                        data-amount="{{ $item->amount }}" title="Edit Amount">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>
                                                    </button>

                                                    <!-- Delete Form -->
                                                    <form action="{{ route('delallowanceEmp', $item->id) }}"
                                                        method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="delete-confirm p-2 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition"
                                                            title="Delete">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-4 h-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                            </svg>
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
            </div>
        </div>
    </main>

    <!-- Edit Amount Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-white/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div
            class="bg-white rounded-lg p-6 w-full max-w-sm shadow-lg relative transform transition-all duration-300 scale-95">
            <button id="closeEditModal"
                class="absolute top-4 right-4 text-white hover:text-gray-300 bg-red-500 p-1 px-4 rounded-full">✕</button>

            <h1 class="text-xl font-semibold mb-6 text-gray-800">Edit Jumlah</h1>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label class="font-semibold text-black">Tunjangan:</label>
                    <input type="text" id="editNameDisplay"
                        class="bg-gray-100 border border-gray-300 text-gray-500 p-2.5 rounded-lg w-full" disabled>
                </div>

                <div class="space-y-2">
                    <label class="font-semibold text-black">Jumlah Baru (Rp):</label>
                    <input type="text" name="amount" id="editAmount"
                        class="currency bg-gray-50 border border-gray-300 text-gray-900 p-2.5 rounded-lg w-full focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 shadow transition font-semibold">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            @if (count($employeeAllowances) > 0)
                new DataTable('#allowTable', {
                    paging: false,
                    info: false,
                    searching: false
                });
            @endif

            function formatCurrency(value) {
                let rawValue = value.replace(/\D/g, '');
                if (rawValue === '') return '';
                let numberValue = parseInt(rawValue, 10);
                return numberValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('.currency').on('input', function() {
                let val = $(this).val();
                $(this).val(formatCurrency(val));
            });

            // Edit Modal Logic
            const editModal = $('#editModal');
            $('.editAmountBtn').click(function() {
                const btn = $(this);
                $('#editNameDisplay').val(btn.data('name'));
                let rawAmount = btn.data('amount');
                let amountStr = String(rawAmount).split('.')[0];
                $('#editAmount').val(formatCurrency(amountStr));
                $('#editForm').attr('action', `/allowance-employee/${btn.data('id')}/update`);

                editModal.removeClass('hidden');
            });

            $('#closeEditModal').click(() => editModal.addClass('hidden'));

            // Close modal on click outside
            $(window).click((e) => {
                if (e.target === editModal[0]) editModal.addClass('hidden');
            });

            $('form').on('submit', function() {
                $('.currency').each(function() {
                    let cleanVal = $(this).val().replace(/\./g, '');
                    $(this).val(cleanVal);
                });
            });

            // SweetAlert Delete Confirmation
            $('.delete-confirm').click(function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Remove allowance?',
                    text: "You can assign it again later.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!'
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
