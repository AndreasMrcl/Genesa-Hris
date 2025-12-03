<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Home</title>
    @include('ess.layout.head')
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto">

    <!-- HEADER -->
    <div class="bg-sky-800 p-5 rounded-b-3xl shadow-md">
        <div class="flex justify-between items-center">
            <div. class="space-y-4">
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-building text-white"></i>
                    {{ $userCompany->company }}
                </h1>
                <p class="text-lg text-white/80">Hi, {{ auth()->user()->name }}</p>
            </div.>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="bg-white p-2 rounded-lg shadow hover:bg-gray-100 transition">
                    <i class="material-icons text-black rotate-180">logout</i>
                </button>
            </form>
        </div>
    </div>

    <!-- QUICK MENU -->
    <div class="p-4">
        <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Quick Menu</h2>

            <div class="grid grid-cols-4 gap-4 text-center">
                <a href="" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-cyan-100 text-cyan-600 rounded-xl shadow-sm">
                        <i class="fas fa-fingerprint text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Attendance</p>
                </a>

                <a href="" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-sky-100 text-sky-600 rounded-xl shadow-sm">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Leave</p>
                </a>

                <a href="" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-xl shadow-sm">
                        <i class="fas fa-money-check text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Payroll</p>
                </a>

                <a href="" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl shadow-sm">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Profile</p>
                </a>

                <a href="" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl shadow-sm">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Profile</p>
                </a>

                <a href="" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl shadow-sm">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Profile</p>
                </a>

                <a href="" class="flex flex-col items-center gap-2">
                    <div
                        class="w-14 h-14 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl shadow-sm">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Profile</p>
                </a>
            </div>
        </div>
    </div>

    <!-- TODAY STATUS -->
    <div class="p-4">
        <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Today's Status</h2>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Clock In</span>
                    {{-- <span class="font-semibold">
                        {{ $today?->clock_in ? $today->clock_in : '-' }}
                    </span> --}}
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Clock Out</span>
                    {{-- <span class="font-semibold">
                        {{ $today?->clock_out ? $today->clock_out : '-' }}
                    </span> --}}
                </div>

                <div class="mt-4">
                    {{-- <span class="px-4 py-2 rounded-lg text-sm font-bold
                        @if (!$today?->clock_in) bg-red-100 text-red-600
                        @elseif($today?->clock_in && !$today?->clock_out) bg-yellow-100 text-yellow-700
                        @else bg-emerald-100 text-emerald-700
                        @endif">
                        @if (!$today?->clock_in) Not Checked In
                        @elseif($today?->clock_in && !$today?->clock_out) Working
                        @else Finished
                        @endif
                    </span> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- ANNOUNCEMENT -->
    <div class="p-4 pb-24">
        <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Announcements</h2>

            {{-- @forelse($announcements as $a)
                <div class="p-4 mb-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                    <h3 class="font-semibold text-gray-700">{{ $a->title }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $a->description }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $a->created_at->format('d M Y') }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No announcements yet.</p>
            @endforelse --}}
        </div>
    </div>

    @include('layout.loading')

</body>

</html>
