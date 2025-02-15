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

                <h3 class="text-muted">Mutual Fund Details</h3>

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

        $('#fundDetailsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('fund.details') }}",
            columns: [
                { data: 'fund_name', name: 'fund_name', className: 'text-center' },

                // Latest NAV with Date
                {
                    data: null,
                    name: 'last_nav',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.current_nav +
                            '<br><span style="font-size: 0.8em;">' +
                            row.nav_date + '</span>';
                    }
                },

                // Combined Total Cost and Units
                {
                    data: null,
                    name: 'total_cost_and_units',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.total_investment +
                            '<br><span style="font-size: 0.8em;">' +
                            row.total_units + ' units</span>';
                    }
                },

                { data: 'current_value', name: 'current_value', className: 'text-center' },

                // Profit/Loss with Color Indication
                {
                    data: 'absolute_profit_or_loss',
                    name: 'absolute_profit_or_loss',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (row.profit_or_loss < 0) {
                            return '<span class="text-danger">-' + data + '</span>';
                        } else if (row.profit_or_loss > 0) {
                            return '<span class="text-success">+' + data + '</span>';
                        } else {
                            return data;
                        }
                    }
                },

                // Percentage Gain/Loss with Color Indication
                {
                    data: 'percentage_gain',
                    name: 'percentage_gain',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        let value = parseFloat(data);
                        if (value > 0) {
                            return '<span class="text-success">+' + value.toFixed(2) + '%</span>';
                        } else if (value < 0) {
                            return '<span class="text-danger">' + value.toFixed(2) + '%</span>';
                        } else {
                            return value.toFixed(2) + '%';
                        }
                    }
                }
            ],
            order: [[0, "asc"]]
        });
    });
</script>
@endsection

@section('styles')
<style>
    .custom-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1.1;
    }

    .custom-header .subtext {
        font-size: 0.90em;
        margin-top: -2px;
    }

    /* Table Header Styles */
    .table thead th {
        vertical-align: middle;
        color: black;  /* Standard black color */
        font-weight: normal;  /* No highlight */
    }
</style>
@endsection