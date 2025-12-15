<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Attendance</title>
    @include('layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-5 space-y-6">
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-user-check text-blue-600"></i> Input Attendance
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Manage employee attendance summary data</p>
                </div>
                <a href="{{ route('attendance') }}"
                    class="p-2 px-6 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition flex items-center gap-2">
                    <span>&larr;</span> Back
                </a>
            </div>

            <!-- Header: Date Selection -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h2 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                    Select Period
                </h2>

                <form action="{{ route('manageattendance') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Start Date</label>
                        <input type="date" name="start" value="{{ $start ?? '' }}"
                            class="rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">End Date</label>
                        <input type="date" name="end" value="{{ $end ?? '' }}"
                            class="rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition font-bold shadow-md">
                        Load Data
                    </button>

                    @if ($start && $end)
                        <a href="{{ route('attendance') }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-bold">
                            Cancel
                        </a>
                    @endif
                </form>
            </div>

            @if ($errors->any())
                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                    <span class="font-bold">Error:</span> {{ $errors->first() }}
                </div>
            @endif

            @if ($start && $end && count($employees) > 0)
                <form action="{{ route('postattendance') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period_start" value="{{ $start }}">
                    <input type="hidden" name="period_end" value="{{ $end }}">

                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div
                            class="p-4 bg-blue-50 border-b border-blue-100 flex justify-between items-center sticky top-0 z-10">
                            <div>
                                <h3 class="font-bold text-blue-800 text-lg">Input Attendance Data</h3>
                                <p class="text-xs text-blue-600">Data will be saved for period:
                                    <strong>{{ $start }}</strong> to <strong>{{ $end }}</strong></p>
                            </div>
                            <button type="submit"
                                class="px-8 py-2.5 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-bold flex items-center gap-2">
                                <i class="fas fa-save"></i> Save All Changes
                            </button>
                        </div>

                        <div class="overflow-auto max-h-[65vh]">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-100 text-gray-600 uppercase text-xs sticky top-0 z-10 shadow-sm">
                                    <tr>
                                        <th class="p-4 w-12 bg-gray-100 border-b">No</th>
                                        <th class="p-4 w-64 bg-gray-100 border-b">Employee Name</th>
                                        <th class="p-4 text-center w-20 bg-orange-100 border-b text-orange-800">Late
                                        </th>
                                        <th class="p-4 text-center w-24 bg-red-50 border-b text-red-800">Alpha</th>
                                        <th class="p-4 text-center w-24 bg-indigo-50 border-b text-indigo-800">Permit
                                            (Letter)</th>
                                        <th class="p-4 text-center w-24 bg-gray-50 border-b text-gray-800">Permit</th>
                                        <th class="p-4 text-center w-24 bg-yellow-50 border-b text-yellow-800">Sick</th>
                                        <th class="p-4 text-center w-20 bg-blue-100 border-b text-blue-800">Leave</th>
                                        <th class="p-4 text-center w-24 bg-green-50 border-b text-green-800">Present
                                        </th>
                                        <th class="p-4 text-center bg-gray-100 border-b">Note</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-200">
                                    @php $no = 1; @endphp
                                    @foreach ($employees as $emp)
                                        @php
                                            $existing = $attendances[$emp->id] ?? null;

                                            $machineVal = $machineData[$emp->id]['present'] ?? 0;
                                            $machineLate = $machineData[$emp->id]['late'] ?? 0;

                                            $savedVal = $existing ? $existing->total_present : 0;
                                            $savedLate = $existing ? $existing->total_late : 0;

                                            $displayVal = $existing ? $savedVal : $machineVal;
                                            $displayLate = $existing ? $savedLate : $machineLate;
                                        @endphp
                                        <tr class="hover:bg-blue-50 transition group">
                                            <td class="p-4 text-center text-gray-400 font-medium">{{ $no++ }}
                                            </td>
                                            <td class="p-4 font-bold text-gray-800">
                                                {{ $emp->name }}
                                                <div class="text-xs text-gray-500 font-normal">
                                                    {{ $emp->position->name }} | {{ $emp->working_days }} | {{ $emp->fingerprint_id }}</div>
                                            </td>

                                            <!-- Inputs -->
                                            <td class="p-2 text-center bg-orange-50/50">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][late]"
                                                    value="{{ $existing->total_late ?? 0 }}"
                                                    class="w-20 text-center text-orange-700 border-orange-300 rounded focus:ring-orange-500 p-2 border">
                                            </td>
                                            <td class="p-2 text-center bg-red-50/30">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][alpha]"
                                                    value="{{ $existing->total_alpha ?? 0 }}"
                                                    class="w-20 text-center text-red-700 border-red-200 rounded focus:ring-red-500 p-2 border shadow-sm">
                                            </td>
                                            <td class="p-2 text-center">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][permission_letter]"
                                                    value="{{ $existing->total_permission_letter ?? 0 }}"
                                                    class="w-20 text-center text-indigo-700 border-indigo-200 rounded focus:ring-indigo-500 p-2 border">
                                            </td>
                                            <td class="p-2 text-center">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][permission]"
                                                    value="{{ $existing->total_permission ?? 0 }}"
                                                    class="w-20 text-center text-gray-700 border-gray-200 rounded focus:ring-indigo-500 p-2 border">
                                            </td>
                                            <td class="p-2 text-center">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][sick]"
                                                    value="{{ $existing->total_sick ?? 0 }}"
                                                    class="w-20 text-center text-yellow-700 border-yellow-200 rounded focus:ring-yellow-500 p-2 border">
                                            </td>
                                            <td class="p-2 text-center bg-blue-50/50">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][leave]"
                                                    value="{{ $existing->total_leave ?? 0 }}"
                                                    class="w-20 text-center text-blue-700 border-blue-300 rounded focus:ring-blue-500 p-2 border">
                                            </td>
                                            <td class="p-2 text-center bg-green-50/50">
                                                <input type="number" min="0"
                                                    name="data[{{ $emp->id }}][present]"
                                                    value="{{ $displayVal }}"
                                                    class="w-20 text-center font-bold text-green-700 border-green-300 rounded focus:ring-green-500 p-2 border shadow-sm"
                                                    required>
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="data[{{ $emp->id }}][note]"
                                                    value="{{ $existing->note ?? '' }}"
                                                    class="w-full text-gray-600 border-gray-200 rounded focus:ring-blue-500 p-2 border"
                                                    placeholder="Add remarks...">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-200 text-right text-xs text-gray-500">
                            Total Employees: {{ count($employees) }}
                        </div>
                    </div>
                </form>
            @endif

        </div>
    </main>

    @include('layout.loading')
</body>

</html>
