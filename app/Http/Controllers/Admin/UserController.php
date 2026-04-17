<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function toggleRider(User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Admin role cannot be changed.');
        }

        $user->is_rider = !$user->is_rider;
        $user->save();

        $role = $user->is_rider ? 'Rider' : 'Customer';
        return back()->with('success', "User promoted to {$role} successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Admins cannot be deleted.');
        }

        $user->delete();
        return back()->with('success', 'User account deleted successfully.');
    }
}
