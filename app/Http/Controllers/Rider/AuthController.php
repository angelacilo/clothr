<?php
namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check() && auth()->user()->role === 'rider') {
            return redirect()->route('rider.dashboard');
        }
        return view('rider.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (auth()->user()->role !== 'rider') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account is not authorized for the Rider Portal.',
                ])->onlyInput('email');
            }
            $request->session()->regenerate();
            return redirect()->route('rider.dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('rider.login');
    }
}
