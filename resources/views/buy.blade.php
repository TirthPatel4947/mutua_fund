@extends('common_template')

@section('content')
<!-- BEGIN: Content -->
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Buy Fund Forms</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12"></div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-icons">Buy Fund</h4>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <form class="form" id="buy-fund-form" novalidate>
                    @csrf <!-- CSRF token added here -->
                    <div class="form-body">
                        <div class="form-group">
                            <label for="fundname">Fund Name</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="feather icon-briefcase"></i></div>
                                    </div>
                                    <select id="fundname" class="form-control select2" name="fundname_id" required></select>

                                </div>
                            </div>
                            <small class="text-danger" id="fundname-error"></small>
                        </div>

                        <div class="form-group">
                            <label for="date">Buy Date</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="feather icon-calendar"></i></div>
                                    </div>
                                    <input type="date" id="date" class="form-control" name="date" required>
                                </div>
                            </div>
                            <small class="text-danger" id="date-error"></small>
                        </div>

                        <div class="form-group">
                            <label for="totalprice">Investment Amount</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-dollar-sign"></i></div>
                                    </div>
                                    <input type="text" id="totalprice" class="form-control pl-5" placeholder="Buy amount" name="totalprice" required>
                                </div>
                            </div>
                            <small class="text-danger" id="totalprice-error"></small>
                        </div>

                        <div class="form-group">
                            <label for="quantityofshare">Unit to buy</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-cogs"></i></div>
                                    </div>
                                    <input type="number" id="quantityofshare" class="form-control" placeholder="Quantity of shares to buy" name="quantityofshare" required>
                                </div>
                            </div>
                            <small class="text-danger" id="quantityofshare-error"></small>
                        </div>
                    </div>

                    <div class="form-actions right">
                        <button type="reset" class="btn btn-warning mr-1">
                            <i class="feather icon-x"></i> Cancel
                        </button>
                        <button type="button" id="save-btn" class="btn btn-primary">
                            <i class="fa fa-check-square-o"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="sidenav-overlay"></div>
<div class="drag-target"></div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#fundname').select2({
            placeholder: "Search and select a fund",
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('buy.funds.search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(fund) {
                            return {
                                id: fund.id,
                                text: fund.fundname
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#totalprice').on('input', function() {
            calculateUnitsFromAmount();
        });

        $('#quantityofshare').on('input', function() {
            calculateAmountFromUnits();
        });

        function calculateUnitsFromAmount() {
            var investmentAmount = parseFloat($('#totalprice').val());
            var fundId = $('#fundname').val();
            var date = $('#date').val();

            if (investmentAmount && fundId && date) {
                $('#quantityofshare').prop('disabled', true).val('Loading...');
                $.ajax({
                    url: "{{ route('buy.getNavPrice') }}",
                    method: "GET",
                    data: {
                        fund_id: fundId,
                        date: date
                    },
                    success: function(response) {
                        if (response.nav_price > 0) {
                            var units = investmentAmount / response.nav_price;
                            $('#quantityofshare').val(units.toFixed(2));
                        } else {
                            $('#quantityofshare').val('NAV not found');
                            alert('NAV not found for the selected date.');
                        }
                    },
                    error: function() {
                        $('#quantityofshare').val('Error');
                        alert('An error occurred while fetching the NAV price.');
                    },
                    complete: function() {
                        $('#quantityofshare').prop('disabled', false);
                    }
                });
            }
        }

        function calculateAmountFromUnits() {
            var units = parseFloat($('#quantityofshare').val());
            var fundId = $('#fundname').val();
            var date = $('#date').val();

            if (units && fundId && date) {
                $('#totalprice').prop('disabled', true).val('Loading...');
                $.ajax({
                    url: "{{ route('buy.getNavPrice') }}",
                    method: "GET",
                    data: {
                        fund_id: fundId,
                        date: date
                    },
                    success: function(response) {
                        if (response.nav_price > 0) {
                            var investmentAmount = units * response.nav_price;
                            $('#totalprice').val(investmentAmount.toFixed(2));
                        } else {
                            $('#totalprice').val('NAV not found');
                            alert('NAV not found for the selected date.');
                        }
                    },
                    error: function() {
                        $('#totalprice').val('Error');
                        alert('An error occurred while fetching the NAV price.');
                    },
                    complete: function() {
                        $('#totalprice').prop('disabled', false);
                    }
                });
            }
        }

        $('#save-btn').on('click', function(e) {
            e.preventDefault();
            var formData = {
                _token: '{{ csrf_token() }}',
                fundname_id: $('#fundname').val(), // Use fundname_id instead of fundname
                date: $('#date').val(),
                totalprice: $('#totalprice').val(),
                quantityofshare: $('#quantityofshare').val()
            };

            $.ajax({
                url: "{{ route('buyFund.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    alert(response.message);
                    window.location.href = "{{ route('dashboard') }}";
                },
                error: function(xhr) {
                    alert("Failed to save the form data. Please try again.");
                }
            });
        });
    });
</script>