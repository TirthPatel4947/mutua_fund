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

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-muted mb-0">Mutual Fund Details</h3>
                    <div class="d-flex align-items-center">
                        <label for="portfolioSelect" class="form-label me-2 mb-0">Select Portfolio:</label>
                        <select id="portfolioSelect" class="form-select" style="width: 170px; height: 38px;">
                            <option value="all">All Portfolios</option>
                            @foreach($portfolios as $portfolio)
                            <option value="{{ $portfolio->id }}">{{ $portfolio->name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>


                <table class="table table-bordered text-center mt-3" id="fundDetailsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Fund Name</th>
                            <th>
                                <div class="d-flex flex-column">
                                    <span>Latest NAV</span>
                                    <span class="text-muted" style="font-size: 0.7em;">NAV Date</span>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex flex-column">
                                    <span>Invested Amount</span>
                                    <span class="text-muted" style="font-size: 0.7em;">Units Held</span>
                                </div>
                            </th>
                            <th>Market Value</th>
                            <th>Profit/Loss</th>
                            <th>Percentage Gain/Loss</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Add CSRF token to AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let table = $('#fundDetailsTable').DataTable({
            processing: true,
            serverSide: true,
            language: {
                emptyTable: "No data available in the selected portfolio"
            },
            ajax: {
                url: "{{ route('fund.details') }}",
                data: function(d) {
                    d.portfolio_id = $('#portfolioSelect').val();
                }
            },
            columns: [{
                    data: 'fund_name',
                    name: 'fund_name',
                    className: 'text-center'
                },
                {
                    data: null,
                    name: 'last_nav',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.current_nav +
                            '<br><span style="font-size: 0.8em;">' + row.nav_date + '</span>';
                    }
                },
                {
                    data: null,
                    name: 'total_cost_and_units',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.total_investment +
                            '<br><span style="font-size: 0.8em;">' + row.total_units + ' units</span>';
                    }
                },
                {
                    data: 'current_value',
                    name: 'current_value',
                    className: 'text-center'
                },
                {
                    data: 'absolute_profit_or_loss',
                    name: 'absolute_profit_or_loss',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.profit_or_loss < 0 ?
                            '<span class="text-danger">' + data + '</span>' :
                            '<span class="text-success">' + data + '</span>';
                    }
                },
                {
                    data: 'percentage_gain',
                    name: 'percentage_gain',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        let value = parseFloat(data);
                        return value > 0 ?
                            '<span class="text-success">' + value.toFixed(2) + '%</span>' :
                            '<span class="text-danger">' + value.toFixed(2) + '%</span>';
                    }
                }
            ],
            order: [
                [0, "asc"]
            ]
        });


        $('#portfolioSelect').change(function() {
            table.ajax.reload();
        });
    });
</script>
@endsection

@section('styles')
<style>
    .table thead th {
        vertical-align: middle;
        color: black;
        font-weight: normal;
    }
</style>
@endsection