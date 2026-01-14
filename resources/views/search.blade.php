<!DOCTYPE html>
<html lang="en">

<head>
    <title>Hasil Pencarian</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            border-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-magnifying-glass text-cyan-600"></i>
                        Hasil Pencarian
                    </h1>
                    <p class="text-sm text-gray-500">
                        Hasil berdasarkan kata kunci pencarian Anda di seluruh sistem
                    </p>
                </div>
            </div>

            <!-- Header -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800">
                        <i class="fas fa-building text-cyan-600"></i> Branch Management
                    </h1>
                    <p class="text-sm text-gray-500">Manage company locations and categories</p>
                </div>
                <button id="addBtn"
                    class="px-6 py-3 bg-cyan-600 text-white rounded-lg shadow-md hover:bg-cyan-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Branch
                </button>
            </div>

            <!-- Branch Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="branchTable" class="w-full text-left">
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
                                        <div class="text-xs text-gray-400">Created:
                                            {{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</div>
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="bg-cyan-100 text-cyan-800 text-xs px-3 py-1 rounded-full font-bold border border-cyan-200 uppercase">
                                            {{ str_replace('_', ' ', $item->category ?? 'General') }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs space-y-1">
                                        <div class="flex items-center gap-2"><i
                                                class="fas fa-phone text-gray-400 w-4"></i> {{ $item->phone }}</div>
                                        <div class="flex items-center gap-2"><i
                                                class="fas fa-map-marker-alt text-gray-400 w-4"></i>
                                            {{ \Illuminate\Support\Str::limit($item->address, 30) }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <button
                                                class="editBtn w-10 h-10 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                data-phone="{{ $item->phone }}" data-address="{{ $item->address }}"
                                                data-category="{{ $item->category }}" title="Edit">
                                                <i class="fas fa-edit text-lg"></i>
                                            </button>

                                            <form method="post" action="{{ route('delbranch', ['id' => $item->id]) }}"
                                                class="inline deleteForm">
                                                @csrf @method('delete')
                                                <button type="button"
                                                    class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                    title="Delete">
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

            <!-- Header -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-id-badge text-slate-600"></i> Job Positions
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Manage job titles and default salaries</p>
                </div>
                <button id="addBtn"
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg shadow-md hover:bg-slate-800 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Position
                </button>
            </div>

            <!-- Position Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="positionTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Name</th>
                                <th class="p-4 font-bold">Category</th>
                                <th class="p-4 font-bold text-right">Salary</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($positions as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $item->name }}</td>
                                    <td class="p-4">
                                        <span
                                            class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-bold border border-gray-200 uppercase">
                                            {{ str_replace('_', ' ', $item->category) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-mono text-slate-600">
                                        Rp {{ number_format($item->base_salary_default, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <button
                                                class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition"
                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                data-category="{{ $item->category }}"
                                                data-salary="{{ $item->base_salary_default }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form method="post" action="{{ route('desposition', $item->id) }}"
                                                class="inline deleteForm">
                                                @csrf @method('delete')
                                                <button type="button"
                                                    class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition"
                                                    title="Delete">
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

            <!-- Header Section -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-bullhorn text-red-700"></i> Company Announcements
                    </h1>
                    <p class="text-sm text-gray-500">Your central hub for company-wide updates.</p>
                </div>
                <button id="addBtn"
                    class="px-6 py-3 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Announcement
                </button>
            </div>

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="announcementTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Date</th>
                                <th class="p-4 font-bold">Content</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($announcements as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium">{{ $no++ }}</td>
                                    <td class="p-4 font-medium">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="p-4 text-xs text-gray-600 italic max-w-xs truncate">
                                        "{{ \Illuminate\Support\Str::limit($item->content, 40) }}"
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Edit Button --}}
                                            <button
                                                class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}" data-content="{{ $item->content }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Delete Button --}}
                                            <form method="post"
                                                action="{{ route('delannouncement', ['id' => $item->id]) }}"
                                                class="inline deleteForm">
                                                @csrf
                                                @method('delete')
                                                <button type="button"
                                                    class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                    title="Delete">
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


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#branchTable', {});
        });

        $(document).ready(function() {
            new DataTable('#positionTable', {});
        });

        $(document).ready(function() {
            new DataTable('#announcementTable', {});
        });
    </script>
    @include('sweetalert::alert')
    @include('layout.loading')
</body>

</html>
