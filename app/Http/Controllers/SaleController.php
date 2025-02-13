<?php

namespace App\Http\Controllers;  // This must be the first statement

use Illuminate\Http\Request;
use App\Models\sale;
use Illuminate\Support\Facades\DB;
use App\Models\mutual_nav_history;
use App\Models\ReportHistory;

class SaleController
{
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
        // Get the search term from the request
        $search = $request->input('search');

        // Query to filter funds based on the search
        $funds = Sale::where('fundname', 'LIKE', '%' . $search . '%')->get(['id', 'fundname']);

        // Return the filtered funds as a JSON response
        return response()->json($funds);
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
            // Retrieve fund name based on the ID
            $fundName = DB::table('mutualfund_master')
                ->where('id', $validatedData['fundname_id'])
                ->value('fundname');

            if (!$fundName) {
                return response()->json(['error' => 'Invalid Fund ID provided.'], 422);
            }

            // Store the report history with status = 0 for sale
            ReportHistory::create([
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
