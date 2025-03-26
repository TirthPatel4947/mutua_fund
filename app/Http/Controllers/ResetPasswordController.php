<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController
{
    public function showResetForm($token)
    {
        return view('passwords_reset', ['token' => $token, 'email' => request()->get('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',  // Ensure email is required
            'password' => 'required|min:6|confirmed',       // Ensure password and confirmation match
            'token' => 'required'                           // Ensure token is provided
        ]);
    
        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Reset password logic
                $user->password = Hash::make($password);
                $user->save();
                Auth::login($user);  // Log the user in after password reset
            }
        );
    
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
    
}
