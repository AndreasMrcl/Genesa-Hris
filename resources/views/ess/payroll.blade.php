<!DOCTYPE html>
<html lang="en">

<head>
    <title>ESS | Payroll</title>
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    @include('ess.layout.head')
</head>

<body class="bg-gray-50 font-sans w-full md:max-w-sm mx-auto">

    <!-- HEADER / BACK BUTTON -->
    <div class="p-2">
        <a href="{{ route('ess-home') }}"
            class="inline-flex items-center gap-2 px-6 py-2 bg-white text-gray-700 rounded-xl text-3xl">
            <span>&larr;</span>
        </a>
    </div>


    <!-- PAYROLL -->
    <div class="p-2">
        <!-- Back Button -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
            <!-- Table Section -->
            <h2 class="text-lg font-bold text-gray-800 mb-3">Payroll</h2>
            <div class="overflow-auto">

            </div>
        </div>
    </div>


    @include('layout.loading')

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Init DataTable
            new DataTable('#myTable', {});
        });
    </script>
</body>

</html>
