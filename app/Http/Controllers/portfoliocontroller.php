<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortfolioController
{
    // GET method to show the portfolio page (form or portfolio data)
    public function index()
    {
        // Return the portfolio view (for displaying the portfolio form or list)
        return view('portfolio');
    }

    // POST method to handle the form submission (e.g., create or update portfolio)
    public function store(Request $request)
    {
        // Validate the incoming form data
        $validatedData = $request->validate([
            'portfolio_name' => 'required|string|max:255',
        ]);

        // You can save or update the portfolio in the database
        // Portfolio::create($validatedData); // Example for creating a new portfolio

        // Redirect or send a success message
        return redirect()->route('portfolio.index')->with('success', 'Portfolio created successfully!');
    }
}
