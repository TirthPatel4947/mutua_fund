<?php

namespace App\Http\Controllers;

use App\Models\ReportHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReportController
{
    /**
     * Display the report view.
     */
    public function index()
    {
        return view('report');
    }

    /**
     * Get Buy Reports (Yajra DataTables) with Date Range Filter
     */
    public function getBuyReports(Request $request)
    {
        $buyReports = ReportHistory::with('fund')
            ->where('status', 1);
    
        // Apply date range filter if provided
        if ($request->has('date_range') && $request->date_range) {
            $dateRange = explode(' - ', $request->date_range);
            $startDate = \Carbon\Carbon::parse($dateRange[0])->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateRange[1])->endOfDay();
            $buyReports->whereBetween('date', [$startDate, $endDate]);
        }
    
        return DataTables::of($buyReports)
            ->addColumn('fund_name', function ($report) {
                return optional($report->fund)->fundname ?? 'N/A';
            })
            ->addColumn('buying_date', function ($report) {
                return \Carbon\Carbon::parse($report->date)->format('Y-m-d');
            })
            ->addColumn('quantity_of_shares', function ($report) {
                return $report->unit;
            })
            ->addColumn('price_per_unit', function ($report) {
                return '₹' . number_format($report->price / ($report->unit ?: 1), 2);
            })
            ->addColumn('total_price', function ($report) {
                return '₹' . number_format($report->price, 2);
            })
            ->make(true);
    }

    /**
     * Get Sell Reports (Yajra DataTables) with Date Range Filter
     */
    public function getSellReports(Request $request)
    {
        $sellReports = ReportHistory::with('fund')
            ->where('status', 0);
    
        // Apply date range filter if provided
        if ($request->has('date_range') && $request->date_range) {
            $dateRange = explode(' - ', $request->date_range);
            $startDate = \Carbon\Carbon::parse($dateRange[0])->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateRange[1])->endOfDay();
            $sellReports->whereBetween('date', [$startDate, $endDate]);
        }
    
        return DataTables::of($sellReports)
            ->addColumn('fund_name', function ($report) {
                return optional($report->fund)->fundname ?? 'N/A';
            })
            ->addColumn('selling_date', function ($report) {
                return \Carbon\Carbon::parse($report->date)->format('Y-m-d');
            })
            ->addColumn('quantity_of_shares', function ($report) {
                return $report->unit;
            })
            ->addColumn('price_per_unit', function ($report) {
                return '₹' . number_format($report->price / ($report->unit ?: 1), 2);
            })
            ->addColumn('total_price', function ($report) {
                return '₹' . number_format($report->price, 2);
            })
            ->make(true);
    }



    /**
     * Delete a report record.
     */
    public function destroy($id)
    {
        $report = ReportHistory::findOrFail($id);
        $report->delete();

        return response()->json(['success' => 'Record deleted successfully.']);
    }
}
