@extends('layouts.shop')

@section('extra_css')
/* ── Hero ── */
.hero {
    position: relative;
    height: 92vh;
    min-height: 620px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: #1c1917;
}
.hero__img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
    opacity: .72;
    transform: scale(1.04);
    animation: heroZoom 12s ease-out forwards;
}
@keyframes heroZoom { to { transform: scale(1); } }
.hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, rgba(28,25,23,.75) 0%, rgba(28,25,23,.3) 65%, transparent 100%);
    z-index: 2;
}
.hero__content {
    position: relative;
    color: #fff;
    max-width: 620px;
    z-index: 3;
    animation: fadeUp .9s .1s ease-out both;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:none; } }
.hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: var(--accent-warm);
    margin-bottom: 22px;
}
.hero__eyebrow::before { content:''; width:28px; height:1.5px; background:var(--accent-warm); }
.hero__title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(44px, 6vw, 72px);
    font-weight: 700;
    line-height: 1.08;
    margin-bottom: 22px;
    letter-spacing: -.01em;
}
.hero__sub { font-size: 17px; color: rgba(255,255,255,.75); margin-bottom: 40px; line-height: 1.65; max-width: 440px; }
.hero__actions { display: flex; gap: 14px; flex-wrap: wrap; }
.hero__btn-primary {
    background: #fff;
    color: var(--ink);
    padding: 15px 32px;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: .06em;
    text-transform: uppercase;
    border-radius: 6px;
    transition: background .25s, color .25s, transform .2s;
    display: inline-block;
}
.hero__btn-primary:hover { background: var(--accent-warm); color: #fff; transform: translateY(-2px); }
.hero__btn-secondary {
    border: 1.5px solid rgba(255,255,255,.45);
    color: rgba(255,255,255,.85);
    padding: 15px 32px;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: .06em;
    text-transform: uppercase;
    border-radius: 6px;
    transition: background .25s, border-color .25s, transform .2s;
    display: inline-block;
}
.hero__btn-secondary:hover { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.7); transform: translateY(-2px); }

/* Scroll hint */
.hero__scroll {
    position: absolute;
    bottom: 36px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,.45);
    font-size: 10px;
    letter-spacing: .15em;
    text-transform: uppercase;
}
.hero__scroll-line {
    width: 1px;
    height: 48px;
    background: linear-gradient(to bottom, transparent, rgba(255,255,255,.4));
    animation: scrollPulse 1.8s infinite;
}
@keyframes scrollPulse { 0%,100%{opacity:.3;transform:scaleY(.85);} 50%{opacity:1;transform:scaleY(1);} }

/* ── Trust Bar ── */
.trust-bar {
    background: var(--sand);
    border-bottom: 1px solid var(--border);
    padding: 18px 0;
}
.trust-bar__inner {
    display: flex;
    justify-content: center;
    gap: 56px;
    flex-wrap: wrap;
}
.trust-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--ink-soft);
}
.trust-item i { color: var(--accent-warm); }

/* ── Category Cards ── */
.categories__grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; }
.category-card {
    background: var(--white);
    border: 1px solid var(--border);
    padding: 32px 16px;
    text-align: center;
    border-radius: 14px;
    transition: all .25s;
    cursor: pointer;
}
.category-card:hover { transform: translateY(-5px); border-color: var(--ink); box-shadow: var(--shadow-md); }
.category-card__icon {
    width: 52px;
    height: 52px;
    background: var(--sand);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px;
    font-size: 22px;
    transition: background .25s;
}
.category-card:hover .category-card__icon { background: var(--ink); color: #fff; }
.category-card h3 { font-size: 13px; font-weight: 700; color: var(--ink); }

/* ── Deals row ── */
.deals-row { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }
.deals-panel {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow-xs);
}
.deals-panel__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}
.deals-panel__title { font-size: 18px; font-weight: 800; color: var(--ink); display: flex; align-items: center; gap: 8px; }
.deals-panel__link { font-size: 12px; font-weight: 700; color: var(--ink-muted); letter-spacing: .04em; }
.deals-panel__link:hover { color: var(--ink); }
.mini-product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.mini-product__img { aspect-ratio: 1/1; border-radius: 10px; overflow: hidden; margin-bottom: 8px; background: var(--sand); }
.mini-product__img img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s; }
.mini-product:hover .mini-product__img img { transform: scale(1.05); }
.mini-product__price { font-weight: 800; font-size: 13px; color: var(--ruby); }
.mini-product__tag  { font-size: 10px; color: var(--ink-faint); font-weight: 600; letter-spacing: .05em; }

/* ── Tab bar ── */
.tab-bar {
    display: flex;
    justify-content: center;
    gap: 0;
    margin-bottom: 48px;
    border-bottom: 1px solid var(--border);
}
.tab-link {
    font-size: 13px;
    font-weight: 600;
    padding: 14px 28px;
    cursor: pointer;
    color: var(--ink-muted);
    border-bottom: 2.5px solid transparent;
    transition: color .2s, border-color .2s;
    letter-spacing: .02em;
    user-select: none;
}
.tab-link.is-active { color: var(--ink); border-bottom-color: var(--ink); font-weight: 700; }

