<!DOCTYPE html>
<html lang="en">

<head>
    <title>Profile</title>
    @include('layout.head')

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>

<body class="bg-gray-100">

    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            {{-- HEADER --}}
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-gray-800"></i>
                    Profile
                </h1>
                <p class="text-sm text-gray-500">Manage your profile and company information</p>
            </div>

            {{-- MAIN CARD --}}
            <div class="w-full rounded-xl bg-white shadow-sm mx-auto p-6 space-y-10 border border-gray-200">

                {{-- PROFILE --}}
                <section>
                    <h2 class="font-bold text-xl mb-4 text-gray-800">Personal Info</h2>

                    <div class="space-y-4 p-5 bg-gray-50 border border-gray-200 rounded-xl">
                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">Name</span>
                            <span class="text-gray-900 font-semibold">{{ auth()->user()->name }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">Email</span>
                            <span class="text-gray-900 font-semibold">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </section>

                {{-- COMPANY --}}
                <section>
                    <h2 class="font-bold text-xl mb-4 text-gray-800">Company</h2>

                    <div class="space-y-4 p-5 bg-gray-50 border border-gray-200 rounded-xl">

                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">Name</span>
                            <span class="text-gray-900 font-semibold">{{ $userCompany->company }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">Bank</span>
                            <span class="text-gray-900 font-semibold">{{ $userCompany->bank }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">No Rekening</span>
                            <span class="text-gray-900 font-semibold">{{ $userCompany->no_rek }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">Address</span>
                            <span class="text-gray-900 font-semibold">{{ $userCompany->location }}</span>
                        </div>

                        {{-- MAP AREA --}}
                        <div id="map"
                            class="w-full h-64 rounded-xl overflow-hidden shadow-sm border border-gray-300"></div>

                        {{-- EDIT BUTTON --}}
                        <a href=""
                            class="block text-center mt-3 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-sm">
                            Edit Company
                        </a>
                    </div>
                </section>

            </div>
        </div>
    </main>

    @include('sweetalert::alert')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var address = encodeURIComponent("{{ $userCompany->location }}");

            fetch(`https://nominatim.openstreetmap.org/search?q=${address}&format=json&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        var latitude = data[0].lat;
                        var longitude = data[0].lon;

                        var map = L.map('map').setView([latitude, longitude], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap'
                        }).addTo(map);

                        L.marker([latitude, longitude]).addTo(map)
                            .bindPopup('<b>{{ $userCompany->company }}</b><br>{{ $userCompany->location }}')
                            .openPopup();
                    }
                })
                .catch(err => console.error("Map error:", err));
        });
    </script>

</body>

</html>
