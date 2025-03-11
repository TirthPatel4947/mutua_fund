<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\BuyReportExport;
use App\Exports\SellReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController 
{
    public function export(Request $request)
    {
        $action = $request->input('action');  // 'buy' or 'sell'

        $filters = [
            'portfolio_id' => $request->input('portfolio_id'),
            'date_range' => $request->input('date_range'),
        ];

        if ($action === 'buy') {
            return Excel::download(new BuyReportExport($filters), 'buy_report.xlsx');
        }

        if ($action === 'sell') {
            return Excel::download(new SellReportExport($filters), 'sell_report.xlsx');
        }

        return back()->with('error', 'Invalid report type selected.');
    }
}
