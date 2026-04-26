<?php

/**
 * FILE: Admin/AdminController.php
 * 
 * What this file does:
 * This controller handles the management of "Couriers" (delivery companies).
 * The admin can add new courier accounts and delete old ones.
 * When a courier is added, the system automatically creates a User account 
 * for them with the role "courier".
 * 
 * How it connects to the project:
 * - It is called by routes in routes/admin.php.
 * - It uses the User and Courier models.
 * - It allows the courier portal to work by providing login credentials 
 *   to the delivery companies.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Shows the list of all registered Couriers.
     * 
     * @return view — the couriers management page
     */
    public function couriers()
    {
        // Get all couriers and their linked user accounts.
        $couriers = Courier::with('user')->get();
        return view('admin.couriers', compact('couriers'));
    }

    /**
     * Saves a new Courier and creates their login account.
     * 
     * @param Request $request — contains name, email, password, and courier code
     * @return redirect — back with success message
     */
    public function storeCourier(Request $request)
    {
        // VALIDATION: Ensure the name, code, email, and password are correct.
        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:10|unique:couriers,code',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // STEP 1: Create the User account so the courier can log in.
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            // Hash::make() encrypts the password for security.
            'password' => Hash::make($request->password),
            'role'     => 'courier', // Assign the specific role.
        ]);

        // STEP 2: Create the Courier profile linked to that user.
        Courier::create([
            'name'    => $request->name,
            'code'    => strtoupper($request->code), // Force uppercase for codes like "JT".
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Courier ' . $request->name . ' created successfully.');
    }

    /**
     * Deletes a Courier and their associated User account.
     * 
     * @param int $id — the ID of the courier to delete
     * @return redirect — back with success message
     */
    public function deleteCourier($id)
    {
        // Find the courier profile.
        $courier = Courier::findOrFail($id);
        // Find the linked user account.
        $user = User::findOrFail($courier->user_id);
        
        // Remove both from the database.
        $courier->delete();
        $user->delete();
        
        return back()->with('success', 'Courier deleted successfully.');
    }
}
