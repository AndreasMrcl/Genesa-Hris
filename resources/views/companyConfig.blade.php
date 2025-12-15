<!DOCTYPE html>
<html lang="en">

<head>
    <title>Company Configuration</title>
    @include('layout.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-cogs text-slate-600"></i> Payroll Configuration
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Global settings for tax and insurance calculation</p>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Section -->
            <div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    <form action="{{ route('updatecompanyConfig') }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- TAX SETTINGS -->
                        <div>
                            <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-4 border-b pb-2">
                                <i class="fas fa-file-invoice-dollar mr-1"></i> Tax Settings (PPh 21)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Calculation Method</label>
                                    <select name="tax_method" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500">
                                        <option value="GROSS" {{ $config->tax_method == 'GROSS' ? 'selected' : '' }}>GROSS (Salary Cut)</option>
                                        <option value="NET" {{ $config->tax_method == 'NET' ? 'selected' : '' }}>NET (Company Paid)</option>
                                        <option value="GROSS_UP" {{ $config->tax_method == 'GROSS_UP' ? 'selected' : '' }}>GROSS UP (Tax Allowance)</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Determines who pays the tax.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Regional Minimum Wage (UMP)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2.5 text-gray-500 font-medium">Rp</span>
                                        <input type="text" name="ump_amount" value="{{ number_format($config->ump_amount, 0, ',', '.') }}" class="currency w-full pl-10 rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Used for validation and BPJS minimum basis.</p>
                                </div>
                            </div>
                        </div>

                        <!-- BPJS SETTINGS -->
                        <div>
                            <h3 class="text-sm font-bold text-emerald-600 uppercase tracking-wider mb-4 border-b pb-2">
                                <i class="fas fa-shield-alt mr-1"></i> BPJS Configuration
                            </h3>
                            
                            <!-- Master Switches -->
                            <div class="flex gap-6 mb-6">
                                <label class="inline-flex items-center cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                    <input type="checkbox" name="bpjs_kes_active" value="1" {{ $config->bpjs_kes_active ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Activate BPJS Kesehatan</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                    <input type="checkbox" name="bpjs_tk_active" value="1" {{ $config->bpjs_tk_active ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Activate BPJS Ketenagakerjaan</span>
                                </label>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <!-- BPJS Kesehatan -->
                                <div class="col-span-2">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">BPJS Kesehatan</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Company (%)</label>
                                            <input type="number" step="0.01" name="kes_comp_percent" value="{{ $config->kes_comp_percent }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Employee (%)</label>
                                            <input type="number" step="0.01" name="kes_emp_percent" value="{{ $config->kes_emp_percent }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Max Cap Amount</label>
                                            <input type="number" name="kes_cap_amount" value="{{ $config->kes_cap_amount }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <!-- JKM -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">JKK Rate (Jaminan Kecelakaan Kerja)</h4>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Company (%)</label>
                                        <input type="number" step="0.01" name="bpjs_jkk_rate" value="{{ $config->bpjs_jkk_rate }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Company risk rate (0.24% - 1.74%).</p>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">JKM (Jaminan Kematian)</h4>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Company (%)</label>
                                        <input type="number" step="0.01" name="jkm_comp_percent" value="{{ $config->jkm_comp_percent }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- JHT -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">JHT (Jaminan Hari Tua)</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Company (%)</label>
                                            <input type="number" step="0.01" name="jht_comp_percent" value="{{ $config->jht_comp_percent }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Employee (%)</label>
                                            <input type="number" step="0.01" name="jht_emp_percent" value="{{ $config->jht_emp_percent }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- JP -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">JP (Jaminan Pensiun)</h4>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Comp (%)</label>
                                            <input type="number" step="0.01" name="jp_comp_percent" value="{{ $config->jp_comp_percent }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Emp (%)</label>
                                            <input type="number" step="0.01" name="jp_emp_percent" value="{{ $config->jp_emp_percent }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Cap Amount</label>
                                            <input type="number" name="jp_cap_amount" value="{{ $config->jp_cap_amount }}" class="w-full rounded-lg border-gray-300 shadow-sm p-2 border text-sm" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SOCIAL CONTRIBUTION SETTINGS -->
                        <div class="mt-8">
                            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4 border-b pb-2">
                                <i class="fas fa-hand-holding-heart mr-1"></i> Social Contributions
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Infaq / Zakat Rate</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="infaq_percent" value="{{ $config->infaq_percent }}" class="w-full pr-10 rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" placeholder="0">
                                        <span class="absolute right-3 top-2.5 text-gray-500 font-medium">%</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Automatic deduction from Base Salary for all employees. Set 0 to disable.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PTKP & TER SETTINGS -->
                        <div class="mt-8">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider">
                                    <i class="fas fa-users-cog mr-1"></i> PTKP & TER Configuration
                                </h3>
                                <a href="{{ route('taxConfig') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase flex items-center gap-1 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-200 transition">
                                    <i class="fas fa-edit"></i> Manage Rates
                                </a>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach(['A', 'B', 'C'] as $category)
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 pb-2">
                                            <h4 class="font-bold text-gray-700">TER Kategori {{ $category }}</h4>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            @foreach($ptkps->where('ter_category', $category) as $ptkp)
                                                <div>
                                                    <div class="flex justify-between mb-1">
                                                        <label class="text-xs font-bold text-gray-500">{{ $ptkp->code }}</label>
                                                        <span class="text-xs text-gray-400">PTKP Amount</span>
                                                    </div>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-2 text-gray-500 font-medium text-xs">Rp</span>
                                                        <input type="text" name="ptkp[{{ $ptkp->id }}][amount]" 
                                                               value="{{ number_format($ptkp->amount, 0, ',', '.') }}" 
                                                               class="currency w-full pl-8 rounded-md border-gray-300 shadow-sm p-1.5 border text-sm font-semibold focus:ring-2 focus:ring-indigo-500">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-3 italic">* Changing these values will affect PPh 21 calculation (TER Method).</p>
                        </div>

                        <div class="pt-4 flex justify-end border-t border-gray-100">
                            <button type="submit" class="px-8 py-3 bg-slate-800 text-white font-bold rounded-lg shadow-lg hover:bg-slate-900 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                <i class="fas fa-save"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Currency Formatter
            function formatCurrency(value) {
                let rawValue = value.replace(/\D/g, '');
                if (rawValue === '') return '';
                let numberValue = parseInt(rawValue, 10);
                return numberValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('.currency').on('input', function() {
                let val = $(this).val();
                $(this).val(formatCurrency(val));
            });

            // Clean input before submit
            $('form').on('submit', function() {
                $('.currency').each(function() {
                    let cleanVal = $(this).val().replace(/\./g, '');
                    $(this).val(cleanVal);
                });
            });
        });
    </script>
    @include('layout.loading')
</body>
</html>