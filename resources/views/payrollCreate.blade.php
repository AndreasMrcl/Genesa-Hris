<!DOCTYPE html>
<html lang="en">

<head>
    <title>Proses Payroll</title>
    @include('layout.head')
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 flex justify-center">

            <div class="w-full max-w-2xl bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-indigo-600 p-6">
                    <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-money-check-alt"></i> Proses Payroll
                    </h1>
                    <p class="text-indigo-100 mt-1">Pilih periode absensi untuk menghitung gaji.</p>
                </div>

                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r">
                            <p class="font-bold flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i>
                                Error</p>
                            <ul class="list-disc list-inside mt-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('postpayroll') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Periode Kehadiran</label>

                            @if ($availablePeriods->count() > 0)
                                <select name="selected_period"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-3 border focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white text-gray-800 text-lg"
                                    required>
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach ($availablePeriods as $period)
                                        <option value="{{ $period->period_start }}|{{ $period->period_end }}">
                                            {{ \Carbon\Carbon::parse($period->period_start)->format('d M Y') }}
                                            &nbsp;&nbsp;—&nbsp;&nbsp;
                                            {{ \Carbon\Carbon::parse($period->period_end)->format('d M Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-2">
                                    *Hanya periode yang telah diinput melalui <strong>Menu Kehadiran</strong> yang akan
                                    muncul di sini.
                                </p>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                    <p class="text-yellow-800 font-semibold">
                                        Data kehadiran belum tersedia!
                                    </p>
                                    <p class="text-sm text-yellow-600 mt-1">
                                        Silakan input rekap kehadiran terlebih dahulu sebelum menjalankan payroll.
                                    </p>
                                    <a href="{{ route('attendance') }}" class="inline-block mt-3 text-indigo-600 font-bold hover:underline">
                                        Ke Menu Kehadiran &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if ($availablePeriods->count() > 0)
                            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                                <a href="{{ route('payroll') }}"
                                    class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-bold">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-lg transition font-bold flex items-center gap-2 transform hover:-translate-y-0.5">
                                    <i class="fas fa-cogs"></i> Proses Payroll
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </main>
    @include('layout.loading')
</body>

</html>
