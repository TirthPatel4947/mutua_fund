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

                    <table class="table table-bordered mt-3" id="fundDetailsTable">
                        <thead>
                            <tr>
                                <th>Fund Name</th>
                                <th>Total Units</th>
                                <th>Investment Amount</th>
                                <th>Current Value</th>
                                <th>Profit/Loss</th>
                                <th>Absolute Profit/Loss</th>
                                <th>Percentage Gain</th>
                                <th>Current NAV</th>
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
                    { data: 'fund_name', name: 'fund_name' },
                    { data: 'total_units', name: 'total_units', className: 'text-end' },
                    { data: 'total_investment', name: 'total_investment', className: 'text-end' },
                    { data: 'current_value', name: 'current_value', className: 'text-end' },
                    { data: 'profit_or_loss_label', name: 'profit_or_loss_label', className: 'text-center' },
                    { data: 'absolute_profit_or_loss', name: 'absolute_profit_or_loss', className: 'text-end' },
                    { 
                        data: 'percentage_gain', 
                        name: 'percentage_gain', 
                        orderable: false,  // Disable sorting for percentage
                        className: 'text-end' 
                    },
                    { data: 'current_nav', name: 'current_nav', className: 'text-end' }
                ],
                order: [[ 0, "asc" ]]
            });
        });
    </script>
@endsection
