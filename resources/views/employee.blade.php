<!DOCTYPE html>
<html lang="en">

<head>
    <title>Employee Management</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            border-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800">
                        <i class="fas fa-users text-indigo-600"></i> Employee Management
                    </h1>
                    <p class="text-sm text-gray-500 ">Manage your team members and their details</p>
                </div>
                <button id="addBtn"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Employee
                </button>
            </div>

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg text-center" width="5%">No</th>
                                <th class="p-4 font-bold">Name / Branch</th>
                                <th class="p-4 font-bold">Position / Status</th>
                                <th class="p-4 font-bold">Contacts</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($employees as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 font-medium">{{ $no++ }}</td>
                                    <td class="p-4 space-y-1">
                                        <div class="font-bold text-gray-900 text-base">{{ $item->name }}</div>
                                        <div class="text-xs text-gray-500"><i class="fas fa-building"></i>
                                            {{ $item->branch->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">NIK: {{ $item->nik }}</div>
                                    </td>
                                    <td class="p-4 space-y-1">
                                        <div class="font-medium text-gray-700">{{ $item->position->name ?? '-' }}</div>
                                        <div class="">
                                                <span
                                                    class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full font-bold border border-yellow-200">
                                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                </span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-xs">
                                        <div class="flex items-center gap-2 mb-1"><i
                                                class="fas fa-envelope text-gray-400 w-4"></i> {{ $item->email }}</div>
                                        <div class="flex items-center gap-2"><i
                                                class="fas fa-phone text-gray-400 w-4"></i> {{ $item->phone }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Tombol Edit --}}
                                            <button
                                                class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition cursor-pointer"
                                                data-id="{{ $item->id }}" 
                                                data-name="{{ $item->name }}"
                                                data-branch="{{ $item->branch_id }}" 
                                                data-email="{{ $item->email }}"
                                                data-nik="{{ $item->nik }}" 
                                                data-fingerprint_id="{{ $item->fingerprint_id }}" 
                                                data-ktp="{{ $item->ktp }}"
                                                data-npwp="{{ $item->npwp }}"
                                                data-bpjs-kes-no="{{ $item->bpjs_kesehatan_no }}"
                                                data-bpjs-tk-no="{{ $item->bpjs_ketenagakerjaan_no }}"
                                                data-phone="{{ $item->phone }}" 
                                                data-address="{{ $item->address }}"
                                                data-position-id="{{ $item->position_id }}"
                                                data-base-salary="{{ $item->base_salary }}"
                                                data-join="{{ $item->join_date }}" 
                                                data-status="{{ $item->status }}"
                                                data-ptkp="{{ $item->ptkp_status ?? 'TK/0' }}"
                                                data-working-days="{{ $item->working_days }}"
                                                data-payroll-method="{{ $item->payroll_method }}"
                                                data-part-kes="{{ $item->participates_bpjs_kes }}"
                                                data-part-tk="{{ $item->participates_bpjs_tk }}"
                                                data-part-jp="{{ $item->participates_bpjs_jp }}" 
                                                data-bank-name="{{ $item->bank_name }}"
                                                data-bank-no="{{ $item->bank_account_no }}" 
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Tombol Allowance --}}
                                            <a href="{{ route('allowanceEmp', $item->id) }}"
                                                class="w-9 h-9 flex items-center justify-center bg-emerald-500 text-white rounded-lg shadow hover:bg-emerald-600 hover:scale-105 transition"
                                                title="Allowances">
                                                <i class="fas fa-hand-holding-dollar"></i>
                                            </a>

                                            {{-- Tombol Deduction --}}
                                            <a href="{{ route('deductionEmp', $item->id) }}"
                                                class="w-9 h-9 flex items-center justify-center bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 hover:scale-105 transition"
                                                title="Deductions">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </a>

                                            {{-- Tombol Delete --}}
                                            <form method="post"
                                                action="{{ route('delemployee', ['id' => $item->id]) }}"
                                                class="inline deleteForm">
                                                @csrf @method('delete')
                                                <button type="button" class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition cursor-pointer" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL ADD EMPLOYEE -->
    <div id="addModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-0 w-full max-w-4xl shadow-2xl relative my-5 flex flex-col max-h-[90vh]">
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600"><i
                            class="fas fa-user-plus"></i></div>
                    Add New Employee
                </h2>
                <button id="closeAddModal"
                    class="text-gray-400 hover:text-red-500 transition text-2xl leading-none">&times;</button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-8 overflow-y-auto custom-scrollbar">
                <form id="addForm" method="post" action="{{ route('postemployee') }}" enctype="multipart/form-data"
                    class="space-y-8">
                    @csrf

                    <!-- SECTION 1: Personal Info -->
                    <div>
                        <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-4 border-b pb-2">
                            Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password <span
                                        class="text-red-500">*</span></label>
                                <input type="password" name="password"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    required>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK (Employee ID)
                                    <span class="text-red-500">*</span></label>
                                <input type="number" name="nik"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Phone <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="phone"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">KTP (National
                                    ID)</label>
                                <input type="number" name="ktp"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Address</label>
                                <textarea name="address" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Employment Details -->
                    <div>
                        <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-4 border-b pb-2">
                            Employment Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Branch <span
                                        class="text-red-500">*</span></label>
                                <select name="branch_id" id="branchSelect" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                                    <option value="">-- Select --</option>
                                    @foreach ($branch as $bra) 
                                        <option value="{{ $bra->id }}" data-category="{{ $bra->category }}">
                                            {{ $bra->name }}
                                        </option> 
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Position <span class="text-red-500">*</span></label>
                                <select name="position_id" id="positionSelect" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500" required>
                                    <option value="">-- Select Branch First --</option>
                                </select>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Join Date <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="join_date"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status <span
                                        class="text-red-500">*</span></label>
                                <select name="status" id="statusSelect"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                    required>
                                    <option value="PKWT">PKWT</option>
                                    <option value="PKWTT">PKWTT</option>
                                    <option value="DAILY_WORKER">Daily Worker</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: Payroll & BPJS -->
                    <div class="bg-indigo-50 p-5 rounded-xl border border-indigo-100">
                        <h3 class="text-sm font-bold text-indigo-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fas fa-money-check-alt"></i> Payroll, Tax & Insurance
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                            <div>
                                <label id="labelBaseSalary" class="block text-xs font-bold text-gray-600 uppercase mb-1">Base Salary (Rp)
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="base_salary"
                                    class="currency w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white"
                                    placeholder="0" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Work Days <span class="text-red-500">*</span></label>
                                <input type="number" name="working_days" value="27" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Payroll Method</label>
                                <select name="payroll_method" id="payrollMethod" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="cash">Cash (Tunai)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">PTKP Status</label>
                                <select name="ptkp_status"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                                    <option value="">-- Select PTKP --</option>
                                    @foreach($ptkps as $ptkp)
                                        <option value="{{ $ptkp->code }}">{{ $ptkp->code }} ({{ $ptkp->ter_category }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">NPWP</label>
                                <input type="number" name="npwp"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Fingerprint. Id</label>
                                <input type="number" name="fingerprint_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">BPJS Kesehatan
                                    No.</label>
                                <input type="number" name="bpjs_kesehatan_no"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">BPJS
                                    Ketenagakerjaan No.</label>
                                <input type="number" name="bpjs_ketenagakerjaan_no"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                            </div>
                        </div>

                        <div class="border-t border-indigo-200 pt-4 mb-4">
                            <span class="block text-xs font-bold text-gray-600 uppercase mb-2">Programs
                                Participation</span>
                            <div class="flex gap-6">
                                <label
                                    class="inline-flex items-center cursor-pointer hover:bg-white px-3 py-1 rounded transition">
                                    <input type="checkbox" name="participates_bpjs_kes" value="1" checked
                                        class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">BPJS Kesehatan</span>
                                </label>
                                <label
                                    class="inline-flex items-center cursor-pointer hover:bg-white px-3 py-1 rounded transition">
                                    <input type="checkbox" name="participates_bpjs_tk" value="1" checked
                                        class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">BPJS TK</span>
                                </label>
                                <label
                                    class="inline-flex items-center cursor-pointer hover:bg-white px-3 py-1 rounded transition">
                                    <input type="checkbox" name="participates_bpjs_jp" value="1" checked
                                        class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Jaminan Pensiun</span>
                                </label>
                            </div>
                        </div>

                        <!-- ADDED: Bank Details -->
                        <div id="bankAccountSection" class="border-t border-indigo-200 pt-4">
                            <span class="text-sm font-bold text-indigo-700 uppercase tracking-wider mb-4 flex items-center gap-2"><i class="ri-bank-fill"></i> Bank Account Details</span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bank Name</label>
                                    <input type="text" name="bank_name" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white" placeholder="e.g. BCA">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Account Number</label>
                                    <input type="number" name="bank_account_no" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" id="cancelAdd"
                            class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit"
                            class="px-8 py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition">Save
                            Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT EMPLOYEE -->
    <div id="editModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-0 w-full max-w-4xl shadow-2xl relative my-5 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600"><i
                            class="fas fa-user-edit"></i></div>
                    Edit Employee
                </h2>
                <button id="closeEditModal"
                    class="text-gray-400 hover:text-red-500 transition text-2xl leading-none">&times;</button>
            </div>

            <!-- Body -->
            <div class="p-8 overflow-y-auto custom-scrollbar">
                <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-8">
                    @csrf @method('put')

                    <!-- SECTION 1 -->
                    <div>
                        <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">
                            Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name</label>
                                <input type="text" id="editName" name="name"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border" required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                                <input type="email" id="editEmail" name="email"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border" required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password
                                    (Optional)</label>
                                <input type="password" id="editPassword" name="password"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border"
                                    placeholder="Leave blank to keep">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK</label>
                                <input type="number" id="editNik" name="nik"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border" required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Phone</label>
                                <input type="number" id="editPhone" name="phone"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border" required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">KTP</label>
                                <input type="number" id="editKtp" name="ktp"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Address</label>
                                <textarea id="editAddress" name="address" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2 -->
                    <div>
                        <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">
                            Employment Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Branch</label>
                                <select id="editBranch" name="branch_id" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border" required>
                                    @foreach ($branch as $bra) 
                                        <option value="{{ $bra->id }}" data-category="{{ $bra->category }}">
                                            {{ $bra->name }}
                                        </option> 
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Position</label>
                                <select id="editPositionSelect" name="position_id" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border" required>
                                    <option value="">-- Select Branch First --</option>
                                </select>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Join Date</label>
                                <input type="date" id="editJoinDate" name="join_date"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border" required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                                <select id="editStatus" name="status"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border" required>
                                    <option value="PKWT">PKWT</option>
                                    <option value="PKWTT">PKWTT</option>
                                    <option value="DAILY_WORKER">Daily Worker</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: Payroll -->
                    <div class="bg-blue-50 p-5 rounded-xl border border-blue-100">
                        <h3
                            class="text-sm font-bold text-blue-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fas fa-coins"></i> Payroll & Tax Insurance
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Base Salary
                                    (Rp)</label>
                                <input type="text" id="editBaseSalary" name="base_salary"
                                    class="currency w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-white"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Work Days</label>
                                <input type="number" id="editWorkingDays" name="working_days" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-white" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Payroll Method</label>
                                <select id="editPayrollMethod" name="payroll_method" class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-white">
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="cash">Cash (Tunai)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">PTKP Status</label>
                                <select id="editPtkp" name="ptkp_status"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-white">
                                    <option value="">-- Select PTKP --</option>
                                    @foreach($ptkps as $ptkp)
                                        <option value="{{ $ptkp->code }}">{{ $ptkp->code }} ({{ $ptkp->ter_category }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">NPWP</label>
                                <input type="number" id="editNpwp" name="npwp"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-white">
                            </div>
                                                        <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Fingerprint. Id</label>
                                <input type="number" id="editFingerprint" name="fingerprint_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">BPJS Kesehatan
                                    No.</label>
                                <input type="number" id="editBpjsKesNo" name="bpjs_kesehatan_no"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">BPJS
                                    Ketenagakerjaan No.</label>
                                <input type="number" id="editBpjsTkNo" name="bpjs_ketenagakerjaan_no"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border bg-white">
                            </div>
                        </div>

                        <div class="border-t border-blue-200 pt-4 mb-4">
                            <span class="block text-xs font-bold text-gray-600 uppercase mb-2">Programs
                                Participation</span>
                            <div class="flex gap-6">
                                <label
                                    class="inline-flex items-center cursor-pointer hover:bg-white px-3 py-1 rounded transition">
                                    <input type="hidden" name="participates_bpjs_kes" value="0">
                                    <input type="checkbox" id="editBpjsKes" name="participates_bpjs_kes"
                                        value="1"
                                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 font-medium">BPJS Kesehatan</span>
                                </label>
                                <label
                                    class="inline-flex items-center cursor-pointer hover:bg-white px-3 py-1 rounded transition">
                                    <input type="hidden" name="participates_bpjs_tk" value="0">
                                    <input type="checkbox" id="editBpjsTk" name="participates_bpjs_tk"
                                        value="1"
                                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 font-medium">BPJS TK</span>
                                </label>
                                <label
                                    class="inline-flex items-center cursor-pointer hover:bg-white px-3 py-1 rounded transition">
                                    <input type="hidden" name="participates_bpjs_jp" value="0">
                                    <input type="checkbox" id="editBpjsJp" name="participates_bpjs_jp"
                                        value="1"
                                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 font-medium">Jaminan Pensiun</span>
                                </label>
                            </div>
                        </div>

                        <!-- ADDED: Bank Details Edit -->
                        <div id="editBankAccountSection" class="border-t border-blue-200 pt-4">
                            <span class="text-sm font-bold text-indigo-700 uppercase tracking-wider mb-4 flex items-center gap-2"><i class="ri-bank-fill"></i> Bank Account Details</span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bank
                                        Name</label>
                                    <input type="text" id="editBankName" name="bank_name"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white"
                                        placeholder="e.g. BCA">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Account
                                        Number</label>
                                    <input type="number" id="editBankNo" name="bank_account_no"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500 bg-white">
                                </div>
                            </div>
                    </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" id="closeEditModalBtn"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <button type="submit"
                    class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition">Update
                    Employee</button>
            </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Init DataTable
            new DataTable('#myTable', {});

            function formatCurrency(value) {
                let rawValue = value.replace(/\D/g, '');
                if (rawValue === '') return '';
                let numberValue = parseInt(rawValue, 10);
                return numberValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Saat mengetik
            $('.currency').on('input', function() {
                let val = $(this).val();
                $(this).val(formatCurrency(val));
            });

            function toggleSalaryLabel(status, labelId) {
                const label = $(labelId);
                if (status === 'DAILY_WORKER') {
                    label.html('Daily Rate (Rp) <span class="text-red-500">*</span>');
                } else {
                    label.html('Base Salary (Rp) <span class="text-red-500">*</span>');
                }
            }

            $('#statusSelect').change(function() {
                const label = $('input[name="base_salary"]').prev('label');
                toggleSalaryLabel($(this).val(), label);
            });

            $('#editStatus').change(function() {
                const label = $('#editBaseSalary').prev('label');
                toggleSalaryLabel($(this).val(), label);
            });

            function toggleBankDetails(payrollMethod, sectionId, inputs) {
                const section = $(sectionId);
                const bankInputs = $(inputs);

                if (payrollMethod === 'cash') {
                    section.addClass('hidden');
                } else {
                    section.removeClass('hidden');
                    bankInputs.val(''); 
                }
            }

            $('#payrollMethod').change(function() {
                toggleBankDetails($(this).val(), '#bankAccountSection', '#bankAccountSection input');
            });

            $('#editPayrollMethod').change(function() {
                toggleBankDetails($(this).val(), '#editBankAccountSection', '#editBankAccountSection input');
            });

            toggleBankDetails($('#payrollMethod').val(), '#bankAccountSection', '#bankAccountSection input');

            const allPositions = @json($positions);

            function updatePositionDropdown(branchId, targetSelectId) {
                const targetSelect = $(targetSelectId);
                targetSelect.empty().append('<option value="">-- Select Position --</option>');

                const branchSelectId = targetSelectId === '#positionSelect' ? '#branchSelect' : '#editBranch';
                const selectedOption = $(branchSelectId).find('option:selected');
                const category = selectedOption.data('category');

                if (category) {
                    const filtered = allPositions.filter(p => p.category === category || p.category === 'general');
                    filtered.forEach(p => {
                        targetSelect.append(`<option value="${p.id}" data-salary="${p.base_salary_default}">${p.name}</option>`);
                    });
                }
            }

            function autofillSalary(posSelectId, salaryInputId) {
                const selected = $(posSelectId).find('option:selected');
                const salary = selected.data('salary');
                if (salary > 0) {
                    $(salaryInputId).val(formatCurrency(String(salary).split('.')[0]));
                }
            }

            $('#branchSelect').change(function() {updatePositionDropdown($(this).val(), '#positionSelect');});
            $('#positionSelect').change(function() {autofillSalary('#positionSelect', 'input[name="base_salary"]');});

            $('#editBranch').change(function() {updatePositionDropdown($(this).val(), '#editPositionSelect');});
            $('#editPositionSelect').change(function() {autofillSalary('#editPositionSelect', '#editBaseSalary');});

            // Modal Logic
            const addModal = $('#addModal');
            const editModal = $('#editModal');

            $('#addBtn').click(() => addModal.removeClass('hidden'));
            $('#closeAddModal, #cancelAdd').click(() => addModal.addClass('hidden'));
            $('#closeEditModal, #closeEditModalBtn').click(() => editModal.addClass('hidden'));

            // Click outside to close
            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
                if (e.target === editModal[0]) editModal.addClass('hidden');
            });

            // Edit Button Logic
            $(document).on('click', '.editBtn', function() {
                const btn = $(this);
                const id = btn.data('id');

                $('#editName').val(btn.data('name'));
                $('#editBranch').val(btn.data('branch'));
                $('#editEmail').val(btn.data('email'));
                $('#editPassword').val('');
                $('#editNik').val(btn.data('nik'));
                $('#editFingerprint').val(btn.data('fingerprint_id'));
                $('#editPhone').val(btn.data('phone'));
                $('#editAddress').val(btn.data('address'));
                $('#editJoinDate').val(btn.data('join'));
                $('#editKtp').val(btn.data('ktp'));
                $('#editStatus').val(btn.data('status'));
                let status = btn.data('status');
                const label = $('#editBaseSalary').prev('label');
                toggleSalaryLabel(status, label);

                let rawSalary = btn.data('base-salary');
                let salaryStr = String(rawSalary).split('.')[0];
                // Payroll info
                $('#editBaseSalary').val(formatCurrency(salaryStr));
                $('#editPtkp').val(btn.data('ptkp'));
                $('#editNpwp').val(btn.data('npwp'));
                $('#editBpjsKesNo').val(btn.data('bpjs-kes-no'));
                $('#editBpjsTkNo').val(btn.data('bpjs-tk-no'));

                // Bank Info
                $('#editBankName').val(btn.data('bank-name'));
                $('#editBankNo').val(btn.data('bank-no'));

                toggleBankDetails(payrollMethod, '#editBankAccountSection', '#editBankAccountSection input');
            
                // Checkboxes
                $('#editBpjsKes').prop('checked', btn.data('part-kes') == 1);
                $('#editBpjsTk').prop('checked', btn.data('part-tk') == 1);
                $('#editBpjsJp').prop('checked', btn.data('part-jp') == 1);

                $('#editWorkingDays').val(btn.data('working-days'));
                $('#editPayrollMethod').val(btn.data('payroll-method'));

                updatePositionDropdown(btn.data('branch'), '#editPositionSelect');
                setTimeout(() => {
                    $('#editPositionSelect').val(btn.data('position-id')); 
                }, 50);

                $('#editForm').attr('action', `/employee/${id}/update`);

                editModal.removeClass('hidden');
            });

            $('form').on('submit', function() {
                $('.currency').each(function() {
                    let cleanVal = $(this).val().replace(/\./g, '');
                    $(this).val(cleanVal);
                });
            });

            // Delete Confirm
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>

    @include('sweetalert::alert')
    @include('layout.loading')

</body>

</html>