<!DOCTYPE html>
<html lang="en">

<head>
    <title>Profile</title>
    @include('layout.head')

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>

<body class="bg-gray-50">

    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')

        <div class="p-6 space-y-6">

            <!-- HEADER -->
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-slate-600"></i> Profile Settings
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your personal profile and company information</p>
                </div>
            </div>

            <!-- SUCCESS ALERT -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <!-- MAIN CARD -->
            <div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6 space-y-10">

                <!-- PERSONAL INFO -->
                <section>
                    <h2 class="font-bold text-sm text-indigo-600 uppercase tracking-wider mb-4 border-b pb-2">
                        <i class="fa-solid fa-user mr-1"></i> Personal Information
                    </h2>

                    <div class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ auth()->user()->email }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    readonly>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- COMPANY INFO -->
                <section>
                    <h2 class="font-bold text-sm text-emerald-600 uppercase tracking-wider mb-4 border-b pb-2">
                        <i class="fa-solid fa-building mr-1"></i> Company Information
                    </h2>

                    <form action="{{ route('updatecompany', $userCompany->id) }}" method="POST" class="space-y-6"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Company Name</label>
                                <input type="text" name="company" value="{{ $userCompany->company }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-emerald-500"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Bank</label>
                                <input type="text" name="bank" value="{{ $userCompany->bank }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">No Rekening</label>
                                <input type="text" name="no_rek" value="{{ $userCompany->no_rek }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                                <textarea name="location"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-emerald-500" rows="3"
                                    readonly>{{ $userCompany->location }}</textarea>
                            </div>

                        </div>

                        <!-- MAP -->
                        <div id="map"
                            class="w-full h-64 rounded-xl overflow-hidden shadow-sm border border-gray-300"></div>

                        <!-- SAVE BUTTON -->
                        <div class="pt-4 flex justify-end border-t border-gray-100">
                            <button type="submit"
                                class="px-8 py-3 bg-slate-800 text-white font-bold rounded-lg shadow-lg hover:bg-slate-900 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                <i class="fas fa-save"></i> Save Company Info
                            </button>
                        </div>
                    </form>

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
