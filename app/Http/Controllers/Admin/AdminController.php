<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function storeCourier(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:10|unique:couriers,code',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'courier',
        ]);

        Courier::create([
            'name'    => $request->name,
            'code'    => strtoupper($request->code),
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Courier ' . $request->name . ' created successfully.');
    }
}
