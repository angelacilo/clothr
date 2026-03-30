<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        return view('admin.settings', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth()->user();
        if (!$admin) {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'bio']);

        if ($request->hasFile('avatar')) {
            if ($admin->avatar) {
                $oldPath = str_replace(['/storage/', 'storage/'], '', $admin->avatar);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = 'storage/' . $path; // Matches how profile works
        }

        $admin->update($data);
        return back()->with('success', 'Profile updated successfully');
    }
}
