<?php

/**
 * FILE: Shop/HomeController.php
 * 
 * What this file does:
 * This controller handles the public-facing pages that every customer sees.
 * It manages the Homepage (with featured products and trending items) 
 * and the information pages (like Contact Us, Shipping, and FAQ).
 * 
 * How it connects to the project:
 * - It is called by the main routes in routes/web.php.
 * - It uses the Product and Category models to show the latest store items.
 * - The views it returns are in resources/views/shop/.
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Shows the public Homepage of the store.
     * 
     * This function gathers different groups of products (Featured, On Sale, Trending)
     * to showcase them on the main page.
     * 
     * @return view — the shop index page
     */
    public function index()
    {
        // 1. Get products marked as "Featured" (the best items).
        $featured = Product::where('isArchived', false)
            ->orderBy('isFeatured', 'desc')
            ->latest()
            ->take(12)
            ->get();
            
        // 2. Get products that are currently "On Sale".
        $superDeals = Product::where('isOnSale', true)
            ->where('isArchived', false)
            ->latest()
            ->take(8)
            ->get();
            
        // 3. Get products marked as "Trending" and sort by how many have been sold.
        $topTrends = Product::where('isTrending', true)
            ->where('isArchived', false)
            ->orderBy('sales_count', 'desc')
            ->take(8)
            ->get();
            
        // 4. Get all visible categories for the navigation menu.
        $categories = Category::where('isVisible', true)->get();
        
        // 5. Get the current user's wishlist product IDs (for the heart icons)
        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlistProductIds = auth()->user()->wishlists()->pluck('product_id')->toArray();
        }
        
        // Send all these groups of products to the homepage view.
        return view('shop.index', compact('featured', 'superDeals', 'topTrends', 'categories', 'wishlistProductIds'));
    }

    /**
     * Shows static information pages (About Us, Contact, FAQ, etc.).
     * 
     * @param string $slug — the name of the page to show (e.g., "contact")
     * @return view — the info page with specific content
     */
    public function info($slug)
    {
        // This is a list of all the information content for the website.
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

        // If the admin/user tries to visit a page that doesn't exist, show a 404 error.
        if (!isset($contents[$slug])) {
            abort(404);
        }

        // Wrap the content into an object for easier use in the blade file.
        $page = (object) $contents[$slug];
        
        return view('shop.info', compact('page'));
    }
}
