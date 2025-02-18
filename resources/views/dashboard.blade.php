@extends('common_template')

@section('content')
<title>Dashboard</title>
<div class="row grouped-multiple-statistics-card">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Row for Investor Name and Invest Button -->
                <div class="row mb-4">
                    <div class="col-6 text-left">
                        <h3 class="text-muted">Investor</h3>
                        <h1 class="text-bold-600 text-primary text-uppercase" style="font-size: 30px;">Tirth Patel</h1>
                    </div>
                    <div class="col-6 text-right">
                        <a href="{{ route('card') }}">
                            <button class="btn btn-danger rounded-pill px-4 py-1" style="font-size: 16px;">
                                Invest Now
                            </button>
                        </a>
                    </div>
                </div>

                <!-- Row for 4 Cards -->
                <div class="row">
                    <!-- Current Value Card -->
                    <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <div class="icon-container mb-3">
                                    <i class="feather icon-dollar-sign font-large-2 text-primary"></i>
                                </div>
                                <h5 class="card-title text-muted">Current Value</h5>
                                <h3 class="text-bold-600">₹{{ number_format($currentValue, 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Value Card -->
                    <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <div class="icon-container mb-3">
                                    <i class="feather icon-arrow-up font-large-2 text-danger"></i>
                                </div>
                                <h5 class="card-title text-muted">Investment Amount</h5>
                                <h3 class="text-bold-600">₹{{ number_format($investmentAmount, 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Current Percentage Card -->
                    <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <div class="icon-container mb-3">
                                    <i class="feather icon-percent font-large-2 text-warning"></i>
                                </div>
                                <h5 class="card-title text-muted">Current Percentage (%)</h5>
                                <h3 class="text-bold-600">
                                    <span style="color: {{ (float)$percentageGain > 0 ? 'green' : ((float)$percentageGain < 0 ? 'red' : 'black') }};">
                                        {{ $percentageGain }}%
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Profit/Loss Card -->
                    <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <div class="icon-container mb-3">
                                    <i class="feather icon-briefcase font-large-2 {{ $profitOrLoss > 0 ? 'text-success' : 'text-danger' }}"></i>
                                </div>
                                <h5 class="card-title text-muted">Profit/Loss</h5>
                                <h3 style="color: {{ $profitOrLoss > 0 ? 'darkgreen' : 'red' }};">
                                    {{ $profitOrLoss > 0 ? '+' : '-' }} ₹{{ number_format($absoluteProfitOrLoss, 2) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- See More Link -->
                <div class="row mt-3">
                    <div class="col-12 text-right">
                        <a href="{{ route('fund.details') }}" class="text-primary" style="font-size: 16px;">
                            See More →
                        </a>
                    </div>
                </div>
            </div>
        </div>

   <!-- Row Container for Performance Charts and Recent Buyers -->
<div class="row match-height">
    <!-- Bar Chart: Mutual Fund Performance -->
    <div class="col-xl-8 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Yearly Investment</h4>
            </div>

            <div class="card-content">
                <div class="card-body">
                    <!-- Adjusted Bar Chart Size to Max Small -->
                    <canvas id="barChart" width="200" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart: Investment Distribution -->
    <div class="col-xl-4 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Investment Distribution</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <canvas id="pieChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make an AJAX call to get the data
        $.ajax({
            url: '/get-investment-data',  // Replace with your actual endpoint
            type: 'GET',
            success: function(response) {
                // Access data from the response
                var years = response.years;
                var investmentValues = response.investmentValues;
                var fundNames = response.fundNames;
                var fundInvestments = response.fundInvestments;

                // Log data to check
                console.log("Years:", years);
                console.log("Investment Values:", investmentValues);
                console.log("Fund Names:", fundNames);
                console.log("Fund Investments:", fundInvestments);

                // Ensure non-empty arrays to avoid rendering issues
                if (years.length === 0) {
                    years = ['No data available'];
                    investmentValues = [0]; // Default value
                }

                if (fundNames.length === 0) {
                    fundNames = ['No data available'];
                    fundInvestments = [0]; // Default value
                }

                // Bar Chart for Yearly Investment
                var barCtx = document.getElementById('barChart').getContext('2d');
                var barChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: years,
                        datasets: [{
                            label: 'Investment Value (INR)',
                            data: investmentValues,
                            backgroundColor: '#4CAF50',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            title: {
                                display: true,
                                text: 'Year-wise Investment Value'
                            }
                        },
                        // Optional: Adjust bar width or other settings if needed
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Pie Chart for Investment Distribution
                var pieCtx = document.getElementById('pieChart').getContext('2d');
                var pieChart = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: fundNames,
                        datasets: [{
                            data: fundInvestments,
                            backgroundColor: ['#FF7043', '#29B6F6', '#FFCA28', '#8E44AD', '#4CAF50']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            title: {
                                display: true,
                                text: 'Investment Distribution by Fund'
                            }
                        }
                    }
                });
            }
        });
    });
</script>
    







<!-- table details  -->
<div class="row match-height">
    <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Mutual Fund Performance Overview</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="reload"><i class="feather icon-rotate-cw"></i></a></li>
                        <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    </ul>
                </div>
                <div class="float-right mt-2">
                    <a href="{{ route('import') }}">
                        <button class="btn btn-primary btn-sm mr-2" id="importExcelBtn">
                            <i class="feather icon-download"></i> Import Excel
                        </button>
                    </a>
                    <button class="btn btn-success btn-sm" id="exportExcelBtn">
                        <i class="feather icon-upload"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <p>Total funds: 3... Active funds: 1.</p>
                </div>
                <div class="table-responsive">
                    <table id="fund-performance" class="table table-hover mb-0 ps-container ps-theme-default">
                        <thead>
                            <tr>
                                <th>Fund Name</th>
                                <th>Total Value</th>
                                <th>Investment NAV</th>
                                <th>Current NAV</th>
                                <th>Unit</th>
                                <th>Annual Return (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Fund A -->
                            <tr data-toggle="collapse" data-target="#fundDetails1" class="clickable-row">
                                <td class="text-truncate">Growth Fund A</td>
                                <td class="text-truncate">$ 22,500</td>
                                <td class="text-truncate">$ 140.00</td>
                                <td class="text-truncate">$ 150.00</td>
                                <td class="text-truncate">150</td>
                                <td class="text-truncate">5.25</td>
                            </tr>
                            <!-- Details for Fund A -->
                            <tr id="fundDetails1" class="collapse">
                                <td colspan="7">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Investor</th>
                                                <th>Investment Amount</th>
                                                <th>Investment NAV</th>
                                                <th>Units</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Tirth</td>
                                                <td>$ 12,500</td>
                                                <td>$ 140.00</td>
                                                <td>90</td>
                                            </tr>
                                            <tr>
                                                <td>Parth</td>
                                                <td>$ 10,000</td>
                                                <td>$ 140.00</td>
                                                <td>60</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <!-- Fund B -->
                            <tr data-toggle="collapse" data-target="#fundDetails2" class="clickable-row">
                                <td class="text-truncate">Equity Fund B</td>
                                <td class="text-truncate">$ 26,000</td>
                                <td class="text-truncate">$ 125.00</td>
                                <td class="text-truncate">$ 130.00</td>
                                <td class="text-truncate">200</td>
                                <td class="text-truncate">4.80</td>
                            </tr>
                            <!-- Details for Fund B -->
                            <tr id="fundDetails2" class="collapse">
                                <td colspan="7">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Investor</th>
                                                <th>Investment Amount</th>
                                                <th>Investment NAV</th>
                                                <th>Units</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>tirth</td>
                                                <td>$ 16,000</td>
                                                <td>$ 125.00</td>
                                                <td>128</td>
                                            </tr>
                                            <tr>
                                                <td>parth</td>
                                                <td>$ 10,000</td>
                                                <td>$ 125.00</td>
                                                <td>80</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <!-- Fund C -->
                            <tr data-toggle="collapse" data-target="#fundDetails3" class="clickable-row">
                                <td class="text-truncate">Real Estate Fund C</td>
                                <td class="text-truncate">$ 25,000</td>
                                <td class="text-truncate">$ 245.00</td>
                                <td class="text-truncate">$ 250.00</td>
                                <td class="text-truncate">100</td>
                                <td class="text-truncate">3.75</td>
                            </tr>
                            <!-- Details for Fund C -->
                            <tr id="fundDetails3" class="collapse">
                                <td colspan="7">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Investor</th>
                                                <th>Investment Amount</th>
                                                <th>Investment NAV</th>
                                                <th>Units</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>tirth</td>
                                                <td>$ 15,000</td>
                                                <td>$ 245.00</td>
                                                <td>61</td>
                                            </tr>
                                            <tr>
                                                <td>parth</td>
                                                <td>$ 10,000</td>
                                                <td>$ 245.00</td>
                                                <td>40</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <!detials in table -->
<!-- Social & Weather -->
<div class="row match-height">
    <div class="col-xl-4 col-lg-12">
        <div class="card bg-gradient-x-danger">
            <div class="card-content">
                <div class="card-body">
                    <div class="animated-weather-icons text-center float-left">
                        <svg version="1.1" id="cloudHailAlt2" class="climacon climacon_cloudHailAlt climacon-blue-grey climacon-darken-2 height-100" viewBox="15 15 70 70">
                            <g class="climacon_iconWrap climacon_iconWrap-cloudHailAlt">
                                <g class="climacon_wrapperComponent climacon_wrapperComponent-hailAlt">
                                    <g class="climacon_component climacon_component-stroke climacon_component-stroke_hailAlt climacon_component-stroke_hailAlt-left">
                                        <circle cx="42" cy="65.498" r="2"></circle>
                                    </g>
                                    <g class="climacon_component climacon_component-stroke climacon_component-stroke_hailAlt climacon_component-stroke_hailAlt-middle">
                                        <circle cx="49.999" cy="65.498" r="2"></circle>
                                    </g>
                                    <g class="climacon_component climacon_component-stroke climacon_component-stroke_hailAlt climacon_component-stroke_hailAlt-right">
                                        <circle cx="57.998" cy="65.498" r="2"></circle>
                                    </g>
                                    <g class="climacon_component climacon_component-stroke climacon_component-stroke_hailAlt climacon_component-stroke_hailAlt-left">
                                        <circle cx="42" cy="65.498" r="2"></circle>
                                    </g>
                                    <g class="climacon_component climacon_component-stroke climacon_component-stroke_hailAlt climacon_component-stroke_hailAlt-middle">
                                        <circle cx="49.999" cy="65.498" r="2"></circle>
                                    </g>
                                    <g class="climacon_component climacon_component-stroke climacon_component-stroke_hailAlt climacon_component-stroke_hailAlt-right">
                                        <circle cx="57.998" cy="65.498" r="2"></circle>
                                    </g>
                                </g>
                                <g class="climacon_wrapperComponent climacon_wrapperComponent-cloud">
                                    <path class="climacon_component climacon_component-stroke climacon_component-stroke_cloud" d="M63.999,64.941v-4.381c2.39-1.384,3.999-3.961,3.999-6.92c0-4.417-3.581-8-7.998-8c-1.602,0-3.084,0.48-4.334,1.291c-1.23-5.317-5.974-9.29-11.665-9.29c-6.626,0-11.998,5.372-11.998,11.998c0,3.549,1.55,6.728,3.999,8.924v4.916c-4.776-2.768-7.998-7.922-7.998-13.84c0-8.835,7.162-15.997,15.997-15.997c6.004,0,11.229,3.311,13.966,8.203c0.663-0.113,1.336-0.205,2.033-0.205c6.626,0,11.998,5.372,11.998,12C71.998,58.863,68.656,63.293,63.999,64.941z"></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <div class="weather-details text-center">
                        <span class="block white darken-1">Snow</span>
                        <span class="font-large-2 block white darken-4">-5&deg;</span>
                        <span class="font-medium-4 text-bold-500 white darken-1">London, UK</span>
                    </div>
                </div>
                <div class="card-footer bg-gradient-x-danger border-0">
                    <div class="row">
                        <div class="col-4 text-center display-table-cell white">
                            <i class="me-wind font-large-1 lighten-3 align-middle"></i> <span class="align-middle">2MPH</span>
                        </div>
                        <div class="col-4 text-center display-table-cell white">
                            <i class="me-sun2 font-large-1 lighten-3 align-middle"></i> <span class="align-middle">2%</span>
                        </div>
                        <div class="col-4 text-center display-table-cell white">
                            <i class="me-thermometer font-large-1 lighten-3 align-middle"></i> <span class="align-middle">13.0&deg;</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12">
        <div class="card bg-gradient-x-info white">
            <div class="card-content">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fa fa-twitter font-large-2"></i>
                    </div>
                    <div class="tweet-slider">
                        <ul>
                            <li>Congratulations to Rob Jones in accounting for winning our <span class="yellow">#NFL</span> football pool!
                                <p class="text-italic pt-1">- John Doe</p>
                            </li>
                            <li>Contests are a great thing to partner on. Partnerships immediately <span class="yellow">#DOUBLE</span> the reach.
                                <p class="text-italic pt-1">- John Doe</p>
                            </li>
                            <li>Puns, humor, and quotes are great content on <span class="yellow">#Twitter</span>. Find some related to your business.
                                <p class="text-italic pt-1">- John Doe</p>
                            </li>
                            <li>Are there <span class="yellow">#common-sense</span> facts related to your business? Combine them with a great photo.
                                <p class="text-italic pt-1">- John Doe</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12">
        <div class="card bg-gradient-x-primary white">
            <div class="card-content">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fa fa-facebook font-large-2"></i>
                    </div>
                    <div class="fb-post-slider">
                        <ul>
                            <li>Congratulations to Rob Jones in accounting for winning our #NFL football pool!
                                <p class="text-italic pt-1">- John Doe</p>
                            </li>
                            <li>Contests are a great thing to partner on. Partnerships immediately #DOUBLE the reach.
                                <p class="text-italic pt-1">- John Doe</p>
                            </li>
                            <li>Puns, humor, and quotes are great content on #Twitter. Find some related to your business.
                                <p class="text-italic pt-1">- John Doe</p>
                            </li>
                            <li>Are there #common-sense facts related to your business? Combine them with a great photo.
                                <p class="text-italic pt-1">- John Doe</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Social & Weather -->
<!-- Basic Horizontal Timeline -->
<div class="row match-height">
    <div class="col-xl-8 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Horizontal Timeline</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="reload"><i class="feather icon-rotate-cw"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="card-text">
                        <section class="cd-horizontal-timeline">
                            <div class="timeline">
                                <div class="events-wrapper">
                                    <div class="events">
                                        <ol>
                                            <li><a href="#0" data-date="16/01/2015" class="selected">16 Jan</a></li>
                                            <li><a href="#0" data-date="28/02/2015">28 Feb</a></li>
                                            <li><a href="#0" data-date="20/04/2015">20 Mar</a></li>
                                            <li><a href="#0" data-date="20/05/2015">20 May</a></li>
                                            <li><a href="#0" data-date="09/07/2015">09 Jul</a></li>
                                            <li><a href="#0" data-date="30/08/2015">30 Aug</a></li>
                                            <li><a href="#0" data-date="15/09/2015">15 Sep</a></li>
                                        </ol>
                                        <span class="filling-line" aria-hidden="true"></span>
                                    </div>
                                    <!-- .events -->
                                </div>
                                <!-- .events-wrapper -->
                                <ul class="cd-timeline-navigation">
                                    <li><a href="#0" class="prev inactive">Prev</a></li>
                                    <li><a href="#0" class="next">Next</a></li>
                                </ul>
                                <!-- .cd-timeline-navigation -->
                            </div>
                            <!-- .timeline -->
                            <div class="events-content">
                                <ol>
                                    <li class="selected" data-date="16/01/2015">
                                        <blockquote class="blockquote border-0">
                                            <div class="media">
                                                <div class="media-left">
                                                    <img class="media-object img-xl mr-1" src="../../../app-assets/images/portrait/small/avatar-s-5.png" alt="Generic placeholder image">
                                                </div>
                                                <div class="media-body">
                                                    Sometimes life is going to hit you in the head with a brick. Don't lose faith.
                                                </div>
                                            </div>
                                            <footer class="blockquote-footer text-right">Steve Jobs
                                                <cite title="Source Title">Entrepreneur</cite>
                                            </footer>
                                        </blockquote>
                                        <p class="lead mt-2">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur aspernatur at.
                                        </p>
                                    </li>
                                    <li data-date="28/02/2015">
                                        <blockquote class="blockquote border-0">
                                            <div class="media">
                                                <div class="media-left">
                                                    <img class="media-object img-xl mr-1" src="../../../app-assets/images/portrait/small/avatar-s-6.png" alt="Generic placeholder image">
                                                </div>
                                                <div class="media-body">
                                                    Sometimes life is going to hit you in the head with a brick. Don't lose faith.
                                                </div>
                                            </div>
                                            <footer class="blockquote-footer text-right">Steve Jobs
                                                <cite title="Source Title">Entrepreneur</cite>
                                            </footer>
                                        </blockquote>
                                        <p class="lead mt-2">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur aspernatur at.
                                        </p>
                                    </li>
                                    <li data-date="20/04/2015">
                                        <blockquote class="blockquote border-0">
                                            <div class="media">
                                                <div class="media-left">
                                                    <img class="media-object img-xl mr-1" src="../../../app-assets/images/portrait/small/avatar-s-7.png" alt="Generic placeholder image">
                                                </div>
                                                <div class="media-body">
                                                    Sometimes life is going to hit you in the head with a brick. Don't lose faith.
                                                </div>
                                            </div>
                                            <footer class="blockquote-footer text-right">Steve Jobs
                                                <cite title="Source Title">Entrepreneur</cite>
                                            </footer>
                                        </blockquote>
                                        <p class="lead mt-2">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur aspernatur at.
                                        </p>
                                    </li>
                                    <li data-date="20/05/2015">
                                        <blockquote class="blockquote border-0">
                                            <div class="media">
                                                <div class="media-left">
                                                    <img class="media-object img-xl mr-1" src="../../../app-assets/images/portrait/small/avatar-s-8.png" alt="Generic placeholder image">
                                                </div>
                                                <div class="media-body">
                                                    Sometimes life is going to hit you in the head with a brick. Don't lose faith.
                                                </div>
                                            </div>
                                            <footer class="blockquote-footer text-right">Steve Jobs
                                                <cite title="Source Title">Entrepreneur</cite>
                                            </footer>
                                        </blockquote>
                                        <p class="lead mt-2">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur aspernatur at.
                                        </p>
                                    </li>
                                    <li data-date="09/07/2015">
                                        <blockquote class="blockquote border-0">
                                            <div class="media">
                                                <div class="media-left">
                                                    <img class="media-object img-xl mr-1" src="../../../app-assets/images/portrait/small/avatar-s-9.png" alt="Generic placeholder image">
                                                </div>
                                                <div class="media-body">
                                                    Sometimes life is going to hit you in the head with a brick. Don't lose faith.
                                                </div>
                                            </div>
                                            <footer class="blockquote-footer text-right">Steve Jobs
                                                <cite title="Source Title">Entrepreneur</cite>
                                            </footer>
                                        </blockquote>
                                        <p class="lead mt-2">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur aspernatur at.
                                        </p>
                                    </li>
                                    <li data-date="30/08/2015">
                                        <blockquote class="blockquote border-0">
                                            <div class="media">
                                                <div class="media-left">
                                                    <img class="media-object img-xl mr-1" src="../../../app-assets/images/portrait/small/avatar-s-6.png" alt="Generic placeholder image">
                                                </div>
                                                <div class="media-body">
                                                    Sometimes life is going to hit you in the head with a brick. Don't lose faith.
                                                </div>
                                            </div>
                                            <footer class="blockquote-footer text-right">Steve Jobs
                                                <cite title="Source Title">Entrepreneur</cite>
                                            </footer>
                                        </blockquote>
                                        <p class="lead mt-2">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur aspernatur at.
                                        </p>
                                    </li>
                                    <li data-date="15/09/2015">
                                        <blockquote class="blockquote border-0">
                                            <div class="media">
                                                <div class="media-left">
                                                    <img class="media-object img-xl mr-1" src="../../../app-assets/images/portrait/small/avatar-s-7.png" alt="Generic placeholder image">
                                                </div>
                                                <div class="media-body">
                                                    Sometimes life is going to hit you in the head with a brick. Don't lose faith.
                                                </div>
                                            </div>
                                            <footer class="blockquote-footer text-right">Steve Jobs
                                                <cite title="Source Title">Entrepreneur</cite>
                                            </footer>
                                        </blockquote>
                                        <p class="lead mt-2">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur aspernatur at.
                                        </p>
                                    </li>
                                </ol>
                            </div>
                            <!-- .events-content -->
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Basic Card</h4>
            </div>
            <div class="card-content">
                <img class="img-fluid" src="../../../app-assets/images/carousel/06.jpg" alt="Card image cap">
                <div class="card-body">
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    <a href="#" class="card-link">Card link</a>
                    <a href="#" class="card-link">Another link</a>
                </div>
            </div>
            <div class="card-footer border-top-blue-grey border-top-lighten-5 text-muted">
                <span class="float-left">3 hours ago</span>
                <span class="float-right">
                    <a href="#" class="card-link">Read More <i class="fa fa-angle-right"></i></a>
                </span>
            </div>
        </div>
    </div>
</div>
<!--/ Basic Horizontal Timeline -->

</div>
</div>
@endsection