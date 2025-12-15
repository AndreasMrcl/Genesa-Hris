<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Company</title>
    @include('layout.head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- end sidenav -->
    <main class="w-5/6 mx-auto">
        @include('layout.navbar')

        <div class="p-6 space-y-6">

            <!-- HEADER -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                    <i class="fas fa-building text-gray-800"></i>
                    Add Company
                </h1>
                <p class="text-sm text-gray-500">Register your company information accurately</p>
            </div>

            <!-- MAIN CONTENT CARD -->
            <div class="w-full rounded-xl bg-white shadow-sm mx-auto p-6 space-y-10 border border-gray-200">

                @if ($errors->any())
                    <div class="bg-red-200 text-red-800 p-4 rounded-xl border border-red-300">
                        <ul class="space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="companyForm" class="space-y-10" method="post" action="{{ route('postcompany') }}"
                    enctype="multipart/form-data">
                    @csrf @method('post')

                    <!-- PENANGGUNG JAWAB -->
                    <section>
                        <h2 class="font-bold text-xl mb-4 text-gray-800">Penanggung Jawab</h2>

                        <div
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 bg-gray-50 border border-gray-200 rounded-xl">

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Name</label>
                                <input type="text"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    id="name" name="name" value="{{ old('name') }}" required />
                                @error('name')
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Nomor WhatsApp</label>
                                <input type="text"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    id="no_telpon" name="no_telpon" value="{{ old('no_telpon') }}" required />
                                @error('no_telpon')
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Foto KTP</label>
                                <input type="file"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    id="ktp" name="ktp" required>
                                @error('ktp')
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </section>

                    <!-- BANK -->
                    <section>
                        <h2 class="font-bold text-xl mb-4 text-gray-800">Rekening Bank</h2>

                        <div
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 bg-gray-50 border border-gray-200 rounded-xl">

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Atas Nama</label>
                                <input type="text"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    id="atas_nama" name="atas_nama" value="{{ old('atas_nama') }}" required />
                            </div>

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Bank</label>
                                <input type="text"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    id="bank" name="bank" value="{{ old('bank') }}" required />
                            </div>

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">No Rekening</label>
                                <input type="number"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    id="no_rek" name="no_rek" value="{{ old('no_rek') }}" required>
                            </div>

                        </div>
                    </section>

                    <!-- COMPANY -->
                    <section>
                        <h2 class="font-bold text-xl mb-4 text-gray-800">Company</h2>

                        <div class="space-y-4 p-5 bg-gray-50 border border-gray-200 rounded-xl">

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Company Name</label>
                                <input type="text"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    id="company" name="company" value="{{ old('company') }}" required />
                            </div>

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Location</label>
                                <input type="text"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    id="location" name="location" value="{{ old('location') }}" required readonly />

                                <input type="text" id="searchLocation"
                                    class="bg-white border border-gray-300 text-gray-900 p-3 rounded-xl w-full"
                                    placeholder="Search location..." />

                                <div class="flex gap-3">
                                    <button type="button" id="searchBtn"
                                        class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl w-full font-semibold shadow-sm">
                                        Search
                                    </button>

                                    <button type="button" id="locateBtn"
                                        class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-xl w-full font-semibold shadow-sm">
                                        Use My Location
                                    </button>
                                </div>

                                <div id="map"
                                    class="w-full h-64 rounded-xl border border-gray-300 shadow-sm overflow-hidden">
                                </div>
                            </div>

                        </div>
                    </section>

                    <div class="pt-4 flex justify-end border-t border-gray-100">
                        <button type="submit"
                            class="px-8 py-3 bg-slate-800 text-white font-bold rounded-lg shadow-lg hover:bg-slate-900 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <i class="fas fa-save"></i> Save Company Info
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var map = L.map('map').setView([-6.21462, 106.84513], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Add a marker
        var marker = L.marker([-6.21462, 106.84513]).addTo(map);
        marker.bindPopup('Your Company Location').openPopup();

        // Handle the search button click to get coordinates from the geocoding service
        document.getElementById('searchBtn').onclick = function() {
            var searchInput = document.getElementById('searchLocation').value;
            if (searchInput) {
                var url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchInput)}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            var location = data[0];
                            map.setView([location.lat, location.lon], 15);
                            marker.setLatLng([location.lat, location.lon]);
                            document.getElementById('location').value = location
                                .display_name; // Set descriptive location
                        } else {
                            alert('Location not found');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching location:', error);
                    });
            }
        };

        document.getElementById('locateBtn').onclick = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;

                    // Update the map view
                    map.setView([lat, lon], 15);
                    marker.setLatLng([lat, lon]);

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.display_name) {
                                // Set the input value to the location name
                                document.getElementById('location').value = data.display_name;
                            } else {
                                document.getElementById('location').value = "Location name not found";
                            }
                        })
                        .catch(error => {
                            console.error("Error with reverse geocoding:", error);
                            document.getElementById('location').value = "Error fetching location name";
                        });
                }, function(error) {
                    // Handle geolocation errors
                    alert("Error fetching your location: " + error.message);
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        };

        const form = document.getElementById('companyForm');
        
    </script>

    @include('layout.loading')

</body>

</html>
