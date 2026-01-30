<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manajemen Absensi</title>
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

            <!-- Header Section -->
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-clock text-indigo-600"></i> Rekap Absensi
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Daftar periode absensi yang tercatat</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <!-- SYNC FINGERSPOT (Backup Data) -->
                    <button onclick="document.getElementById('syncModal').classList.remove('hidden')"
                        class="p-2 px-4 bg-white text-blue-600 border border-blue-200 rounded-lg shadow hover:bg-blue-50 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i> Sync Fingerspot
                    </button>
                    
                    <!-- AUTO-GENERATE (Process Data) -->
                    <button onclick="document.getElementById('autoGenerateModal').classList.remove('hidden')"
                        class="p-2 px-4 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-magic"></i> Auto-Generate
                    </button>
                    
                    <!-- MANUAL INPUT -->
                    <a href="{{ route('manageattendance') }}"
                        class="p-2 px-6 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition font-semibold flex items-center w-fit gap-2">
                        <i class="fas fa-plus"></i> Input Manual
                    </a>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-bold text-indigo-900 mb-2">Cara Kerja Sistem Absensi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                            <div class="bg-white rounded-lg p-3 border border-indigo-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                        <i class="fas fa-fingerprint text-xs"></i>
                                    </div>
                                    <span class="font-bold text-gray-700">1. Sync Fingerspot</span>
                                </div>
                                <p class="text-gray-600">Ambil data dari mesin absensi jika webhook terlewat</p>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 border border-indigo-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                                        <i class="fas fa-magic text-xs"></i>
                                    </div>
                                    <span class="font-bold text-gray-700">2. Auto-Generate</span>
                                </div>
                                <p class="text-gray-600">Proses otomatis: GPS + Fingerspot + Leave → Rekap</p>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 border border-indigo-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                        <i class="fas fa-edit text-xs"></i>
                                    </div>
                                    <span class="font-bold text-gray-700">3. Input Manual</span>
                                </div>
                                <p class="text-gray-600">Edit manual jika ada koreksi data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold">Rentang Periode</th>
                                <th class="p-4 font-bold text-center">Source</th>
                                <th class="p-4 font-bold text-center">Total Karyawan</th>
                                <th class="p-4 font-bold text-right">Terakhir Diperbarui</th>
                                <th class="p-4 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700 text-sm">
                            @foreach ($batches as $batch)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <a href="{{ route('manageattendance', ['start' => $batch->period_start, 'end' => $batch->period_end]) }}"
                                                class="text-lg font-bold text-blue-600 mb-1">
                                                {{ \Carbon\Carbon::parse($batch->period_start)->format('d M Y') }} -
                                                {{ \Carbon\Carbon::parse($batch->period_end)->format('d M Y') }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $firstRecord = \App\Models\Attendance::where('period_start', $batch->period_start)
                                                ->where('period_end', $batch->period_end)
                                                ->first();
                                        @endphp
                                        @if($firstRecord)
                                            <span class="text-xs font-bold px-2 py-1 rounded border {{ $firstRecord->source_badge }}">
                                                <i class="fas fa-{{ $firstRecord->source == 'gps' ? 'map-marker-alt' : ($firstRecord->source == 'fingerspot' ? 'fingerprint' : 'layer-group') }}"></i>
                                                {{ strtoupper($firstRecord->source) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                                            {{ $batch->total_records }} Data
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-500 text-right">
                                        {{ \Carbon\Carbon::parse($batch->last_updated)->diffForHumans() }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('delattendance') }}" method="POST"
                                            class="inline-block delete-batch-form">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="start" value="{{ $batch->period_start }}">
                                            <input type="hidden" name="end" value="{{ $batch->period_end }}">

                                            <button type="button"
                                                class="delete-confirm p-2 w-9 h-9 text-white bg-red-500 rounded-lg shadow hover:bg-red-600 transition"
                                                title="Delete Batch">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- SYNC FINGERSPOT MODAL -->
    <div id="syncModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
            <button onclick="document.getElementById('syncModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>

            <h2 class="text-xl font-bold mb-4 text-gray-800 flex items-center gap-2">
                <i class="fas fa-sync-alt text-blue-600"></i> Sync dari Fingerspot Cloud
            </h2>

            <p class="text-sm text-gray-500 mb-4">
                Ambil data absensi dari Fingerspot Cloud jika webhook realtime terlewat atau gagal.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-xs text-blue-700">
                <i class="fas fa-info-circle"></i> 
                <strong>Catatan:</strong> Data akan disimpan ke database lokal sebagai backup. Setelah sync, gunakan tombol "Auto-Generate" untuk membuat rekap.
            </div>

            <form action="{{ route('fingerspotFetch') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 p-2.5 border" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 p-2.5 border" required>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-download"></i> Sync Data Sekarang
                </button>
            </form>
        </div>
    </div>

    <!-- AUTO-GENERATE MODAL -->
    <div id="autoGenerateModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
            <button onclick="document.getElementById('autoGenerateModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>

            <h2 class="text-xl font-bold mb-4 text-gray-800 flex items-center gap-2">
                <i class="fas fa-magic text-indigo-600"></i> Auto-Generate Rekap Absensi
            </h2>

            <p class="text-sm text-gray-500 mb-4">
                Sistem akan otomatis memproses data dari GPS Attendance dan Fingerspot untuk membuat rekap bulanan.
            </p>

            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 mb-4">
                <p class="text-xs text-indigo-700 font-bold mb-2"><i class="fas fa-list-check"></i> Proses Otomatis:</p>
                <ul class="text-xs text-indigo-600 space-y-1">
                    <li>✓ Ambil data GPS Attendance (ESS Mobile)</li>
                    <li>✓ Ambil data Fingerspot (Mesin Absensi)</li>
                    <li>✓ Gabungkan dengan data Cuti/Izin/Sakit</li>
                    <li>✓ Validasi dengan Schedule karyawan</li>
                    <li>✓ Hitung Late, Alpha, dan lainnya</li>
                </ul>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-xs text-yellow-700">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Prioritas Data:</strong> GPS > Fingerspot > Alpha
            </div>

            <form action="{{ route('attendance-auto-generate') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 p-2.5 border" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 p-2.5 border" required>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-cogs"></i> Generate Rekap Sekarang
                </button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {});

            $('.delete-confirm').click(function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus periode ini?',
                    text: 'Semua data absensi pada periode ini akan dihapus dan tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')

</body>

</html>