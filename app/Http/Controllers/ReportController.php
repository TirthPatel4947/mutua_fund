<?php

namespace App\Http\Controllers;

use App\Models\ReportHistory;
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
     * Get Buy Reports (Yajra DataTables)
     */
    public function getBuyReports()
    {
        $buyReports = ReportHistory::with('fund')->where('status', 1);

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
     * Get Sell Reports (Yajra DataTables)
     */
    public function getSellReports()
    {
        $sellReports = ReportHistory::with('fund')->where('status', 0);

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
    $report = ReportHistory::findOrFail($id); // Use ReportHistory since that's the model used
    $report->delete();

    return response()->json(['success' => 'Record deleted successfully.']);
}

}
