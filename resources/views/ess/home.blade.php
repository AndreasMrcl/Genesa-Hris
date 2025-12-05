<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Home</title>
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
    <div class="bg-gradient-to-br from-sky-800 to-sky-700 p-6 rounded-b-3xl shadow-xl relative overflow-hidden">

        <!-- Subtle decorative circles -->
        <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-10 bottom-0 w-28 h-28 bg-white/5 rounded-full blur-xl"></div>

        <div class="relative flex justify-between items-center">

            <!-- LEFT CONTENT -->
            <div class="space-y-3">
                <!-- Company -->
                <h1 class="text-2xl font-bold text-white flex items-center gap-2 drop-shadow-md">
                    <i class="fas fa-building text-white/90"></i>
                    {{ $employee->compani->company }}
                </h1>

                <!-- User Info -->
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0ea5e9&color=fff"
                        class="w-12 h-12 rounded-xl shadow-md border border-white/30" alt="avatar">

                    <div>
                        <p class="text-white text-base font-semibold leading-tight">
                            Hi, {{ auth()->user()->name }}
                        </p>
                        <p class="text-sm text-white/80 leading-tight">
                            {{ auth()->user()->position }}
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
                📢 Pengumuman: Gajian bulan Desember akan dipercepat menjadi tanggal 27.
                • Libur Natal dimulai tanggal 24–26 Desember.
                • Meeting bulanan akan diadakan tanggal 15 pukul 09.00 WIB.
                • Mohon lengkapi approval lembur sebelum tanggal 10.
            </div>
        </div>
    </div>

    <!-- QUICK MENU -->
    <div class="p-2">
        <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Quick Menu</h2>

            <div class="grid grid-cols-3 gap-4 text-center">

                <!-- Attendance -->
                <a href="{{ route('ess-attendance') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-cyan-100 text-cyan-600 rounded-xl shadow-sm">
                        <i class="fas fa-fingerprint text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Attendance</p>
                </a>

                <!-- Leave -->
                <a href="{{ route('ess-leave') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-sky-100 text-sky-600 rounded-xl shadow-sm">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Leave</p>
                </a>

                <!-- Overtime -->
                <a href="{{ route('ess-overtime') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl shadow-sm">
                        <i class="fas fa-business-time text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Overtime</p>
                </a>

                <!-- Note -->
                <a href="{{ route('ess-note') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-yellow-100 text-yellow-600 rounded-xl shadow-sm">
                        <i class="fas fa-note-sticky text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Note</p>
                </a>

                <!-- Payroll -->
                <a href="{{ route('ess-payroll') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-xl shadow-sm">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Payroll</p>
                </a>

                <!-- Organization -->
                <a href="{{ route('ess-organization') }}" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-gray-100 text-gray-600 rounded-xl shadow-sm">
                        <i class="fas fa-user-circle text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Organization</p>
                </a>

            </div>

        </div>
    </div>

    <!-- TODAY STATUS -->
    <div class="p-2 pb-20">
        <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Today's Status</h2>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Clock In</span>
                    <span class="font-semibold">
                        {{ $attendance?->clock_in ? $attendance->clock_in : '-' }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Clock Out</span>
                    <span class="font-semibold">
                        {{ $attendance?->clock_out ? $attendance->clock_out : '-' }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Status</span>
                    @php
                        $statusColor = match ($attendance->status) {
                            'present' => 'bg-green-100 text-green-700 border-green-200',
                            'absent' => 'bg-red-100 text-red-700 border-red-200',
                            'leave' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'sick' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            'off' => 'bg-gray-100 text-gray-600 border-gray-200',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp

                    <span
                        class="{{ $statusColor }} px-3 py-1 rounded-full text-xs font-bold border uppercase shadow-sm">
                        {{ $attendance->status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

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
