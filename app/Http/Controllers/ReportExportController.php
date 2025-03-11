<?php

namespace App\Http\Controllers;

use App\Exports\BuyReportExport;
use App\Exports\SellReportExport;
use App\Exports\CombinedReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController
{
    public function exportBuy(Request $request)
    {
        $filters = [
            'portfolio_id' => $request->input('portfolio_id'),
            'date_range' => $request->input('date_range'),
        ];

        return Excel::download(new BuyReportExport($filters), 'buy_report.xlsx');
    }

    public function exportSell(Request $request)
    {
        $filters = [
            'portfolio_id' => $request->input('portfolio_id'),
            'date_range' => $request->input('date_range'),
        ];

        return Excel::download(new SellReportExport($filters), 'sell_report.xlsx');
    }

    public function exportCombined(Request $request)
    {
        $filters = [
            'portfolio_id' => $request->input('portfolio_id'),
            'date_range' => $request->input('date_range'),
        ];

        return Excel::download(new CombinedReportExport($filters), 'combined_report.xlsx');
    }
}
