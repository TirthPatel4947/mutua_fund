<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\mutual_nav_history;
use App\Models\MutualFund_Master;
use App\Models\ReportHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Portfolio;
use Illuminate\Support\Facades\DB;


class ReportController
{

    
    public function index()
    {
        $userId = Auth::id(); // Get the authenticated user ID
        $portfolios = Portfolio::where('user_id', $userId)->get(); // Fetch portfolios for the logged-in user
    
        return view('report', compact('portfolios'));
    }
    
    /**
     * Get Buy Reports (Yajra DataTables) with Date Range Filter
     */
    public function getBuyReports(Request $request)
    {
        // Get the authenticated user ID
        $userId = auth()->id();
    
        $buyReports = ReportHistory::with('fund', 'portfolio')
            ->where('status', 1)
            ->where('user_id', $userId); // Filter by user_id
    
        // Apply portfolio filter if provided
        if ($request->has('portfolio_id') && $request->portfolio_id) {
            $buyReports->where('portfolio_id', $request->portfolio_id);
        }
    
        // Apply date range filter if provided
        if ($request->has('date_range') && $request->date_range) {
            $dateRange = explode(' - ', $request->date_range);
            $startDate = \Carbon\Carbon::parse($dateRange[0])->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateRange[1])->endOfDay();
            $buyReports->whereBetween('date', [$startDate, $endDate]);
        }
    
        return DataTables::of($buyReports)
            ->addColumn('name', function ($report) {
                return optional($report->portfolio)->name ?? 'N/A';
            })
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
        // Get the authenticated user ID
        $userId = auth()->id();
    
        $sellReports = ReportHistory::with('fund', 'portfolio')
            ->where('status', 0)
            ->where('user_id', $userId); // Filter by user_id
    
        // Apply portfolio filter if provided
        if ($request->has('portfolio_id') && $request->portfolio_id) {
            $sellReports->where('portfolio_id', $request->portfolio_id);
        }
    
