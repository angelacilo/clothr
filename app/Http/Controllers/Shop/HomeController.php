<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::where('isArchived', false)->orderBy('isFeatured', 'desc')->latest()->take(12)->get();
        $superDeals = Product::where('isOnSale', true)->where('isArchived', false)->latest()->take(8)->get();
        $topTrends = Product::where('isTrending', true)->where('isArchived', false)->orderBy('sales_count', 'desc')->take(8)->get();
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.index', compact('featured', 'superDeals', 'topTrends', 'categories'));
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
                '
            ],
            'shipping' => [
                'title' => 'Shipping Info',
                'body' => '
                    <p>We aim to deliver your orders safely and on time.</p>
                    <h3>Shipping Time</h3>
                    <ul>
                        <li><strong>Within Butuan City:</strong> 1–2 days</li>
                        <li><strong>Other areas:</strong> 3–10 days</li>
                    </ul>
                '
            ],
            'returns'   => ['title' => 'Returns', 'body' => '<p>Request a return within 7 days after delivery if damaged or wrong item.</p>'],
            'faq'       => ['title' => 'FAQ', 'body' => '<div class="faq-item"><div class="faq-q">Q: What payment methods do you accept?</div><div class="faq-a">A: We accept Cash on Delivery (COD) only.</div></div>'],
            'about'     => ['title' => 'About Us', 'body' => '<p>Our online store aims to provide customers with quality products.</p>'],
            'privacy'   => ['title' => 'Privacy Policy', 'body' => '<p>We respect your privacy and are committed to protecting your personal information.</p>'],
            'terms'     => ['title' => 'Terms of Service', 'body' => '<p>By using our website, you agree to our terms.</p>']
        ];

        if (!isset($contents[$slug])) {
            abort(404);
        }

        $page = (object) $contents[$slug];
        return view('shop.info', compact('page'));
    }
}
