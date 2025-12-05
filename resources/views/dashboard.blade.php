<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    @include('layout.head')
</head>

<body class="bg-gray-50">

    <!-- sidenav  -->
    @include('layout.sidebar')
    <!-- end sidenav -->
    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        <!-- Navbar -->
        @include('layout.navbar')
        <!-- end Navbar -->
        <div class="p-6">

            <!-- chart section -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-2 lg:grid-cols-2 ">
                <!-- chart 1: Total Order -->
                <div class="p-6 bg-white rounded-xl shadow-xl">
                    <h1 class="font-light">Total Order</h1>
                    <i class="fa fa-arrow-up text-lime-500"></i>
                    <canvas id="grafikHistoy" width="100" height="50"></canvas>
                </div>
                <!-- chart 2: Total Revenue -->
                <div class="p-6 bg-white rounded-xl shadow-xl">
                    <h1 class="font-light">Total Revenue</h1>
                    <i class="fa fa-arrow-up text-lime-500"></i>
                    <canvas id="grafikRevenue" width="100" height="50"></canvas>
                </div>
                <!-- chart 3: Settlement -->
                <div class="p-6 bg-white rounded-xl shadow-xl">
                    <h1 class="font-light">Settlement</h1>
                    <i class="fa fa-arrow-up text-lime-500"></i>
                    <label for="dateSelect">Select date:</label>
                    <select class="border bg-gray-100 p-2 rounded-xl" id="dateSelect" onchange="updateChart()">

                    </select>
                    <canvas id="grafikSettlement" width="100" height="50"></canvas>
                </div>
                <!-- chart 4: Total Expense -->
                <div class="p-6 bg-white rounded-xl shadow-xl">
                    <h1 class="font-light">Total Expense</h1>
                    <i class="fa fa-arrow-up text-lime-500"></i>
                    <canvas id="grafikExpense" width="100" height="50"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
// ===============================
// DUMMY DATA
// ===============================
const dummyOrder = {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    data: [120, 150, 180, 200, 170, 210]
};

const dummyRevenue = {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    data: [22000000, 25000000, 28000000, 35000000, 30000000, 37000000]
};

const dummySettlementByDate = {
    "2025-01-01": [5, 7, 6, 8, 9, 10],
    "2025-01-02": [3, 6, 5, 7, 4, 8],
    "2025-01-03": [6, 9, 8, 10, 7, 12]
};

const dummyExpense = {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    data: [5000000, 7000000, 6500000, 6000000, 7200000, 6800000]
};


// ===============================
// TOTAL ORDER CHART
// ===============================
new Chart(document.getElementById("grafikHistoy"), {
    type: "line",
    data: {
        labels: dummyOrder.labels,
        datasets: [{
            label: "Total Orders",
            data: dummyOrder.data,
            borderColor: "rgb(75, 192, 192)",
            borderWidth: 2,
            fill: false,
        }]
    }
});

// ===============================
// TOTAL REVENUE CHART
// ===============================
new Chart(document.getElementById("grafikRevenue"), {
    type: "bar",
    data: {
        labels: dummyRevenue.labels,
        datasets: [{
            label: "Total Revenue",
            data: dummyRevenue.data,
            backgroundColor: "rgba(54, 162, 235, 0.7)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1,
        }]
    }
});

// ===============================
// SETTLEMENT CHART (with date select)
// ===============================
const dateSelect = document.getElementById("dateSelect");
Object.keys(dummySettlementByDate).forEach(date => {
    const opt = document.createElement("option");
    opt.value = date;
    opt.textContent = date;
    dateSelect.appendChild(opt);
});

let settlementChart = new Chart(document.getElementById("grafikSettlement"), {
    type: "line",
    data: {
        labels: ["A", "B", "C", "D", "E", "F"],
        datasets: [{
            label: "Settlement",
            data: dummySettlementByDate["2025-01-01"],
            borderColor: "rgb(255, 159, 64)",
            borderWidth: 2,
            fill: false,
        }]
    }
});

function updateChart() {
    const selectedDate = dateSelect.value;
    settlementChart.data.datasets[0].data = dummySettlementByDate[selectedDate];
    settlementChart.update();
}

// ===============================
// TOTAL EXPENSE CHART
// ===============================
new Chart(document.getElementById("grafikExpense"), {
    type: "bar",
    data: {
        labels: dummyExpense.labels,
        datasets: [{
            label: "Total Expense",
            data: dummyExpense.data,
            backgroundColor: "rgba(255, 99, 132, 0.7)",
            borderColor: "rgba(255, 99, 132, 1)",
            borderWidth: 1,
        }]
    }
});
</script>


    @include('sweetalert::alert')

</body>

</html>
