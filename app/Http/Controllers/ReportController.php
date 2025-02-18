<?php

namespace App\Http\Controllers;

use App\Models\mutual_nav_history;
use App\Models\MutualFund_Master;
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
    // Show the form for editing a specific buy fund record
    public function edit($id)
    {
        // Fetch the data using the ID and load the related fund data
        $buyData = ReportHistory::with('fund')->find($id);
    
        // Check if data exists
        if (!$buyData) {
            return response()->json(['error' => 'Data not found.'], 404);
        }
    
        // Fetch all available funds for the dropdown
        $funds = MutualFund_Master::all();
    
        // If it's an AJAX request, return JSON response
        if (request()->ajax()) {
            return response()->json([
                'fund_name' => $buyData->fund->fundname ?? 'N/A',
                'buying_date' => $buyData->date,
                'quantity_of_shares' => $buyData->unit,
                'price_per_unit' => $buyData->price / ($buyData->unit ?: 1),
                'total_price' => $buyData->price,
            ]);
        }
    
        // Otherwise, return the view with data
        return view('buy', compact('buyData', 'funds'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'fundname_id' => 'required|exists:mutualfund_master,id',
            'date' => 'required|date',
            'totalprice' => 'required|numeric|min:0',
            'quantityofshare' => 'required|numeric|min:0',
        ]);
    
        try {
            $buyRecord = ReportHistory::findOrFail($id);
    
            $buyRecord->fundname_id = $validatedData['fundname_id'];
            $buyRecord->date = $validatedData['date'];
            $buyRecord->unit = $validatedData['quantityofshare'];
            $buyRecord->price = $validatedData['totalprice'];
            // $buyRecord->total = $validatedData['quantityofshare'] * $validatedData['totalprice'];
            $buyRecord->status = 1;
            $buyRecord->save();
    
            return response()->json(['message' => 'Fund successfully updated!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update the form data. Error: ' . $e->getMessage()], 500);
        }
    }
    

}
