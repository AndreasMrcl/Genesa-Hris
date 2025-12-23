<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | My Schedule</title>
    @include('ess.layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto min-h-screen flex flex-col shadow-lg border-x border-gray-100">

    <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 border-b border-gray-200">
        <div class="p-3 flex items-center justify-between">
            <a href="{{ route('ess-home') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left text-base"></i>
            </a>
            <h1 class="font-bold text-base text-gray-800">My Schedule</h1>
            <div class="w-9"></div> 
        </div>

        <div class="px-4 pb-4 pt-2">
            <div class="grid grid-cols-3 gap-3">

                <div class="bg-gray-50 rounded-xl p-2.5 border border-gray-100 text-center">
                    <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Shifts</p>
                    <p class="text-lg font-extrabold text-gray-700">{{ $schedules->count() }}</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-2.5 border border-gray-100 text-center">
                    <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Est. Hours</p>
                    <p class="text-lg font-extrabold text-indigo-600">{{ $totalHours }}</p>
                </div>

                <div class="bg-indigo-600 rounded-xl p-2.5 shadow-md shadow-indigo-200 text-center flex flex-col justify-center">
                    <p class="text-[9px] uppercase font-bold text-indigo-200 tracking-wider">Next In</p>
                    <p class="text-xs font-bold text-white leading-tight mt-0.5">{{ $nextShiftText }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-3 flex-grow space-y-3">
        
        @forelse ($schedules as $schedule)
            <div class="relative group">
                @if(!$loop->last)
                    <div class="absolute left-[2.1rem] top-10 bottom-[-16px] w-0.5 bg-gray-200 -z-10"></div>
                @endif

                <div class="flex gap-3">

                    <div class="flex flex-col items-center min-w-[3.2rem]">
                        <div class="w-12 h-12 rounded-xl flex flex-col items-center justify-center border {{ $schedule->is_today ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-200' : 'bg-white border-gray-200 text-gray-600' }}">
                            <span class="text-[10px] font-medium uppercase {{ $schedule->is_today ? 'text-indigo-100' : 'text-gray-400' }}">
                                {{ $schedule->date_formatted['day_name'] }}
                            </span>
                            <span class="text-lg font-bold leading-none">
                                {{ $schedule->date_formatted['day_num'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-grow p-3 rounded-xl border shadow-sm relative overflow-hidden {{ $schedule->card_style }}">
                        <div class="absolute left-0 top-0 bottom-0 w-1" style="background-color: {{ $schedule->shift_color }}"></div>

                        <div class="pl-2">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm">{{ $schedule->shift_name }}</h3>
                                    @if($schedule->is_today)
                                        <span class="inline-block mt-0.5 text-[9px] font-bold text-white bg-indigo-500 px-1.5 py-px rounded">TODAY</span>
                                    @endif
                                </div>
                                
                                @if($schedule->shift)
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Duration</p>
                                        <p class="text-xs font-bold text-gray-600">{{ $schedule->shift->duration ?? '-' }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($schedule->shift)
                                <div class="flex items-center gap-3 text-xs mt-2 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-sign-in-alt text-emerald-500"></i>
                                        <div>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase">In</p>
                                            <p class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="w-px h-5 bg-gray-300 mx-auto"></div>

                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-sign-out-alt text-rose-500"></i>
                                        <div>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase">Out</p>
                                            <p class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-red-50 p-2 rounded-lg border border-red-100 text-center">
                                    <span class="text-xs font-bold text-red-500 flex items-center justify-center gap-1">
                                        <i class="fas fa-coffee"></i> Day Off
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-[50vh] text-center p-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-300">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700">All Clear!</h3>
                <p class="text-xs text-gray-400 mt-1">No upcoming shifts scheduled for now.</p>
            </div>
        @endforelse

    </div>

    @include('layout.loading')

</body>
</html>
