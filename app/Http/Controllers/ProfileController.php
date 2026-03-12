<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Address;
use App\Models\Wishlist;
use App\Models\Product;

class ProfileController extends Controller
{
    public function index()
    {
        return redirect()->route('profile.orders');
    }

    public function orders(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = auth()->user()->orders();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $orders = $query->latest()->get();
        return view('profile.orders', compact('orders', 'status'));
    }

    public function addresses()
    {
        $addresses = auth()->user()->addresses;
        return view('profile.addresses', compact('addresses'));
    }

    public function setDefaultAddress($id)
    {
        $user = auth()->user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->where('id', $id)->update(['is_default' => true]);
        
        return back()->with('status', 'Default address updated!');
    }

    public function deleteAddress($id)
    {
        $user = auth()->user();
        $user->addresses()->where('id', $id)->delete();
        
        return back()->with('status', 'Address removed!');
    }

    public function wishlist()
    {
        $wishlistItems = auth()->user()->wishlists()->with('product')->get();
        return view('profile.wishlist', compact('wishlistItems'));
    }

    public function settings()
    {
        $user = auth()->user();
        return view('profile.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);
        return back()->with('status', 'Profile updated successfully!');
    }

    public function toggleWishlist($id)
    {
        $user = auth()->user();
        $wishlist = Wishlist::where('user_id', $user->id)->where('product_id', $id)->first();
        
        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $id]);
            return response()->json(['status' => 'added']);
        }
    }
}
