<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Riwayat Absensi</title>
    @include('ess.layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto min-h-screen shadow-lg border-x border-gray-100">

    <!-- HEADER -->
    <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 border-b border-gray-200">
        <div class="p-3 flex items-center justify-between">
            <a href="{{ route('ess-home') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left text-base"></i>
            </a>
            <h1 class="font-bold text-base text-gray-800">Riwayat Absensi</h1>
            <div class="w-9"></div>
        </div>

        @php
            $latest = $attendances->sortByDesc('period_end')->first();
        @endphp
        <div class="px-4 pb-4 pt-2">
            <div class="bg-blue-600 rounded-2xl p-4 shadow-lg shadow-blue-200 text-white relative overflow-hidden">

                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>

                <p class="text-[10px] uppercase font-bold text-blue-200 tracking-wider mb-1">Latest Recap</p>
                @if ($latest)
                    <div class="flex justify-between items-end">
                        <div>
                            <h2 class="text-3xl font-extrabold">{{ $latest->total_present }}</h2>
                            <span class="text-xs text-blue-100 font-medium">Days Present</span>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-white mb-1">
                                {{ \Carbon\Carbon::parse($latest->period_end)->format('F Y') }}
                            </p>
                            @if ($latest->total_late > 0)
                                <span
                                    class="bg-white/20 text-white px-2 py-0.5 rounded text-[10px] font-bold border border-white/10 backdrop-blur-sm">
                                    {{ $latest->total_late }}x Late
                                </span>
                            @else
                                <span
                                    class="bg-emerald-400/30 text-emerald-50 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-400/20 backdrop-blur-sm">
                                    On Time
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <h2 class="text-xl font-bold">-</h2>
                    <p class="text-xs text-blue-200">No records found</p>
                @endif
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="p-3 flex-grow space-y-3">

        @forelse ($attendances->sortByDesc('period_end') as $item)
            @php
                $endDate = \Carbon\Carbon::parse($item->period_end);
            @endphp

            <!-- ATTENDANCE CARD -->
            <div
                class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition relative overflow-hidden group">

                <div class="flex gap-3">
                    <div class="flex flex-col items-center min-w-[3.5rem]">
                        <div
                            class="w-14 h-14 rounded-xl flex flex-col items-center justify-center border border-gray-200 bg-gray-50">
                            <span class="text-[10px] font-bold uppercase text-gray-400">
                                {{ $endDate->format('M') }}
                            </span>
                            <span class="text-lg font-extrabold text-gray-700 leading-none">
                                {{ $endDate->format('Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Period Range</p>
                                <p class="text-xs font-bold text-gray-700 mt-0.5">
                                    {{ \Carbon\Carbon::parse($item->period_start)->format('d M') }} -
                                    {{ \Carbon\Carbon::parse($item->period_end)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="text-center">
                                <span
                                    class="block text-lg font-extrabold text-green-600 leading-none">{{ $item->total_present }}</span>
                                <span class="text-[9px] text-green-600 font-bold uppercase">Present</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 bg-gray-50 p-2 rounded-lg border border-gray-100">
                            <div class="text-center">
                                <p class="text-[9px] text-orange-400 font-bold uppercase mb-0.5">Late</p>
                                <p class="text-xs font-bold text-orange-600">{{ $item->total_late }}</p>
                            </div>
                            <div class="text-center border-l border-gray-200">
                                <p class="text-[9px] text-blue-400 font-bold uppercase mb-0.5">Sick</p>
                                <p class="text-xs font-bold text-blue-600">{{ $item->total_sick }}</p>
                            </div>
                            <div class="text-center border-l border-gray-200">
                                <p class="text-[9px] text-red-400 font-bold uppercase mb-0.5">Alpha</p>
                                <p class="text-xs font-bold text-red-600">{{ $item->total_alpha }}</p>
                            </div>
                            <div class="text-center pt-2 border-t border-gray-200">
                                <p class="text-[9px] text-indigo-400 font-bold uppercase mb-0.5">Leave</p>
                                <p class="text-xs font-bold text-indigo-600">{{ $item->total_leave }}</p>
                            </div>
                            <div class="text-center pt-2 border-t border-l border-gray-200 col-span-2">
                                <p class="text-[9px] text-purple-400 font-bold uppercase mb-0.5">Permission</p>
                                <p class="text-xs font-bold text-purple-600">
                                    {{ $item->total_permission }} <span
                                        class="text-[9px] text-purple-300 font-normal">days</span>
                                </p>
                            </div>
                        </div>

                        @if ($item->note)
                            <div class="mt-2 flex items-start gap-1.5">
                                <i class="fas fa-info-circle text-[10px] text-gray-400 mt-0.5"></i>
                                <p class="text-[10px] text-gray-500 italic leading-tight">{{ $item->note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-[50vh] text-center p-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-300">
                    <i class="fas fa-clipboard-list text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700">No Attendance Data</h3>
                <p class="text-xs text-gray-400 mt-1">Your attendance records will appear here once generated by HR.</p>
            </div>
        @endforelse

    </div>

    @include('layout.loading')
</body>

</html>
