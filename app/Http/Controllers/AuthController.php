<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auth;

class AuthController
{
    // Show the login form
    public function showLoginForm()
    {
        return view('login');
    }

    // Show the signup form
    public function showSignupForm()
    {
        return view('signup');
    }

    // Handle the signup form submission
   // Handle the signup form submission
public function storeSignup(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|numeric',
        'dob_day' => 'required|numeric',
        'dob_month' => 'required|numeric',
        'dob_year' => 'required|numeric',
        'password' => 'required|string|min:8|confirmed', // password confirmation
    ]);

    // Format the date of birth (now using birthdate column)
    $birthdate = "{$request->dob_year}-{$request->dob_month}-{$request->dob_day}"; // Format the birthdate

    // Create the new user
    $user = Auth::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'birthdate' => $birthdate, // Save it as 'birthdate' instead of 'dob'
        'password' => bcrypt($request->password),  // Hash the password
    ]);

    // After user is created, redirect them to login or wherever necessary
    return redirect()->route('login')->with('status', 'Account created successfully! Please log in.');
}

  
}
