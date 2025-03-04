<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController 
{
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'pan_no' => 'required|string|max:10|unique:users,pan_no,' . $user->id,
            'birthdate' => 'required|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }

            // Store new avatar in "public/avatars" directory
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;
        }

        $user->update($validatedData);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $user = auth()->user();
    
        // Delete old avatar if it exists
        if ($user->avatar && file_exists(public_path('assets/images/' . $user->avatar))) {
            unlink(public_path('assets/images/' . $user->avatar));
        }
    
        // Store new avatar
        if ($request->file('avatar')) {
            $filename = time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move(public_path('assets/images/'), $filename);
            
            $user->avatar = $filename; // Store only filename in DB
            $user->save();
        }
    
        return redirect()->back()->with('success', 'Avatar updated successfully.');
    }
    

    public function removeAvatar()
{
    $user = auth()->user();

    // Delete the avatar if it exists
    if ($user->avatar && file_exists(public_path('assets/images/' . $user->avatar))) {
        unlink(public_path('assets/images/' . $user->avatar));
    }

    $user->avatar = null; // Remove from DB
    $user->save();

    return response()->json(['success' => true]);
}

}
