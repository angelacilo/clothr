<?php

/**
 * FILE: Admin/SettingsController.php
 * 
 * What this file does:
 * This controller allows the admin to update their own profile information,
 * such as their name, email address, phone number, and profile picture (avatar).
 * 
 * How it connects to the project:
 * - It is called by routes in routes/admin.php.
 * - It uses the User model to update the logged-in admin's data.
 * - The view it returns is resources/views/admin/settings.blade.php.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Shows the admin settings / profile page.
     * 
     * @return view — the settings page
     */
    public function index()
    {
        // Get the information of the person who is currently logged in.
        $admin = auth()->user();
        
        return view('admin.settings', compact('admin'));
    }

    /**
     * Saves the updated profile information for the admin.
     * 
     * @param Request $request — contains the new profile data
     * @return redirect — back to the settings page with success message
     */
    public function updateProfile(Request $request)
    {
        // Identify who is logged in.
        $admin = auth()->user();
        if (!$admin) {
            return back()->with('error', 'Unauthorized');
        }

        // VALIDATION: Ensure inputs are correct.
        // The email unique check allows the user to keep their current email.
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $admin->id,
            'phone'  => 'nullable|string|max:20',
            'bio'    => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Grab only the text fields we need.
        $data = $request->only(['name', 'email', 'phone', 'bio']);

        // Handle profile picture upload.
        if ($request->hasFile('avatar')) {
            // Delete the old photo from the server storage to save space.
            if ($admin->avatar) {
                $oldPath = str_replace(['/storage/', 'storage/'], '', $admin->avatar);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
            
            // Save the new photo to the "avatars" folder.
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = 'storage/' . $path;
        }

        // Update the user record in the database.
        $admin->update($data);
        
        return back()->with('success', 'Profile updated successfully');
    }
}
