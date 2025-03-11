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
                        <h1 class="text-bold-600 text-primary text-uppercase" style="font-size: 30px;">
                            {{ Auth::user()->first_name . ' ' . Auth::user()->last_name ?? 'Guest' }}
                        </h1>
                    </div>


                    <div class="col-6 text-right">
                        <a href="{{ route('import') }}">
                            <button class="btn btn-primary  rounded-pill px-4 py-1" id="importExcelBtn">
                                <i class="feather icon-download"></i> Import Excel
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
                                <h5 class="card-title text-muted">Current Percentage </h5>
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
        <!-- performance chart  js  -->
        <!-- Include required libraries -->
        <!-- Include required libraries -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script> <!-- Include Chart.js datalabels plugin -->

        <div class="row match-height">
            <!-- Bar Chart: Mutual Fund Performance -->
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yearly Investment</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <!-- Wrapper for horizontal scrolling -->
                            <div class="chart-wrapper">
                                <div id="barChart"></div> <!-- ApexCharts bar chart here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                $.ajax({
                    url: '/get-investment-data', // API Call to Fetch Data
                    type: 'GET',
                    success: function(response) {
                        console.log("Response Data:", response); // Debugging Log

                        var years = response.years || [];
                        var investmentValues = response.investmentValues || [];
                        var salesValues = response.salesValues || [];

                        console.log("Years:", years);
                        console.log("Investment Values:", investmentValues);
                        console.log("Sales Values:", salesValues);

                        // Reverse data for proper chronological order
                        const reversedYears = [...years].reverse();
                        const reversedInvestmentValues = investmentValues.reverse().map(value => Number(value));
                        const reversedSalesValues = salesValues.reverse().map(value => Number(value));

                        document.getElementById('barChart').innerHTML = ""; // Ensure chart refresh

                        var options = {
                            chart: {
                                type: 'bar',
                                height: 400,
                                stacked: false
                            },
                            series: [{
                                    name: 'Investment (Buy) INR',
                                    data: reversedInvestmentValues
                                },
                                {
                                    name: 'Sales (Sell) INR',
                                    data: reversedSalesValues
                                }
                            ],
                            xaxis: {
                                categories: reversedYears,
                                title: {
                                    text: 'Years'
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Value (INR)'
                                },
                                labels: {
                                    formatter: val => val.toLocaleString()
                                }
                            },
                            colors: ['#4CAF50', '#FF7043'], // Green for Buy, Red for Sell
                            dataLabels: {
                                enabled: true
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '50%',
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            title: {
                                text: 'Year-wise Investment & Sales',
                                align: 'center'
                            },
                            legend: {
                                position: 'top'
                            }
                        };

                        setTimeout(() => {
                            var chart = new ApexCharts(document.querySelector("#barChart"), options);
                            chart.render();
                            console.log("Chart Rendered!");
                        }, 500);
                    },
                    error: function(error) {
                        console.error("Error fetching data:", error);
                    }
                });
            });
        </script>

        <!-- Custom CSS -->
        <style>
            .chart-wrapper {
                width: 100%;
                overflow-x: auto;
                /* Allow horizontal scrolling */
                overflow-y: hidden;
                /* Prevent vertical scroll */
                white-space: nowrap;
                /* Prevent wrapping */
                position: relative;
                /* Ensure smooth scroll */
            }

            #barChart {
                display: block;
                /* Ensure it's block-level */
                height: 400px;
                /* Fixed height */
                width: 100%;
                /* Allow the chart to adjust width dynamically */
                min-width: 2000px;
                /* Ensure enough width */
            }

            #pieChart {
                width: 100%;
                /* Ensure pie chart fits within its container */
                height: 350px;
                /* Adjust height for optimal display */
            }
        </style>




        <!-- table details  -->
        <div class="row match-height">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Top Gainers & Losers</h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                         <div class="row">
    <!-- Top Gainers -->
    <div class="col-md-6 d-flex">
        <div class="rounded shadow-sm p-3 w-100"
            style="background: linear-gradient(135deg, #e0f7e9, #b2d8b2); border-left: 5px solid #28a745; min-height: 150px;">
            <h5 class="text-success font-weight-bold mb-3">📈 Top Gainers</h5>
            <div id="topGainers"></div>
        </div>
    </div>

    <!-- Top Losers -->
    <div class="col-md-6 d-flex">
        <div class="rounded shadow-sm p-3 w-100"
            style="background: linear-gradient(135deg, #fdecea, #f5c6cb); border-left: 5px solid #dc3545; min-height: 150px;">
            <h5 class="text-danger font-weight-bold mb-3">📉 Top Losers</h5>
            <div id="topLosers"></div>
        </div>
    </div>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
