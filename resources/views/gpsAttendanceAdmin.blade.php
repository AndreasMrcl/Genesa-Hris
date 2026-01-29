<!DOCTYPE html>
<html lang="en">
<head>
    <title>GPS Attendance Monitoring</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
                        <i class="fas fa-map-marked-alt text-indigo-600"></i> GPS Attendance Monitoring
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Real-time location tracking & attendance records</p>
                </div>
                
                <form action="{{ route('gps-attendance') }}" method="GET" class="flex gap-2">
                    <input type="date" name="date" value="{{ $date }}" 
                        class="rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-bold">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Total Check-In</p>
                    <p class="text-2xl font-bold text-green-600">{{ $logs->where('check_in_time', '!=', null)->count() }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Completed</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $logs->where('check_out_time', '!=', null)->count() }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Late</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $logs->where('status', 'late')->count() }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Incomplete</p>
                    <p class="text-2xl font-bold text-red-600">{{ $logs->where('check_out_time', null)->count() }}</p>
                </div>
            </div>

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold">Karyawan</th>
                                <th class="p-4 font-bold text-center">Check-In</th>
                                <th class="p-4 font-bold text-center">Check-Out</th>
                                <th class="p-4 font-bold text-center">Durasi</th>
                                <th class="p-4 font-bold text-center">Jarak (m)</th>
                                <th class="p-4 font-bold text-center">Status</th>
                                <th class="p-4 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ $log->employee->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $log->employee->position->name ?? '-' }} | 
                                        {{ $log->employee->branch->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    @if($log->check_in_time)
                                        <div class="text-sm font-bold text-gray-700">{{ $log->check_in_time->format('H:i') }}</div>
                                        <div class="text-xs text-gray-400">{{ $log->check_in_time->format('d M Y') }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    @if($log->check_out_time)
                                        <div class="text-sm font-bold text-gray-700">{{ $log->check_out_time->format('H:i') }}</div>
                                        <div class="text-xs text-gray-400">{{ $log->check_out_time->format('d M Y') }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <span class="text-sm font-bold text-indigo-600">{{ $log->work_duration }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="text-xs">
                                        <div>In: {{ number_format($log->check_in_distance ?? 0) }}m</div>
                                        @if($log->check_out_distance)
                                            <div>Out: {{ number_format($log->check_out_distance) }}m</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="text-xs font-bold px-2 py-1 rounded border {{ $log->status_badge }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <button onclick="viewDetails({{ $log->id }})" 
                                        class="text-blue-600 hover:text-blue-800 p-2 bg-blue-50 rounded-lg hover:bg-blue-100">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                    Tidak ada data absensi pada tanggal ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {
                order: [[1, 'asc']] // Sort by check-in time
            });
        });

        function viewDetails(id) {
            // Implement detail view modal or redirect
            alert('Detail view for attendance ID: ' + id);
        }
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')
</body>
</html>