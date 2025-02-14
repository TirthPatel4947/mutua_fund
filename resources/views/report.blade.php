@extends('common_template')
@section('content')
<title>Buy/Sale Report</title>

<div class="content-body">
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <div style="padding: 10px; background-color: #f9f9f9; border-radius: 8px;">
                    <h5 style="font-weight: bold;">Select Action</h5>
                    <select class="form-control" id="actionSelect" style="max-width: 150px; display: inline-block;" onchange="showReport()">
                        <option value="buy" selected>Buy</option>
                        <option value="sell">Sell</option>
                    </select>
                </div>
            </div>

            <!-- Buy Report Table -->
            <div id="buyReport" class="report-section mt-4">
                <h3 class="text-center text-white bg-success py-2 rounded-top font-weight-bold">Buy Report</h3>
                <table class="table table-bordered table-striped" id="buyTable">
                    <thead>
                        <tr>
                            <th>Fund Name</th>
                            <th>Buying Date</th>
                            <th>Quantity of Shares</th>
                            <th>Price Per Unit</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Sell Report Table -->
            <div id="sellReport" class="report-section mt-4 d-none">
                <h3 class="text-center text-white bg-info py-2 rounded-top font-weight-bold">Sell Report</h3>
                <table class="table table-bordered table-striped" id="sellTable">
                    <thead>
                        <tr>
                            <th>Fund Name</th>
                            <th>Selling Date</th>
                            <th>Quantity of Shares</th>
                            <th>Price Per Unit</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<!-- Yajra DataTables Script -->
<script>
$(document).ready(function() {
    // Initialize Buy Report Table
    var buyTable = $('#buyTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("report.buy") }}',
        columns: [
            { data: 'fund_name', name: 'fund_name' },
            { data: 'buying_date', name: 'buying_date' },
            { data: 'quantity_of_shares', name: 'quantity_of_shares' },
            { data: 'price_per_unit', name: 'price_per_unit' },
            { data: 'total_price', name: 'total_price' }
        ]
    });

    // Initialize Sell Report Table but do not draw it yet
    var sellTable = $('#sellTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("report.sell") }}',
        columns: [
            { data: 'fund_name', name: 'fund_name' },
            { data: 'selling_date', name: 'selling_date' },
            { data: 'quantity_of_shares', name: 'quantity_of_shares' },
            { data: 'price_per_unit', name: 'price_per_unit' },
            { data: 'total_price', name: 'total_price' }
        ],
        autoWidth: false // Prevent auto-resizing issues
    });
});

// Function to toggle between Buy and Sell tables
function showReport() {
    const action = document.getElementById('actionSelect').value;
    const buySection = document.getElementById('buyReport');
    const sellSection = document.getElementById('sellReport');

    if (action === 'buy') {
        buySection.classList.remove('d-none');
        sellSection.classList.add('d-none');
        $('#buyTable').DataTable().columns.adjust().draw(); // Adjust and redraw Buy Table
    } else {
        sellSection.classList.remove('d-none');
        buySection.classList.add('d-none');
        $('#sellTable').DataTable().columns.adjust().draw(); // Adjust and redraw Sell Table
    }
}

</script>
@endsection
