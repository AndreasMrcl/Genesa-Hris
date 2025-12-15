<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payroll Branches</title>
    @include('layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
                        <i class="fas fa-building text-cyan-600"></i> Branch List
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Period: 
                        <span class="font-bold text-gray-700">
                            {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
                        </span>
                    </p>
                </div>
                <a href="{{ route('payroll') }}" 
                   class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition flex items-center gap-2 text-sm">
                   <i class="fas fa-arrow-left"></i> Back to Periods
                </a>
            </div>

            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold rounded-tl-lg">Branch Name</th>
                                <th class="p-4 font-bold text-center">Category</th>
                                <th class="p-4 font-bold text-center">Employees</th>
                                <th class="p-4 font-bold text-center">Total Expense</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @foreach ($branchStats as $stat)
                                <tr class="hover:bg-cyan-50 transition cursor-pointer" 
                                    onclick="window.location='{{ route('payrollBranchEmployees', ['start' => $start, 'end' => $end, 'branch' => $stat->id]) }}'">
                                    
                                    <td class="p-4">
                                        <div class="font-bold text-lg text-cyan-700">
                                            {{ $stat->name }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="text-xs uppercase font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded border">
                                            {{ str_replace('_', ' ', $stat->category) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="font-bold text-gray-800 text-normal">
                                            {{ $stat->employee_count }}
                                        </span>
                                        <span class="text-xs text-gray-400">Staff</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="font-bold text-gray-800 ">
                                            Rp {{ number_format($stat->total_expense, 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
                                <td class="p-4 text-center" colspan="2">TOTAL</td>
                                <td class="p-4 text-center text-base">{{ $branchStats->sum('employee_count') }}</td>
                                <td class="p-4 text-center text-base">Rp {{ number_format($branchStats->sum('total_expense'), 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <p class="text-xs text-gray-400 italic">* Click on a row to view employee details for that branch.</p>
        </div>
    </main>
</body>
</html>