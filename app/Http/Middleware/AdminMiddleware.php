<?php

/**
 * FILE: AdminMiddleware.php
 * 
 * What this file does:
 * Think of this as a "Security Guard" or "Traffic Cop".
 * It stands in front of the Admin routes and checks every person 
 * trying to enter.
 * 
 * How it works:
 * If you try to go to "/admin/dashboard", this file stops you and asks:
 * 1. Are you logged in?
 * 2. Are you actually an Admin?
 * 
 * If the answer is NO, it kicks you back to the homepage.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request — the page the user wants to see
     * @param  \Closure  $next — the "Go Ahead" signal to let the user through
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in AND if the is_admin column in DB is true.
        if (auth()->check() && auth()->user()->is_admin) {
            // Let them through to the page they asked for.
            return $next($request);
        }

        // If they are not an admin, block them and send them home.
        return redirect('/')->with('error', 'Unauthorized access.');
    }
}
