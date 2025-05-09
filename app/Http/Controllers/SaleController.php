<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use App\Models\MutualNavHistory;
use App\Models\ReportHistory;
use App\Models\Portfolio;

class SaleController
{
    public function index()
    {
        $user_id = auth()->id(); // Get authenticated user ID
        //$funds = Sale::all(); // Fetch all sales (assuming this holds sale records)
        $funds = array();
        $portfolios = Portfolio::where('user_id', $user_id)->get(); // Fetch only user-specific portfolios

        return view('sale', compact('funds', 'portfolios')); // Pass portfolios to the view
    }

    public function getFunds(Request $request)
    {
        $search = $request->input('search');
        $funds = Sale::where('fundname', 'LIKE',  $search . '%')->get(['id', 'fundname']);
        $formattedFunds = $funds->map(fn($fund) => ['id' => $fund->id, 'text' => $fund->fundname]);
        return response()->json(['results' => $formattedFunds]);
    }

    public function getNavPrice(Request $request)
    {
        $fundId = $request->input('fund_id');
        $date = $request->input('date');
        $navHistory = MutualNavHistory::where('fundname_id', $fundId)->whereDate('date', $date)->first();
        return $navHistory ? response()->json(['nav_price' => $navHistory->nav]) : response()->json(['nav_price' => 0], 404);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fundname_id' => 'required|exists:mutualfund_master,id',
            'portfolio_id' => 'required|exists:portfolios,id',
            'date' => 'required|date',
            'totalprice' => 'required|numeric|min:0',
            'quantityofshare' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        try {
            $userId = auth()->id();
            $fundName = DB::table('mutualfund_master')->where('id', $validatedData['fundname_id'])->value('fundname');
            if (!$fundName) {
                return response()->json(['error' => 'Invalid Fund ID provided.'], 422);
            }

            ReportHistory::create([
                'user_id' => $userId,
                'fundname_id' => $validatedData['fundname_id'],
                'portfolio_id' => $validatedData['portfolio_id'],
                'date' => $validatedData['date'],
                'unit' => -1 * $validatedData['quantityofshare'], // Negative for sale
                'price' => $validatedData['price_per_unit'], 
                'total' => -1 * $validatedData['totalprice'], // Negative for sale
                'status' => 0, // Sale
            ]);

            return response()->json(['message' => 'Fund sale successfully saved!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save the form data. Error: ' . $e->getMessage()], 500);
        }
    }

    public function getPortfolios(Request $request)
    {
        $search = $request->input('search');
        $user_id = auth()->id();
        $portfolios = Portfolio::where('user_id', $user_id)
            ->where('name', 'like', "%$search%")
            ->select('id', 'name')
            ->get();
        return response()->json(['results' => $portfolios->map(fn($portfolio) => ['id' => $portfolio->id, 'text' => $portfolio->name])]);
    }
}
