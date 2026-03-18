<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\Order;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function checkout()
    {
        $addresses = auth()->user()->addresses ?? [];
        return view('shop.checkout', compact('addresses'));
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_info' => 'required|array',
            'items' => 'required|array',
            // Notice: 'total' is removed/ignored here to enforce DB-only pricing
        ]);

        $customer_info = $validated['customer_info'];
        
        if (!empty($customer_info['address_id'])) {
            // RULE 3: Secure address load
            $address = \App\Models\Address::where('id', $customer_info['address_id'])
                                        ->where('user_id', auth()->id())
                                        ->firstOrFail();
                                        
            $customer_info = [
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'email' => auth()->user()->email,
                'phone' => $address->phone,
                'address_line_1' => $address->address_line_1,
                'city' => $address->city,
                'zip_code' => $address->zip_code,
                'country' => $address->country,
                'region' => $address->region ?? '',
            ];
        }

        try {
            $order = $this->orderService->placeOrder(auth()->id(), $validated['items'], $customer_info);
            return response()->json(['success' => true, 'order_id' => $order->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function confirmation($id)
    {
        // Must belong to user
        $order = Order::where('user_id', auth()->id())->findOrFail($id);
        return view('shop.confirmation', compact('order'));
    }
}
