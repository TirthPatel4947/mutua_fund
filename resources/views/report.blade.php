@extends('common_template')
@section('content')
<title>Buy/Sale Report</title>

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2"></div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2"></div>
</div>

<div class="content-body">
    <!-- Card for Select Action, Search Bar, and Reports -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <!-- Option Selection (Buy / Sell) -->
                <!-- <div class="col-md-6"> -->
                    <div style="padding: 10px; background-color: #f9f9f9; border-radius: 8px;">
                        <h5 style="font-weight: bold;">Select Action</h5>
                        <select class="form-control" id="actionSelect" style="max-width: 150px; display: inline-block;" onchange="showReport()">
                            <option value="buy" selected>Buy</option>
                            <option value="sell">Sell</option>
                        </select>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="col-md-6">
                <div class="input-group" style="width: 210%; margin-top: 20px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i> <!-- Search icon -->
                            </span>
                        </div>
                        <input type="text" class="form-control" id="searchBar" placeholder="Search by Fund Name">
                    </div>
                </div>
            </div>

            <!-- Buy Report Section (Initially Visible) -->
            <div id="buyReport" class="report-section mt-4">
                <div class="table-responsive rounded border shadow-sm mb-3">
                    <h3 class="text-center text-white bg-success py-2 rounded-top font-weight-bold">Buy Report</h3>
                    <table class="table table-bordered table-striped mb-0" id="buyTable">
                        <thead class="bg-primary text-white sticky-top">
                            <tr>
                                <th class="text-center">Fund Name</th>
                                <th class="text-center">Buying Date</th>
                                <th class="text-center">Quantity of Shares</th>
                                <th class="text-center">Price Per Unit</th>
                                <th class="text-center">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($buyReports as $report)
                            <tr>
                                <td class="text-center">{{ optional($report->fund)->fundname ?? 'N/A' }}</td>
                                <td class="text-center">{{ $report->date }}</td>
                                <td class="text-center">{{ $report->unit }}</td>
                                <td class="text-center">₹{{ number_format($report->price / ($report->unit ?: 1), 2) }}</td>
                                <td class="text-center">₹{{ number_format($report->price, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No Buy Data Available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sell Report Section (Initially Hidden) -->
            <div id="sellReport" class="report-section mt-4 d-none">
                <div class="table-responsive rounded border shadow-sm mb-3">
                    <h3 class="text-center text-white bg-info py-2 rounded-top font-weight-bold">Sell Report</h3>
                    <table class="table table-bordered table-striped mb-0" id="sellTable">
                        <thead class="bg-danger text-white sticky-top">
                            <tr>
                                <th class="text-center">Fund Name</th>
                                <th class="text-center">Selling Date</th>
                                <th class="text-center">Quantity of Shares</th>
                                <th class="text-center">Price Per Unit</th>
                                <th class="text-center">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sellReports as $report)
                            <tr>
                                <td class="text-center">{{ optional($report->fund)->fundname ?? 'N/A' }}</td>
                                <td class="text-center">{{ $report->date }}</td>
                                <td class="text-center">{{ $report->unit }}</td>
                                <td class="text-center">₹{{ number_format($report->price / ($report->unit ?: 1), 2) }}</td>
                                <td class="text-center">₹{{ number_format($report->price, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No Sell Data Available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to show buy or sell report based on selection
    function showReport() {
        const action = document.getElementById('actionSelect').value;
        document.getElementById('buyReport').classList.toggle('d-none', action !== 'buy');
        document.getElementById('sellReport').classList.toggle('d-none', action !== 'sell');
    }

    // Adding search functionality for the table
    document.getElementById('searchBar').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const buyRows = document.getElementById('buyTable').getElementsByTagName('tr');
        const sellRows = document.getElementById('sellTable').getElementsByTagName('tr');

        // Filter Buy Report
        filterTable(buyRows, searchValue);

        // Filter Sell Report
        filterTable(sellRows, searchValue);
    });

    // Function to filter rows based on search value
    function filterTable(rows, searchValue) {
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            const fundNameCell = cells[0];

            if (fundNameCell) {
                const fundName = fundNameCell.textContent || fundNameCell.innerText;

                if (fundName.toLowerCase().indexOf(searchValue) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    }
</script>

@endsection
