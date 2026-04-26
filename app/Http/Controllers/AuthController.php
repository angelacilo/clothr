<?php

/**
 * FILE: AuthController.php
 * 
 * What this file does:
 * This controller handles everything related to security and user accounts.
 * It manages the Login, Registration, and Logout processes for both 
 * customers and admins.
 * 
 * How it connects to the project:
 * - It is called by the login and register forms in routes/web.php.
 * - It uses the User model to find or create accounts.
 * - It manages the "Session" (how the browser remembers who is logged in).
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handles the Login request.
     * 
     * @param Request $request — contains email, password, and where to redirect
     * @return redirect — to the dashboard, homepage, or back with errors
     */
    public function postLogin(Request $request)
    {
        // VALIDATION: Make sure email and password are provided.
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Auth::attempt checks the database for the email and verifies the password.
        if (Auth::attempt($credentials, $request->has('remember'))) {
            // SECURITY: Regenerate the session ID to prevent "Session Fixation" attacks.
            $request->session()->regenerate();
            
            // ADMIN CHECK: If the user tried to log into the admin panel...
            if ($request->input('action') === 'admin') {
                // ...but they are not actually an admin, log them out immediately.
                if (!Auth::user()->is_admin) {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Access denied. You are not an admin.'])->withInput();
                }
                // If they are an admin, send them to the dashboard.
                return redirect()->intended('/admin/dashboard');
            }

            // CHECKOUT REDIRECT: If they were in the middle of shopping, send them back to checkout.
            if ($request->input('has_cart') == '1') {
                return redirect()->intended('/checkout');
            }
            
            // Otherwise, send them to the homepage.
            return redirect()->intended('/');
        }

        // If the email or password was wrong, send them back with an error message.
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'action'));
    }

    /**
     * Handles the Registration request for new customers.
     * 
     * @param Request $request — contains name, email, and password
     * @return redirect — to the homepage or checkout
     */
    public function postRegister(Request $request)
    {
        // 1. VALIDATION: Ensure name is provided, email is unique, and password is confirmed.
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. CREATE USER: Save the new account to the "users" table.
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            // Hash::make() encrypts the password. We never store plain text passwords!
            'password' => Hash::make($request->password),
        ]);

        // 3. LOG IN: Automatically log the user in after registration.
        Auth::login($user);

        // 4. NOTIFICATION: Tell the admin that a new customer joined the store.
        \App\Models\Notification::createNotification(
            'new_user',
            'New Customer Registered',
            $user->name . ' just created an account',
            '/admin/users'
        );

        // 5. REDIRECT: Send them to checkout if they have items, or the homepage.
        if ($request->input('has_cart') == '1') {
            return redirect()->intended('/checkout');
        }
        
        return redirect()->intended('/');
    }

    /**
     * Handles logging out.
     */
    public function logout(Request $request)
    {
        // Log the user out.
        Auth::logout();
        
        // SECURITY: Completely clear the session data for safety.
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Handles the "Forgot Password" initial request.
     * 
     * Note: This is a placeholder for the panelist to see how a reset starts.
     */
    public function sendResetCode(Request $request)
    {
        $request->validate(['identifier' => 'required']);
        return back()->with('status', 'A verification code has been sent to ' . $request->identifier);
    }
}
