@extends('common_template')

@section('title', 'Fund Details')

@section('content')
<div class="container">
    <h2 class="my-4">Fund-wise Details</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fund Name</th>
                <th>Total Units</th>
                <th>Total Investment</th>
                <th>Current Value</th>
                <th>Profit/Loss</th>
                <th>Profit/Loss Label</th>
                <th>Percentage Gain</th>
                <th>Current NAV</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fundDetails as $fund)
                <tr>
                    <td>{{ $fund['fund_name'] }}</td>
                    <td>{{ $fund['total_units'] }}</td>
                    <td>{{ $fund['total_investment'] }}</td>
                    <td>{{ $fund['current_value'] }}</td>
                    <td>{{ $fund['profit_or_loss'] }}</td>
                    <td>{{ $fund['profit_or_loss_label'] }}</td>
                    <td>{{ $fund['percentage_gain'] }}</td>
                    <td>{{ $fund['current_nav'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 class="mt-5">Summary</h3>
    <ul>
        <li>Total Units: {{ $totalUnits }}</li>
        <li>Total Investment: {{ $totalInvestment }}</li>
        <li>Current Value: {{ $currentValue }}</li>
    </ul>
</div>
@endsection
