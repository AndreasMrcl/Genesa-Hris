<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Absen</title>
    @include('ess.layout.head')
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto">


    <!-- BOTTOM BAR -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg md:max-w-sm mx-auto">
        <div class="grid grid-cols-3 text-center py-2">

            <!-- Home -->
            <a href="{{ route('ess-home') }}"
                class="flex flex-col items-center {{ request()->routeIs('ess-home') ? 'text-sky-600' : 'text-gray-600 hover:text-sky-600' }}">
                <i class="fas fa-home text-xl"></i>
                <span class="text-xs font-semibold mt-1">Home</span>
            </a>

            <!-- Attendance (ACTIVE) -->
            <a href="{{ route('ess-absen') }}"
                class="flex flex-col items-center {{ request()->routeIs('ess-absen') ? 'text-sky-600' : 'text-gray-600 hover:text-sky-600' }}">
                <i class="fas fa-fingerprint text-xl"></i>
                <span class="text-xs font-semibold mt-1">Absen</span>
            </a>

            <!-- Profile -->
            <a href="{{ route('ess-profil') }}"
                class="flex flex-col items-center {{ request()->routeIs('ess-profil') ? 'text-sky-600' : 'text-gray-600 hover:text-sky-600' }}">
                <i class="fas fa-user text-xl"></i>
                <span class="text-xs font-semibold mt-1">Profile</span>
            </a>

        </div>
    </div>

</body>

</html>
