<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Buy;
use App\Models\MutualFund_Master;
use App\Models\MutualNavHistory;
use App\Models\ReportHistory;
use App\Models\Portfolio;

class BuyController 
{
    // Display the buy form
    public function index()
    {
        $user_id = auth()->id();
        // $funds = MutualFund_Master::all();
        $funds = array();
        $portfolios = Portfolio::where('user_id', $user_id)->get(); // Fetch user-specific portfolios
    
        return view('buy', compact('funds', 'portfolios')); // Pass portfolios to the view
    }
    
    // Fetch funds dynamically for select2 dropdown
    public function getFunds(Request $request)
    {
        $search = $request->input('search');
    
        $funds = MutualFund_Master::where('fundname', 'LIKE', $search . '%')
            ->get(['id', 'fundname']);
    
        // Convert to Select2-compatible format
        $formattedFunds = $funds->map(function ($fund) {
            return ['id' => $fund->id, 'text' => $fund->fundname];
        });
    
        return response()->json(['results' => $formattedFunds]); // Correct JSON format
    }

    // Fetch NAV price for a selected fund and date
    public function getNavPrice(Request $request)
    {
        $fundId = $request->input('fund_id');
        $date = $request->input('date');

        if (empty($fundId) || empty($date)) {
            return response()->json(['nav_price' => 0, 'error' => 'Fund ID and Date are required.'], 422);
        }

        $navHistory = MutualNavHistory::where('fundname_id', $fundId)
            ->whereDate('date', $date)
            ->first();

        if ($navHistory) {
            return response()->json(['nav_price' => $navHistory->nav]);
        }

        return response()->json(['nav_price' => 0, 'error' => 'NAV not found for the selected date.'], 404);
    }
 
    // Store Buy Fund in the report_history table
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'fundname_id' => 'required|exists:mutualfund_master,id',
            'portfolio_id' => 'required|exists:portfolios,id',
            'date' => 'required|date',
            'totalprice' => 'required|numeric|min:0',
            'quantityofshare' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0', 
        ]);
        
    
        try {
            // Get authenticated user's ID
            $userId = auth()->id(); 
    
            if (!$userId) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }
    
            // Create the report history entry
            $reportHistory = ReportHistory::create([
                'user_id' => $userId,
                'fundname_id' => $validatedData['fundname_id'],
                'portfolio_id' => $validatedData['portfolio_id'],
                'date' => $validatedData['date'],
                'unit' => $validatedData['quantityofshare'],
                'price' => $validatedData['price_per_unit'], 
                'total' => $validatedData['totalprice'], 
                'status' => 1, 
            ]);
            
    
            return response()->json(['message' => 'Fund purchase successfully saved!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save the form data. Error: ' . $e->getMessage()], 500);
        }
    }
    
    
    
   // Fetch portfolios dynamically for select2 dropdown (User-specific portfolios)
public function getPortfolios(Request $request)
{
    $search = $request->input('search');
    $user_id = auth()->id(); // Get the current authenticated user's ID
    
    // Fetch portfolios that belong to the authenticated user and match the search term
    $portfolios = Portfolio::where('user_id', $user_id)
        ->where('name', 'like', "%$search%")
        ->select('id', 'name')
        ->get();

    return response()->json([
        'results' => $portfolios->map(function ($portfolio) {
            return [
                'id' => $portfolio->id,
                'text' => $portfolio->name
            ];
        })
    ]);
}

    
}
