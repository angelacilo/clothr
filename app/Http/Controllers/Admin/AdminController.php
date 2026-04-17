<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function couriers()
    {
        $couriers = Courier::with('user')->get();
        return view('admin.couriers', compact('couriers'));
    }

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

    public function deleteCourier($id)
    {
        $courier = Courier::findOrFail($id);
        $user = User::findOrFail($courier->user_id);
        
        $courier->delete();
        $user->delete();
        
        return back()->with('success', 'Courier deleted successfully.');
    }
}
