@extends('layouts.shop')

@section('title', $product->name)

@section('extra_css')
    /* ── Layout ── */
    .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; padding: 40px 0; }
    .product-gallery { display: flex; flex-direction: column; gap: 20px; }

    /* ── Main image ── */
    .product-main-img {
        aspect-ratio: 4/5; background: #f8f9fa; border-radius: 20px; overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,.08); position: relative;
    }
    .product-main-img img {
        width: 100%; height: 100%; object-fit: cover;
        transition: opacity .35s ease, transform .35s ease;
    }
    .product-main-img img.swapping { opacity: 0; transform: scale(1.02); }

    /* ── Thumbnail strip ── */
    .thumb-strip { display: flex; gap: 10px; flex-wrap: wrap; }
    .thumb-item {
        width: 72px; height: 72px; border-radius: 10px; overflow: hidden;
        border: 2px solid transparent; cursor: pointer; transition: .2s;
        background: #f3f4f6;
    }
    .thumb-item:hover  { border-color: #aaa; }
    .thumb-item.active { border-color: #111; box-shadow: 0 0 0 1px #111; }
    .thumb-item img    { width:100%; height:100%; object-fit:cover; }

    /* ── Info panel ── */
    .product-info { padding-top: 10px; }
    .product-info h1 { font-size: 38px; font-weight: 800; margin-bottom: 10px; letter-spacing: -.02em; line-height: 1.1; }
    .product-info .category { color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .15em; margin-bottom: 20px; display: block; }
    .product-info .price { font-size: 28px; font-weight: 800; margin-bottom: 28px; display: flex; align-items: center; gap: 14px; }
    .product-info .description { color: var(--text-secondary); font-size: 15px; margin-bottom: 36px; line-height: 1.75; }

    /* ── Option groups ── */
    .option-group { margin-bottom: 28px; }
    .option-label { display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
    .option-row   { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .option-value { font-size: 13px; font-weight: 600; color: var(--text-muted); }

    /* ── Size buttons ── */
    .size-btns { display: flex; gap: 8px; flex-wrap: wrap; }
    .size-btn {
        min-width: 50px; height: 48px; border: 1.5px solid #e5e7eb; border-radius: 10px;
        font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center;
        transition: .2s; background: #fff; cursor: pointer; padding: 0 12px;
    }
    .size-btn:hover:not(:disabled) { border-color: #111; }
    .size-btn.active { background: #111; color: #fff; border-color: #111; box-shadow: 0 4px 12px rgba(0,0,0,.18); }
    .size-btn:disabled { opacity: .35; cursor: not-allowed; text-decoration: line-through; }

    /* ── Color swatches ── */
    .color-btns { display: flex; gap: 12px; flex-wrap: wrap; }
    .color-btn {
        width: 40px; height: 40px; border-radius: 50%; border: 2.5px solid transparent;
        cursor: pointer; padding: 3px; transition: .2s; background: none;
    }
    .color-btn.active { border-color: #111; transform: scale(1.12); }
    .color-swatch { display: block; width: 100%; height: 100%; border-radius: 50%; border: 1px solid rgba(0,0,0,.12); }

    /* ── Quantity ── */
    .qty-input { display:flex; align-items:center; gap:10px; background:#f3f4f6; width:fit-content; padding:6px; border-radius:12px; }
    .qty-btn   { width:36px; height:36px; border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    .qty-val   { width:40px; text-align:center; font-weight:700; font-size:16px; }

    /* ── Actions ── */
    .action-group { display:flex; flex-direction:column; gap:14px; margin-top:36px; }
    .add-to-cart-btn {
        background:#111; color:#fff; width:100%; padding:20px; font-size:15px; font-weight:800;
        text-transform:uppercase; letter-spacing:.1em; border-radius:14px; transition:.3s;
        box-shadow: 0 8px 20px rgba(0,0,0,.12);
    }
    .add-to-cart-btn:hover { background:#222; transform:translateY(-2px); box-shadow:0 14px 28px rgba(0,0,0,.18); }
    .add-to-cart-btn:disabled { opacity:.4; cursor:not-allowed; transform:none; }
    .wishlist-btn {
        width:100%; padding:16px; border:1.5px solid #e5e7eb; border-radius:14px;
        font-size:14px; font-weight:700; display:flex; align-items:center; justify-content:center;
        gap:10px; color:var(--text-primary); transition:.2s;
    }
    .wishlist-btn:hover { background:#f9fafb; border-color:#111; }
    .wishlist-btn.active { color:#ef4444; border-color:#fecaca; background:#fff1f2; }

    /* ── Stock badge ── */
    .stock-badge {
        display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700;
        padding:4px 10px; border-radius:20px; margin-top:6px;
    }
    .stock-badge.in  { background:#d1fae5; color:#065f46; }
    .stock-badge.low { background:#fef3c7; color:#92400e; }
    .stock-badge.out { background:#fee2e2; color:#991b1b; }

    /* ── Per-item variant rows (qty>1) ── */
    .variant-rows { border:1.5px solid #e5e7eb; border-radius:12px; overflow:hidden; }
    .variant-row  { display:grid; grid-template-columns:40px 1fr 1fr; border-bottom:1px solid #f0f0f0; align-items:center; }
    .variant-row:last-child { border-bottom:none; }
    .variant-row-num { text-align:center; font-size:12px; font-weight:700; color:#9ca3af; padding:10px 6px; border-right:1px solid #f0f0f0; }
    .variant-select  { width:100%; padding:10px 12px; border:none; border-right:1px solid #f0f0f0; outline:none; font-family:inherit; font-size:13px; font-weight:600; background:#fff; cursor:pointer; }
    .variant-select:last-child { border-right:none; }

    /* ── Reviews ── */
    .reviews-section  { margin-top:80px; padding-top:60px; border-top:1px solid var(--border-color); }
    .review-item      { margin-bottom:40px; }
    .review-meta      { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
    .review-stars     { color:#fbbf24; display:flex; gap:2px; }
    .review-content   { font-size:15px; color:var(--text-secondary); line-height:1.65; }

    @media (max-width:768px) {
        .product-detail { grid-template-columns:1fr; gap:40px; }
        .product-info h1 { font-size:28px; }
    }
@endsection

@section('content')
<div class="container section">
    <div class="product-detail">

        {{-- ── GALLERY ── --}}
        <div class="product-gallery">
            <div class="product-main-img">
                <img id="mainProductImg"
                     src="{{ $product->images[0] ?? '/placeholder.png' }}"
                     alt="{{ $product->name }}">
            </div>
            {{-- Thumbnails built by JS from variant images --}}
            <div class="thumb-strip" id="thumbStrip"></div>
        </div>

        {{-- ── INFO ── --}}
        <div class="product-info">
            <span class="category">{{ $product->category->name ?? 'Uncategorized' }}</span>
            <h1>{{ $product->name }}</h1>

            <p class="price">
                @if($product->isOnSale && $product->originalPrice)
                    <span style="color:#2563eb;">₱{{ number_format($product->price,2) }}</span>
                    <span style="color:var(--text-muted); text-decoration:line-through; font-size:18px; font-weight:400;">
                        ₱{{ number_format($product->originalPrice,2) }}
                    </span>
                @else
                    ₱{{ number_format($product->price,2) }}
                @endif
            </p>

            <p class="description">{{ $product->description }}</p>

            {{-- ── COLOR VARIANTS ── --}}
            @if(!empty($product->variants) || !empty($product->colors))
            <div class="option-group" id="colorGroup">
                <div class="option-row">
                    <span class="option-label">Color</span>
                    <span class="option-value" id="selectedColorLabel">—</span>
                </div>
                <div class="color-btns" id="colorBtnsContainer">
                    {{-- Rendered by JS --}}
                </div>
            </div>
            @endif

            {{-- ── SIZE VARIANTS ── --}}
            @if(!empty($product->sizes))
            <div class="option-group" id="sizeGroup">
                <div class="option-row">
                    <span class="option-label">Size</span>
                    <span class="option-value" id="selectedSizeLabel">Select a size</span>
                </div>
                <div class="size-btns" id="sizeBtnsContainer">
                    {{-- Rendered by JS --}}
                </div>
                <div id="stockBadgeContainer" style="margin-top:8px;"></div>
            </div>
            @endif

            {{-- ── QUANTITY ── --}}
            <div class="option-group">
                <span class="option-label" style="margin-bottom:12px; display:block;">Quantity</span>
                <div class="qty-input">
                    <button class="qty-btn" onclick="updateQty(-1)">−</button>
                    <span class="qty-val" id="qty">1</span>
                    <button class="qty-btn" onclick="updateQty(1)">+</button>
                </div>
            </div>

            {{-- Per-item variant rows (qty > 1) --}}
            <div id="variantRowsContainer" class="option-group" style="display:none;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span class="option-label">Variant Per Item</span>
                    <span style="font-size:12px; color:var(--text-muted);">Pick size &amp; color per unit</span>
                </div>
                <div class="variant-rows" id="variantRows"></div>
            </div>

            {{-- ── ACTIONS ── --}}
            <div class="action-group">
                <button class="add-to-cart-btn" id="addToCartBtn" onclick="handleAddToCart()">Add to Bag</button>
                <button class="wishlist-btn" onclick="toggleWishlist({{ $product->id }}, this)">
                    <i data-lucide="heart" size="20"></i> Add to Wishlist
                </button>
            </div>
        </div>
    </div>

    {{-- ── REVIEWS ── --}}
    <div class="reviews-section">
        <h2 class="section-title">Customer Reviews</h2>
        @forelse($product->reviews ?? [] as $review)
            <div class="review-item">
                <div class="review-meta">
                    <div class="review-stars">
                        @for($s=1;$s<=5;$s++)
                            <i data-lucide="star" {{ $s<=$review->rating ? 'fill="currentColor"' : '' }} size="16"></i>
                        @endfor
                    </div>
                    <span style="font-weight:700; font-size:14px;">{{ $review->user->name ?? 'Customer' }}</span>
                    <span style="color:var(--text-muted); font-size:13px;">Verified Buyer</span>
                </div>
                <p class="review-content">{{ $review->comment }}</p>
            </div>
        @empty
            {{-- Placeholder reviews --}}
            <div class="review-item">
                <div class="review-meta">
                    <div class="review-stars">
                        <i data-lucide="star" fill="currentColor" size="16"></i>
                        <i data-lucide="star" fill="currentColor" size="16"></i>
                        <i data-lucide="star" fill="currentColor" size="16"></i>
                        <i data-lucide="star" fill="currentColor" size="16"></i>
                        <i data-lucide="star" fill="currentColor" size="16"></i>
                    </div>
                    <span style="font-weight:700; font-size:14px;">Sarah M.</span>
                    <span style="color:var(--text-muted); font-size:13px;">Verified Buyer</span>
                </div>
                <p class="review-content">Absolutely love this! The fabric is high quality and the fit is perfect. Received so many compliments.</p>
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
                    <span style="font-weight:700; font-size:14px;">Maria L.</span>
                    <span style="color:var(--text-muted); font-size:13px;">Verified Buyer</span>
                </div>
                <p class="review-content">Beautiful design and colors. Runs slightly small—size up if you're between sizes.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('extra_js')
<script>
/* ══════════════════════════════════════════════════════════
   PRODUCT DATA from PHP
══════════════════════════════════════════════════════════ */
const VARIANTS     = {!! json_encode($product->variants ?? []) !!};   // structured variant array
const FLAT_SIZES   = {!! json_encode($product->sizes   ?? []) !!};    // legacy fallback
const FLAT_COLORS  = {!! json_encode($product->colors  ?? []) !!};    // legacy fallback
const DEFAULT_IMG  = '{{ $product->images[0] ?? "/placeholder.png" }}';

/* ══════════════════════════════════════════════════════════
   STATE
══════════════════════════════════════════════════════════ */
let selectedColor     = null;   // color name string
let selectedColorHex  = null;
let selectedColorImg  = null;
let selectedSize      = null;
let quantity          = 1;

/* ══════════════════════════════════════════════════════════
   COLOR MAP (fallback for legacy string-only colors)
══════════════════════════════════════════════════════════ */
const COLOR_MAP = {
    'white':'#ffffff','black':'#1a1a1a','red':'#e53e3e','blue':'#3182ce','navy':'#1a365d',
    'pink':'#f687b3','green':'#38a169','yellow':'#ecc94b','orange':'#ed8936','purple':'#805ad5',
    'grey':'#a0aec0','gray':'#a0aec0','brown':'#8b4513','beige':'#f5f0e8','cream':'#fffdd0',
    'champagne':'#f7e7ce','camel':'#c19a6b','silver':'#c0c0c0','gold':'#ffd700','maroon':'#800000',
    'teal':'#319795','coral':'#ff6b6b','lavender':'#967bb6','mint':'#98ff98','olive':'#808000',
    'tan':'#d2b48c','ivory':'#fffff0','khaki':'#c3b091','rose':'#ff007f','salmon':'#fa8072',
    'lilac':'#c8a2c8','turquoise':'#40e0d0','cyan':'#00bcd4','magenta':'#e91e63','indigo':'#3949ab',
    'violet':'#8e24aa','charcoal':'#36454f','rust':'#b7410e','mustard':'#e1ad01',
};
function nameToHex(name) {
    return name ? (COLOR_MAP[name.toLowerCase().trim()] || '#cccccc') : '#cccccc';
}

/* ══════════════════════════════════════════════════════════
   BUILD NORMALISED VARIANT LIST
   Works whether data is structured (VARIANTS) or legacy flat
══════════════════════════════════════════════════════════ */
function buildNormalisedVariants() {
    if (VARIANTS && VARIANTS.length > 0) {
        return VARIANTS.map(function(v) {
            return {
                color:    v.color    || '',
                colorHex: v.colorHex || nameToHex(v.color),
                image:    v.image    || null,
                sizes:    v.sizes    || {},   // {S:10, M:5, ...}
            };
        });
    }
    // Legacy: flat color names, no per-color images / no structured sizes
    return FLAT_COLORS.map(function(c) {
        return { color:c, colorHex:nameToHex(c), image:null, sizes:{} };
    });
}
const NORM_VARIANTS = buildNormalisedVariants();

/* ══════════════════════════════════════════════════════════
   INIT — render thumbnails + swatches
══════════════════════════════════════════════════════════ */
(function init() {
    renderThumbs();
    renderColorSwatches();
    // Auto-select first color
    if (NORM_VARIANTS.length > 0) {
        activateColor(0);
    } else if (FLAT_SIZES.length > 0) {
        // No colors, but sizes exist — render sizes
        renderSizeButtons(FLAT_SIZES, {});
    }
})();

/* ── Thumbnails ── */
function renderThumbs() {
    var strip = document.getElementById('thumbStrip');
    if (!strip) return;
    strip.innerHTML = '';
    var thumbImages = [];

    // Collect unique images: default first, then per-variant
    if (DEFAULT_IMG && DEFAULT_IMG !== '/placeholder.png') thumbImages.push({src:DEFAULT_IMG, label:'Main'});
    NORM_VARIANTS.forEach(function(v) {
        if (v.image && v.image !== DEFAULT_IMG && v.image !== '/placeholder.png') {
            thumbImages.push({src:v.image, label:v.color});
        }
    });

    if (thumbImages.length <= 1) return; // no strip needed

    thumbImages.forEach(function(t, i) {
        var div = document.createElement('div');
        div.className = 'thumb-item' + (i===0 ? ' active':'');
        div.title     = t.label;
        div.innerHTML = '<img src="'+t.src+'" alt="'+t.label+'">';
        div.onclick   = function() {
            document.querySelectorAll('.thumb-item').forEach(function(d){d.classList.remove('active');});
            div.classList.add('active');
            changeMainImage(t.src);
        };
        strip.appendChild(div);
    });
}

/* ── Color swatches ── */
function renderColorSwatches() {
    var container = document.getElementById('colorBtnsContainer');
    if (!container) return;
    container.innerHTML = '';
    NORM_VARIANTS.forEach(function(v, idx) {
        var btn = document.createElement('button');
        btn.className        = 'color-btn';
        btn.title            = v.color;
        btn.dataset.idx      = idx;
        btn.innerHTML        = '<span class="color-swatch" style="background:'+v.colorHex+';"></span>';
        btn.onclick          = function() { activateColor(idx); };
        container.appendChild(btn);
    });
}

/* ══════════════════════════════════════════════════════════
   ACTIVATE COLOR
══════════════════════════════════════════════════════════ */
function activateColor(idx) {
    var v = NORM_VARIANTS[idx];
    if (!v) return;

    selectedColor    = v.color;
    selectedColorHex = v.colorHex;
    selectedColorImg = v.image;
    selectedSize     = null; // reset size on color change

    // Update label
    var lbl = document.getElementById('selectedColorLabel');
    if (lbl) lbl.textContent = v.color;

    // Update active swatch
    document.querySelectorAll('.color-btn').forEach(function(b) {
        b.classList.toggle('active', +b.dataset.idx === idx);
    });

    // Swap main image
    if (v.image) {
        changeMainImage(v.image);
        // Sync thumb strip
        document.querySelectorAll('.thumb-item').forEach(function(d) {
            d.classList.toggle('active', d.querySelector('img') && d.querySelector('img').src === new URL(v.image, location.href).href);
        });
    }

    // Render size buttons for this color
    var sizeGroup = document.getElementById('sizeGroup');
    if (sizeGroup) {
        renderSizeButtons(Object.keys(v.sizes).length > 0 ? Object.keys(v.sizes) : FLAT_SIZES, v.sizes);
    }

    // Clear size label
    var szLbl = document.getElementById('selectedSizeLabel');
    if (szLbl) szLbl.textContent = 'Select a size';
    var stockBadge = document.getElementById('stockBadgeContainer');
    if (stockBadge) stockBadge.innerHTML = '';

    updateCartButtonState();
}

/* ══════════════════════════════════════════════════════════
   SIZE BUTTONS
══════════════════════════════════════════════════════════ */
function renderSizeButtons(sizeList, stockMap) {
    var container = document.getElementById('sizeBtnsContainer');
    if (!container) return;
    container.innerHTML = '';

    sizeList.forEach(function(sz) {
        var stock = stockMap[sz] != null ? stockMap[sz] : 999;
        var btn   = document.createElement('button');
        btn.className        = 'size-btn';
        btn.textContent      = sz;
        btn.disabled         = stock <= 0;
        btn.dataset.size     = sz;
        btn.dataset.stock    = stock;
        btn.onclick          = function() { selectSize(btn, sz, stock); };
        container.appendChild(btn);
    });
}

function selectSize(btn, size, stock) {
    if (btn.disabled) return;
    document.querySelectorAll('.size-btn').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    selectedSize = size;

    var lbl = document.getElementById('selectedSizeLabel');
    if (lbl) lbl.textContent = size;

    // Stock badge
    var badge = document.getElementById('stockBadgeContainer');
    if (badge) {
        if (stock > 10)      badge.innerHTML = '<span class="stock-badge in">● In Stock</span>';
        else if (stock > 0)  badge.innerHTML = '<span class="stock-badge low">⚠ Only '+ stock +' left</span>';
        else                 badge.innerHTML = '<span class="stock-badge out">✕ Out of Stock</span>';
    }

    updateCartButtonState();
}

/* ══════════════════════════════════════════════════════════
   CART BUTTON STATE
══════════════════════════════════════════════════════════ */
function updateCartButtonState() {
    var btn   = document.getElementById('addToCartBtn');
    if (!btn) return;
    var hasSizes   = (document.getElementById('sizeGroup') !== null);
    var hasColors  = NORM_VARIANTS.length > 0;
    var sizeOk     = !hasSizes  || selectedSize  !== null;
    var colorOk    = !hasColors || selectedColor !== null;
    btn.disabled   = !(sizeOk && colorOk);
}

/* ══════════════════════════════════════════════════════════
   QUANTITY
══════════════════════════════════════════════════════════ */
function updateQty(delta) {
    quantity = Math.max(1, quantity + delta);
    document.getElementById('qty').textContent = quantity;
    renderVariantRows();
}

/* ══════════════════════════════════════════════════════════
   PER-ITEM VARIANT ROWS (qty > 1)
══════════════════════════════════════════════════════════ */
function renderVariantRows() {
    var container = document.getElementById('variantRowsContainer');
    var rowsEl    = document.getElementById('variantRows');
    var hasSizes  = (document.getElementById('sizeGroup') !== null) && FLAT_SIZES.length > 0;
    var hasColors = NORM_VARIANTS.length > 0;

    if (quantity <= 1 || (!hasSizes && !hasColors)) {
        container.style.display = 'none';
        return;
    }
    container.style.display = 'block';
    rowsEl.innerHTML = '';

    var sizeOpts  = hasSizes  ? FLAT_SIZES.map(function(s){return '<option>'+s+'</option>';}).join('') : '<option value="">—</option>';
    var colorOpts = hasColors ? NORM_VARIANTS.map(function(v){return '<option>'+v.color+'</option>';}).join('') : '<option value="">—</option>';

    for (var i = 1; i <= quantity; i++) {
        var row = document.createElement('div');
        row.className = 'variant-row';
        row.innerHTML =
            '<div class="variant-row-num">'+ i +'</div>'
            + (hasSizes  ? '<select class="variant-select" data-type="size">'  + sizeOpts  + '</select>' : '')
            + (hasColors ? '<select class="variant-select" data-type="color">' + colorOpts + '</select>' : '');
        rowsEl.appendChild(row);
    }
}

/* ══════════════════════════════════════════════════════════
   IMAGE SWAP (smooth transition)
══════════════════════════════════════════════════════════ */
function changeMainImage(src) {
    var img = document.getElementById('mainProductImg');
    img.classList.add('swapping');
    setTimeout(function() {
        img.src = src;
        img.classList.remove('swapping');
    }, 180);
}

/* ══════════════════════════════════════════════════════════
   ADD TO CART
══════════════════════════════════════════════════════════ */
function handleAddToCart() {
    var baseProduct = {
        id:    {{ $product->id }},
        name:  '{{ addslashes($product->name) }}',
        price: {{ $product->price }},
        image: selectedColorImg || '{{ addslashes($product->images[0] ?? "") }}',
    };

    var combos = [];
    var hasSizes  = (document.getElementById('sizeGroup') !== null);
    var hasColors = NORM_VARIANTS.length > 0;

    if (quantity > 1 && (hasSizes || hasColors)) {
        document.querySelectorAll('#variantRows .variant-row').forEach(function(row) {
            var sEl  = row.querySelector('[data-type="size"]');
            var cEl  = row.querySelector('[data-type="color"]');
            combos.push({ size: sEl ? sEl.value : '', color: cEl ? cEl.value : '' });
        });
    } else {
        combos.push({ size: selectedSize || '', color: selectedColor || '' });
    }

    combos.forEach(function(combo) {
        var existing = cart.find(function(item) {
            return item.id === baseProduct.id && item.size === combo.size && item.color === combo.color;
        });
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push(Object.assign({}, baseProduct, { size:combo.size, color:combo.color, quantity:1, is_selected:true }));
        }
    });

    localStorage.setItem('clothr_cart', JSON.stringify(cart));
    updateCartCount();
    showToast('{{ addslashes($product->name) }} added to cart!');

    if (isLoggedIn) {
        combos.forEach(function(combo) {
            fetch('/api/cart/update', {
                method:'POST',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
                body: JSON.stringify(Object.assign({}, baseProduct, { size:combo.size, color:combo.color, quantity:1 }))
            });
        });
    }
}

/* ══════════════════════════════════════════════════════════
   WISHLIST
══════════════════════════════════════════════════════════ */
function toggleWishlist(id, btn) {
    var isActive = btn.classList.toggle('active');
    btn.innerHTML = isActive
        ? '<i data-lucide="heart" size="20" fill="currentColor"></i> Added to Wishlist'
        : '<i data-lucide="heart" size="20"></i> Add to Wishlist';
    lucide.createIcons();
    showToast(isActive ? 'Added to wishlist' : 'Removed from wishlist', 'info');
}
</script>
@endsection
