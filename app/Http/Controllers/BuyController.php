<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Buy;
use App\Models\MutualFund_Master;
use App\Models\MutualNavHistory;
use App\Models\MutualFundMaster;
use App\Models\ReportHistory;

class BuyController
{
    // Display the buy form
    public function index()
    {
        $funds = Buy::all(); // Fetch available funds
        return view('buy', compact('funds')); // Pass funds to the view
    }

    // Fetch funds dynamically for select2 dropdown
    public function getFunds(Request $request)
    {
        $search = $request->input('search');
    
        $funds = Buy::where('fundname', 'LIKE', '%' . $search . '%')
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
            'fundname_id' => 'required|exists:mutualfund_master,id', // Validate fundname_id
            'date' => 'required|date',
            'totalprice' => 'required|numeric|min:0',
            'quantityofshare' => 'required|numeric|min:0',
        ]);
    
        try {
            // Get authenticated user's ID
            $userId = auth()->id(); // Ensure user is authenticated
    
            if (!$userId) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }
    
            // Store the report history with status = 1 and user ID
            ReportHistory::create([
                'user_id' => $userId, // Store the authenticated user's ID
                'fundname_id' => $validatedData['fundname_id'],
                'date' => $validatedData['date'],
                'unit' => $validatedData['quantityofshare'],
                'price' => $validatedData['totalprice'],
                'total' => $validatedData['quantityofshare'] * $validatedData['totalprice'],
                'status' => 1,
            ]);
    
            return response()->json(['message' => 'Fund purchase successfully saved!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save the form data. Error: ' . $e->getMessage()], 500);
        }
    }
    
}
