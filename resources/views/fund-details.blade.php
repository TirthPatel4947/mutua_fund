@extends('common_template')

@section('content')
    <title>Fund Details</title>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Back Link -->
                    <a href="{{ url('/dashboard') }}" class="btn btn-secondary mb-3">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    
                    <h3 class="text-muted">Fund-wise Details</h3>
                    <!-- Search bar with icon -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i> <!-- Search icon -->
                            </span>
                        </div>
                        <input type="text" id="searchBar" class="form-control" placeholder="Search by Fund Name">
                    </div>

                    <table class="table table-bordered mt-3" id="fundTable">
                        <thead>
                            <tr>
                                <th>Fund Name</th>
                                <th>Total Units</th>
                                <th>Investment Amount</th>
                                <th>Current Value</th>
                                <th>Profit/Loss</th>
                                <th>Percentage Gain</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fundDetails as $fund)
                                <tr>
                                    <td>{{ $fund['fund_name'] }}</td>
                                    <td>{{ $fund['total_units'] }}</td>
                                    <td>₹{{ $fund['total_investment'] }}</td>
                                    <td>₹{{ $fund['current_value'] }}</td>
                                    <td>
                                        {{ $fund['profit_or_loss_label'] }}: ₹{{ $fund['absolute_profit_or_loss'] }}
                                    </td>
                                    <td>{{ $fund['percentage_gain'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Adding JavaScript for search functionality -->
    <script>
        // Get the search input and table
        const searchInput = document.getElementById('searchBar');
        const table = document.getElementById('fundTable');
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const fundNameCell = cells[0];

                if (fundNameCell) {
                    const fundName = fundNameCell.textContent || fundNameCell.innerText;

                    // If fund name matches the search query, show the row, otherwise hide it
                    if (fundName.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        });
    </script>
@endsection