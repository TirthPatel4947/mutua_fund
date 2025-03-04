<?php

namespace App\Http\Controllers;  // This must be the first statement

use Illuminate\Http\Request;
use App\Models\sale;
use Illuminate\Support\Facades\DB;
use App\Models\mutual_nav_history;
use App\Models\ReportHistory;

class SaleController
{
    //test
    // Fetch and display the sale form
    public function index()
    {
        // Get all funds
        $funds = Sale::all();
        return view('sale', compact('funds')); // Pass the funds to the view
    }

    // Fetch funds dynamically for the AJAX request (used in the select2)
    public function getFunds(Request $request)
    {
        $search = $request->input('search');
    
        $funds = sale::where('fundname', 'LIKE', '%' . $search . '%')
            ->get(['id', 'fundname']);
    
        // Convert to Select2-compatible format
        $formattedFunds = $funds->map(function ($fund) {
            return ['id' => $fund->id, 'text' => $fund->fundname];
        });
    
        return response()->json(['results' => $formattedFunds]); // Correct JSON format
    }
    // Fetch NAV price based on fund_id and date
    public function getNavPrice(Request $request)
    {
        $fundId = $request->input('fund_id');
        $date = $request->input('date');

        // Fetch NAV price from the mutual_nav_history table
        $navHistory = mutual_nav_history::where('fundname_id', $fundId)
            ->whereDate('date', $date)
            ->first();

        if ($navHistory) {
            // Return the NAV price (assuming 'nav' column holds the price for one unit)
            return response()->json(['nav_price' => $navHistory->nav]);
        }

        // Handle case where NAV is not found
        return response()->json(['nav_price' => 0], 404);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fundname_id' => 'required|exists:mutualfund_master,id',
            'date' => 'required|date',
            'totalprice' => 'required|numeric|min:0',
            'quantityofshare' => 'required|numeric|min:0', // Quantity must be positive here
        ]);
    
        try {
            // Get the authenticated user ID
            $userId = auth()->id(); 
    
            // Retrieve fund name based on the ID
            $fundName = DB::table('mutualfund_master')
                ->where('id', $validatedData['fundname_id'])
                ->value('fundname');
    
            if (!$fundName) {
                return response()->json(['error' => 'Invalid Fund ID provided.'], 422);
            }
    
            // Store the report history with status = 0 for sale and user_id
            ReportHistory::create([
                'user_id' => $userId, // Store the user ID
                'fundname_id' => $validatedData['fundname_id'],
                'date' => $validatedData['date'],
                'unit' => -1 * $validatedData['quantityofshare'], // Store units as negative for sale
                'price' => -1 * $validatedData['totalprice'], // Price remains positive
                'total' => -1 * $validatedData['quantityofshare'] * $validatedData['totalprice'], // Total is negative
                'status' => 0, // Set status as 0 for sale
            ]);
    
            return response()->json(['message' => 'Fund sale successfully saved!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save the form data. Error: ' . $e->getMessage()], 500);
        }
    }
    
}
