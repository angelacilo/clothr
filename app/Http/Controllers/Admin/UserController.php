<?php

/**
 * FILE: Admin/UserController.php
 * 
 * What this file does:
 * This controller displays the list of all registered users in the admin panel.
 * 
 * How it connects to the project:
 * - It is called by routes in routes/admin.php.
 * - It uses the User model to fetch customer data.
 * - The view it returns is resources/views/admin/users.blade.php.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Shows the users management page.
     * 
     * This function gets the newest users first and displays 20 per page.
     * 
     * @return view — the users list page
     */
    public function index()
    {
        // Fetch all users, newest first (latest()), and use pagination.
        $users = User::latest()->paginate(20);
        
        // Pass the users list to the blade view.
        return view('admin.users', compact('users'));
    }
}
