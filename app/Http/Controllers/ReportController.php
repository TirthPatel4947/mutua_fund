<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\MutualFund_Master;
use App\Models\ReportHistory;
use App\Models\Portfolio;
use Carbon\Carbon;

class ReportController 
{
    public function index()
    {
        $userId = Auth::id();
        $portfolios = Portfolio::where('user_id', $userId)->get();
        return view('report', compact('portfolios'));
    }

    public function getReports(Request $request)
    {
        $userId = auth()->id();
        
        $reports = ReportHistory::with('fund', 'portfolio')
            ->where('user_id', $userId);
        
        if ($request->filled('type') && $request->type !== 'all') {
            $reports->where('status', $request->type === 'buy' ? 1 : 0);
        }
        
        if ($request->filled('portfolio_id')) {
            $reports->where('portfolio_id', $request->portfolio_id);
        }
        
        if ($request->filled('date_range')) {
            $dateRange = explode(' - ', $request->date_range);
            $startDate = Carbon::parse($dateRange[0])->startOfDay();
            $endDate = Carbon::parse($dateRange[1])->endOfDay();
            $reports->whereBetween('date', [$startDate, $endDate]);
        }
    
        return DataTables::of($reports)
            ->addColumn('portfolio_name', fn($report) => optional($report->portfolio)->name ?? 'N/A')
            ->addColumn('fund_name', fn($report) => optional($report->fund)->fundname ?? 'N/A')
            ->addColumn('date', fn($report) => Carbon::parse($report->date)->format('Y-m-d'))
            ->addColumn('type', fn($report) => $report->unit > 0 ? 'Purchase' : 'Redemption')
            ->addColumn('quantity_of_shares', fn($report) => $report->unit)
            ->addColumn('price_per_unit', fn($report) => '₹' . number_format($report->price, 2))
            ->addColumn('total_price', fn($report) => '₹' . number_format($report->total, 2)) // ✅ Fetching from DB
            ->make(true);
    }

    public function destroy($id)
    {
        $userId = auth()->id();
        $report = ReportHistory::where('id', $id)->where('user_id', $userId)->first();
        
        if (!$report) {
            return response()->json(['error' => 'Unauthorized or record not found.'], 403);
        }
        
        $report->delete();
        return response()->json(['success' => 'Record deleted successfully.']);
    }

    public function edit($id)
    {
        $userId = auth()->id();
        $buyData = ReportHistory::with(['fund', 'portfolio'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if (!$buyData) {
            return response()->json(['error' => 'Unauthorized or data not found.'], 403);
        }
        
        $funds = MutualFund_Master::where('id', $buyData->fundname_id)->get();
        $portfolios = Portfolio::where('user_id', $userId)->get();
        
        return view('buy', compact('buyData', 'funds', 'portfolios'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'fundname_id' => 'required|exists:mutualfund_master,id',
            'portfolio_id' => 'required|exists:portfolios,id',
            'date' => 'required|date',
            'quantityofshare' => 'required|numeric|min:0.0001',
            'price_per_unit' => 'required|numeric|min:0',
        ]);
    
        $userId = auth()->id();
        $buyRecord = ReportHistory::where('id', $id)->where('user_id', $userId)->first();
    
        if (!$buyRecord) {
            return response()->json(['error' => 'Unauthorized or record not found.'], 403);
        }
    
        // ✅ Calculate total before updating
        $total = $validatedData['quantityofshare'] * $validatedData['price_per_unit'];
    
        // ✅ Updating including `total`
        $buyRecord->update([
            'fundname_id' => $validatedData['fundname_id'],
            'portfolio_id' => $validatedData['portfolio_id'],
            'date' => $validatedData['date'],
            'unit' => $validatedData['quantityofshare'],
            'price' => $validatedData['price_per_unit'],
            'total' => $total, // ✅ Manually updating total
        ]);
    
        return response()->json([
            'message' => 'Fund successfully updated!',
            'price_per_unit' => number_format($validatedData['price_per_unit'], 2),
            'total' => number_format($total, 2),
        ], 200);
    }
    
    

    public function editSale($id)
    {
        $userId = auth()->id();
        $saleData = ReportHistory::with(['fund', 'portfolio'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if (!$saleData) {
            return response()->json(['error' => 'Unauthorized or data not found.'], 403);
        }
    
        $funds = MutualFund_Master::where('id', $saleData->fundname_id)->get();
        $portfolios = Portfolio::where('user_id', $userId)->get();
    
        return view('sale', compact('saleData', 'funds', 'portfolios'));
    }

    public function updateSale(Request $request, $id)
    {
        $validatedData = $request->validate([
            'fundname_id' => 'required|exists:mutualfund_master,id',
            'portfolio_id' => 'required|exists:portfolios,id',
            'date' => 'required|date',
            'quantityofshare' => 'required|numeric|min:0.0001',
            'price_per_unit' => 'required|numeric|min:0',
        ]);
        $total = -($validatedData['quantityofshare'] * $validatedData['price_per_unit']);
        $userId = auth()->id();
        $buyRecord = ReportHistory::where('id', $id)->where('user_id', $userId)->first();
    
        if (!$buyRecord) {
            return response()->json(['error' => 'Unauthorized or record not found.'], 403);
        }
    
        $buyRecord->update([
            'fundname_id' => $validatedData['fundname_id'],
            'portfolio_id' => $validatedData['portfolio_id'],
            'date' => $validatedData['date'],
            'unit' => -$validatedData['quantityofshare'], // Keeps unit negative for sales
            'price' => $validatedData['price_per_unit'], 
            'total' => $total, // Now total is also negative
        ]);
        
        
        return response()->json([
            'message' => 'Fund successfully updated!',
            'price_per_unit' => number_format($validatedData['price_per_unit'], 2),
            'total' => number_format($total, 2),
        ], 200);
    }
}
