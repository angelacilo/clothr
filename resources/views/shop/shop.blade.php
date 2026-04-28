@extends('layouts.shop')

@section('title', 'All Products')

@section('extra_css')
    .shop-layout { display: grid; grid-template-columns: 240px 1fr; gap: 40px; padding-top: 40px; }
    
    .sidebar-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.05em; }
    .filter-group { margin-bottom: 40px; }
    .filter-list li { margin-bottom: 12px; font-size: 14px; }
    .filter-list a { color: var(--text-secondary); font-weight: 500; }
    .filter-list a:hover, .filter-list a.active { color: #000; font-weight: 700; }
    
    .price-range { margin-top: 20px; }
    .range-slider { width: 100%; height: 4px; background: #eee; position: relative; border-radius: 2px; }
    .range-progress { position: absolute; height: 100%; background: #000; left: 0%; right: 0%; }
    .range-handle { position: absolute; width: 16px; height: 16px; background: #fff; border: 2px solid #000; border-radius: 50%; top: 50%; transform: translate(-50%, -50%); cursor: pointer; }
    .range-values { display: flex; justify-content: space-between; margin-top: 15px; font-size: 13px; font-weight: 500; color: var(--text-secondary); }
    .price-input-group { display: flex; gap: 10px; margin-top: 15px; }
    .price-input { width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 12px; }

    .shop-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .product-count { font-size: 14px; color: var(--text-secondary); }
    .sort-select { padding: 8px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 14px; outline: none; background: #fff; cursor: pointer; }

    @media (max-width: 768px) {
        .shop-layout { grid-template-columns: 1fr; }
        .shop-sidebar { display: none; } /* Could be a drawer on mobile but keeping it simple for now */
    }
@endsection

@section('content')
<div class="container section">
    <h1 style="font-size: 40px; font-weight: 800; margin-bottom: 40px;">{{ request('deals') ? 'Super Deals' : 'All Products' }}</h1>

    <div class="shop-layout">
        <aside class="shop-sidebar">
            <div class="filter-group">
                <h3 class="sidebar-title">Category</h3>
                <ul class="filter-list">
                    <li><a href="{{ route('shop', request()->only(['deals'])) }}" class="{{ !request('category') || request('category') == 'all' ? 'active' : '' }}">All {{ request('deals') ? 'Deals' : 'Products' }}</a></li>
                    @foreach($categories as $cat)
                        <li><a href="{{ route('shop', array_merge(request()->only(['deals']), ['category' => $cat->slug])) }}" class="{{ request('category') == $cat->slug ? 'active' : '' }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="filter-group">
                <h3 class="sidebar-title">Price Range</h3>
                <div class="price-range">
                    <form action="{{ route('shop') }}" method="GET" id="price-filter-form">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div class="price-input-group">
                            <input type="number" name="min_price" id="min_price" class="price-input" value="{{ request('min_price', 0) }}" placeholder="Min ₱">
                            <input type="number" name="max_price" id="max_price" class="price-input" value="{{ request('max_price', 5000) }}" placeholder="Max ₱">
                        </div>
                        <button type="submit" class="btn-sso-black" style="padding: 8px; font-size: 12px; margin-top: 10px;">Apply Filter</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="shop-content">
            <div class="shop-header">
                <div class="product-count">{{ $products->count() }} products</div>
                <select class="sort-select" onchange="window.location.href = '{{ route('shop', array_merge(request()->query(), ['sort' => 'SORT_VALUE'])) }}'.replace('SORT_VALUE', this.value);">
                    <option value="featured" {{ $sort == 'featured' ? 'selected' : '' }}>Featured</option>
                    <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>Price Low–High</option>
                    <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>Price High–Low</option>
                    <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest</option>
                </select>
            </div>

            <div class="products__grid">
                @if($products->count() > 0)
                    @foreach($products as $product)
                        <div class="product-card">
                            @if($product->isNew)
                                <span class="product-badge">New</span>
                            @elseif($product->isOnSale)
                                <span class="product-badge" style="background: #2563eb;">Sale</span>
                            @endif
                            @php $inWishlist = in_array($product->id, $wishlistProductIds ?? []); @endphp
                            <button class="product-card__wishlist {{ $inWishlist ? 'active' : '' }}" onclick="event.preventDefault(); toggleWishlistGlobal({{ $product->id }}, this)">
                                <i data-lucide="heart" size="18" {{ $inWishlist ? 'fill=currentColor' : '' }}></i>
                            </button>
                            <a href="{{ route('product', $product->id) }}">
                                <div class="product-card__img-box">
                                    <img src="{{ $product->images[0] ?? '/placeholder.png' }}" class="product-card__img" alt="{{ $product->name }}">
                                </div>
                            </a>
                            <a href="{{ route('product', $product->id) }}" class="product-card__add">Choose Options</a>
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
                @else
                    <p>No products found matching your filters.</p>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection
