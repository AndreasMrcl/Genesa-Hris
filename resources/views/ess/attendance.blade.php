<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Attendance</title>
    @include('ess.layout.head')
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto">

    <!-- HEADER / BACK BUTTON -->
    <div class="p-2">
        <a href="{{ route('ess-home') }}"
            class="inline-flex items-center gap-2 px-6 py-2 bg-white text-gray-700 rounded-xl text-3xl">
            <span>&larr;</span>
        </a>
    </div>

    <!-- ATTENDANCE -->
    <div class="p-2">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">

            <!-- Attendance List -->
            <div class="space-y-4">

                @forelse ($attendances as $item)
                    <!-- Period -->
                    <div class="mb-3">
                        <p class="text-sm text-gray-500">Period</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($item->period_start)->format('d M Y') }}
                            –
                            {{ \Carbon\Carbon::parse($item->period_end)->format('d M Y') }}
                        </p>
                    </div>

                    <!-- Summary -->
                    <div class="grid grid-cols-3 gap-3 text-center text-sm">

                        <div class="bg-gray-50 rounded-xl p-2.5 border border-gray-100 text-center">
                            <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Present</p>
                            <p class="font-bold text-green-600">
                                {{ $item->total_present }}
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-2.5 border border-gray-100 text-center">
                            <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Late</p>
                            <p class="font-bold text-yellow-600">
                                {{ $item->total_late }}
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-2.5 border border-gray-100 text-center">
                            <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Sick</p>
                            <p class="font-bold text-blue-600">
                                {{ $item->total_sick }}
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-2.5 border border-gray-100 text-center">
                            <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Leave</p>
                            <p class="font-bold text-indigo-600">
                                {{ $item->total_leave }}
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-2.5 border border-gray-100 text-center">
                            <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Permission</p>
                            <p class="font-bold text-purple-600">
                                {{ $item->total_permission }}
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-2.5 border border-gray-100 text-center">
                            <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Alpha</p>
                            <p class="font-bold text-red-600">
                                {{ $item->total_alpha }}
                            </p>
                        </div>

                    </div>

                    <!-- Note -->
                    @if ($item->note)
                        <div class="mt-3 text-xs text-gray-500 italic">
                            Note: {{ $item->note }}
                        </div>
                    @endif

                @empty
                    <div class="text-center text-gray-500 py-10">
                        <i class="fas fa-calendar-times text-3xl mb-3"></i>
                        <p>No attendance history available</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>

    @include('layout.loading')
</body>

</html>
