<?php

namespace App\Http\Controllers;

use App\Models\ReportHistory;

class ReportController
{
    /**
     * Display the report view with buy and sell data.
     */
    public function index()
    {
        // Eager load 'fund' relation for better performance
        $buyReports = ReportHistory::with('fund')->where('status', 1)->get();
        $sellReports = ReportHistory::with('fund')->where('status', 0)->get();

        // Return the view with fetched data
        return view('report', compact('buyReports', 'sellReports'));
    }
}