        // Apply date range filter if provided
        if ($request->has('date_range') && $request->date_range) {
            $dateRange = explode(' - ', $request->date_range);
            $startDate = \Carbon\Carbon::parse($dateRange[0])->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateRange[1])->endOfDay();
            $sellReports->whereBetween('date', [$startDate, $endDate]);
        }
    
        return DataTables::of($sellReports)
            ->addColumn('name', function ($report) {
                return optional($report->portfolio)->name ?? 'N/A';
            })
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
        $userId = auth()->id();
        
        // Find the record with a user_id check
        $report = ReportHistory::where('id', $id)->where('user_id', $userId)->first();
    
        if (!$report) {
            return response()->json(['error' => 'Unauthorized or record not found.'], 403);
        }
    
        $report->delete();
    
        return response()->json(['success' => 'Record deleted successfully.']);
    }
    
    // Show the form for editing a specific buy fund record
    public function edit($id)
    {
        $userId = auth()->id();
    
        // Fetch the data ensuring it belongs to the logged-in user
        $buyData = ReportHistory::with(['fund', 'portfolio']) // Ensure 'portfolio' is loaded
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    
        if (!$buyData) {
            return response()->json(['error' => 'Unauthorized or data not found.'], 403);
        }
    
        // Fetch all available funds and portfolios for the dropdown
        $funds = MutualFund_Master::all();
        $portfolios = Portfolio::where('user_id', $userId)->get(); // Fetch only user-specific portfolios
    
        // If it's an AJAX request, return JSON response
        if (request()->ajax()) {
            return response()->json([
                'fund_name' => $buyData->fund->fundname ?? 'N/A',
                'fund_id' => $buyData->fund->id ?? null,
                'portfolio_id' => $buyData->portfolio->id ?? null, // Include portfolio_id
                'buying_date' => $buyData->date,
                'quantity_of_shares' => $buyData->unit,
                'price_per_unit' => $buyData->price / ($buyData->unit ?: 1),
                'total_price' => $buyData->price,
            ]);
        }
    
        // Otherwise, return the view with data
        return view('buy', compact('buyData', 'funds', 'portfolios'));
    }
    

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'fundname_id' => 'required|exists:mutualfund_master,id',
            'portfolio_id' => 'required|exists:portfolios,id', // New validation for portfolio
            'date' => 'required|date',
            'totalprice' => 'required|numeric|min:0',
            'quantityofshare' => 'required|numeric|min:0',
        ]);
    
        try {
            $userId = auth()->id();
    
            // Find the record ensuring it belongs to the logged-in user
            $buyRecord = ReportHistory::where('id', $id)->where('user_id', $userId)->first();
    
            if (!$buyRecord) {
                return response()->json(['error' => 'Unauthorized or record not found.'], 403);
            }
    
            // Update values
            $buyRecord->fundname_id = $validatedData['fundname_id'];
            $buyRecord->portfolio_id = $validatedData['portfolio_id']; // Assign portfolio_id
            $buyRecord->date = $validatedData['date'];
            $buyRecord->unit = $validatedData['quantityofshare'];
            $buyRecord->price = $validatedData['totalprice'];
            $buyRecord->status = 1;
            $buyRecord->save();
    
            return response()->json(['message' => 'Fund successfully updated!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update the form data. Error: ' . $e->getMessage()], 500);
        }
    }
    
    
    public function editSale($id)
    {
        $userId = auth()->id(); // Get authenticated user's ID
    
        // Fetch the sale data ensuring it belongs to the logged-in user
        $saleData = ReportHistory::with(['fund', 'portfolio'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    
        // Check if data exists
        if (!$saleData) {
            return response()->json(['error' => 'Unauthorized or data not found.'], 403);
        }
    
        // Fetch all available funds and user-specific portfolios for the dropdown
        $funds = MutualFund_Master::all();
        $portfolios = Portfolio::where('user_id', $userId)->get(); // Fetch only user-specific portfolios
    
        // If it's an AJAX request, return JSON response
        if (request()->ajax()) {
            return response()->json([
                'fund_name' => $saleData->fund->fundname ?? 'N/A',
                'fund_id' => $saleData->fund->id ?? null, // Ensure fund_id is included
                'portfolio_id' => $saleData->portfolio->id ?? null, // Fetch from relationship
                'selling_date' => $saleData->date,
                'quantity_of_shares' => $saleData->unit,
                'price_per_unit' => $saleData->price / ($saleData->unit ?: 1),
                'total_price' => $saleData->price,
            ]);
        }

        // Otherwise, return the view with data
        return view('sale', compact('saleData', 'funds', 'portfolios'));
    }
    
    
    
 public function updateSale(Request $request, $id)
{
    $validatedData = $request->validate([
        'fundname_id' => 'required|exists:mutualfund_master,id',
        'portfolio_id' => 'required|exists:portfolios,id', // Validate portfolio_id
        'date' => 'required|date',
        'totalprice' => 'required|numeric|min:0',
        'quantityofshare' => 'required|numeric|min:0',
    ]);

    try {
        $userId = auth()->id();

        // Find the sale record ensuring it belongs to the logged-in user
        $saleRecord = ReportHistory::where('id', $id)->where('user_id', $userId)->first();

        if (!$saleRecord) {
            return response()->json(['error' => 'Unauthorized or record not found.'], 403);
        }

        // Update the sale record with negative values
        $saleRecord->fundname_id = $validatedData['fundname_id'];
        $saleRecord->portfolio_id = $validatedData['portfolio_id']; // Assign portfolio_id
        $saleRecord->date = $validatedData['date'];
        $saleRecord->unit = -abs($validatedData['quantityofshare']); // Store as negative for sale
        $saleRecord->price = -abs($validatedData['totalprice']); // Store as negative for sale
        $saleRecord->status = 0; // Marking as a sale
        $saleRecord->save();

        return response()->json(['message' => 'Sale successfully updated!'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update the sale record. Error: ' . $e->getMessage()], 500);
    }
}
public function combinedReport(Request $request)
{
    $userId = auth()->id(); // Get authenticated user ID

    $query = DB::table('report_history')
        ->join('mutualfund_master', 'report_history.fundname_id', '=', 'mutualfund_master.id')
        ->join('portfolios', 'report_history.portfolio_id', '=', 'portfolios.id')
        ->select(
            DB::raw("CASE 
                        WHEN report_history.status = 1 THEN 'Purchase' 
                        WHEN report_history.status = 0 THEN 'Redemption' 
                    END AS type"),
            'portfolios.name as portfolio_name',
            'mutualfund_master.fundname as fund_name',
            'report_history.date as date',
            'report_history.unit as quantity_of_shares',

            // Corrected Price Per Unit Calculation
            DB::raw("ROUND(
                CASE 
                    WHEN report_history.status = 1 THEN report_history.price / NULLIF(report_history.unit, 0)
                    WHEN report_history.status = 0 THEN ABS(report_history.price) / ABS(NULLIF(report_history.unit, 0))
                END, 2) as price_per_unit"),

            // Corrected Total Price Calculation with Redemption Fix
            DB::raw("ROUND(
                CASE 
                    WHEN report_history.status = 1 THEN report_history.price 
                    WHEN report_history.status = 0 THEN ABS(report_history.unit) * (ABS(report_history.price) / ABS(NULLIF(report_history.unit, 0))) * -1
                END, 2) as total_price"),
            
            'report_history.id'
        )
        ->where('report_history.user_id', $userId);

    // Apply filters
    if ($request->filled('date_range')) {
        $dateRange = explode(' - ', $request->date_range);
        $query->whereBetween('report_history.date', [$dateRange[0], $dateRange[1]]);
    }

    if ($request->filled('portfolio_id')) {
        $query->where('report_history.portfolio_id', $request->portfolio_id);
    }

    return datatables()->of($query)->make(true);
}





}