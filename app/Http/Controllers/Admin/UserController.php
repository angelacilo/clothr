<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('orders')
                    ->where('is_admin', false)
                    ->latest()
                    ->paginate(20);
                    
        return view('admin.users', compact('users'));
    }
}
