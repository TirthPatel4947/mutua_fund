<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PortfolioController
{
    // Show portfolios for the logged-in user
    public function index()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        // Fetch only the logged-in user's portfolios
        $portfolios = Portfolio::where('user_id', Auth::id())->get();

        return view('portfolio', compact('portfolios'));
    }

    // Store the portfolio for the logged-in user
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to create a portfolio.');
        }
    
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);
    
        Portfolio::create([
            'name' => $validatedData['name'],
            'user_id' => Auth::id()
        ]);
    
        // Flash success message
        Session::flash('success', 'Portfolio created successfully!');
    
        return redirect()->route('portfolio.index'); // Redirect back to the portfolio page
    }
}