$(document).ready(function () {
    $.ajax({
        url: "{{ route('fetch.top.data') }}",  // Replace with your route
        method: "GET",
        success: function (response) {
            renderFunds("#topGainers", response.topGainers, "green");
            renderFunds("#topLosers", response.topLosers, "red");
        },
        error: function (error) {
            console.error("Error fetching data:", error);
        }
    });

    function renderFunds(containerId, funds, color) {
        let html = "";
        if (funds.length > 0) {
            funds.forEach(fund => {
                const sign = fund.difference >= 0 ? "+" : "-";
                const textColor = fund.difference >= 0 ? "green" : "red";

                html += `
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <div class="text-left">
                            <strong>${fund.fundname}</strong>
                        </div>
                        <div class="text-right" style="color: ${textColor}; font-weight: bold;">
                            ₹${fund.difference} (${sign}${fund.percentage_change}%)
                        </div>
                    </div>
                    <hr class="my-1" style="border-top: 1px dashed #aaa;"> <!-- Separator Line -->
                `;
            });
        } else {
            html = `<p class="text-muted">No data available</p>`;
        }
        $(containerId).html(html);
    }
});
</script>



        <!-- <!detials in table -->
        <!-- Mutual Fund Ads Section -->
        <div class="row match-height">
            <!-- Zerodha Ad -->
            <div class="col-xl-4 col-lg-12">
                <div class="card bg-gradient-x-danger">
                    <div class="card-content">
                        <div class="card-body text-center">
                            <img src="{{ asset('assets/images/zerodha-banner.jpg') }}" alt="Zerodha Mutual Fund" class="img-fluid">


                            <h4 class="white mt-2">Invest Smart with Zerodha</h4>
                            <p class="white">Start investing in mutual funds with zero commission.</p>
                            <a href="https://zerodha.com/mutualfunds/" class="btn btn-black">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mutual Fund Tips -->
            <div class="col-xl-4 col-lg-12">
                <div class="card bg-gradient-x-info white">
                    <div class="card-content">
                        <div class="card-body text-center">
                            <i class="fa fa-line-chart font-large-2"></i>
                            <div class="investment-tips-slider">
                                <ul>
                                    <li>"SIP is the best way to create long-term wealth. Start today!"</li>
                                    <li>"Diversify your portfolio to reduce risk and maximize returns."</li>
                                    <li>"Check expense ratios before choosing a mutual fund."</li>
                                    <li>"Long-term investment beats market timing. Stay invested!"</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fund Performance Highlights -->
            <div class="col-xl-4 col-lg-12">
                <div class="card bg-gradient-x-primary white">
                    <div class="card-content">
                        <div class="card-body text-center">
                            <i class="fa fa-bar-chart font-large-2"></i>
                            <div class="fund-performance-slider">
                                <ul>
                                    <li>"Axis Bluechip Fund: 12% CAGR over 5 years!"</li>
                                    <li>"Mirae Asset Emerging Bluechip Fund: High growth potential!"</li>
                                    <li>"Best ELSS funds for tax saving: Invest before March 31!"</li>
                                    <li>"HDFC Mid-Cap Opportunities Fund: 15% CAGR over 10 years!"</li>
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