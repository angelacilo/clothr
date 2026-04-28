<?php

/**
 * FILE: Profile/ProfileController.php
 * 
 * What this file does:
 * This controller manages the customer's personal dashboard (Profile).
 * It allows customers to see their Order History, manage their Saved Addresses, 
 * view their Wishlist, and update their Profile Settings (name, email, avatar).
 * 
 * How it connects to the project:
 * - It is called by routes starting with "/profile" in routes/web.php.
 * - It uses the User, Order, and Wishlist models.
 * - Every function here requires the user to be logged in (Middleware check).
 */

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Wishlist;

class ProfileController extends Controller
{
    /**
     * Redirects the user to their orders list by default.
     */
    public function index()
    {
        return redirect()->route('profile.orders');
    }

    /**
     * Shows the customer's order history.
     * 
     * @param Request $request — contains status filters (e.g., show only "Pending" orders)
     * @return view — the customer's orders page
     */
    public function orders(Request $request)
    {
        $status = $request->get('status', 'all');
        // Only get orders belonging to the currently logged-in user.
        $query = auth()->user()->orders();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $orders = $query->latest()->get();
        return view('profile.orders', compact('orders', 'status'));
    }

    /**
     * Shows detailed information about a single order.
     * 
     * @param int $id — the ID of the order
     * @return view — the order details page
     */
    public function orderDetails($id)
    {
        // SECURITY: Ensure the order actually belongs to the logged-in user.
        // findOrFail will show a 404 error if someone tries to guess an order ID 
        // that doesn't belong to them.
        $order = Order::where('user_id', auth()->id())->findOrFail($id);
        
        return view('profile.order-details', compact('order'));
    }

    /**
     * Shows the user's saved shipping addresses.
     * 
     * @return view — the addresses management page
     */
    public function addresses()
    {
        $addresses = auth()->user()->addresses;
        return view('profile.addresses', compact('addresses'));
    }

    /**
     * Sets a specific address as the "Default" for checkout.
     * 
     * @param int $id — the ID of the address
     * @return redirect — back with success message
     */
    public function setDefaultAddress($id)
    {
        $user = auth()->user();
        // STEP 1: Set ALL of this user's addresses to "not default".
        $user->addresses()->update(['is_default' => false]);
        // STEP 2: Set the chosen one to "default".
        $user->addresses()->where('id', $id)->update(['is_default' => true]);
        
        return back()->with('status', 'Default address updated!');
    }

    /**
     * Removes an address from the user's profile.
     * 
     * @param int $id — the ID of the address
     * @return redirect — back with success message
     */
    public function deleteAddress($id)
    {
        $user = auth()->user();
        
        // SECURITY: Find the address but only if it belongs to this user.
        $address = $user->addresses()->where('id', $id)->firstOrFail();
        $address->delete();
        
        return back()->with('status', 'Address removed!');
    }

    /**
     * Shows the user's wishlist items.
     * 
     * @return view — the wishlist page
     */
    public function wishlist()
    {
        // Load the wishlist and also the product details for each item.
        $wishlistItems = auth()->user()->wishlists()->with('product')->get();
        return view('profile.wishlist', compact('wishlistItems'));
    }

    /**
     * Adds or Removes a product from the wishlist.
     * 
     * This is used by the "Heart" button on the shop page.
     * 
     * @param int $id — the product ID
     * @return json — status (added or removed)
     */
    public function toggleWishlist($id)
    {
        $user = auth()->user();
        // Check if it is already in the wishlist.
        $wishlist = Wishlist::where('user_id', $user->id)->where('product_id', $id)->first();
        
        if ($wishlist) {
            // If it exists, delete it (remove from wishlist).
            $wishlist->delete();
            return response()->json(['status' => 'removed']);
        } else {
            // If it doesn't exist, create it (add to wishlist).
            Wishlist::create(['user_id' => $user->id, 'product_id' => $id]);
            return response()->json(['status' => 'added']);
        }
    }

    /**
     * Shows the profile settings page.
     */
    public function settings()
    {
        $user = auth()->user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Saves the updated personal information for the customer.
     * 
     * @param Request $request — contains new name, email, and avatar
     * @return redirect — back with success message
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        // VALIDATION: Ensure the updated email is unique but allow the current one.
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle profile picture upload.
        if ($request->hasFile('avatar')) {
            // Delete old photo if it exists.
            if ($user->avatar) {
                $oldPath = str_replace('storage/', '', $user->avatar);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
            // Save new photo.
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = 'storage/' . $path;
        }

        // Update the database.
        $user->update($validated);
        
        return back()->with('status', 'Profile updated successfully!');
    }

    /**
     * Shows the reviews written by the customer and products awaiting review.
     */
    public function reviews(Request $request)
    {
        $status = $request->get('status', 'awaiting');
        $user = auth()->user();

        // Always get delivered orders count for the tab badge
        $deliveredOrders = Order::where('user_id', $user->id)
            ->where('status', 'Delivered')
            ->latest()
            ->get();

        if ($status == 'reviewed') {
            $reviews = $user->reviews()->with('product')->latest()->get();
            return view('profile.reviews', compact('reviews', 'status', 'deliveredOrders'));
        }

        return view('profile.reviews', compact('deliveredOrders', 'status'));
    }
}
