<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BuyController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\portfoliocontroller;
use App\Http\Controllers\MutualFundMasterController;
use App\Http\Controllers\navcontroller;




Route::get('/', function () {
    return view('welcome');
});

// login page 
Route::get('/login', function () {
    return view('Loginpage');
});


// deshbord page
Route::get('/dashboard', [DashboardController::class, 'showInvestmentAmount'])->name('dashboard');
Route::get('/fund-details', [DashboardController::class, 'fundDetails'])->name('fund.details');


// edit account
Route::get('/user', [profilecontroller::class, 'edit'])->name('user');


// pan card
Route::get('/card', [CardController::class, 'showForm'])->name('card');
Route::post('/card', [CardController::class, 'processForm']);





// Route to show the import form (named as 'import')
Route::get('/import', [ImportController::class, 'showImportForm'])->name('import');

// Route to handle the file upload (POST request)
Route::post('/import', [ImportController::class, 'import'])->name('import.excel');

// Route for successful import page
Route::get('/import-success', function () {
    return view('import-success'); // This page will show after successful import
});



// buy/sale report page
Route::get('/report', [ReportController::class, 'index'])->name('report');


// buy fund


Route::get('/buy', [BuyController::class, 'index'])->name('buy');
Route::get('/buy/funds/search', [BuyController::class, 'getFunds'])->name('buy.funds.search');
Route::get('/buy/get-nav-price', [BuyController::class, 'getNavPrice'])->name('buy.getNavPrice');
Route::post('/buy-fund/store', [BuyController::class, 'store'])->name('buyFund.store');






// sale fund
Route::get('/sale', [SaleController::class, 'index'])->name('sale');
Route::get('/sell/funds/search', [SaleController::class, 'getFunds'])->name('sell.funds.search');
Route::get('/sale/get-nav-price', [SaleController::class, 'getNavPrice'])->name('sale.getNavPrice');
Route::post('/sale/store', [SaleController::class, 'store'])->name('sale.store'); // Save Sale Data
//portfolio

Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::post('/portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
// api for fund name 
Route::get('/mutual-fund-store', [MutualFundMasterController::class, 'fetch_fund'])->name('mutual-fund-store');

// api for nav

Route::get('fetch-nav', [navcontroller::class, 'nav']);
