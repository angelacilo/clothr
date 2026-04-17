<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function postLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            
            if (Auth::user()->is_admin) {
                return redirect()->intended('/admin/dashboard');
            }

            if (Auth::user()->is_rider) {
                return redirect()->intended('/rider/dashboard');
            }

            if ($request->input('has_cart') == '1') {
                return redirect()->intended('/checkout');
            }
            
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'action'));
    }

    public function postRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        // Trigger New User Notification
        \App\Models\Notification::createNotification(
            'new_user',
            'New Customer Registered',
            $user->name . ' just created an account',
            '/admin/users'
        );

        if ($request->input('has_cart') == '1') {
            return redirect()->intended('/checkout');
        }
        
        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['identifier' => 'required']);
        return back()->with('status', 'A verification code has been sent to ' . $request->identifier);
    }
}
