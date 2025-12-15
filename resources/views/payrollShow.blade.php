<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payslip - {{ $payroll->employee->name }}</title>
    @include('layout.head')
    <style>
        @media print {
            body * { visibility: hidden; }
            #printableArea, #printableArea * { visibility: visible; }
            #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        
        <div class="p-5">
            <!-- Action Bar -->
            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('payrollBranchEmployees', [
                        'start' => $payroll->pay_period_start, 
                        'end' => $payroll->pay_period_end, 
                        'branch' => $payroll->employee->branch_id
                   ]) }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center gap-2 font-medium transition">
                    <i class="fas fa-arrow-left"></i> Back to Employee List
                </a>
                <button onclick="window.print()" class="px-6 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-gray-900 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Slip
                </button>
            </div>

            <!-- SLIP GAJI AREA -->
            <div id="printableArea" class="max-w-4xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
                
                <!-- Header Slip -->
                <div class="bg-gray-50 p-8 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="w-32 text-left">
                            <img class="w-32 h-fit mx-auto" src="{{ asset('logo.png') }}" alt="Logo">
                        </div>
                        <div class="text-center">
                            <h2 class="text-lg">Pengembangan Usaha Sultan Agung</h2>
                            <h1 class="text-2xl font-bold text-gray-800 uppercase">{{ Auth::user()->compani->company ?? 'Company Name' }}</h1>
                            <h2 class="text-lg">{{ Auth::user()->compani->location }}</h2>
                        </div>
                        <div class="w-32 text-right"></div>
                    </div>
                </div>
                <div class="border-y border-gray-500">
                    <div class="bg-gray-400">
                        <p class="uppercase text-center text-white tracking-[10px]">Payslip Employee</p>
                    </div>
                </div>
                
                <!-- Employee Info -->
                <div class="p-8 border-b border-gray-200">
                    <div class="grid grid-cols-3 gap-8">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Employee Name</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $payroll->employee->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Position</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $payroll->employee->position->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Period</p>
                            <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Branch</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $payroll->employee->branch->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Details Table -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        
                        <!-- Earnings Column -->
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b-2 border-gray-100 pb-2">Earnings (Pendapatan)</h3>
                            <div class="space-y-3 text-sm">
                                @foreach($payroll->payrollDetails->whereIn('category', ['base', 'allowance']) as $item)
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-gray-700 font-medium">{{ $item->name }}</p>
                                        </div>
                                        <p class="text-gray-900 font-semibold">Rp {{ number_format($item->amount, 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Total Earnings -->
                            <div class="mt-6 pt-3 border-t border-gray-200 flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-600 font-bold text-sm">Total Gross Pay</p>
                                <p class="text-gray-900 font-bold">Rp {{ number_format($payroll->base_salary + $payroll->total_allowances, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Deductions Column -->
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b-2 border-gray-100 pb-2">Deductions (Potongan)</h3>
                            <div class="space-y-3 text-sm">
                                @foreach($payroll->payrollDetails->where('category', 'deduction') as $item)
                                    <div class="flex justify-between items-center">
                                        <div><p class="text-gray-700 font-medium">{{ $item->name }}</p></div>
                                        <p class="text-red-600 font-medium">- Rp {{ number_format($item->amount, 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 pt-3 border-t border-gray-200 flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-600 font-bold text-sm">Total Deductions</p>
                                <p class="text-red-600 font-bold">- Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $benefits = $payroll->benefits;
                    @endphp

                    @if($benefits->count() > 0)
                        <div class="mt-10 pt-6 border-t border-dashed border-gray-300">
                            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Company Paid Benefits (Non-Cash)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-2 text-xs">
                                @foreach($benefits as $item)
                                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                                        <p class="text-gray-500 font-medium">{{ $item->name }}</p>
                                        <p class="text-gray-600 font-bold">Rp {{ number_format($item->amount, 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[10px] text-gray-400 mt-3 italic border-l-2 border-gray-300 pl-2 ml-1">
                                * Benefit ini dibayarkan perusahaan (Asuransi/Pajak) dan tidak mengurangi gaji bersih Anda.
                            </p>
                        </div>
                    @endif             
                </div>

                <!-- NET PAY (Bottom Bar) -->
                <div class="bg-gray-50 p-8 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Take Home Pay</p>
                            <p class="text-xs text-gray-400">Transfer to Bank Account</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-indigo-700">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Signature (Optional) -->
                <div class="p-8 pb-12 grid grid-cols-2 gap-8 text-center mt-8">
                    <div>
                        <p class="text-sm text-gray-500 mb-16">Employee Signature</p>
                        <p class="text-sm font-bold text-gray-700 border-t border-gray-300 inline-block px-8 pt-2">{{ $payroll->employee->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-16">Authorized Signature</p>
                        <p class="text-sm font-bold text-gray-700 border-t border-gray-300 inline-block px-8 pt-2">HR Manager</p>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>