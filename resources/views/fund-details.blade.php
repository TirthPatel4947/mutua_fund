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
                            <th>Last Price (NAV)</th>
                            <th>
                                <div class="d-flex flex-column">
                                    <span>Total Cost</span>
                                    <span class="text-muted" style="font-size: 0.7em;">COST PER UNIT</span>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex flex-column">
                                    <span>Current Value</span>
                                    <span class="text-muted" style="font-size: 0.7em;">UNITS HELD</span>
                                </div>
                            </th>
                            <th>Total Return (%)</th>
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
                    className: 'text-center'
                },
                {
                    data: 'current_nav',
                    className: 'text-center'
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function(data) {
                        return `<strong>${data.total_cost}</strong> <br> <small class="text-muted">(${data.cost_per_unit} per unit)</small>`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function(data) {
                        return `<strong>${data.current_value}</strong> <br> <small class="text-muted">(${data.total_units} Units)</small>`;
                    }
                },
                {
                    data: 'total_return',
                    className: 'text-center',
                    render: function(data) {
                        let numericValue = parseFloat(data);
                        let colorClass = numericValue >= 0 ? "text-success" : "text-danger";
                        let sign = numericValue > 0 ? "+" : ""; // Add "+" sign for positive numbers
                        return `<span class="${colorClass}">${sign}${data}</span>`;
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