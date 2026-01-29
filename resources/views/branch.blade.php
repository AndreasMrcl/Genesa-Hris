<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manajemen Cabang</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
                    <h1 class="font-bold text-2xl text-gray-800">
                        <i class="fas fa-building text-cyan-600"></i> 
                        Manajemen Cabang
                    </h1>
                    <p class="text-sm text-gray-500">
                        Kelola lokasi dan kategori cabang perusahaan
                    </p>
                </div>
                <button id="addBtn"
                    class="px-6 py-3 bg-cyan-600 text-white rounded-lg shadow-md hover:bg-cyan-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> 
                    Tambah Cabang
                </button>
            </div>

            <!-- Table -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Nama Cabang</th>
                                <th class="p-4 font-bold">Kategori</th>
                                <th class="p-4 font-bold">Kontak</th>
                                <th class="p-4 font-bold text-center">GPS</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($branches as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium">
                                        <div class="flex items-center justify-center">
                                            {{ $no++ }}
                                        </div>
                                    </td>
                                    <td class="p-4 space-y-1">
                                        <a href="{{ route('outlet', ['branchId' => $item->id]) }}" class="group block">
                                            <div class="font-bold text-gray-900 text-base group-hover:text-cyan-600">{{ $item->name }}</div>
                                            <div class="text-xs text-gray-400">Created:
                                                {{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</div>
                                        </a>
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
                                    <td class="p-4 text-center">
                                        @if($item->latitude && $item->longitude)
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-xs font-mono text-gray-600 bg-green-50 px-2 py-1 rounded border border-green-200">
                                                    <i class="fas fa-check-circle text-green-600"></i> Set
                                                </span>
                                                <span class="text-[10px] text-gray-400">{{ number_format($item->gps_radius ?? 5000) }}m</span>
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded border border-red-200">
                                                <i class="fas fa-times-circle"></i> Not Set
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <button
                                                class="editBtn w-10 h-10 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}" 
                                                data-name="{{ $item->name }}"
                                                data-phone="{{ $item->phone }}" 
                                                data-address="{{ $item->address }}"
                                                data-category="{{ $item->category }}"
                                                data-latitude="{{ $item->latitude }}"
                                                data-longitude="{{ $item->longitude }}"
                                                data-gps-radius="{{ $item->gps_radius ?? 5000 }}"
                                                title="Edit">
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

        </div>
    </main>

    <!-- ADD MODAL -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-0 w-full max-w-2xl shadow-2xl relative my-5 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl sticky top-0 z-10">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-8 h-8 bg-cyan-100 rounded-full flex items-center justify-center text-cyan-600">
                        <i class="fas fa-building"></i>
                    </div>
                    Tambah Cabang
                </h2>
                <button id="closeAddModal" class="text-gray-400 hover:text-red-500 transition text-2xl leading-none">&times;</button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-6 overflow-y-auto flex-grow">
                <form id="addForm" method="post" action="{{ route('postbranch') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Cabang <span class="text-red-500">*</span></label>
                            <input type="text" name="name"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500"
                                required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="category"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500"
                                required>
                                <option value="general">Umum</option>
                                <option value="food_beverage">Makanan & Minuman</option>
                                <option value="retail">Ritel</option>
                                <option value="hospitality">Perhotelan</option>
                                <option value="education">Pendidikan</option>
                                <option value="creative">Kreatif</option>
                                <option value="health_beauty">Kesehatan & Kecantikan</option>
                                <option value="logistics">Logistik</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="phone"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500"
                                required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                            <textarea name="address" rows="2"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-cyan-500" required></textarea>
                        </div>
                    </div>

                    <!-- GPS Section -->
                    <div class="bg-indigo-50 p-5 rounded-xl border border-indigo-100">
                        <h3 class="text-sm font-bold text-indigo-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt"></i> Lokasi GPS
                        </h3>
                        
                        <div class="space-y-4">
                            <input type="text" id="addLocation" name="location_display" readonly
                                class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                placeholder="Klik peta atau gunakan pencarian untuk menandai lokasi" />
                            
                            <input type="hidden" name="latitude" id="addLatitude">
                            <input type="hidden" name="longitude" id="addLongitude">

                            <input type="text" id="addSearchLocation"
                                class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                placeholder="Cari lokasi (contoh: Monas Jakarta)..." />

                            <div class="flex gap-3">
                                <button type="button" onclick="searchAddLocation()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl w-full font-semibold shadow-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <button type="button" onclick="useMyLocationAdd()"
                                    class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-xl w-full font-semibold shadow-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-crosshairs"></i> Lokasi Saya
                                </button>
                            </div>

                            <div id="addMap" class="w-full h-64 rounded-xl border border-gray-300 shadow-sm overflow-hidden"></div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Radius GPS (meter)</label>
                                <input type="number" name="gps_radius" value="5000" min="100" max="50000"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500">
                                <p class="text-xs text-gray-400 mt-1">Jarak maksimal karyawan dapat absen (Default: 5000m = 5km)</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" id="cancelAdd"
                            class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-8 py-2.5 bg-cyan-600 text-white font-bold rounded-lg shadow-md hover:bg-cyan-700 transition">
                            Simpan Cabang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-0 w-full max-w-2xl shadow-2xl relative my-5 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl sticky top-0 z-10">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                        <i class="fas fa-edit"></i>
                    </div>
                    Edit Cabang
                </h2>
                <button id="closeModal" class="text-gray-400 hover:text-red-500 transition text-2xl leading-none">&times;</button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-6 overflow-y-auto flex-grow">
                <form id="editForm" method="post" class="space-y-5">
                    @csrf @method('put')
                    
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Cabang <span class="text-red-500">*</span></label>
                            <input type="text" id="editName" name="name"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select id="editCategory" name="category"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="general">Umum</option>
                                <option value="food_beverage">Makanan & Minuman</option>
                                <option value="retail">Ritel</option>
                                <option value="hospitality">Perhotelan</option>
                                <option value="education">Pendidikan</option>
                                <option value="creative">Kreatif</option>
                                <option value="health_beauty">Kesehatan & Kecantikan</option>
                                <option value="logistics">Logistik</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon <span class="text-red-500">*</span></label>
                            <input type="text" id="editPhone" name="phone"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="editAddress" name="address" rows="2"
                                class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500" required></textarea>
                        </div>
                    </div>

                    <!-- GPS Section -->
                    <div class="bg-blue-50 p-5 rounded-xl border border-blue-100">
                        <h3 class="text-sm font-bold text-blue-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt"></i> Lokasi GPS
                        </h3>
                        
                        <div class="space-y-4">
                            <input type="text" id="editLocation" name="location_display" readonly
                                class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                placeholder="Klik peta atau gunakan pencarian untuk menandai lokasi" />
                            
                            <input type="hidden" name="latitude" id="editLatitude">
                            <input type="hidden" name="longitude" id="editLongitude">

                            <input type="text" id="editSearchLocation"
                                class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                placeholder="Cari lokasi..." />

                            <div class="flex gap-3">
                                <button type="button" onclick="searchEditLocation()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl w-full font-semibold shadow-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <button type="button" onclick="useMyLocationEdit()"
                                    class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-xl w-full font-semibold shadow-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-crosshairs"></i> Lokasi Saya
                                </button>
                            </div>

                            <div id="editMap" class="w-full h-64 rounded-xl border border-gray-300 shadow-sm overflow-hidden"></div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Radius GPS (meter)</label>
                                <input type="number" id="editGpsRadius" name="gps_radius" value="5000" min="100" max="50000"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-400 mt-1">Jarak maksimal karyawan dapat absen (Default: 5000m = 5km)</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" id="closeEditModalBtn"
                            class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition">
                            Perbarui Cabang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#myTable', {});

            const addModal = $('#addModal');
            const editModal = $('#editModal');

            // Modal Toggles
            $('#addBtn').click(() => {
                addModal.removeClass('hidden');
                setTimeout(() => initAddMap(), 100);
            });
            
            $('#closeAddModal, #cancelAdd').click(() => addModal.addClass('hidden'));
            $('#closeModal, #closeEditModalBtn').click(() => editModal.addClass('hidden'));

            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
                if (e.target === editModal[0]) editModal.addClass('hidden');
            });

            // Edit Logic
            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                $('#editName').val(btn.data('name'));
                $('#editPhone').val(btn.data('phone'));
                $('#editAddress').val(btn.data('address'));
                $('#editCategory').val(btn.data('category'));
                $('#editGpsRadius').val(btn.data('gps-radius'));
                
                const lat = btn.data('latitude');
                const lon = btn.data('longitude');
                
                if (lat && lon) {
                    $('#editLatitude').val(lat);
                    $('#editLongitude').val(lon);
                    $('#editLocation').val(`Lat: ${parseFloat(lat).toFixed(6)}, Lon: ${parseFloat(lon).toFixed(6)}`);
                }
                
                $('#editForm').attr('action', `/branch/${btn.data('id')}/update`);
                editModal.removeClass('hidden');
                
                setTimeout(() => initEditMap(lat, lon), 100);
            });

            // Delete Confirm
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus Cabang?',
                    text: 'Data cabang yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // ===== MAP FUNCTIONS =====
        let addMap, addMarker;
        let editMap, editMarker;

        // Initialize Add Map
        function initAddMap() {
            if (addMap) {
                addMap.remove();
            }
            
            addMap = L.map('addMap').setView([-6.21462, 106.84513], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(addMap);
            
            addMarker = L.marker([-6.21462, 106.84513], {draggable: true}).addTo(addMap);
            addMarker.bindPopup('Drag marker atau klik peta').openPopup();
            
            addMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateAddCoordinates(pos.lat, pos.lng);
            });
            
            addMap.on('click', function(e) {
                addMarker.setLatLng(e.latlng);
                updateAddCoordinates(e.latlng.lat, e.latlng.lng);
            });
        }

        function updateAddCoordinates(lat, lng) {
            document.getElementById('addLatitude').value = lat;
            document.getElementById('addLongitude').value = lng;
            document.getElementById('addLocation').value = `Lat: ${lat.toFixed(6)}, Lon: ${lng.toFixed(6)}`;
        }

        function searchAddLocation() {
            const searchInput = document.getElementById('addSearchLocation').value;
            if (searchInput) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchInput)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const location = data[0];
                            addMap.setView([location.lat, location.lon], 15);
                            addMarker.setLatLng([location.lat, location.lon]);
                            updateAddCoordinates(location.lat, location.lon);
                        } else {
                            alert('Lokasi tidak ditemukan');
                        }
                    });
            }
        }

        function useMyLocationAdd() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    addMap.setView([lat, lon], 15);
                    addMarker.setLatLng([lat, lon]);
                    updateAddCoordinates(lat, lon);
                });
            } else {
                alert("Geolocation tidak didukung browser Anda.");
            }
        }

        // Initialize Edit Map
        function initEditMap(lat, lng) {
            if (editMap) {
                editMap.remove();
            }
            
            const defaultLat = lat || -6.21462;
            const defaultLon = lng || 106.84513;
            
            editMap = L.map('editMap').setView([defaultLat, defaultLon], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(editMap);
            
            editMarker = L.marker([defaultLat, defaultLon], {draggable: true}).addTo(editMap);
            editMarker.bindPopup('Drag marker atau klik peta');
            
            editMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateEditCoordinates(pos.lat, pos.lng);
            });
            
            editMap.on('click', function(e) {
                editMarker.setLatLng(e.latlng);
                updateEditCoordinates(e.latlng.lat, e.latlng.lng);
            });
        }

        function updateEditCoordinates(lat, lng) {
            document.getElementById('editLatitude').value = lat;
            document.getElementById('editLongitude').value = lng;
            document.getElementById('editLocation').value = `Lat: ${lat.toFixed(6)}, Lon: ${lng.toFixed(6)}`;
        }

        function searchEditLocation() {
            const searchInput = document.getElementById('editSearchLocation').value;
            if (searchInput) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchInput)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const location = data[0];
                            editMap.setView([location.lat, location.lon], 15);
                            editMarker.setLatLng([location.lat, location.lon]);
                            updateEditCoordinates(location.lat, location.lon);
                        } else {
                            alert('Lokasi tidak ditemukan');
                        }
                    });
            }
        }

        function useMyLocationEdit() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    editMap.setView([lat, lon], 15);
                    editMarker.setLatLng([lat, lon]);
                    updateEditCoordinates(lat, lon);
                });
            } else {
                alert("Geolocation tidak didukung browser Anda.");
            }
        }
    </script>
    @include('sweetalert::alert')
    @include('layout.loading')
</body>

</html>