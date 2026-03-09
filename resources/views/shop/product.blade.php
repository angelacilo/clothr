@extends('layouts.shop')

@section('title', $product->name)

@section('extra_css')
    .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; padding-top: 40px; }
    .product-gallery { display: grid; gap: 20px; }
    .product-main-img { aspect-ratio: 1/1; background: #f4f4f4; border-radius: var(--radius-md); overflow: hidden; }
    .product-main-img img { width: 100%; height: 100%; object-fit: cover; }
    
    .product-info h1 { font-size: 36px; font-weight: 800; margin-bottom: 10px; }
    .product-info .category { color: var(--text-muted); font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 20px; display: block; }
    .product-info .price { font-size: 24px; font-weight: 700; margin-bottom: 30px; }
    .product-info .description { color: var(--text-secondary); font-size: 16px; margin-bottom: 40px; line-height: 1.6; }
    
    .option-group { margin-bottom: 30px; }
    .option-label { font-size: 14px; font-weight: 700; margin-bottom: 15px; display: block; }
    .size-btns { display: flex; gap: 12px; }
    .size-btn { padding: 12px 20px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; cursor: pointer; }
    .size-btn.active { background: #000; color: #fff; border-color: #000; }
    
    .qty-input { display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: var(--radius-sm); width: fit-content; margin-bottom: 40px; }
    .qty-btn { padding: 12px 20px; font-size: 18px; font-weight: 600; }
    .qty-val { padding: 0 10px; min-width: 40px; text-align: center; font-weight: 700; }
    
    .add-to-cart-btn { background: #000; color: #fff; width: 100%; padding: 20px; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; border-radius: var(--radius-sm); }
    .add-to-cart-btn:hover { background: var(--accent-hover); }

    @media (max-width: 768px) {
        .product-detail { grid-template-columns: 1fr; }
    }
@endsection

@section('content')
<div class="container section">
    <div class="product-detail">
        <div class="product-gallery">
            <div class="product-main-img">
                <img src="{{ $product->images[0] ?? '/placeholder.png' }}" alt="{{ $product->name }}">
            </div>
            @if(count($product->images ?? []) > 1)
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                    @foreach($product->images as $img)
                        <div style="aspect-ratio: 1/1; background: #eee; border-radius: 4px; overflow: hidden; cursor: pointer;">
                            <img src="{{ $img }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="product-info">
            <span class="category">{{ $product->category->name ?? 'Uncategorized' }}</span>
            <h1>{{ $product->name }}</h1>
            <p class="price">
                @if($product->isOnSale && $product->originalPrice)
                    <span style="color: #2563eb;">₱{{ number_format($product->price, 2) }}</span>
                    <span style="color: var(--text-muted); text-decoration: line-through; margin-left:15px; font-size: 18px; font-weight: 400;">₱{{ number_format($product->originalPrice, 2) }}</span>
                @else
                    ₱{{ number_format($product->price, 2) }}
                @endif
            </p>
            
            <p class="description">{{ $product->description }}</p>

            @if($product->sizes)
                <div class="option-group">
                    <span class="option-label">Select Size</span>
                    <div class="size-btns">
                        @foreach($product->sizes as $size)
                            <button class="size-btn {{ $loop->first ? 'active' : '' }}" onclick="selectSize(this)">{{ $size }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="option-group">
                <span class="option-label">Quantity</span>
                <div class="qty-input">
                    <button class="qty-btn" onclick="updateQty(-1)">−</button>
                    <span class="qty-val" id="qty">1</span>
                    <button class="qty-btn" onclick="updateQty(1)">+</button>
                </div>
            </div>

            <button class="add-to-cart-btn" onclick="handleAddToCart()">Add to Bag</button>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    let selectedSize = '{{ $product->sizes[0] ?? "M" }}';
    let quantity = 1;

    function selectSize(btn) {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedSize = btn.innerText;
    }

    function updateQty(delta) {
        quantity = Math.max(1, quantity + delta);
        document.getElementById('qty').innerText = quantity;
    }

    function handleAddToCart() {
        const product = {
            id: {{ $product->id }},
            name: '{{ $product->name }}',
            price: {{ $product->price }},
            image: '{{ $product->images[0] ?? "" }}',
            size: selectedSize,
            quantity: quantity
        };
        
        // Add multiple times based on quantity
        for(let i=0; i < quantity; i++) {
             // In a real app we'd just add one item with a quantity property, 
             // but our addToCartGlobal currently adds 1 each time.
             // Let's just adjust our internal cart logic here.
        }
        
        // Fix: Let's use a better addToCart that takes quantity
        const existing = cart.find(item => item.id === product.id && item.size === product.size);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push(product);
        }
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        updateCartCount();
        alert('{{ $product->name }} added to cart!');
    }
</script>
@endsection
