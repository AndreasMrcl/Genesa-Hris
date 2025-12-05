<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS |Attendance</title>
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


    <!-- ATTENDANCE -->
    <div class="p-2">
        <!-- Back Button -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">
             <!-- Header Section -->
            <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-clock text-indigo-600"></i> Attendance
                    </h1>
                    <p class="text-sm text-gray-500">Manage daily attendance records</p>
                </div>

            <!-- Table Section -->
            <div class="overflow-auto">
                <table id="myTable" class="w-full text-left">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <tr>
                            <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                            <th class="p-4 font-bold">Date</th>
                            <th class="p-4 font-bold text-center">Clock In</th>
                            <th class="p-4 font-bold text-center">Clock Out</th>
                            <th class="p-4 font-bold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach ($attendances as $item)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="p-4 font-medium">{{ $no++ }}</td>
                                <td class="p-4 font-medium">
                                    {{ \Carbon\Carbon::parse($item->attendance_date)->format('d M Y') }}
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-mono">
                                        {{ $item->clock_in ? \Carbon\Carbon::parse($item->clock_in)->format('H:i') : '-' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-mono">
                                        {{ $item->clock_out ? \Carbon\Carbon::parse($item->clock_out)->format('H:i') : '-' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    @php
                                        $statusColor = match ($item->status) {
                                            'present' => 'bg-green-100 text-green-700 border-green-200',
                                            'absent' => 'bg-red-100 text-red-700 border-red-200',
                                            'leave' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'sick' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'off' => 'bg-gray-100 text-gray-600 border-gray-200',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span
                                        class="{{ $statusColor }} px-3 py-1 rounded-full text-xs font-bold border uppercase shadow-sm">
                                        {{ $item->status }}
                                    </span>
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
