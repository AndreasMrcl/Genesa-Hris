<!DOCTYPE html>
<html lang="en">
<head>
    <title>Run Payroll</title>
    @include('layout.head')
</head>
<body class="bg-gray-50">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-5 flex justify-center">
            
            <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-indigo-600 p-6">
                    <h1 class="text-2xl font-bold text-white">Run Payroll Process</h1>
                    <p class="text-indigo-100">Generate salary slips for all active employees</p>
                </div>

                <div class="p-8">
                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                            <p class="font-bold">Error</p>
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('postpayroll') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Period Start</label>
                                <input type="date" name="pay_period_start" 
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    value="{{ old('pay_period_start', date('Y-m-01')) }}" required>
                                <p class="text-xs text-gray-500">Usually the 1st of the month</p>
                            </div>

                            <!-- End Date -->
                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Period End</label>
                                <input type="date" name="pay_period_end" 
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    value="{{ old('pay_period_end', date('Y-m-t')) }}" required>
                                <p class="text-xs text-gray-500">Usually the last day of the month</p>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Important Note</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>This process will generate payroll records for <strong>ALL</strong> employees in your company based on their base salary and assigned allowances/deductions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4">
                            <a href="{{ route('payroll') }}" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium">
                                Cancel
                            </a>
                            <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-lg transition font-bold flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Generate Payroll
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    @include('layout.loading')
</body>
</html>