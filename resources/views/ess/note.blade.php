<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Note</title>
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    @include('ess.layout.head')
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto">

    <!-- HEADER / BACK BUTTON -->
    <div class="p-2">
        <a href="{{ route('ess-home') }}"
            class="inline-flex items-center gap-2 px-6 py-2 bg-white text-gray-700 rounded-xl text-3xl">
            <span>&larr;</span>
        </a>
    </div>


    <!-- NOTE -->
    <div class="p-2">
        <!-- Back Button -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">
            
            <!-- Header Section -->
            <div>
                <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-teal-600"></i> Employee Notes
                </h1>
                <p class="text-sm text-gray-500">Manage notes, warnings, and rewards</p>
            </div>

            <!-- Table Section -->
            <div class="overflow-auto">
                <table id="myTable" class="w-full text-left">
                    <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                        <tr>
                            <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                            <th class="p-4 font-bold">Date</th>
                            <th class="p-4 font-bold">Employee</th>
                            <th class="p-4 font-bold">Type</th>
                            <th class="p-4 font-bold">Content</th>
                            <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @php $no = 1; @endphp
                        @foreach ($notes as $item)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="p-4 font-medium">{{ $no++ }}</td>
                                <td class="p-4 font-medium">
                                    {{ \Carbon\Carbon::parse($item->note_date)->format('d M Y') }}
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $item->employee->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->employee->position ?? '' }}</div>
                                </td>
                                <td class="p-4">
                                    @php
                                        $typeColor = match ($item->type) {
                                            'warning' => 'bg-red-100 text-red-700 border-red-200',
                                            'reward' => 'bg-green-100 text-green-700 border-green-200',
                                            'performance' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'general' => 'bg-gray-100 text-gray-700 border-gray-200',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span
                                        class="{{ $typeColor }} px-3 py-1 rounded-full text-xs font-bold border uppercase shadow-sm">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td class="p-4 text-xs text-gray-600 italic max-w-xs truncate">
                                    "{{ \Illuminate\Support\Str::limit($item->content, 40) }}"
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-center items-center gap-2">
                                        {{-- Edit Button --}}
                                        <button
                                            class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                            data-id="{{ $item->id }}" data-employee="{{ $item->employee_id }}"
                                            data-date="{{ $item->note_date }}" data-type="{{ $item->type }}"
                                            data-content="{{ $item->content }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- Delete Button --}}
                                        <form method="post" action="{{ route('delnote', ['id' => $item->id]) }}"
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


    @include('layout.loading')

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Init DataTable
            new DataTable('#myTable', {});
        });
    </script>
</body>

</html>