/* ── Newsletter ── */
.newsletter {
    background: var(--ink);
    padding: 80px 0;
    margin-top: 0;
}
.newsletter__inner { text-align: center; }
.newsletter__tag {
    display: inline-block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: var(--accent-warm);
    margin-bottom: 16px;
}
.newsletter h2 {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    font-weight: 600;
    color: #fff;
    margin-bottom: 12px;
}
.newsletter p { color: rgba(255,255,255,.5); font-size: 15px; margin-bottom: 36px; }
.newsletter__form {
    display: flex;
    max-width: 480px;
    margin: 0 auto;
    gap: 0;
    border-radius: 8px;
    overflow: hidden;
    border: 1.5px solid rgba(255,255,255,.18);
}
.newsletter__form input {
    flex: 1;
    padding: 15px 20px;
    background: rgba(255,255,255,.07);
    border: none;
    outline: none;
    color: #fff;
    font-size: 14px;
    font-family: inherit;
}
.newsletter__form input::placeholder { color: rgba(255,255,255,.3); }
.newsletter__form button {
    background: var(--accent-warm);
    color: var(--ink);
    padding: 15px 26px;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: .06em;
    text-transform: uppercase;
    border: none;
    cursor: pointer;
    transition: background .2s;
}
.newsletter__form button:hover { background: #d4b898; }

@media (max-width: 900px) {
    .categories__grid { grid-template-columns: repeat(3, 1fr); }
    .deals-row { grid-template-columns: 1fr; }
}
@media (max-width: 580px) {
    .trust-bar__inner { gap: 24px; }
    .categories__grid { grid-template-columns: repeat(2, 1fr); }
    .hero__title { font-size: 38px; }
}
@endsection

@section('content')
<!-- ── HERO ── -->
<section class="hero">
    <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=2070&auto=format&fit=crop" class="hero__img" alt="Hero">
    <div class="hero__overlay"></div>
    <div class="container">
        <div class="hero__content">
            <span class="hero__eyebrow">New Season 2026</span>
            <h1 class="hero__title">Dress for the<br>Life You Want</h1>
            <p class="hero__sub">Curated collections for the modern woman. Timeless pieces, contemporary silhouettes.</p>
            <div class="hero__actions">
                <a href="{{ route('shop') }}" class="hero__btn-primary">Shop Now</a>
                <a href="{{ route('category', 'new-arrivals') }}" class="hero__btn-secondary">New Arrivals</a>
            </div>
        </div>
    </div>
    <div class="hero__scroll">
        <div class="hero__scroll-line"></div>
        <span>Scroll</span>
    </div>
</section>

<!-- ── TRUST BAR ── -->
<div class="trust-bar">
    <div class="container trust-bar__inner">
        <div class="trust-item"><i data-lucide="truck" size="18"></i> Free Shipping Over ₱2,500</div>
        <div class="trust-item"><i data-lucide="rotate-ccw" size="18"></i> Easy 30-Day Returns</div>
        <div class="trust-item"><i data-lucide="shield-check" size="18"></i> Secure Checkout</div>
        <div class="trust-item"><i data-lucide="headphones" size="18"></i> 24/7 Support</div>
    </div>
</div>

<!-- ── CATEGORIES ── -->
<section class="section container">
    <span class="section-eyebrow">Browse By</span>
    <h2 class="section-title">Shop by Category</h2>
    <div class="categories__grid">
        @php
            $catIcons = ['👗','👚','👖','👜','👟'];
            $i = 0;
        @endphp
        @foreach($categories as $category)
            <a href="{{ route('category', $category->slug) }}" class="category-card" style="text-decoration:none; color:inherit;">
                <div class="category-card__icon">{{ $catIcons[$i++ % count($catIcons)] }}</div>
                <h3>{{ $category->name }}</h3>
            </a>
        @endforeach
    </div>
</section>

<!-- ── DEALS & TRENDS ── -->
<section class="section container" style="padding-top:0;">
    <div class="deals-row">
        <!-- Super Deals -->
        <div class="deals-panel">
            <div class="deals-panel__header">
                <span class="deals-panel__title">
                    <i data-lucide="zap" size="20" style="color:#ef4444;"></i> Super Deals
                </span>
                <a href="{{ route('shop', ['deals' => 1]) }}" class="deals-panel__link">View All →</a>
            </div>
            <div class="mini-product-grid">
                @foreach($superDeals->take(3) as $product)
                    <a href="{{ route('product', $product->id) }}" class="mini-product" style="text-decoration:none; color:inherit;">
                        <div class="mini-product__img">
                            <img src="{{ $product->images[0] ?? '/placeholder.png' }}" alt="{{ $product->name }}">
                        </div>
                        <div class="mini-product__price">₱{{ number_format($product->price, 0) }}</div>
                        <div class="mini-product__tag">FLASH SALE</div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Top Trends -->
        <div class="deals-panel">
            <div class="deals-panel__header">
                <span class="deals-panel__title">
                    <i data-lucide="trending-up" size="20" style="color:#8b5cf6;"></i> Top Trends
                </span>
                <a href="{{ route('shop') }}" class="deals-panel__link">View All →</a>
            </div>
            <div class="mini-product-grid">
                @foreach($topTrends->take(3) as $product)
                    <a href="{{ route('product', $product->id) }}" class="mini-product" style="text-decoration:none; color:inherit;">
                        <div class="mini-product__img">
                            <img src="{{ $product->images[0] ?? '/placeholder.png' }}" alt="{{ $product->name }}">
                        </div>
                        <div class="mini-product__price" style="color:var(--ink);">₱{{ number_format($product->price, 0) }}</div>
                        <div class="mini-product__tag">#TRENDINGNOW</div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- ── FEATURED COLLECTION ── -->
<section class="section container" style="padding-top:20px;">
    <span class="section-eyebrow">Handpicked For You</span>
    <h2 class="section-title">Featured Collection</h2>

    <div class="tab-bar" id="collectionTabs">
        <span class="tab-link is-active" data-target="women">Women</span>
        <span class="tab-link" data-target="men">Men</span>
        <span class="tab-link" data-target="kids">Kids</span>
        <span class="tab-link" data-target="home">Home &amp; Living</span>
    </div>

    <div class="products__grid" id="productGrid">
        @foreach($featured as $product)
            @php
                $catName  = strtolower($product->category->name ?? '');
                $tabTarget = 'women';
                if (str_contains($catName, 'men'))  $tabTarget = 'men';
                if (str_contains($catName, 'women')) $tabTarget = 'women';
                if (str_contains($catName, 'kid') || str_contains($catName, 'boy') || str_contains($catName, 'girl')) $tabTarget = 'kids';
                if (str_contains($catName, 'home') || str_contains($catName, 'jewel') || str_contains($catName, 'electronic')) $tabTarget = 'home';
            @endphp
            <div class="product-card collection-item" data-category="{{ $tabTarget }}">
                @if($product->isNew)
                    <span class="product-badge">New</span>
                @elseif($product->isOnSale)
                    <span class="product-badge" style="background:var(--cobalt);">Sale</span>
                @endif

                <button class="product-card__wishlist" onclick="event.preventDefault(); toggleWishlistGlobal({{ $product->id }}, this)">
                    <i data-lucide="heart" size="17"></i>
                </button>

                <a href="{{ route('product', $product->id) }}">
                    <div class="product-card__img-box">
                        <img src="{{ $product->images[0] ?? '/placeholder.png' }}" class="product-card__img" alt="{{ $product->name }}" loading="lazy">
                        <div class="product-card__overlay">
                            <span class="product-card__add">Choose Options</span>
                        </div>
                    </div>
                </a>

                <h3>{{ $product->name }}</h3>
                <p class="price">
                    @if($product->isOnSale && $product->originalPrice)
                        <span class="sale-price">₱{{ number_format($product->price, 2) }}</span>
                        <span class="old-price">₱{{ number_format($product->originalPrice, 2) }}</span>
                    @else
                        ₱{{ number_format($product->price, 2) }}
                    @endif
                </p>
            </div>
        @endforeach
    </div>
</section>

<!-- ── NEWSLETTER ── -->
<section class="newsletter">
    <div class="container newsletter__inner">
        <span class="newsletter__tag">Stay Connected</span>
        <h2>Join the CLOTHR Circle</h2>
        <p>Get first access to new arrivals, exclusive offers, and style inspiration.</p>
        <form class="newsletter__form" onsubmit="return false;">
            <input type="email" placeholder="Your email address">
            <button type="submit">Subscribe</button>
        </form>
    </div>
</section>
@endsection

@section('extra_js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    const tabs  = document.querySelectorAll('.tab-link');
    const items = document.querySelectorAll('.collection-item');
    const grid  = document.getElementById('productGrid');

    const emptyMsg = document.createElement('div');
    emptyMsg.style.cssText = 'grid-column:1/-1;text-align:center;padding:64px 20px;color:var(--ink-faint);font-size:15px;';
    emptyMsg.innerHTML = '<i data-lucide="inbox" size="36" style="margin-bottom:14px;opacity:.4;display:block;margin-left:auto;margin-right:auto;"></i>No items in this category yet.';
    grid.appendChild(emptyMsg);
    lucide.createIcons();

    const filter = (target) => {
        let count = 0;
        items.forEach(item => {
            const show = item.dataset.category === target;
            item.style.display = show ? '' : 'none';
            if (show) count++;
        });
        emptyMsg.style.display = count === 0 ? 'block' : 'none';
    };

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('is-active'));
            tab.classList.add('is-active');
            filter(tab.dataset.target);
        });
    });

    filter('women');
});
</script>
@endsection
