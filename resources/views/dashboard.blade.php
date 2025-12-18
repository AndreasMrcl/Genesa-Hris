<!DOCTYPE html>
<html lang="en">

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
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-indigo-600"></i>
                        Dashboard
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Overview of attendance, payroll, and employee performance
                    </p>
                </div>
                <div class="text-sm text-gray-500">
                    {{ now()->format('l, d F Y') }}
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Employees</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalEmployees }} </h2>
                    <p class="text-xs text-emerald-600 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up"></i>
                        +{{ $newEmployeesThisMonth }} this month
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Attendance Rate</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">96%</h2>
                    <p class="text-xs text-indigo-600 mt-2">Avg monthly</p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Payroll</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">Rp 370M</h2>
                    <p class="text-xs text-gray-400 mt-2">Current period</p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Expense</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">Rp 68M</h2>
                    <p class="text-xs text-rose-600 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up"></i> Operational
                    </p>
                </div>

            </div>

            <!-- CHARTS -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 space-y-8">

                <!-- SECTION TITLE -->
                <h2 class="text-sm font-bold text-indigo-600 uppercase tracking-wider border-b pb-2">
                    <i class="fa-solid fa-chart-area mr-1"></i> Analytics Overview
                </h2>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                    <!-- TOTAL ORDER -->
                    <div class="border border-gray-100 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-700 mb-2">Attendance History</h3>
                        <canvas id="grafikHistoy" height="120"></canvas>
                    </div>

                    <!-- REVENUE -->
                    <div class="border border-gray-100 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-700 mb-2">Payroll Distribution</h3>
                        <canvas id="grafikRevenue" height="120"></canvas>
                    </div>

                    <!-- SETTLEMENT -->
                    <div class="border border-gray-100 rounded-xl p-5">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-semibold text-gray-700">Settlement Report</h3>
                            <select id="dateSelect"
                                class="border border-gray-300 rounded-lg px-3 py-1 text-sm bg-gray-50"
                                onchange="updateChart()"></select>
                        </div>
                        <canvas id="grafikSettlement" height="120"></canvas>
                    </div>

                    <!-- EXPENSE -->
                    <div class="border border-gray-100 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-700 mb-2">Operational Expense</h3>
                        <canvas id="grafikExpense" height="120"></canvas>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const dummyOrder = {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            data: [120, 150, 180, 200, 170, 210]
        };

        const dummyRevenue = {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            data: [22, 25, 28, 35, 30, 37]
        };

        const dummySettlementByDate = {
            "2025-01-01": [5, 7, 6, 8, 9, 10],
            "2025-01-02": [3, 6, 5, 7, 4, 8],
            "2025-01-03": [6, 9, 8, 10, 7, 12]
        };

        const dummyExpense = {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            data: [5, 7, 6.5, 6, 7.2, 6.8]
        };

        new Chart(grafikHistoy, {
            type: "line",
            data: {
                labels: dummyOrder.labels,
                datasets: [{
                    data: dummyOrder.data,
                    borderColor: "#6366f1",
                    tension: 0.4
                }]
            }
        });

        new Chart(grafikRevenue, {
            type: "bar",
            data: {
                labels: dummyRevenue.labels,
                datasets: [{
                    data: dummyRevenue.data,
                    backgroundColor: "#22c55e"
                }]
            }
        });

        Object.keys(dummySettlementByDate).forEach(date => {
            dateSelect.innerHTML += `<option value="${date}">${date}</option>`;
        });

        let settlementChart = new Chart(grafikSettlement, {
            type: "line",
            data: {
                labels: ["A", "B", "C", "D", "E", "F"],
                datasets: [{
                    data: dummySettlementByDate["2025-01-01"],
                    borderColor: "#f59e0b",
                    tension: 0.4
                }]
            }
        });

        function updateChart() {
            settlementChart.data.datasets[0].data =
                dummySettlementByDate[dateSelect.value];
            settlementChart.update();
        }

        new Chart(grafikExpense, {
            type: "bar",
            data: {
                labels: dummyExpense.labels,
                datasets: [{
                    data: dummyExpense.data,
                    backgroundColor: "#ef4444"
                }]
            }
        });
    </script>

    @include('sweetalert::alert')

</body>

</html>
