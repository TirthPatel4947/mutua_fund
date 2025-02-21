@extends('common_template')

@section('content')
<title>Buy</title>
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
                        <!-- Fund Name -->
                        <div class="form-group">
                            <label for="fundname">Fund Name</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="feather icon-briefcase"></i></div>
                                    </div>
                                    <select id="fundname" class="form-control select2" name="fundname_id" required>
                                        <option value="">Select Fund</option>
                                        @foreach($funds as $fund)
                                        <option value="{{ $fund->id }}" {{ isset($buyData) && $buyData->fundname_id == $fund->id ? 'selected' : '' }}>
                                            {{ $fund->fundname }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <small class="text-danger" id="fundname-error"></small>
                        </div>

                        <!-- Buy Date -->
                        <div class="form-group">
                            <label for="date">Buy Date</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="feather icon-calendar"></i></div>
                                    </div>
                                    <input type="date" id="date" class="form-control" name="date" value="{{ old('date', $buyData->date ?? '') }}" required>
                                </div>
                            </div>
                            <small class="text-danger" id="date-error"></small>
                        </div>

                        <!-- Price per Unit -->
                        <div class="form-group">
                            <label for="price_per_unit">Price per Unit</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-dollar-sign"></i></div>
                                    </div>
                                    <input type="text" id="price_per_unit" class="form-control pl-5" placeholder="Price per unit" name="price_per_unit"  value="{{ old('price_per_unit', isset($buyData) ? $buyData->price / ($buyData->unit ?: 1) : '') }}" required>
                                </div>
                            </div>
                            <small class="text-danger" id="price_per_unit-error"></small>
                        </div>

                        <!-- Investment Amount -->
                        <div class="form-group">
                            <label for="totalprice">Investment Amount</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-dollar-sign"></i></div>
                                    </div>
                                    <input type="text" id="totalprice" class="form-control pl-5" placeholder="Buy amount" name="totalprice" value="{{ old('totalprice', $buyData->price ?? '') }}" required>
                                </div>
                            </div>
                            <small class="text-danger" id="totalprice-error"></small>
                        </div>

                        <!-- Unit to Buy -->
                        <div class="form-group">
                            <label for="quantityofshare">Unit to buy</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-cogs"></i></div>
                                    </div>
                                    <input type="number" id="quantityofshare" class="form-control" placeholder="Quantity of shares to buy" name="quantityofshare" value="{{ old('quantityofshare', $buyData->unit ?? '') }}" required>
                                </div>
                            </div>
                            <small class="text-danger" id="quantityofshare-error"></small>
                        </div>

                    
                    </div>

                    <!-- Form Actions -->
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

<!-- Overlays for UI -->
<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    // Initialize select2 for fund selection
    $('#fundname').select2({
        placeholder: "Search and select a fund",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: "{{ route('sell.funds.search') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { search: params.term };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(fund) {
                        return { id: fund.id, text: fund.fundname };
                    })
                };
            },
            cache: true
        }
    });

    // Auto-fill price per unit when date or fund changes
    $('#date, #fundname').on('change', function() {
        autoFillPricePerUnit();
    });

    function autoFillPricePerUnit() {
        var date = $('#date').val();
        var fundId = $('#fundname').val();
        if (fundId && date) {
            $.ajax({
                url: "{{ route('sale.getNavPrice') }}",
                method: "GET",
                data: { fund_id: fundId, date: date },
                success: function(response) {
                    if (response.nav_price) {
                        $('#price_per_unit').val(parseFloat(response.nav_price).toFixed(2));
                    } else {
                        $('#price_per_unit').val('NAV not found');
                        alert('NAV not found for the selected date.');
                    }
                }
            });
        }
    }

    // Auto calculation logic
    $('#totalprice').on('input', function() {
        calculateUnitsFromAmount();
    });

    $('#quantityofshare').on('input', function() {
        calculateAmountFromUnits();
    });

    function calculateUnitsFromAmount() {
        var investmentAmount = parseFloat($('#totalprice').val());
        var pricePerUnit = parseFloat($('#price_per_unit').val());
        if (!isNaN(investmentAmount) && !isNaN(pricePerUnit) && pricePerUnit > 0) {
            $('#quantityofshare').val((investmentAmount / pricePerUnit).toFixed(2));
        }
    }

    function calculateAmountFromUnits() {
        var units = parseFloat($('#quantityofshare').val());
        var pricePerUnit = parseFloat($('#price_per_unit').val());
        if (!isNaN(units) && !isNaN(pricePerUnit) && pricePerUnit > 0) {
            $('#totalprice').val((units * pricePerUnit).toFixed(2));
        }
    }

    $('#save-btn').on('click', function(e) {
        e.preventDefault();
        var formData = {
            _token: '{{ csrf_token() }}',
            fundname_id: $('#fundname').val(),
            date: $('#date').val(),
            totalprice: $('#totalprice').val(),
            quantityofshare: $('#quantityofshare').val(),
            price_per_unit: $('#price_per_unit').val()
        };
        $.ajax({
            url: "{{ isset($buyData) ? route('report.update', $buyData->id) : route('buyFund.store') }}",
            type: "{{ isset($buyData) ? 'PUT' : 'POST' }}",
            data: formData,
            success: function(response) {
                alert(response.message);
                window.location.href = "{{ route('dashboard') }}";
            },
            error: function() {
                alert("Failed to save the form data. Please try again.");
            }
        });
    });
});

</script>
