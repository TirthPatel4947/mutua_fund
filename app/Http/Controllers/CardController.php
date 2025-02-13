<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardController
{
    // Show the form
    public function showForm()
    {
        return view('pancard');
    }

    // Handle the form submission
    public function processForm(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'pan' => 'required|string',
            'otp' => 'required|string',
        ]);

        // Simulate form processing
        return redirect()->route('card')->with('success', 'Your investment data fetched successfully!');
    }
}
