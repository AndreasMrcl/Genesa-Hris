<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Payroll History</title>
    @include('ess.layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto min-h-screen shadow-lg border-x border-gray-100">

    <div class="sticky top-0 bg-white/95 backdrop-blur-md z-20 border-b border-gray-200">
        <div class="p-3 flex items-center justify-between">
            <a href="{{ route('ess-home') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left text-base"></i>
            </a>
            <h1 class="font-bold text-base text-gray-800">Payroll History</h1>
            <div class="w-9"></div> 
        </div>

        @php
            $lastSlip = $payrolls->sortByDesc('created_at')->first();
        @endphp
        <div class="px-4 pb-4 pt-2">
            <div class="bg-indigo-600 rounded-2xl p-4 shadow-lg shadow-indigo-200 text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                
                <p class="text-[10px] uppercase font-bold text-indigo-200 tracking-wider mb-1">Latest Take Home Pay</p>
                @if($lastSlip)
                    <h2 class="text-2xl font-extrabold">Rp {{ number_format($lastSlip->net_salary, 0, ',', '.') }}</h2>
                    <p class="text-xs text-indigo-100 mt-1 flex items-center gap-1">
                        <i class="far fa-calendar-alt"></i> Period: {{ \Carbon\Carbon::parse($lastSlip->pay_period_end)->format('M Y') }}
                    </p>
                @else
                    <h2 class="text-xl font-bold">Rp 0</h2>
                    <p class="text-xs text-indigo-200">No data available</p>
                @endif
            </div>
        </div>
    </div>

    <div class="p-3 flex-grow space-y-3">

        @forelse ($payrolls->sortByDesc('pay_period_end') as $item)
            @php
                $endDate = \Carbon\Carbon::parse($item->pay_period_end);
                
                $methodIcon = $item->payroll_method == 'transfer' ? 'fa-university' : 'fa-money-bill-wave';
            @endphp

            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition relative overflow-hidden group">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-indigo-500"></div>
                <div class="flex gap-3 pl-2">
                    <div class="flex flex-col items-center min-w-[3.5rem]">
                        <div class="w-14 h-14 rounded-xl flex flex-col items-center justify-center border border-gray-200 bg-gray-50">
                            <span class="text-[10px] font-bold uppercase text-gray-400">
                                {{ $endDate->format('M') }}
                            </span>
                            <span class="text-lg font-extrabold text-gray-700 leading-none">
                                {{ $endDate->format('Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide mb-0.5">Net Salary</p>
                                <h3 class="font-bold text-gray-800 text-lg">
                                    Rp {{ number_format($item->net_salary, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                        
                        <div class="mt-3 flex items-center justify-between border-t border-gray-50 pt-2">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas {{ $methodIcon }} text-gray-400"></i>
                                <span>{{ ucfirst($item->payroll_method ?? 'Transfer') }}</span>
                            </div>
                            
                            <a href="{{ route('ess-pdf', $item->id) }}" target="_blank" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition text-xs font-bold">
                                <i class="fas fa-file-download"></i> Slip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-[50vh] text-center p-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-300">
                    <i class="fas fa-file-invoice-dollar text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700">No Payslips Yet</h3>
                <p class="text-xs text-gray-400 mt-1">Your payroll history will appear here once generated.</p>
            </div>
        @endforelse

    </div>

    @include('layout.loading')

</body>

</html>