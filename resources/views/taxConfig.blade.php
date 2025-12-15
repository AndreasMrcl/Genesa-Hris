<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tax Configuration</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
    <style>
        .dataTables_wrapper .dataTables_length select { padding-right: 2rem; border-radius: 0.5rem; }
        .dataTables_wrapper .dataTables_filter input { padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #d1d5db; }
        table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb; }
        
        /* Tab Styles */
        .tab-btn.active { border-bottom: 2px solid #4f46e5; color: #4f46e5; font-weight: bold; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-calculator text-indigo-600"></i> Tax Configuration (PPh 21)
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Manage PTKP statuses and TER (Tarif Efektif Rata-rata) rates.</p>
                </div>
                <a href="{{ route('companyConfig') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition flex items-center gap-2 text-sm">
                    <i class="fas fa-arrow-left"></i> Back to Payroll Config
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center gap-2 border border-green-200">
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

            @php
                $terData = [
                    'A' => $terA,
                    'B' => $terB,
                    'C' => $terC
                ];
            @endphp

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 bg-white rounded-t-xl px-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button id="btn-ptkp" class="tab-btn active py-4 px-1 inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none" onclick="openTab('ptkp')">
                        <i class="fas fa-users"></i> PTKP Status
                    </button>
                    <button id="btn-terA" class="tab-btn py-4 px-1 inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none" onclick="openTab('terA')">
                        <i class="fas fa-layer-group"></i> TER Kategori A
                    </button>
                    <button id="btn-terB" class="tab-btn py-4 px-1 inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none" onclick="openTab('terB')">
                        <i class="fas fa-layer-group"></i> TER Kategori B
                    </button>
                    <button id="btn-terC" class="tab-btn py-4 px-1 inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none" onclick="openTab('terC')">
                        <i class="fas fa-layer-group"></i> TER Kategori C
                    </button>
                </nav>
            </div>

            <!-- TAB CONTENT: PTKP -->
            <div id="ptkp" class="tab-content bg-white p-6 rounded-b-xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-700">PTKP Status List</h3>
                    <button onclick="openPtkpModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition text-xs font-bold uppercase flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Status
                    </button>
                </div>
                
                <table class="w-full text-left border-collapse" id="ptkpTable">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="p-3 bg-gray-50 border-b">Code</th>
                            <th class="p-3 bg-gray-50 border-b">Annual Amount</th>
                            <th class="p-3 bg-gray-50 border-b text-center">TER Category</th>
                            <th class="p-3 bg-gray-50 border-b text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @foreach($ptkps as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-bold">{{ $item->code }}</td>
                            <td class="p-3">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td class="p-3 text-center">
                                <span class="bg-gray-100 px-2 py-1 rounded border font-bold text-gray-600">{{ $item->ter_category }}</span>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick="editPtkp({{ $item }})" class="text-blue-600 hover:text-blue-800 transition"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('delptkp', $item->id) }}" method="POST" class="inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-800 transition delete-confirm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- TAB CONTENT: TER A, B, C -->
            @foreach($terData as $cat => $rates)
                <div id="ter{{ $cat }}" class="tab-content hidden bg-white p-6 rounded-b-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-700">Rates Table - Category {{ $cat }}</h3>
                        <button onclick="openTerModal('{{ $cat }}')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition text-xs font-bold uppercase flex items-center gap-2">
                            <i class="fas fa-plus"></i> Add Rate
                        </button>
                    </div>
                    <div class="overflow-auto max-h-[500px]">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th class="p-3 bg-gray-50 border-b">Gross Income Range (Rp)</th>
                                    <th class="p-3 bg-gray-50 border-b text-center">Rate (%)</th>
                                    <th class="p-3 bg-gray-50 border-b text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($rates as $rate)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 font-mono text-gray-700">
                                        {{ number_format($rate->gross_income_min, 0, ',', '.') }} 
                                        - 
                                        @if($rate->gross_income_max)
                                            {{ number_format($rate->gross_income_max, 0, ',', '.') }}
                                        @else
                                            <span class="text-gray-400 italic">Unlimited</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center font-bold text-blue-600">{{ $rate->rate_percentage }}%</td>
                                    <td class="p-3 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <button onclick="editTer({{ $rate }})" class="text-blue-600 hover:text-blue-800 transition"><i class="fas fa-edit"></i></button>
                                            <form action="{{ route('delter', $rate->id) }}" method="POST" class="inline delete-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="text-red-600 hover:text-red-800 transition delete-confirm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>
    </main>

    <!-- MODAL PTKP -->
    <div id="ptkpModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl relative transform transition-all scale-100">
            <button onclick="document.getElementById('ptkpModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition"><i class="fas fa-times"></i></button>
            <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2" id="ptkpModalTitle">
                <i class="fas fa-user-tag text-indigo-600"></i> Add PTKP Status
            </h2>
            <form id="ptkpForm" method="POST" class="space-y-4">
                @csrf
                <div id="methodField"></div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code</label>
                    <input type="text" name="code" id="ptkpCode" class="w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-indigo-500" placeholder="e.g. TK/0" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Annual Amount (Rp)</label>
                    <input type="text" name="amount" id="ptkpAmount" class="currency w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">TER Category</label>
                    <select name="ter_category" id="ptkpCategory" class="w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                        <option value="A">Category A</option>
                        <option value="B">Category B</option>
                        <option value="C">Category C</option>
                    </select>
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow hover:bg-indigo-700 transition">Save Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TER -->
    <div id="terModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl relative transform transition-all scale-100">
            <button onclick="document.getElementById('terModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition"><i class="fas fa-times"></i></button>
            <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2" id="terModalTitle">
                <i class="fas fa-percentage text-indigo-600"></i> Add TER Rate
            </h2>
            <form id="terForm" method="POST" class="space-y-4">
                @csrf
                <div id="terMethodField"></div>
                <input type="hidden" name="ter_category" id="terCatInput">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Min Income</label>
                        <input type="text" name="gross_income_min" id="terMin" class="currency w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Max Income</label>
                        <input type="text" name="gross_income_max" id="terMax" class="currency w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-indigo-500" placeholder="Infinity">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rate Percentage (%)</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="rate_percentage" id="terRate" class="w-full rounded-lg border-gray-300 p-2.5 border focus:ring-2 focus:ring-indigo-500 pr-8" required>
                        <span class="absolute right-3 top-2.5 text-gray-400 text-sm font-bold">%</span>
                    </div>
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow hover:bg-indigo-700 transition">Save Rate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        
        function openTab(tabName) {
            $('.tab-content').addClass('hidden');
            $('.tab-btn').removeClass('active');
            $('#' + tabName).removeClass('hidden');
            $('#btn-' + tabName).addClass('active');
        }

        $(document).ready(function() {

            let activeTab = 'ptkp';

            @if(session('active_tab'))
                activeTab = "{{ session('active_tab') }}";
            @endif

            openTab(activeTab);

            // Format Currency
            function formatCurrency(value) {
                let rawValue = String(value).replace(/\D/g, ''); 
                if (rawValue === '') return '';
                let numberValue = parseInt(rawValue, 10);
                return numberValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            $('.currency').on('input', function() { $(this).val(formatCurrency($(this).val())); });
            $('form').on('submit', function() {
                $(this).find('.currency').each(function() { $(this).val($(this).val().replace(/\./g, '')); });
            });

            // PTKP Modal Logic
            const ptkpModal = $('#ptkpModal');
            window.openPtkpModal = function() {
                $('#ptkpForm').attr('action', "{{ route('postptkp') }}");
                $('#methodField').html('');
                $('#ptkpModalTitle').html('<i class="fas fa-user-tag text-indigo-600"></i> Add PTKP Status');
                $('#ptkpCode').val('');
                $('#ptkpAmount').val('');
                $('#ptkpCategory').val('A');
                ptkpModal.removeClass('hidden');
            }

            window.editPtkp = function(data) {
                $('#ptkpForm').attr('action', `/companyconfig/tax/ptkp/${data.id}`);
                $('#methodField').html('<input type="hidden" name="_method" value="PUT">');
                $('#ptkpModalTitle').html('<i class="fas fa-edit text-indigo-600"></i> Edit PTKP Status');
                $('#ptkpCode').val(data.code);
                let amount = Math.floor(Number(data.amount));
                $('#ptkpAmount').val(formatCurrency(amount));
                $('#ptkpCategory').val(data.ter_category);
                ptkpModal.removeClass('hidden');
            }

            // TER Modal Logic
            const terModal = $('#terModal');
            window.openTerModal = function(category) {
                $('#terForm').attr('action', "{{ route('postter') }}");
                $('#terMethodField').html('');
                $('#terModalTitle').html('<i class="fas fa-percentage text-indigo-600"></i> Add TER Rate (Category ' + category + ')');
                $('#terCatInput').val(category);
                $('#terMin').val('');
                $('#terMax').val('');
                $('#terRate').val('');
                terModal.removeClass('hidden');
            }

            window.editTer = function(data) {
                $('#terForm').attr('action', `/companyconfig/tax/ter/${data.id}`);
                $('#terMethodField').html('<input type="hidden" name="_method" value="PUT">');
                $('#terModalTitle').html('<i class="fas fa-edit text-indigo-600"></i> Edit TER Rate (Category ' + data.ter_category + ')');
                $('#terCatInput').val(data.ter_category);
                
                let minVal = Math.floor(Number(data.gross_income_min));
                $('#terMin').val(formatCurrency(minVal));
                
                if (data.gross_income_max) {
                    let maxVal = Math.floor(Number(data.gross_income_max));
                    $('#terMax').val(formatCurrency(maxVal));
                } else {
                    $('#terMax').val('');
                }
                $('#terRate').val(data.rate_percentage);
                terModal.removeClass('hidden');
            }
            
            // Close Modal
            $(window).click((e) => {
                if (e.target === ptkpModal[0]) ptkpModal.addClass('hidden');
                if (e.target === terModal[0]) terModal.addClass('hidden');
            });

            // Delete Confirm
            $('.delete-confirm').click(function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete this item?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => { if (result.isConfirmed) form.submit(); });
            });
        });
    </script>
    @include('layout.loading')
</body>
</html>