<?php

/**
 * FILE: VerifyCsrfToken.php
 * 
 * What this file does:
 * This is a CRITICAL security file. It prevents "Cross-Site Request Forgery" (CSRF).
 * 
 * WHY IS THIS IMPORTANT? (For the panelist):
 * Without this, a hacker could trick you into clicking a link that 
 * silently places an order on Clothr using your logged-in account.
 * 
 * HOW IT WORKS:
 * Every time you submit a form (like Login or Checkout), Laravel includes 
 * a secret "Token" (a random string of letters).
 * This middleware checks if that token is correct. 
 * If a hacker tries to submit a form from another website, they won't 
 * have the secret token, so the system will block them.
 */

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs (links) that don't need this security check.
     * Usually, we leave this empty to keep the whole site safe.
     * 
     * @var array
     */
    protected $except = [
        //
    ];
}
