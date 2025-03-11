@extends('common_template')
@section('content')
<title>Buy/Sale Report</title>
<div class="content-body">
    <div class="card mb-3"> <!-- Removed shadow-sm -->
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
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
                            <select class="form-control form-control-lg" id="actionSelect" onchange="showReport()">
                                <option value="all" selected>All</option> <!-- Added "All" option -->
                                <option value="buy">Purchase</option>
                                <option value="sell">Redemption</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="dateRangeFilter" class="font-weight-bold">Select Date Range:</label>
                            <input type="text" class="form-control form-control-lg" id="dateRangeFilter"
                                placeholder="Select Date Range" readonly
                                style="background-color: #fff; color: #000; border: 1px solid #ccc; font-weight: 700;">
                        </div>

                    </div>
                </div>
            </div>
            <div class="float-right mt-2">
                <button class="btn btn-primary btn-sm px-4" id="exportExcelBtn">
                    <i class="feather icon-upload"></i> Export Excel
                </button>
            </div>
            <!-- Combined Report Table -->
            <div id="combinedReport" class="report-section mt-4 d-none">
                <h3 class="text-center text-white bg-warning py-2 rounded-top font-weight-bold">Combined Report</h3>
                <table class="table table-bordered table-striped" id="combinedTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Portfolio Name</th>
                            <th>Fund Name</th>
                            <th>date</th>
                            <th>type</th>
                            <th>Quantity of Shares</th>
                            <th>Price Per Unit</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- Buy Report Table -->
            <div id="buyReport" class="report-section mt-4">
                <h3 class="text-center text-white bg-success py-2 rounded-top font-weight-bold">Buy Report</h3>
                <table class="table table-bordered table-striped" id="buyTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Portfolio Name</th>
                            <th>Fund Name</th>
                            <th>Buying Date</th>
                            <th>Type</th>
                            <th>Quantity of Shares</th>
                            <th>Price Per Unit</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Sell Report Table -->
            <div id="sellReport" class="report-section mt-4 d-none">
                <h3 class="text-center text-white bg-info py-2 rounded-top font-weight-bold">Sell Report</h3>
                <table class="table table-sm table-bordered table-striped" id="sellTable" style="width: 100%; font-size: 0.9em;">
                    <thead>
                        <tr>
                            <th>Portfolio Name</th>
                            <th>Fund Name</th>
                            <th>Selling Date</th>
                            <th>Type</th>
                            <th>Quantity of Shares</th>
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
        // Function to compute the appropriate empty table message
        function getEmptyTableMessage() {
            var dateRange = $('#dateRangeFilter').val();
            var portfolioId = $('#portfolioSelect').val();
            if (dateRange && !portfolioId) {
                return "No data available for the selected date range.";
            } else if (!dateRange && portfolioId) {
                return "No data available for the selected portfolio.";
            } else if (dateRange && portfolioId) {
                return "No data available for the selected date range and portfolio.";
            } else {
                return "No data available.";
            }
        }

        function initializeCombinedTable() {
    return $('#combinedTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("report.combined") }}',
            data: function(d) {
                d.date_range = $('#dateRangeFilter').val();
                d.portfolio_id = $('#portfolioSelect').val();
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
                        ? `/report/edit/${data}`        // Redirect to Buy page
                        : `/report/sale/edit/${data}`; // Redirect to Sale page

                    return `
                        <button class="btn btn-sm btn-primary edit-btn" data-url="${editUrl}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">Delete</button>
                    `;
                }
            }
        ],
        language: {
            emptyTable: "No data available for the selected criteria."
        }
    });
}

        // Initialize Buy Report Table
        function initializeBuyTable() {
            return $('#buyTable').DataTable({
                destroy: true, // Destroy existing instance before reloading
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("report.buy") }}',
                    data: function(d) {
                        d.date_range = $('#dateRangeFilter').val();
                        d.portfolio_id = $('#portfolioSelect').val(); // Pass selected portfolio
                    }
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'fund_name',
                        name: 'fund_name'
                    },
                    {
                        data: 'buying_date',
                        name: 'buying_date'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            return data == 1 ? 'Purchase' : 'Redemption';
                        }
                    },
                    {
                        data: 'quantity_of_shares',
                        name: 'quantity_of_shares'
                    },
                    {
                        data: 'price_per_unit',
                        name: 'price_per_unit'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${data}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">Delete</button>
                        `;
                        }
                    }
                ],
                language: {
                    emptyTable: getEmptyTableMessage()
                },
                drawCallback: function(settings) {
                    var api = this.api();
                    if (api.rows({
                            filter: 'applied'
                        }).count() === 0) {
                        $('#buyTable tbody').html('<tr><td colspan="7" class="text-center">' + getEmptyTableMessage() + '</td></tr>');
                    }
                }
            });
        }

        // Initialize Sell Report Table using your sale data method
        function initializeSellTable() {
            return $('#sellTable').DataTable({
                destroy: true, // Destroy existing instance before reloading
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("report.sell") }}',
                    data: function(d) {
                        d.date_range = $('#dateRangeFilter').val();
                        d.portfolio_id = $('#portfolioSelect').val(); // Pass selected portfolio
                    }
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'fund_name',
                        name: 'fund_name'
                    },
                    {
                        data: 'selling_date',
                        name: 'selling_date'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            return data == 1 ? 'Purchase' : 'Redemption';
                        }
                    },
                    {
                        data: 'quantity_of_shares',
                        name: 'quantity_of_shares'
                    },
                    {
                        data: 'price_per_unit',
                        name: 'price_per_unit'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${data}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">Delete</button>
                        `;
                        }
                    }
                ],
                
                language: {
                    emptyTable: getEmptyTableMessage()
                },
                drawCallback: function(settings) {
                    var api = this.api();
                    if (api.rows({
                            filter: 'applied'
                        }).count() === 0) {
                        $('#sellTable tbody').html('<tr><td colspan="7" class="text-center">' + getEmptyTableMessage() + '</td></tr>');
                    }
                }
                
            });
        }

        // Initialize both tables on page load
        var buyTable = initializeBuyTable();
        var sellTable = initializeSellTable();
        var combinedTable = initializeCombinedTable();

        // Reload tables when portfolio selection changes
        $('#portfolioSelect').on('change', function() {
            buyTable.destroy();
            sellTable.destroy();
            combinedTable.destroy();

            buyTable = initializeBuyTable();
            sellTable = initializeSellTable();
            combinedTable = initializeCombinedTable();
        });

        // Handle Date Range Filter
        $('#dateRangeFilter').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#dateRangeFilter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            buyTable.ajax.reload();
            sellTable.ajax.reload();
            combinedTable.ajax.reload();
        });

        $('#dateRangeFilter').on('cancel.daterangepicker', function() {
            $(this).val('');
            buyTable.ajax.reload();
            sellTable.ajax.reload();
            combinedTable.ajax.reload();
        });

        // Handle Edit and Delete Actions
        $('#buyTable').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            // Redirect to the Buy edit page
            window.location.href = '/report/edit/' + id; // Adjust the path to match the correct route for Buy
        });

        $('#sellTable').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            // Redirect to the Sell edit page
            window.location.href = '/report/sale/edit/' + id; // Adjust the path to match the correct route for Sale
        });
        $('#combinedTable').on('click', '.edit-btn', function() {
    const editUrl = $(this).data('url');
    window.location.href = editUrl;
});

        $('#buyTable, #sellTable, #combinedTable').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: `/report/delete/${id}`,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert('Item deleted successfully.');
                        buyTable.ajax.reload();
                        sellTable.ajax.reload();
                        combinedTable.ajax.reload();
                    },
                    error: function() {
                        alert('An error occurred while deleting.');
                    }
                });
            }
        });



        // Toggle function for Buy vs. Sell views
        function showReport() {
            const action = $('#actionSelect').val();
            const buySection = $('#buyReport');
            const sellSection = $('#sellReport');
            const combinedSection = $('#combinedReport');

            if (action === 'all') {
                buySection.addClass('d-none');
                sellSection.addClass('d-none');
                combinedSection.removeClass('d-none');
                $('#combinedTable').DataTable().columns.adjust().draw();
            } else if (action === 'buy') {
                combinedSection.addClass('d-none');
                buySection.removeClass('d-none');
                sellSection.addClass('d-none');
                $('#buyTable').DataTable().columns.adjust().draw();
            } else if (action === 'sell') {
                combinedSection.addClass('d-none');
                sellSection.removeClass('d-none');
                buySection.addClass('d-none');
                $('#sellTable').DataTable().columns.adjust().draw();
            }
        }
        // Attach the toggle function to the action select change event
        $('#actionSelect').on('change', function() {
            showReport();
        });
        // Default selection to "All" on page load
        $('#actionSelect').val('all').change(); // ✅ Auto-select "All" and trigger change
    });

    
    //export btn click
    $('#exportExcelBtn').on('click', function() {
        const portfolioId = $('#portfolioSelect').val() || '';
        const dateRange = $('#dateRangeFilter').val() || '';

        let exportUrl = '';

        const action = $('#actionSelect').val();
        if (action === 'buy') {
            exportUrl = '{{ route("report.export.buy") }}';
        } else if (action === 'sell') {
            exportUrl = '{{ route("report.export.sell") }}';
        } else {
            exportUrl = '{{ route("report.export.combined") }}';
        }

        // Append filters to export URL
        exportUrl += `?portfolio_id=${portfolioId}&date_range=${encodeURIComponent(dateRange)}`;

        window.location.href = exportUrl;
    });

</script>

<style>
    #dateRangeFilter::placeholder {
        font-weight: 380;
        /* Extra Bold */
        color: #000;
        /* Dark Black */
        opacity: 1;
        /* Ensure full visibility */
    }
</style>
@endsection