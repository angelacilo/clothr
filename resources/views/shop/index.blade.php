@extends('layouts.shop')

@section('extra_css')
    .hero { position: relative; height: 85vh; min-height: 600px; display: flex; align-items: center; overflow: hidden; background: #f0f0f0; }
    .hero__img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; }
    .hero__overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.1); z-index: 2; }
    .hero__content { position: relative; color: #fff; max-width: 600px; z-index: 3; animation: fadeInUp 0.8s ease-out; }
    .hero__title { font-size: 64px; font-weight: 800; line-height: 1.1; margin-bottom: 20px; text-shadow: 0 4px 10px rgba(0,0,0,0.2); }
    .hero__btn { background: #fff; color: #000; padding: 16px 32px; font-weight: 600; font-size: 15px; display: inline-block; transition: 0.3s; }
    .hero__btn:hover { background: #000; color: #fff; transform: translateY(-3px); box-shadow: var(--shadow-md); }

    .categories__grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
    .category-card { background: var(--bg-secondary); border: 1px solid var(--border-color); padding: 40px 20px; text-align: center; border-radius: var(--radius-md); transition: 0.3s; }
    .category-card:hover { transform: translateY(-5px); border-color: #000; box-shadow: var(--shadow-md); }
    .category-card h3 { font-size: 15px; font-weight: 600; margin-top: 10px; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
@endsection

@section('content')
    <section class="hero">
        <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=2070&auto=format&fit=crop" class="hero__img" alt="Hero Image">
        <div class="hero__overlay"></div>
        <div class="container">
            <div class="hero__content">
                <h1 class="hero__title">New Season Styles</h1>
                <p style="font-size: 18px; margin-bottom: 30px;">Discover the latest trends in fashion. Shop our curated collection.</p>
                <a href="{{ route('shop') }}" class="hero__btn">Shop Now →</a>
            </div>
        </div>
    </section>

    <section class="section container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories__grid">
            @foreach($categories as $category)
                <a href="{{ route('category', $category->slug) }}" class="category-card">
                    <h3>{{ $category->name }}</h3>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <h2 style="font-size: 32px; font-weight: 700;">Featured Items</h2>
            <a href="{{ route('shop') }}" style="font-weight: 600; text-decoration: underline;">View All</a>
        </div>
        <div class="products__grid">
            @foreach($featured as $product)
                <div class="product-card">
                    @if($product->isNew)
                        <span class="product-badge">New</span>
                    @elseif($product->isOnSale)
                        <span class="product-badge" style="background: #2563eb;">Sale</span>
                    @endif
                    <a href="{{ route('product', $product->id) }}">
                        <div class="product-card__img-box">
                            <img src="{{ $product->images[0] ?? '/placeholder.png' }}" class="product-card__img" alt="{{ $product->name }}">
                        </div>
                    </a>
                    <button class="product-card__add" onclick="addToCartGlobal({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->images[0] ?? '' }}')">Add to Cart</button>
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

    <section class="section" style="background: #f8f9fa;">
        <div class="container" style="text-align: center;">
            <h2 class="section-title">Stay in the Loop</h2>
            <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto 30px;">Get the latest updates on new arrivals, exclusive offers, and fashion tips.</p>
            <form style="display: flex; max-width: 500px; margin: 0 auto; gap: 10px;">
                <input type="email" placeholder="Enter your email" style="flex: 1; padding: 14px 20px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
                <button type="submit" style="background: #000; color: #fff; padding: 14px 28px; border-radius: var(--radius-sm); font-weight: 600;">Subscribe</button>
            </form>
        </div>
    </section>
@endsection
