@extends('common_template')
@section('content')
<title>Buy/Sale Report</title>

<div class="content-body">
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center" style="padding: 10px; background-color: #f9f9f9; border-radius: 10px; gap: 30px;">
                        <div class="d-flex flex-column">
                            <label for="actionSelect" class="font-weight-bold">Select Action</label>
                            <select class="form-control" id="actionSelect" style="max-width: 150px;" onchange="showReport()">
                                <option value="buy" selected>Purchase</option>
                                <option value="sell">Redemption</option>
                            </select>
                        </div>
                        <div class="d-flex flex-column">
                            <label for="dateRangeFilter" class="font-weight-bold">Select Date Range:</label>
                            <input type="text" class="form-control" id="dateRangeFilter" placeholder="Select date range" readonly style="max-width: 200px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buy Report Table -->
            <div id="buyReport" class="report-section mt-4">
                <h3 class="text-center text-white bg-success py-2 rounded-top font-weight-bold">Buy Report</h3>
                <table class="table table-bordered table-striped" id="buyTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Fund Name</th>
                            <th>Buying Date</th>
                            <th>Quantity of Shares</th>
                            <th>Price Per Unit</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Sell Report Table (Compact Version) -->
            <div id="sellReport" class="report-section mt-4 d-none">
                <h3 class="text-center text-white bg-info py-2 rounded-top font-weight-bold">Sell Report</h3>
                <table class="table table-sm table-bordered table-striped" id="sellTable" style="width: 100%; font-size: 0.9em;">
                    <thead>
                        <tr>
                            <th>Fund Name</th>
                            <th>Selling Date</th>
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
        // Initialize Buy Report Table
        var buyTable = $('#buyTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("report.buy") }}',
                data: function(d) {
                    d.date_range = $('#dateRangeFilter').val();
                }
            },
            columns: [{
                    data: 'fund_name',
                    name: 'fund_name'
                },
                {
                    data: 'buying_date',
                    name: 'buying_date'
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
                emptyTable: "No data available for the selected date range."
            }
        });

        // Initialize Sell Report Table
        var sellTable = $('#sellTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("report.sell") }}',
                data: function(d) {
                    d.date_range = $('#dateRangeFilter').val();
                }
            },
            columns: [{
                    data: 'fund_name',
                    name: 'fund_name'
                },
                {
                    data: 'selling_date',
                    name: 'selling_date'
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
                emptyTable: "No data available for the selected date range."
            }
        });

        // Initialize Date Range Picker
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
        });

        $('#dateRangeFilter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            buyTable.ajax.reload();
            sellTable.ajax.reload();
        });

        // Handle Edit and Delete Actions
        $('#buyTable, #sellTable').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            // Redirect to the edit page with the selected report ID
            window.location.href = '/report/edit/' + id; // Adjust this path according to your routing
        });



        $('#buyTable, #sellTable').on('click', '.delete-btn', function() {
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
                    },
                    error: function() {
                        alert('An error occurred while deleting.');
                    }
                });
            }
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
            $('#buyTable').DataTable().columns.adjust().draw();
        } else {
            sellSection.classList.remove('d-none');
            buySection.classList.add('d-none');
            $('#sellTable').DataTable().columns.adjust().draw();
        }
    }
</script>
@endsection