@extends('layouts.shop')

@section('title', $product->name)

@section('extra_css')
    .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; padding: 40px 0; }
    .product-gallery { display: flex; flex-direction: column; gap: 24px; }
    .product-main-img { aspect-ratio: 4/5; background: #f8f9fa; border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm); }
    .product-main-img img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
    
    .product-info { padding-top: 10px; }
    .product-info h1 { font-size: 40px; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.02em; }
    .product-info .category { color: var(--text-muted); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 24px; display: block; }
    .product-info .price { font-size: 28px; font-weight: 800; margin-bottom: 32px; display: flex; align-items: center; gap: 15px; }
    .product-info .description { color: var(--text-secondary); font-size: 16px; margin-bottom: 40px; line-height: 1.7; }
    
    .option-group { margin-bottom: 32px; }
    .option-label { display: block; font-size: 14px; font-weight: 700; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.05em; }
    
    .size-btns { display: flex; gap: 10px; flex-wrap: wrap; }
    .size-btn { min-width: 50px; height: 50px; border: 1.5px solid var(--border-color); border-radius: 12px; font-size: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; transition: 0.2s; background: #fff; }
    .size-btn:hover { border-color: #000; }
    .size-btn.active { background: #000; color: #fff; border-color: #000; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    .color-btns { display: flex; gap: 16px; }
    .color-btn { width: 44px; height: 44px; border-radius: 50%; border: 2px solid transparent; cursor: pointer; padding: 3px; transition: 0.2s; }
    .color-btn.active { border-color: #000; transform: scale(1.1); }
    .color-swatch { display: block; width: 100%; height: 100%; border-radius: 50%; }

    .qty-input { display: flex; align-items: center; gap: 10px; background: #f3f4f6; width: fit-content; padding: 6px; border-radius: 12px; }
    .qty-btn { width: 36px; height: 36px; border-radius: 8px; background: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; box-shadow: var(--shadow-sm); }
    .qty-val { width: 40px; text-align: center; font-weight: 700; font-size: 16px; }

    .action-group { display: flex; flex-direction: column; gap: 16px; margin-top: 40px; }
    .add-to-cart-btn { background: #000; color: #fff; width: 100%; padding: 22px; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; border-radius: 14px; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .add-to-cart-btn:hover { background: #222; transform: translateY(-2px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
    
    .wishlist-btn { width: 100%; padding: 18px; border: 1.5px solid var(--border-color); border-radius: 14px; font-size: 15px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 10px; color: var(--text-primary); transition: 0.2s; }
    .wishlist-btn:hover { background: #f9fafb; border-color: #000; }
    .wishlist-btn.active { color: #ef4444; border-color: #fecaca; background: #fff1f2; }

    .reviews-section { margin-top: 80px; padding-top: 60px; border-top: 1px solid var(--border-color); }
    .review-item { margin-bottom: 40px; }
    .review-meta { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
    .review-stars { color: #fbbf24; display: flex; gap: 2px; }
    .review-content { font-size: 15px; color: var(--text-secondary); line-height: 1.6; }

    .variant-rows { margin-top: 20px; border: 1.5px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    .variant-row { display: grid; grid-template-columns: 44px 1fr 1fr; gap: 0; border-bottom: 1px solid var(--border-color); align-items: center; }
    .variant-row:last-child { border-bottom: none; }
    .variant-row-num { text-align: center; font-size: 12px; font-weight: 700; color: var(--text-muted); padding: 12px 8px; border-right: 1px solid var(--border-color); }
    .variant-select { width: 100%; padding: 12px 14px; border: none; border-right: 1px solid var(--border-color); outline: none; font-family: inherit; font-size: 13px; font-weight: 600; background: #fff; cursor: pointer; appearance: none; -webkit-appearance: none; }
    .variant-select:last-child { border-right: none; }
    .variant-select:focus { background: #f9fafb; }

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
                        <div style="aspect-ratio: 1/1; background: #eee; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: 0.2s;" onclick="changeMainImage('{{ $img }}'); this.parentElement.querySelectorAll('div').forEach(d => d.style.borderColor='transparent'); this.style.borderColor='#000';">
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

            @if(!empty($product->sizes))
                <div class="option-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <span class="option-label" style="margin-bottom: 0;">Size</span>
                        <span id="selected-size-label" style="font-size: 13px; font-weight: 600; color: var(--text-muted);">{{ $product->sizes[0] }}</span>
                    </div>
                    <div class="size-btns">
                        @foreach($product->sizes as $size)
                            <button class="size-btn {{ $loop->first ? 'active' : '' }}" onclick="selectSize(this, '{{ $size }}')">{{ $size }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($product->colors))
                <div class="option-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <span class="option-label" style="margin-bottom: 0;">Color</span>
                        <span id="selected-color-label" style="font-size: 13px; font-weight: 600; color: var(--text-muted);">{{ $product->colors[0] }}</span>
                    </div>
                    <div class="color-btns" id="color-btns-container">
                        @foreach($product->colors as $color)
                            <button class="color-btn {{ $loop->first ? 'active' : '' }}"
                                    onclick="selectColor(this, '{{ $color }}')"
                                    title="{{ $color }}"
                                    data-color-name="{{ $color }}">
                                <span class="color-swatch"></span>
                            </button>
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

            {{-- Per-quantity variant rows (shown when qty > 1) --}}
            <div id="variant-rows-container" class="option-group" style="display:none;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span class="option-label" style="margin-bottom:0;">Variant Per Item</span>
                    <span style="font-size:12px; color:var(--text-muted);">Pick a size & color for each unit</span>
                </div>
                <div class="variant-rows" id="variant-rows"></div>
            </div>

            <div class="action-group">
                <button class="add-to-cart-btn" onclick="handleAddToCart()">Add to Bag</button>
                <button class="wishlist-btn" onclick="toggleWishlist({{ $product->id }}, this)">
                    <i data-lucide="heart" size="20"></i> Add to Wishlist
                </button>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section">
        <h2 class="section-title">Customer Reviews</h2>
        
        <div class="review-item">
            <div class="review-meta">
                <div class="review-stars">
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                </div>
                <span style="font-weight: 700; font-size: 14px;">Sarah M.</span>
                <span style="color: var(--text-muted); font-size: 13px;">Verified Buyer</span>
            </div>
            <p class="review-content">Absolutely love this dress! The fabric is high quality and the fit is perfect. Received so many compliments when I wore it last weekend.</p>
        </div>

        <div class="review-item">
            <div class="review-meta">
                <div class="review-stars">
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                    <i data-lucide="star" fill="currentColor" size="16"></i>
                    <i data-lucide="star" size="16"></i>
                </div>
                <span style="font-weight: 700; font-size: 14px;">Maria L.</span>
                <span style="color: var(--text-muted); font-size: 13px;">Verified Buyer</span>
            </div>
            <p class="review-content">Beautiful design and colors. Only thing is it runs slightly small around the waist, so maybe size up if you're between sizes.</p>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    let selectedSize = '{{ !empty($product->sizes) ? $product->sizes[0] : "" }}';
    let selectedColor = '{{ !empty($product->colors) ? $product->colors[0] : "" }}';
    let quantity = 1;

    // Map common color names (as typed by admin) to CSS hex values
    const COLOR_MAP = {
        'white':     '#ffffff', 'black':     '#1a1a1a', 'red':       '#e53e3e',
        'blue':      '#3182ce', 'navy':      '#1a365d', 'pink':      '#f687b3',
        'green':     '#38a169', 'yellow':    '#ecc94b', 'orange':    '#ed8936',
        'purple':    '#805ad5', 'grey':      '#a0aec0', 'gray':      '#a0aec0',
        'brown':     '#8b4513', 'beige':     '#f5f0e8', 'cream':     '#fffdd0',
        'champagne': '#f7e7ce', 'camel':     '#c19a6b', 'silver':    '#c0c0c0',
        'gold':      '#ffd700', 'maroon':    '#800000', 'teal':      '#319795',
        'coral':     '#ff6b6b', 'lavender':  '#967bb6', 'mint':      '#98ff98',
        'olive':     '#808000', 'tan':       '#d2b48c', 'ivory':     '#fffff0',
        'khaki':     '#c3b091', 'rose':      '#ff007f', 'salmon':    '#fa8072',
        'lilac':     '#c8a2c8', 'turquoise': '#40e0d0', 'cyan':      '#00bcd4',
        'magenta':   '#e91e63', 'indigo':    '#3949ab', 'violet':    '#8e24aa',
        'charcoal':  '#36454f', 'rust':      '#b7410e', 'mustard':   '#e1ad01',
    };

    function nameToHex(name) {
        const key = name.toLowerCase().trim();
        return COLOR_MAP[key] || null;
    }

    // Paint each color swatch immediately (script runs at bottom of body, DOM is ready)
    (function paintSwatches() {
        document.querySelectorAll('.color-btn[data-color-name]').forEach(function(btn) {
            const name   = btn.getAttribute('data-color-name');
            const hex    = nameToHex(name);
            const swatch = btn.querySelector('.color-swatch');
            if (hex) {
                swatch.style.background = hex;
                swatch.style.border     = '1px solid rgba(0,0,0,0.12)';
            } else {
                // Fallback: show a text-label pill button for unknown color names
                swatch.style.display    = 'none';
                btn.style.width         = 'auto';
                btn.style.height        = '36px';
                btn.style.padding       = '0 12px';
                btn.style.border        = '1.5px solid #ccc';
                btn.style.borderRadius  = '20px';
                btn.style.fontSize      = '12px';
                btn.style.fontWeight    = '700';
                btn.textContent         = name;
            }
        });
    })();

    function changeMainImage(src) {
        document.querySelector('.product-main-img img').src = src;
    }

    function selectSize(btn, size) {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedSize = size;
        document.getElementById('selected-size-label').innerText = size;
    }

    function selectColor(btn, color) {
        document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedColor = color;
        const label = document.getElementById('selected-color-label');
        if (label) label.innerText = color;
    }



    // PHP variant data available in JS
    const PRODUCT_SIZES  = {!! json_encode($product->sizes  ?? []) !!};
    const PRODUCT_COLORS = {!! json_encode($product->colors ?? []) !!};
    const PRODUCT_HAS_SIZES  = PRODUCT_SIZES.length  > 0;
    const PRODUCT_HAS_COLORS = PRODUCT_COLORS.length > 0;

    function updateQty(delta) {
        quantity = Math.max(1, quantity + delta);
        document.getElementById('qty').innerText = quantity;
        renderVariantRows();
    }

    function renderVariantRows() {
        const container = document.getElementById('variant-rows-container');
        const rowsEl    = document.getElementById('variant-rows');

        // Hide table for qty=1 — single selectors handle that
        if (quantity <= 1 || (!PRODUCT_HAS_SIZES && !PRODUCT_HAS_COLORS)) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        rowsEl.innerHTML = '';

        // Build size options HTML
        const sizeOpts = PRODUCT_HAS_SIZES
            ? PRODUCT_SIZES.map(s => `<option value="${s}">${s}</option>`).join('')
            : '<option value="">—</option>';

        // Build color options HTML (with hex swatch next to name where possible)
        const colorOpts = PRODUCT_HAS_COLORS
            ? PRODUCT_COLORS.map(c => `<option value="${c}">${c}</option>`).join('')
            : '<option value="">—</option>';

        for (let i = 1; i <= quantity; i++) {
            const row = document.createElement('div');
            row.className = 'variant-row';
            row.innerHTML = `
                <div class="variant-row-num">${i}</div>
                ${PRODUCT_HAS_SIZES  ? `<select class="variant-select" data-row="${i}" data-type="size">${sizeOpts}</select>`  : ''}
                ${PRODUCT_HAS_COLORS ? `<select class="variant-select" data-row="${i}" data-type="color">${colorOpts}</select>` : ''}
            `;
            rowsEl.appendChild(row);
        }
    }

    function handleAddToCart() {
        const baseProduct = {
            id:    {{ $product->id }},
            name:  '{{ addslashes($product->name) }}',
            price: {{ $product->price }},
            image: '{{ $product->images[0] ?? "" }}',
        };

        let combos = [];

        if (quantity > 1 && (PRODUCT_HAS_SIZES || PRODUCT_HAS_COLORS)) {
            // Collect per-row variants from the dynamic table
            const rows = document.querySelectorAll('#variant-rows .variant-row');
            rows.forEach(function(row) {
                const sizeEl  = row.querySelector('[data-type="size"]');
                const colorEl = row.querySelector('[data-type="color"]');
                combos.push({
                    size:  sizeEl  ? sizeEl.value  : '',
                    color: colorEl ? colorEl.value : '',
                });
            });
        } else {
            // Single unit — use the main selectors
            combos.push({ size: selectedSize, color: selectedColor });
        }

        combos.forEach(function(combo) {
            const existing = cart.find(item =>
                item.id    === baseProduct.id &&
                item.size  === combo.size     &&
                item.color === combo.color
            );
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push(Object.assign({}, baseProduct, { size: combo.size, color: combo.color, quantity: 1, is_selected: true }));
            }
        });

        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        updateCartCount();
        showToast('{{ addslashes($product->name) }} added to cart!');

        if (isLoggedIn) {
            combos.forEach(function(combo) {
                fetch('/api/cart/update', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify(Object.assign({}, baseProduct, { size: combo.size, color: combo.color, quantity: 1 }))
                });
            });
        }
    }

    function toggleWishlist(id, btn) {
        const isActive = btn.classList.toggle('active');
        if (isActive) {
            showToast('Added to wishlist', 'info');
            btn.innerHTML = `<i data-lucide="heart" size="20" fill="currentColor"></i> Added to Wishlist`;
        } else {
            showToast('Removed from wishlist');
            btn.innerHTML = `<i data-lucide="heart" size="20"></i> Add to Wishlist`;
        }
        lucide.createIcons();
    }
</script>
@endsection
