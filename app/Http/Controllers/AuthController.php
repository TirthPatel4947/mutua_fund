<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'birthdate'  => $birthdate, // Save it as 'birthdate' instead of 'dob'
            'password'   => Hash::make($request->password),  // Hash the password
        ]);

        // After user is created, redirect them to login or wherever necessary
        return redirect()->route('login')->with('status', 'Account created successfully! Please log in.');
    }

    public function login(Request $request)
    {
        // Validate the email and password fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('dashboard'));
        }
    
        // Redirect back with an error message if authentication fails
        return redirect()->route('login')->with('error', 'Invalid email or password.');

    }
    
    public function logout(Request $request){
        Auth::logout();
        return redirect()->intended(route('login'));
    }

  
}
