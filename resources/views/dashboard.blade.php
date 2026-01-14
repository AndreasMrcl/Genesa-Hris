<!DOCTYPE html>
<html lang="id">

<head>
    <title>Dashboard</title>
    @include('layout.head')
</head>

<body class="bg-gray-50">

    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')

        <div class="p-6 space-y-6">

            <!-- HEADER -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-indigo-600"></i>
                        Dashboard
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Ringkasan absensi, penggajian, dan kinerja karyawan
                    </p>
                </div>
                <div class="text-sm text-gray-500">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Total Karyawan
                    </p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        {{ $totalEmployees }}
                    </h2>
                    <p class="text-xs text-emerald-600 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up"></i>
                        +{{ $newEmployeesThisMonth }} bulan ini
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Pengajuan Cuti
                    </p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        {{ $totalLeaves }}
                    </h2>
                    <p class="text-xs text-emerald-600 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up"></i>
                        +{{ $newLeavesThisMonth }} bulan ini
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Pengajuan Lembur
                    </p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        {{ $totalOvertime }}
                    </h2>
                    <p class="text-xs text-emerald-600 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up"></i>
                        +{{ $newOvertimesThisMonth }} bulan ini
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Total Biaya Lembur
                    </p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        Rp {{ number_format($overtimePay, 0, ',', '.') }}
                    </h2>
                </div>

            </div>

            <!-- CHARTS -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 space-y-8">

                <!-- SECTION TITLE -->
                <h2 class="text-sm font-bold text-indigo-600 uppercase tracking-wider border-b pb-2">
                    <i class="fa-solid fa-chart-area mr-1"></i>
                    Ringkasan Analitik
                </h2>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                    <!-- TOTAL ATTENDANCE -->
                    <div class="border border-gray-100 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-700 mb-2">
                            Riwayat Absensi
                        </h3>
                        <canvas id="grafikHistoy" height="120"></canvas>
                    </div>

                    <!-- PAYROLL -->
                    <div class="border border-gray-100 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-700 mb-2">
                            Distribusi Penggajian
                        </h3>
                        <canvas id="grafikPayroll" height="120"></canvas>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('grafikHistoy').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [
                    {
                        label: 'Hadir',
                        data: @json($present),
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Terlambat',
                        data: @json($late),
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Alpha',
                        data: @json($alpha),
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Cuti',
                        data: @json($leave),
                        borderWidth: 2,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const payrollCtx = document.getElementById('grafikPayroll').getContext('2d');

        new Chart(payrollCtx, {
            type: 'line',
            data: {
                labels: @json($payrollLabels),
                datasets: [
                    {
                        label: 'Total Biaya Penggajian',
                        data: @json($payrollExpense),
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>

    @include('sweetalert::alert')

</body>
</html>
