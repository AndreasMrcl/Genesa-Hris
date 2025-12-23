<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | My Notes</title>
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
            <h1 class="font-bold text-base text-gray-800">My Performance Notes</h1>
            <div class="w-9"></div> 
        </div>

        <div class="px-4 pb-4 pt-2">
            <div class="bg-teal-600 rounded-2xl p-4 shadow-lg shadow-teal-200 text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                
                <p class="text-[10px] uppercase font-bold text-teal-100 tracking-wider mb-1">Record Summary</p>
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-3xl font-extrabold">{{ $notes->count() }}</h2>
                        <span class="text-xs text-teal-100 font-medium">Total Notes</span>
                    </div>
                
                </div>
            </div>
        </div>
    </div>

    <div class="p-3 flex-grow space-y-3 pb-6">
        
        @forelse ($notes->sortByDesc('note_date') as $item)
            @php
                $date = \Carbon\Carbon::parse($item->note_date);

                $typeConfig = match(strtolower($item->type)) {
                    'warning' => ['color' => 'bg-red-500', 'text' => 'text-red-600', 'icon' => 'fa-exclamation-triangle'],
                    'reward' => ['color' => 'bg-green-500', 'text' => 'text-green-600', 'icon' => 'fa-star'],
                    'performance' => ['color' => 'bg-blue-500', 'text' => 'text-blue-600', 'icon' => 'fa-chart-line'],
                    default => ['color' => 'bg-gray-400', 'text' => 'text-gray-600', 'icon' => 'fa-sticky-note']
                };
            @endphp

            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition relative overflow-hidden group">

                <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $typeConfig['color'] }}"></div>

                <div class="flex gap-3 pl-2">
                    <div class="flex flex-col items-center min-w-[3.5rem]">
                        <div class="w-14 h-14 rounded-xl flex flex-col items-center justify-center border border-gray-200 bg-gray-50">
                            <span class="text-[10px] font-bold uppercase text-gray-400">
                                {{ $date->format('M') }}
                            </span>
                            <span class="text-lg font-extrabold text-gray-700 leading-none">
                                {{ $date->format('d') }}
                            </span>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 mt-1 uppercase">{{ $date->format('Y') }}</span>
                    </div>

                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                                    <i class="fas {{ $typeConfig['icon'] }} {{ $typeConfig['text'] }} text-xs"></i>
                                    {{ ucfirst($item->type) }}
                                </h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                                    {{ $date->format('l, d F Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-100 mt-2 text-xs text-gray-600 leading-relaxed">
                            {{ $item->content }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-[50vh] text-center p-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-300">
                    <i class="fas fa-clipboard-check text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700">Clean Record</h3>
                <p class="text-xs text-gray-400 mt-1">You don't have any performance notes yet.</p>
            </div>
        @endforelse

    </div>

    @include('layout.loading')

</body>
</html>