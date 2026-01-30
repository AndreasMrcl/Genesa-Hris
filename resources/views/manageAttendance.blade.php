<!DOCTYPE html>
<html lang="en">

<head>
    <title>Kelola Absensi</title>
    @include('layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-5 space-y-6">
            
            <!-- Header -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-user-check text-blue-600"></i> Input Absensi
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola rekap data absensi karyawan</p>
                </div>
                <a href="{{ route('attendance') }}"
                    class="p-2 px-6 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition flex items-center gap-2">
                    <span>&larr;</span> Kembali
                </a>
            </div>

            <!-- Date Selection -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h2 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                    Pilih Periode
                </h2>

                <form action="{{ route('manageattendance') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Mulai</label>
                        <input type="date" name="start" value="{{ $start ?? '' }}"
                            class="rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Berakhir</label>
                        <input type="date" name="end" value="{{ $end ?? '' }}"
                            class="rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition font-bold shadow-md">
                        Muat Data
                    </button>

                    @if ($start && $end)
                        <a href="{{ route('attendance') }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-bold">
                            Cancel
                        </a>
                    @endif
                </form>
            </div>

            @if ($errors->any())
                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                    <span class="font-bold">Error:</span> {{ $errors->first() }}
                </div>
            @endif

            @if ($start && $end && count($employees) > 0)
                
                <!-- Smart Detection Info Box -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white flex-shrink-0">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-bold text-blue-900 mb-1">Smart Auto-Detection Aktif</h3>
                            <p class="text-xs text-blue-700 mb-3">
                                Sistem telah otomatis mengisi data kehadiran berdasarkan deteksi dari berbagai sumber
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <!-- GPS Stats -->
                                <div class="bg-white rounded-lg p-3 border border-blue-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-map-marker-alt text-green-600 text-sm"></i>
                                        </div>
                                        <div class="flex-grow">
                                            <p class="text-[10px] text-gray-500 uppercase font-bold">GPS Attendance</p>
                                            <p class="font-bold text-green-600 text-lg">{{ $detectionStats['total_gps'] }}</p>
                                            <p class="text-[9px] text-gray-400">check-ins detected</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Fingerspot Stats -->
                                <div class="bg-white rounded-lg p-3 border border-blue-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-fingerprint text-blue-600 text-sm"></i>
                                        </div>
                                        <div class="flex-grow">
                                            <p class="text-[10px] text-gray-500 uppercase font-bold">Fingerspot</p>
                                            <p class="font-bold text-blue-600 text-lg">{{ $detectionStats['total_fingerspot'] }}</p>
                                            <p class="text-[9px] text-gray-400">scans detected</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Leave Stats -->
                                <div class="bg-white rounded-lg p-3 border border-blue-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-calendar-check text-purple-600 text-sm"></i>
                                        </div>
                                        <div class="flex-grow">
                                            <p class="text-[10px] text-gray-500 uppercase font-bold">Leave/Cuti</p>
                                            <p class="font-bold text-purple-600 text-lg">{{ $detectionStats['total_leaves'] }}</p>
                                            <p class="text-[9px] text-gray-400">approved requests</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 bg-blue-100 border border-blue-200 rounded-lg p-2">
                                <p class="text-[10px] text-blue-700">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Catatan:</strong> Data telah diisi otomatis. Anda tetap bisa mengedit secara manual jika diperlukan koreksi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('postattendance') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period_start" value="{{ $start }}">
                    <input type="hidden" name="period_end" value="{{ $end }}">

                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <!-- Header -->
                        <div class="p-4 bg-blue-50 border-b border-blue-100 flex justify-between items-center sticky top-0 z-10">
                            <div>
                                <h3 class="font-bold text-blue-800 text-lg">Input Data Absensi</h3>
                                <p class="text-xs text-blue-600">Data untuk periode:
                                    <strong>{{ $start }}</strong> s/d <strong>{{ $end }}</strong>
                                </p>
                            </div>
                            <button type="submit"
                                class="px-8 py-2.5 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-bold flex items-center gap-2">
                                <i class="fas fa-save"></i> Simpan Semua Perubahan
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="overflow-auto max-h-[65vh]">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-100 text-gray-600 uppercase text-xs sticky top-0 z-10 shadow-sm">
                                    <tr>
                                        <th class="p-4 w-12 bg-gray-100 border-b">No</th>
                                        <th class="p-4 w-64 bg-gray-100 border-b">Nama Karyawan</th>
                                        <th class="p-4 text-center w-24 bg-red-50 border-b text-orange-800">Late</th>
                                        <th class="p-4 text-center w-24 bg-red-50 border-b text-red-800">Alpha</th>
                                        <th class="p-4 text-center w-24 bg-gray-50 border-b text-gray-800">Izin</th>
                                        <th class="p-4 text-center w-24 bg-yellow-50 border-b text-yellow-800">Sakit</th>
                                        <th class="p-4 text-center w-20 bg-blue-100 border-b text-blue-800">Cuti</th>
                                        <th class="p-4 text-center w-24 bg-green-50 border-b text-green-800">Hadir</th>
                                        <th class="p-4 text-center bg-gray-100 border-b">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-200">
                                    @php $no = 1; @endphp
                                    @foreach ($employees as $emp)
                                        @php
                                            $existing = $attendances[$emp->id] ?? null;
                                            
                                            // Display values
                                            $displayPresent = $existing ? $existing->total_present : $emp->auto_present;
                                            $displaySick = $existing ? $existing->total_sick : $emp->auto_sick;
                                            $displayPermission = $existing ? $existing->total_permission : $emp->auto_permission;
                                            $displayLeave = $existing ? $existing->total_leave : $emp->auto_leave;
                                            $displayAlpha = $existing ? $existing->total_alpha : 0;
                                            $displayLate = $existing ? $existing->total_late : $emp->auto_late;
                                        @endphp
                                        <tr class="hover:bg-blue-50 transition group">
                                            <td class="p-4 text-center text-gray-400 font-medium">{{ $no++ }}</td>
                                            
                                            <!-- Employee Info -->
                                            <td class="p-4">
                                                <div class="font-bold text-gray-800">{{ $emp->name }}</div>
                                                <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                                    <span>{{ $emp->position->name ?? '-' }}</span>
                                                    
                                                    @if($emp->detection_source)
                                                        <span class="text-[9px] px-1.5 py-0.5 rounded border font-bold
                                                            {{ $emp->detection_source == 'gps' ? 'bg-green-50 text-green-600 border-green-200' : '' }}
                                                            {{ $emp->detection_source == 'fingerspot' ? 'bg-blue-50 text-blue-600 border-blue-200' : '' }}
                                                            {{ $emp->detection_source == 'mixed' ? 'bg-purple-50 text-purple-600 border-purple-200' : '' }}">
                                                            <i class="fas fa-{{ $emp->detection_source == 'gps' ? 'map-marker-alt' : ($emp->detection_source == 'fingerspot' ? 'fingerprint' : 'layer-group') }}"></i>
                                                            {{ strtoupper($emp->detection_source) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                @if($emp->gps_count > 0 || $emp->fingerspot_count > 0)
                                                    <div class="text-[9px] text-gray-400 mt-1">
                                                        Auto: 
                                                        @if($emp->gps_count > 0)
                                                            GPS({{ $emp->gps_count }}{{ $emp->gps_late_count > 0 ? ', Late:'.$emp->gps_late_count : '' }})
                                                        @endif
                                                        @if($emp->fingerspot_count > 0)
                                                            {{ $emp->gps_count > 0 ? ' + ' : '' }}Fingerspot({{ $emp->fingerspot_count }})
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>

                                            <!-- Late -->
                                            <td class="p-2 text-center bg-orange-50/30">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][late]"
                                                    value="{{ $displayLate }}"
                                                    class="w-20 text-center text-orange-700 border-orange-200 rounded focus:ring-orange-500 p-2 border shadow-sm">
                                            </td>

                                            <!-- Alpha -->
                                            <td class="p-2 text-center bg-red-50/30">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][alpha]"
                                                    value="{{ $displayAlpha }}"
                                                    class="w-20 text-center text-red-700 border-red-200 rounded focus:ring-red-500 p-2 border shadow-sm">
                                            </td>

                                            <!-- Permission -->
                                            <td class="p-2 text-center">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][permission]"
                                                    value="{{ $displayPermission }}"
                                                    class="w-20 text-center text-gray-700 border-gray-200 rounded focus:ring-indigo-500 p-2 border">
                                            </td>

                                            <!-- Sick -->
                                            <td class="p-2 text-center">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][sick]"
                                                    value="{{ $displaySick }}"
                                                    class="w-20 text-center text-yellow-700 border-yellow-200 rounded focus:ring-yellow-500 p-2 border">
                                            </td>

                                            <!-- Leave -->
                                            <td class="p-2 text-center bg-blue-50/50">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][leave]"
                                                    value="{{ $displayLeave }}"
                                                    class="w-20 text-center text-blue-700 border-blue-300 rounded focus:ring-blue-500 p-2 border">
                                            </td>

                                            <!-- Present (with auto-detection indicator) -->
                                            <td class="p-2 text-center bg-green-50/50">
                                                <div class="relative inline-block">
                                                    <input type="number" min="0"
                                                        name="data[{{ $emp->id }}][present]"
                                                        value="{{ $displayPresent }}"
                                                        class="w-20 text-center font-bold text-green-700 border-green-300 rounded focus:ring-green-500 p-2 border shadow-sm"
                                                        required>
                                                    
                                                    @if($emp->gps_count > 0 || $emp->fingerspot_count > 0)
                                                        <div class="absolute -top-1 -right-1"
                                                            title="Auto-detected from GPS and/or Fingerspot">
                                                            <span class="w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center shadow">
                                                                <i class="fas fa-robot text-white text-[8px]"></i>
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Note -->
                                            <td class="p-2">
                                                <input type="text" name="data[{{ $emp->id }}][note]"
                                                    value="{{ $existing->note ?? '' }}"
                                                    class="w-full text-gray-600 border-gray-200 rounded focus:ring-blue-500 p-2 border text-xs"
                                                    placeholder="Tambahkan catatan...">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center text-xs text-gray-500">
                            <div class="flex items-center gap-3">
                                <span>Total Karyawan: <strong class="text-gray-700">{{ count($employees) }}</strong></span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-robot text-blue-500"></i> 
                                    <span class="text-gray-600">= Auto-detected</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt text-green-500"></i> 
                                    <span class="text-gray-600">= GPS</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-fingerprint text-blue-500"></i> 
                                    <span class="text-gray-600">= Fingerspot</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-layer-group text-purple-500"></i> 
                                    <span class="text-gray-600">= Mixed</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            @endif

        </div>
    </main>

    @include('layout.loading')
</body>

</html>
