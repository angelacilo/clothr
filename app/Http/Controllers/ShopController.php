<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;


class ShopController extends Controller
{
    public function index()
    {
        $featured = Product::where('isFeatured', true)->where('isArchived', false)->take(12)->get();
        $superDeals = Product::where('isOnSale', true)->where('isArchived', false)->latest()->take(8)->get();
        $topTrends = Product::where('isTrending', true)->where('isArchived', false)->orderBy('sales_count', 'desc')->take(8)->get();
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.index', compact('featured', 'superDeals', 'topTrends', 'categories'));
    }

    public function shop(Request $request)
    {
        $query = Product::where('isArchived', false);

        if ($request->has('deals')) {
            $query->where('isOnSale', true);
        }

        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price < 5000) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'featured');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
            default:
                $query->where('isFeatured', true);
                break;
        }

        $products = $query->get();
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.shop', compact('products', 'categories', 'sort'));
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $query = Product::where('category_id', $category->id)->where('isArchived', false);

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price < 5000) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'all');
        switch ($sort) {
            case 'price_low': $query->orderBy('price', 'asc'); break;
            case 'price_high': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'featured': $query->where('isFeatured', true); break;
            default: break;
        }

        $products = $query->get();
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.category', compact('category', 'products', 'categories', 'sort'));
    }

    public function product($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('shop.product', compact('product'));
    }

    public function cart()
    {
        $recommendations = Product::where('isArchived', false)->inRandomOrder()->take(5)->get();
        return view('shop.cart', compact('recommendations'));
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
            'total' => 'required|numeric',
        ]);

        $customer_info = $validated['customer_info'];
        
        if (isset($customer_info['address_id'])) {
            $address = \App\Models\Address::findOrFail($customer_info['address_id']);
            $customer_info = [
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'email' => auth()->user()->email,
                'phone' => $address->phone,
                'address_line_1' => $address->address_line_1,
                'city' => $address->city,
                'zip_code' => $address->zip_code,
                'country' => $address->country,
            ];
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'customer_info' => $customer_info,
            'items' => $validated['items'],
            'total' => $validated['total'],
            'status' => 'Pending'
        ]);

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    public function confirmation($id)
    {
        $order = Order::findOrFail($id);
        return view('shop.confirmation', compact('order'));
    }

    public function info($slug)
    {
        $contents = [
            'contact' => [
                'title' => 'Contact Us',
                'body' => '
                    <p>We’re here to help! If you have any questions or concerns regarding your orders, feel free to contact us.</p>
                    <div class="contact-card">
                        <h3>Customer Support Hours:</h3>
                        <p>Monday – Friday: 9:00 AM – 6:00 PM</p>
                        <h3>Email:</h3>
                        <p><a href="mailto:clothr.co@gmail.com" style="color: #000; font-weight: 600;">clothr.co@gmail.com</a></p>
                        <h3>Phone:</h3>
                        <p><a href="tel:+639664226382" style="color: #000; font-weight: 600;">+63 966 422 6382</a></p>
                    </div>
                    <p>You may also send us a message through the contact form and our team will respond within 24–48 hours.</p>
                '
            ],
            'shipping' => [
                'title' => 'Shipping Info',
                'body' => '
                    <p>We aim to deliver your orders safely and on time.</p>
                    <h3>Processing Time</h3>
                    <p>Orders are processed within 1–2 business days after confirmation.</p>
                    <h3>Shipping Time</h3>
                    <ul>
                        <li><strong>Within Butuan City:</strong> 1–2 days</li>
                        <li><strong>Within Mindanao:</strong> 3–5 days</li>
                        <li><strong>Other areas in the Philippines:</strong> 5–10 days</li>
                    </ul>
                    <h3>Shipping Method</h3>
                    <p>We deliver orders through trusted local courier services.</p>
                    <h3>Payment Method</h3>
                    <p>All orders are paid through Cash on Delivery (COD). This means you will pay for your order only when it arrives at your doorstep.</p>
                '
            ],
            'returns' => [
                'title' => 'Returns',
                'body' => '
                    <p>Your satisfaction is important to us. If there is a problem with your order, you may request a return or replacement.</p>
                    <h3>Return Eligibility</h3>
                    <p>Items can be returned if:</p>
                    <ul>
                        <li>The item received is damaged or defective</li>
                        <li>The wrong item was delivered</li>
                        <li>The request is made within 7 days after delivery</li>
                    </ul>
                    <h3>Return Conditions</h3>
                    <ul>
                        <li>Item must be unused</li>
                        <li>Item must be in original packaging</li>
                        <li>Proof of purchase or order number is required</li>
                    </ul>
                    <p>To request a return, please contact our customer support team and provide the details of your order.</p>
                '
            ],
            'faq' => [
                'title' => 'FAQ',
                'body' => '
                    <div class="faq-item">
                        <div class="faq-q">Q: What payment methods do you accept?</div>
                        <div class="faq-a">A: We currently accept Cash on Delivery (COD) only.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-q">Q: How does Cash on Delivery work?</div>
                        <div class="faq-a">A: You place your order online, and you will pay in cash when the product is delivered to your address.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-q">Q: Can I cancel my order?</div>
                        <div class="faq-a">A: Orders may be canceled before they are shipped.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-q">Q: How long does delivery take?</div>
                        <div class="faq-a">A: Delivery time depends on your location, usually 1–10 business days.</div>
                    </div>
                '
            ],
            'about' => [
                'title' => 'About Us',
                'body' => '
                    <p>Our online store aims to provide customers with quality products and a convenient shopping experience.</p>
                    <p>We focus on simple and reliable transactions by offering Cash on Delivery (COD) so customers can pay only when their order arrives. Our goal is to make online shopping easy, accessible, and trustworthy for everyone.</p>
                '
            ],
            'privacy' => [
                'title' => 'Privacy Policy',
                'body' => '
                    <p>We respect your privacy and are committed to protecting your personal information.</p>
                    <h3>Information We Collect</h3>
                    <ul>
                        <li>Name</li>
                        <li>Contact number</li>
                        <li>Delivery address</li>
                        <li>Email address</li>
                    </ul>
                    <h3>How We Use Your Information</h3>
                    <p>Your information is used to:</p>
                    <ul>
                        <li>Process and deliver your orders</li>
                        <li>Contact you regarding your order</li>
                        <li>Improve our services</li>
                    </ul>
                    <p>Your personal information will be kept confidential and will not be shared with third parties except when required for order delivery.</p>
                '
            ],
            'terms' => [
                'title' => 'Terms of Service',
                'body' => '
                    <p>By using our website, you agree to the following terms and conditions.</p>
                    <h3>Orders</h3>
                    <p>All orders placed through the website are subject to product availability and confirmation.</p>
                    <h3>Payment</h3>
                    <p>We currently accept Cash on Delivery (COD) as the only payment method. Customers must pay the exact amount upon delivery.</p>
                    <h3>Delivery</h3>
                    <p>Customers must provide accurate delivery information to avoid delays.</p>
                    <h3>Changes to Terms</h3>
                    <p>We reserve the right to update these terms at any time without prior notice.</p>
                '
            ]
        ];

        if (!isset($contents[$slug])) {
            abort(404);
        }

        $page = (object) $contents[$slug];
        return view('shop.info', compact('page'));
    }
}
