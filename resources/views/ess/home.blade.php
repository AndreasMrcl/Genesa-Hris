<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Beranda</title>
    @include('ess.layout.head')

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            display: inline-block;
            animation: marquee 15s linear infinite;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto">

    <!-- HEADER -->
    <div class="bg-linear-to-br from-sky-800 to-sky-700 p-6 rounded-b-3xl shadow-xl relative overflow-hidden">

        <!-- Subtle decorative circles -->
        <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-10 bottom-0 w-28 h-28 bg-white/5 rounded-full blur-xl"></div>

        <div class="relative flex justify-between items-center">

            <!-- LEFT CONTENT -->
            <div class="space-y-3">
                <!-- Company -->
                <h1 class="text-2xl font-bold text-white flex items-center gap-2 drop-shadow-md">
                    <i class="fas fa-building text-white/90"></i>
                    {{ $compani->company }}
                </h1>

                <!-- User Info -->
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0ea5e9&color=fff"
                        class="w-12 h-12 rounded-xl shadow-md border border-white/30" alt="avatar">

                    <div>
                        <p class="text-white text-base font-semibold leading-tight">
                            Halo, {{ auth()->user()->name }}
                        </p>
                        <p class="text-sm text-white/80 leading-tight">
                            {{ auth()->user()->position->name }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- LOGOUT BUTTON -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    class="bg-white/90 backdrop-blur-md px-3 py-2 rounded-xl shadow-md hover:bg-white transition-all duration-200 border border-gray-100">
                    <i class="material-icons text-black rotate-180 text-[22px]">logout</i>
                </button>
            </form>

        </div>
    </div>

    <!-- ANNOUNCEMENT -->
    <div class="p-2">
        <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-xl p-3 shadow-sm overflow-hidden">
            <div class="animate-marquee whitespace-nowrap text-sm font-semibold">

                @foreach ($announcements as $item)
                    📢 {{ $item->content ?? 'N/A' }} 
                @endforeach
            </div>
        </div>
    </div>

    <!-- QUICK MENU -->
    <div class="p-2">
        <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Menu Cepat</h2>

            <div class="grid grid-cols-3 gap-4 text-center">

                <!-- Schedule -->
                <a href="{{ route('ess-schedule') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-cyan-100 text-cyan-600 rounded-xl shadow-sm">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Jadwal</p>
                </a>

                <!-- Attendance -->
                <a href="{{ route('ess-attendance') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-cyan-100 text-cyan-600 rounded-xl shadow-sm">
                        <i class="fas fa-fingerprint text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Riwayat Absensi</p>
                </a>

                <!-- Leave -->
                {{-- <a href="{{ route('ess-leave') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-sky-100 text-sky-600 rounded-xl shadow-sm">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Cuti</p>
                </a> --}}

                <!-- Overtime -->
                {{-- <a href="{{ route('ess-overtime') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl shadow-sm">
                        <i class="fas fa-business-time text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Lembur</p>
                </a> --}}

                <!-- Note -->
                <a href="{{ route('ess-note') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-yellow-100 text-yellow-600 rounded-xl shadow-sm">
                        <i class="fas fa-note-sticky text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Catatan</p>
                </a>

                <!-- Payroll -->
                <a href="{{ route('ess-payroll') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-xl shadow-sm">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Gaji</p>
                </a>
                <a href="{{ route('ess-gps-attendance') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-xl shadow-sm">
                        <i class="fas fa-map-marker-alt text-indigo-600 text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Absensi</p>
                </a>
            </div>

            @if(Auth::guard('employee')->user()->position->is_head)
                <div class="pt-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Manajemen Tim</p>
                    
                    <div class="grid grid-cols-3 gap-y-6 gap-x-4 text-center">
                        
                        <!-- Manage Schedule -->
                        <a href="{{ route('ess-coordinator-schedule') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-14 h-14 flex items-center justify-center bg-gray-800 text-white rounded-xl shadow-md group-hover:scale-105 transition duration-300">
                                <i class="fas fa-users-cog text-xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-700">Jadwal Tim</p>
                        </a>

                        <!-- Manage Leave -->
                        <a href="{{ route('ess-coordinator-leave') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-14 h-14 flex items-center justify-center bg-gray-800 text-white rounded-xl shadow-md group-hover:scale-105 transition duration-300">
                                <i class="fas fa-calendar-check text-xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-700">Kelola Cuti</p>
                        </a>

                        <!-- Manage Overtime -->
                        <a href="{{ route('ess-coordinator-overtime') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-14 h-14 flex items-center justify-center bg-gray-800 text-white rounded-xl shadow-md group-hover:scale-105 transition duration-300">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-700">Kelola Lembur</p>
                        </a>

                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- BOTTOM BAR -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg md:max-w-sm mx-auto">
        <div class="grid grid-cols-2 text-center py-2">

            <!-- Home -->
            <a href="{{ route('ess-home') }}"
                class="flex flex-col items-center {{ request()->routeIs('ess-home') ? 'text-sky-600' : 'text-gray-600 hover:text-sky-600' }}">
                <i class="fas fa-home text-xl"></i>
                <span class="text-xs font-semibold mt-1">Beranda</span>
            </a>

            <!-- Profile -->
            <a href="{{ route('ess-profil') }}"
                class="flex flex-col items-center {{ request()->routeIs('ess-profil') ? 'text-sky-600' : 'text-gray-600 hover:text-sky-600' }}">
                <i class="fas fa-user text-xl"></i>
                <span class="text-xs font-semibold mt-1">Profil</span>
            </a>
        </div>
    </div>

</body>

</html>