<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function orders()
    {
        return view('admin.orders');
    }

    public function products()
    {
        return view('admin.products');
    }

    public function archive()
    {
        return view('admin.archive');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function reviews()
    {
        return view('admin.reviews');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
