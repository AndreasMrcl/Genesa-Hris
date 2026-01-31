<!DOCTYPE html>
<html lang="en">
<head>
    <title>ESS | GPS Attendance</title>
    @include('ess.layout.head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .modal-overlay {
            z-index: 9999 !important;
        }
        #map {
            z-index: 1;
        }
        .leaflet-container {
            z-index: 1 !important;
        }
        .leaflet-control-container {
            z-index: 2 !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto min-h-screen flex flex-col shadow-lg border-x border-gray-100">

    <!-- HEADER -->
    <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 border-b border-gray-200">
        <div class="p-3 flex items-center justify-between">
            <a href="{{ route('ess-home') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left text-base"></i>
            </a>
            <h1 class="font-bold text-base text-gray-800">GPS Attendance</h1>
            <div class="w-9"></div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="p-4 flex-grow space-y-4">
        
        @if(isset($todaySchedule))
            @if($todaySchedule && $todaySchedule->shift)
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4 border border-green-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="text-xs uppercase font-bold text-green-600 mb-1">Jadwal Hari Ini</p>
                            <p class="text-lg font-bold text-gray-800">{{ $todaySchedule->shift->name }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-clock"></i> 
                                {{ \Carbon\Carbon::parse($todaySchedule->shift->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($todaySchedule->shift->end_time)->format('H:i') }}
                                @if($todaySchedule->shift->is_cross_day)
                                    <span class="text-purple-600 font-bold">(+1 Hari)</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-center">
                            <div class="w-8 h-8 rounded-full" style="background-color: {{ $todaySchedule->shift->color }}"></div>
                        </div>
                    </div>
                </div>
            @elseif($todaySchedule && !$todaySchedule->shift)
                <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-2xl p-4 border border-orange-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-coffee text-white text-xl"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="text-xs uppercase font-bold text-orange-600 mb-1">Status Hari Ini</p>
                            <p class="text-lg font-bold text-gray-800">Hari Libur</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-info-circle"></i> Tidak perlu melakukan absensi
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-4 border border-red-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                    <div class="flex-grow">
                        <p class="text-xs uppercase font-bold text-red-600 mb-1">Tidak Ada Jadwal</p>
                        <p class="text-sm font-bold text-gray-800">Anda tidak memiliki jadwal hari ini</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-info-circle"></i> Silakan hubungi Admin/Koordinator
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Status Card -->
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg">
            <p class="text-xs uppercase font-bold text-indigo-200 mb-2">Status Hari Ini</p>
            @if($todayAttendance)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-bold">
                            {{ $todayAttendance->check_in_time ? $todayAttendance->check_in_time->format('H:i') : '-' }}
                        </p>
                        <p class="text-xs text-indigo-100">Check-In</p>
                    </div>
                    @if($todayAttendance->check_out_time)
                        <div>
                            <p class="text-2xl font-bold">{{ $todayAttendance->check_out_time->format('H:i') }}</p>
                            <p class="text-xs text-indigo-100">Check-Out</p>
                        </div>
                    @else
                        <div class="flex items-center justify-end">
                            <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold">Aktif</span>
                        </div>
                    @endif
                </div>
                @if($todayAttendance->work_duration)
                    <div class="mt-3 pt-3 border-t border-indigo-500/30">
                        <p class="text-xs text-indigo-200">Durasi Kerja</p>
                        <p class="text-lg font-bold">{{ $todayAttendance->work_duration }}</p>
                    </div>
                @endif
            @else
                <p class="text-lg font-bold">Belum ada absensi hari ini</p>
                <p class="text-xs text-indigo-200 mt-1">Silakan check-in untuk memulai</p>
            @endif
        </div>

        <!-- Map Preview -->
        @if($workLocation && $workLocation->latitude && $workLocation->longitude)
        <div class="bg-white rounded-xl overflow-hidden shadow-md border border-gray-100" id="mapContainer">
            <div id="map" class="h-56 w-full"></div>
            <div class="p-3 bg-gray-50 border-t border-gray-100">
                <div class="flex items-start gap-2 text-xs">
                    <i class="fas fa-map-marker-alt text-red-500 mt-0.5"></i>
                    <div class="flex-grow">
                        <p class="font-bold text-gray-700">{{ $workLocation->name }}</p>
                        <p class="text-gray-500 text-[10px]">{{ $workLocation->address }}</p>
                        <p class="text-indigo-600 font-bold mt-1">Radius: {{ number_format($workLocation->gps_radius ?? 5000) }}m</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mb-2"></i>
            <p class="text-sm font-bold text-yellow-800">Lokasi Kerja Belum Diatur</p>
            <p class="text-xs text-yellow-600 mt-1">Hubungi Admin/HRD untuk mengatur lokasi kerja</p>
        </div>
        @endif

        <!-- Action Buttons -->
        @if($workLocation && $workLocation->latitude && $workLocation->longitude)
            @if(isset($todaySchedule) && $todaySchedule && $todaySchedule->shift)
                <div class="space-y-3">
                    @if(!$todayAttendance || !$todayAttendance->check_in_time)
                        <button onclick="openCheckInModal()" class="w-full py-4 bg-green-600 text-white font-bold rounded-xl shadow-lg hover:bg-green-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i> Check-In Sekarang
                        </button>
                    @elseif(!$todayAttendance->check_out_time)
                        <button onclick="openCheckOutModal()" class="w-full py-4 bg-orange-600 text-white font-bold rounded-xl shadow-lg hover:bg-orange-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-sign-out-alt"></i> Check-Out Sekarang
                        </button>
                    @else
                        <div class="w-full py-4 bg-gray-100 text-gray-500 font-bold rounded-xl text-center border-2 border-dashed border-gray-300">
                            <i class="fas fa-check-circle"></i> Absensi Hari Ini Selesai
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center">
                    <i class="fas fa-ban text-orange-600 text-2xl mb-2"></i>
                    <p class="text-sm font-bold text-orange-800">Tidak Dapat Absen</p>
                    <p class="text-xs text-orange-600 mt-1">
                        @if(!isset($todaySchedule) || !$todaySchedule)
                            Anda tidak memiliki jadwal hari ini
                        @else
                            Hari ini adalah hari libur Anda
                        @endif
                    </p>
                </div>
            @endif
        @endif

        <!-- Recent Logs -->
        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i> Riwayat 7 Hari Terakhir
            </h3>
            <div class="space-y-2">
                @forelse($recentLogs as $log)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div>
                            <p class="text-xs font-bold text-gray-700">{{ $log->attendance_date->format('d M Y') }}</p>
                            <p class="text-[10px] text-gray-500 flex items-center gap-1 mt-1">
                                <i class="fas fa-clock"></i>
                                {{ $log->check_in_time ? $log->check_in_time->format('H:i') : '-' }} - 
                                {{ $log->check_out_time ? $log->check_out_time->format('H:i') : '-' }}
                            </p>
                            @if($log->work_duration)
                                <p class="text-[10px] text-indigo-600 font-bold mt-0.5">
                                    <i class="fas fa-hourglass-half"></i> {{ $log->work_duration }}
                                </p>
                            @endif
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded border {{ $log->status_badge }}">
                            {{ ucfirst($log->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-8">
                        <i class="fas fa-inbox text-2xl mb-2 block text-gray-300"></i>
                        Belum ada riwayat absensi
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Check-In Modal -->
    <div id="checkInModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-end justify-center modal-overlay">
        <div class="bg-white rounded-t-3xl w-full max-w-sm p-6 pb-8 transform transition-all">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">Konfirmasi Check-In</h2>
                <button onclick="closeModal('checkInModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('ess-gps-check-in') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="latitude" id="checkInLat">
                <input type="hidden" name="longitude" id="checkInLon">
                
                <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Lokasi Anda:</p>
                    <p id="userLocationText" class="text-xs font-mono text-gray-700">Mendeteksi...</p>
                    <p id="distanceText" class="text-xs font-bold text-indigo-600 mt-1"></p>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="closeModal('checkInModal')" 
                        class="flex-1 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold">
                        Batal
                    </button>
                    <button type="submit" 
                        class="flex-1 py-3 bg-green-600 text-white rounded-xl font-bold">
                        Check-In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Check-Out Modal -->
    <div id="checkOutModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-end justify-center modal-overlay">
        <div class="bg-white rounded-t-3xl w-full max-w-sm p-6 pb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">Konfirmasi Check-Out</h2>
                <button onclick="closeModal('checkOutModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('ess-gps-check-out') }}" method="POST" enctype="multipart/form-data" id="checkOutForm">
                @csrf
                <input type="hidden" name="latitude" id="checkOutLat">
                <input type="hidden" name="longitude" id="checkOutLon">
                
                <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Lokasi Anda:</p>
                    <p id="userLocationTextOut" class="text-xs font-mono text-gray-700">Mendeteksi...</p>
                    <p id="distanceTextOut" class="text-xs font-bold text-indigo-600 mt-1"></p>
                </div>

                <!-- Early Leave Warning -->
                <div id="earlyLeaveWarning" class="hidden mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                        <div class="flex-grow">
                            <p class="text-xs font-bold text-yellow-800">Peringatan: Pulang Lebih Awal</p>
                            <p class="text-xs text-yellow-700 mt-1">Anda pulang sebelum jam kerja berakhir. Mohon berikan alasan.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 mb-2">
                        <i class="fas fa-clipboard-list"></i> Catatan <span id="notesRequired" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea name="notes" id="notesField" rows="3" 
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-indigo-500"
                        placeholder="Tambahkan catatan"></textarea>
                    <p class="text-xs text-gray-400 mt-1" id="notesHint">Catatan bersifat opsional</p>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="closeModal('checkOutModal')" 
                        class="flex-1 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold">
                        Batal
                    </button>
                    <button type="submit" 
                        class="flex-1 py-3 bg-orange-600 text-white rounded-xl font-bold">
                        Check-Out
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let userLat, userLon;
        const workLat = {{ $workLocation->latitude ?? 0 }};
        const workLon = {{ $workLocation->longitude ?? 0 }};
        const workRadius = {{ $workLocation->gps_radius ?? 5000 }};

        @if($workLocation && $workLocation->latitude && $workLocation->longitude)

        const map = L.map('map').setView([workLat, workLon], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        L.marker([workLat, workLon]).addTo(map)
            .bindPopup('<b>{{ $workLocation->name ?? "Lokasi Kerja" }}</b>').openPopup();

        L.circle([workLat, workLon], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.1,
            radius: workRadius
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                userLat = position.coords.latitude;
                userLon = position.coords.longitude;

                L.marker([userLat, userLon], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41]
                    })
                }).addTo(map).bindPopup('Lokasi Anda');

                map.fitBounds([
                    [workLat, workLon],
                    [userLat, userLon]
                ], { padding: [50, 50] });
            }, (error) => {
                console.error('Geolocation error:', error);
            });
        }
        @endif

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function formatDistance(meters) {
            if (meters < 1000) {
                return Math.round(meters) + ' m';
            }
            return (meters / 1000).toFixed(2) + ' km';
        }

        function openCheckInModal() {
            if (!userLat || !userLon) {
                alert('Mohon aktifkan akses lokasi!');
                return;
            }
            
            const distance = calculateDistance(userLat, userLon, workLat, workLon);
            
            document.getElementById('checkInLat').value = userLat;
            document.getElementById('checkInLon').value = userLon;
            document.getElementById('userLocationText').textContent = `Lat: ${userLat.toFixed(6)}, Lon: ${userLon.toFixed(6)}`;
            document.getElementById('distanceText').textContent = `Jarak dari kantor: ${formatDistance(distance)}`;
            
            document.getElementById('checkInModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openCheckOutModal() {
            if (!userLat || !userLon) {
                alert('Mohon aktifkan akses lokasi!');
                return;
            }
            
            const distance = calculateDistance(userLat, userLon, workLat, workLon);
            
            document.getElementById('checkOutLat').value = userLat;
            document.getElementById('checkOutLon').value = userLon;
            document.getElementById('userLocationTextOut').textContent = `Lat: ${userLat.toFixed(6)}, Lon: ${userLon.toFixed(6)}`;
            document.getElementById('distanceTextOut').textContent = `Jarak dari kantor: ${formatDistance(distance)}`;
            
            checkEarlyLeave();
            
            document.getElementById('checkOutModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function checkEarlyLeave() {
            @if($todayAttendance && isset($todaySchedule) && $todaySchedule && $todaySchedule->shift)
                const now = new Date();
                let shiftEnd = new Date();
                const shiftEndTime = "{{ $todaySchedule->shift->end_time }}".split(':');
                shiftEnd.setHours(parseInt(shiftEndTime[0]), parseInt(shiftEndTime[1]), 0);
                
                const isCrossDay = {{ $todaySchedule->shift->is_cross_day ? 'true' : 'false' }};
                if (isCrossDay) {
                    shiftEnd.setDate(shiftEnd.getDate() + 1);
                }
                
                const toleranceMs = 15 * 60000;
                const earliestAllowedCheckout = new Date(shiftEnd.getTime() - toleranceMs);
                
                if (now < earliestAllowedCheckout) {
                    const minutesEarly = Math.floor((shiftEnd - now) / 60000);
                    const hoursEarly = Math.floor(minutesEarly / 60);
                    const remainingMinutes = minutesEarly % 60;
                    
                    let earlyText = '';
                    if (hoursEarly > 0) {
                        earlyText = `${hoursEarly} jam ${remainingMinutes} menit`;
                    } else {
                        earlyText = `${minutesEarly} menit`;
                    }
                    
                    document.getElementById('earlyLeaveWarning').classList.remove('hidden');
                    document.getElementById('notesRequired').classList.remove('hidden');
                    document.getElementById('notesField').required = true;
                    document.getElementById('notesHint').textContent = 'Wajib diisi karena Anda pulang lebih awal';
                    document.getElementById('notesHint').classList.remove('text-gray-400');
                    document.getElementById('notesHint').classList.add('text-red-500');
                    
                    const warningText = document.querySelector('#earlyLeaveWarning p.text-yellow-700');
                    if (warningText) {
                        warningText.textContent = `Anda pulang ${earlyText} sebelum jam kerja berakhir. Mohon berikan alasan.`;
                    }
                } else {
                    resetNotesField();
                }
            @else
                resetNotesField();
            @endif
        }

        function resetNotesField() {
            document.getElementById('earlyLeaveWarning').classList.add('hidden');
            document.getElementById('notesRequired').classList.add('hidden');
            document.getElementById('notesField').required = false;
            document.getElementById('notesField').placeholder = 'Tambahkan catatan (opsional)';
            document.getElementById('notesHint').textContent = 'Catatan bersifat opsional';
            document.getElementById('notesHint').classList.remove('text-red-500');
            document.getElementById('notesHint').classList.add('text-gray-400');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = 'auto';

            if (id === 'checkOutModal') {
                document.getElementById('checkOutForm').reset();
                resetNotesField();
            }
        }

        document.getElementById('checkInModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('checkInModal');
            }
        });

        document.getElementById('checkOutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('checkOutModal');
            }
        });

        setInterval(() => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    userLat = position.coords.latitude;
                    userLon = position.coords.longitude;
                });
            }
        }, 10000);
    </script>

    @include('sweetalert::alert')
</body>
</html>