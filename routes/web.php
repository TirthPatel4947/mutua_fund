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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportExportController;

Route::get('/', function () {
    return view('welcome');
});

// login page 
// Route::get('/login', function () {
//     return view('Loginpage');
// });
// routes/web.php



Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'storeSignup'])->name('signup.store');
Route::post('/password/create', [AuthController::class, 'createPassword'])->name('password.create');


Route::group(['middleware' => ['UserAccess']], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    // change password
    Route::get('/change-password', function () {
        return view('change-password');
    })->name('password.form');

    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('password.update');


    // deshbord page
    Route::get('/dashboard', [DashboardController::class, 'showInvestmentAmount'])->name('dashboard');
    Route::get('/fund-details', [DashboardController::class, 'fundDetails'])->name('fund.details');
    Route::get('/get-investment-data', [DashboardController::class, 'getInvestmentData']);
    Route::get('/fund-performance', [DashboardController::class, 'getFundPerformance'])->name('fund.performance');
    Route::get('/fetch-top-data', [DashboardController::class, 'fetchTopData'])->name('fetch.top.data');




    // edit account
    Route::get('/user', [profilecontroller::class, 'edit'])->name('user');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/avatar/update', [UserController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::post('/profile/avatar/remove', [UserController::class, 'removeAvatar'])->name('profile.avatar.remove');


    // Route to show the import form (named as 'import')
    Route::post('/import', [ImportController::class, 'import'])->name('import');
    Route::get('/import', [ImportController::class, 'showImportPage'])->name('import.show');
    Route::post('/import/process', [ImportController::class, 'processImport'])->name('import.process');
    Route::post('/import-submit', [ImportController::class, 'submit'])->name('import.submit');
    Route::get('/import-list', [ImportController::class, 'list'])->name('import.list');
    Route::get('/fetch-options/{type}', [ImportController::class, 'fetchOptions']);
    Route::post('/update-record', [ImportController::class, 'updateRecord']);



    // buy/sale report page
    Route::get('/report', [ReportController::class, 'index'])->name('report');
    Route::get('/report/buy', [ReportController::class, 'getBuyReports'])->name('report.buy');
    Route::get('/report/sell', [ReportController::class, 'getSellReports'])->name('report.sell');
    Route::delete('/report/delete/{id}', [ReportController::class, 'destroy'])->name('report.delete');
    Route::get('/report/edit/{id}', [ReportController::class, 'edit'])->name('report.edit');
    Route::put('/report/{id}', [ReportController::class, 'update'])->name('report.update');
    Route::put('/report/update/{id}', [ReportController::class, 'update'])->name('report.update');
    Route::get('/report/sale/edit/{id}', [ReportController::class, 'editSale'])->name('report.sale.edit');
    Route::put('/report/sale/update/{id}', [ReportController::class, 'updateSale'])->name('report.sale.update');
    Route::get('/report/combined', [ReportController::class, 'combinedReport'])->name('report.combined');


    // buy fund
    Route::get('/buy', [BuyController::class, 'index'])->name('buy');
    Route::get('/buy/funds/search', [BuyController::class, 'getFunds'])->name('buy.funds.search');
    Route::get('/buy/get-nav-price', [BuyController::class, 'getNavPrice'])->name('buy.getNavPrice');
    Route::post('/buy-fund/store', [BuyController::class, 'store'])->name('buyFund.store');
    // Add this route for fetching portfolios
    Route::get('/get-portfolios', [BuyController::class, 'getPortfolios']);


    // sale fund
    Route::get('/sale', [SaleController::class, 'index'])->name('sale');
    Route::get('/sell/funds/search', [SaleController::class, 'getFunds'])->name('sell.funds.search');
    Route::get('/sale/get-nav-price', [SaleController::class, 'getNavPrice'])->name('sale.getNavPrice');
    Route::post('/sale/store', [SaleController::class, 'store'])->name('sale.store'); // Save Sale Data
    Route::get('/get-portfolios', [SaleController::class, 'getPortfolios']);

    //portfolio
    // Route to display user's portfolios
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index')->middleware('auth');

    // Route to store a new portfolio (Form Submission)
    Route::post('/portfolio', [PortfolioController::class, 'store'])->name('portfolio.store')->middleware('auth');

    // export file
    Route::get('/report/export-buy', [ReportExportController::class, 'exportBuy'])->name('report.export.buy');
    Route::get('/report/export-sell', [ReportExportController::class, 'exportSell'])->name('report.export.sell');
    Route::get('/report/export-combined', [ReportExportController::class, 'exportCombined'])->name('report.export.combined');
});



// api for fund name 
Route::get('/mutual-fund-store', [MutualFundMasterController::class, 'fetch_fund'])->name('mutual-fund-store');
// api for nav
Route::get('fetch-nav', [navcontroller::class, 'nav']);
