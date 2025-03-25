@extends('common_template')
@section('content')
<title>Buy/Sale Report</title>
<div class="content-body">
    <div class="card mb-3">
        <div class="card-body">
            <div class="row" style="padding: 10px;">
                <div class="col-md-4">
                    <label for="portfolioSelect" class="font-weight-bold">Select Portfolio</label>
                    <select class="form-control form-control-lg" id="portfolioSelect">
                        <option value="">All Portfolios</option>
                        @foreach($portfolios as $portfolio)
                        <option value="{{ $portfolio->id }}">{{ $portfolio->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="actionSelect" class="font-weight-bold">Select Transaction Type</label>
                    <select class="form-control form-control-lg" id="actionSelect">
                        <option value="all" selected>All</option>
                        <option value="buy">Purchase</option>
                        <option value="sell">Redemption</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="dateRangeFilter" class="font-weight-bold">Select Date Range:</label>
                    <input type="text" class="form-control form-control-lg" id="dateRangeFilter" placeholder="Select Date Range" readonly>
                </div>
            </div>
            <div class="float-right mt-2">
                <button class="btn btn-primary btn-sm px-4" id="exportExcelBtn">
                    <i class="feather icon-upload"></i> Export Excel
                </button>
            </div>

            <!-- Reports Table -->
            <div id="reportSection" class="mt-4">
            <h3 id="tableTitle" class="text-center text-white bg-warning py-2 rounded-top font-weight-bold">
    Buy/Sale Report
</h3>

                <table class="table table-bordered table-striped" id="reportTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Portfolio Name</th>
                            <th>Fund Name</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Price Per Unit</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    function updateTableTitle() {
        let selectedFilter = $('#actionSelect').val();
        let newTitle = "Buy/Sale Report"; // Default Title

        if (selectedFilter === 'buy') {
            newTitle = "Buy Report";
        } else if (selectedFilter === 'sell') {
            newTitle = "Sale Report";
        }

        $('#tableTitle').text(newTitle); // Update Title
    }

    // Initialize Date Range Picker
    $('#dateRangeFilter').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear' }
    });

    $('#dateRangeFilter').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        loadReportData();
    });

    $('#dateRangeFilter').on('cancel.daterangepicker', function() {
        $(this).val('');
        loadReportData();
    });

    // DataTable Initialization
    var reportTable = $('#reportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("reports.data") }}',
        data: function(d) {
            d.date_range = $('#dateRangeFilter').val();
            d.portfolio_id = $('#portfolioSelect').val();
            d.type = $('#actionSelect').val();
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error: ", error);
        }
    },
    columns: [
        { data: 'portfolio_name', name: 'portfolio_name' },
        { data: 'fund_name', name: 'fund_name' },
        { data: 'date', name: 'date' },
        { data: 'type', name: 'type' },
        { data: 'quantity_of_shares', name: 'quantity_of_shares' },
        { data: 'price_per_unit', name: 'price_per_unit' },
        { data: 'total_price', name: 'total_price' },
        {
            data: 'id',
            name: 'action',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                const editUrl = row.type === 'Purchase' 
                    ? `/report/edit/${data}`
                    : `/report/sale/edit/${data}`;
                return `
                    <button class="btn btn-sm btn-primary edit-btn" data-url="${editUrl}">Edit</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">Delete</button>
                `;
            }
        }
    ],
    drawCallback: function(settings) {
        let api = this.api();
        let rowCount = api.rows({ search: 'applied' }).count();

        let selectedPortfolio = $('#portfolioSelect').val();
        let selectedDateRange = $('#dateRangeFilter').val();
        let selectedType = $('#actionSelect').val();

        let message = "No data available.";

        if (selectedPortfolio) {
            message = "The selected portfolio has no data.";
        } 
        if (selectedDateRange) {
            message = "No data available for the selected date range.";
        }
        if (selectedType === 'buy') {
            message = "No purchase transactions found.";
        } 
        if (selectedType === 'sell') {
            message = "No redemption transactions found.";
        }
        if (!selectedPortfolio && !selectedDateRange && selectedType === 'all') {
            message = "No transactions found.";
        }

        if (rowCount === 0) {
            $('#reportTable tbody').html(`<tr><td colspan="8" class="text-center font-weight-bold text-danger">${message}</td></tr>`);
        }
    }
});


    // Load Report Data when filters change
    function loadReportData() {
        updateTableTitle(); // Update the table name
        reportTable.ajax.reload();
    }

    $('#portfolioSelect, #actionSelect').on('change', function() {
        loadReportData();
    });

    // Handle Export Button Click
    $('#exportExcelBtn').on('click', function() {
        const portfolioId = $('#portfolioSelect').val() || '';
        const dateRange = $('#dateRangeFilter').val() || '';
        const action = $('#actionSelect').val();

        let exportUrl = '{{ route("report.export.combined") }}';
        if (action === 'buy') exportUrl = '{{ route("report.export.buy") }}';
        else if (action === 'sell') exportUrl = '{{ route("report.export.sell") }}';

        exportUrl += `?portfolio_id=${portfolioId}&date_range=${encodeURIComponent(dateRange)}`;
        window.location.href = exportUrl;
    });

    // Handle Edit Button Click
    $('#reportTable').on('click', '.edit-btn', function() {
        window.location.href = $(this).data('url');
    });

    // Handle Delete Button Click
    $('#reportTable').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: `/report/delete/${id}`,
                type: 'DELETE',
                data: { "_token": "{{ csrf_token() }}" },
                success: function() {
                    alert('Item deleted successfully.');
                    reportTable.ajax.reload();
                },
                error: function() {
                    alert('An error occurred while deleting.');
                }
            });
        }
        
    });
    

    updateTableTitle(); // Set initial title on page load
});

</script>

<style>
#dateRangeFilter::placeholder {
    font-weight: 380;
    color: #000;
    opacity: 1;
}
</style>
@endsection
