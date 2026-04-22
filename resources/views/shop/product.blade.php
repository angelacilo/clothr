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
    .review-item      { margin-bottom:40px; padding-bottom:30px; border-bottom:1px solid #f1f5f9; }
    .review-meta      { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
    .review-stars     { color:#fbbf24; display:flex; gap:2px; }
    .review-content   { font-size:15px; color:var(--text-secondary); line-height:1.65; }
    .review-summary { display: flex; gap: 40px; margin-bottom: 40px; align-items: center; flex-wrap: wrap; }
    .review-avg { text-align: center; min-width: 140px; }
    .review-avg-big { font-size: 48px; font-weight: 800; line-height: 1; color: #111; margin-bottom: 8px; }
    .review-bars { flex: 1; display: flex; flex-direction: column; gap: 8px; min-width: 200px; }
    .review-bar-row { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #64748b; }
    .review-bar-bg { flex: 1; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
    .review-bar-fill { height: 100%; background: #fbbf24; border-radius: 4px; transition: width 0.5s ease; }
    .star-btn { cursor: pointer; color: #cbd5e1; transition: 0.2s; background: none; border: none; padding: 0; outline: none; }
    .star-btn.active { color: #fbbf24; }
    .review-form-card { background: #f8f9fa; border-radius: 12px; padding: 30px; margin-bottom: 40px; border: 1px solid #e2e8f0; }
    .review-edit-actions { display: flex; gap: 12px; margin-top: 14px; }
    .review-edit-actions button { font-size: 13px; font-weight: 700; cursor: pointer; background: none; border: none; color: #64748b; padding: 0; }
    .review-edit-actions button:hover { color: #111; text-decoration: underline; }

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
            <div class="action-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 36px;">
                <button class="add-to-cart-btn" id="addToCartBtn" onclick="handleAddToCart()" style="grid-column: span 2; padding: 18px;">Add to Bag</button>
                <button class="add-to-cart-btn" id="buyNowBtn" onclick="handleBuyNow()" style="grid-column: span 2; padding: 18px; background: #2563eb; box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);">Buy Now</button>
                <button class="wishlist-btn" onclick="toggleWishlist({{ $product->id }}, this)" style="grid-column: span 2;">
                    <i data-lucide="heart" size="20"></i> Add to Wishlist
                </button>
            </div>

            @if($product->description)
            <div class="product-description-section" style="margin-top: 36px; padding-top: 28px; border-top: 1px solid var(--border-color);">
                <span style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px;">Description</span>
                <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.75;">{!! nl2br(e($product->description)) !!}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── REVIEWS ── --}}
    <div class="reviews-section">
        <h2 class="section-title">Customer Reviews</h2>

        <div class="review-summary">
            <div class="review-avg">
                <div class="review-avg-big">{{ number_format($avgRating, 1) }}</div>
                <div class="review-stars" style="justify-content: center; margin-bottom: 6px;">
                    @for($s=1; $s<=5; $s++)
                        <i data-lucide="star" {{ $s<=$avgRating ? 'fill="currentColor"' : '' }} size="18" style="color: {{ $s<=$avgRating ? '#fbbf24' : '#e2e8f0' }}"></i>
                    @endfor
                </div>
                <div style="font-size: 13px; color: #64748b; font-weight: 600;">{{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}</div>
            </div>
            <div class="review-bars">
                @for($i = 5; $i >= 1; $i--)
                    @php 
                        $pct = $totalReviews > 0 ? ($ratingCounts[$i] / $totalReviews) * 100 : 0; 
                    @endphp
                    <div class="review-bar-row">
                        <span style="width: 48px; text-align: right;">{{ $i }} stars</span>
                        <div class="review-bar-bg">
                            <div class="review-bar-fill" style="width: {{ $pct }}%"></div>
                        </div>
                        <span style="width: 32px; text-align: left;">{{ round($pct) }}%</span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Review Form Area --}}
        @auth
            @if($userReview)
                {{-- User already reviewed --}}
                <div class="review-form-card" id="userReviewCard">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Your Review</h3>
                    <div class="review-meta">
                        <div class="review-stars">
                            @for($s=1; $s<=5; $s++)
                                <i data-lucide="star" {{ $s<=$userReview->rating ? 'fill="currentColor"' : '' }} size="16"></i>
                            @endfor
                        </div>
                        <span style="color:var(--text-muted); font-size:13px;">{{ $userReview->updated_at->format('F j, Y') }}</span>
                    </div>
                    <p class="review-content">{{ $userReview->comment }}</p>
                    <div class="review-edit-actions">
                        <button onclick="editReview()">Edit Review</button>
                        <button onclick="deleteReview('{{ $userReview->id }}')" style="color: #ef4444;">Delete</button>
                    </div>
                </div>
                
                {{-- Hidden edit form --}}
                <div class="review-form-card" id="editReviewFormCard" style="display: none;">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Edit Your Review</h3>
                    <form id="editReviewForm" onsubmit="submitEditReview(event, '{{ $userReview->id }}')">
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Your Rating <span style="color: red;">*</span></label>
                            <div id="editStarRating" style="display: flex; gap: 4px;">
                                @for($i=1; $i<=5; $i++)
                                    <button type="button" class="star-btn {{ $i <= $userReview->rating ? 'active' : '' }}" data-val="{{ $i }}" onmouseover="hoverStars(this, 'editStarRating')" onmouseout="resetStars('editStarRating')" onclick="selectStar(this, 'editStarRating')">
                                        <i data-lucide="star" fill="currentColor" size="24"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" id="editRatingValue" value="{{ $userReview->rating }}" required>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Your Comment (optional)</label>
                            <textarea id="editReviewComment" maxlength="1000" rows="4" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-family: inherit; resize: vertical;" onkeyup="document.getElementById('editCharCounter').innerText = this.value.length">{{ $userReview->comment }}</textarea>
                            <div style="font-size: 12px; color: #94a3b8; text-align: right; margin-top: 4px;"><span id="editCharCounter">{{ strlen($userReview->comment) }}</span> / 1000</div>
                        </div>
                        <div style="display: flex; gap: 12px;">
                            <button type="submit" class="btn btn-dark" style="padding: 10px 24px;" id="submitEditBtn">Update Review</button>
                            <button type="button" class="btn btn-outline" style="padding: 10px 24px;" onclick="cancelEditReview()">Cancel</button>
                        </div>
                    </form>
                </div>
            @elseif($canReview)
                {{-- User can review --}}
                <div class="review-form-card">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Write a Review</h3>
                    <form id="newReviewForm" onsubmit="submitNewReview(event)">
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Your Rating <span style="color: red;">*</span></label>
                            <div id="newStarRating" style="display: flex; gap: 4px;">
                                @for($i=1; $i<=5; $i++)
                                    <button type="button" class="star-btn" data-val="{{ $i }}" onmouseover="hoverStars(this, 'newStarRating')" onmouseout="resetStars('newStarRating')" onclick="selectStar(this, 'newStarRating')">
                                        <i data-lucide="star" fill="currentColor" size="24"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" id="newRatingValue" value="" required>
                            <div id="ratingError" style="color: red; font-size: 12px; display: none; margin-top: 4px;">Please select a rating.</div>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Your Comment (optional)</label>
                            <textarea id="newReviewComment" maxlength="1000" placeholder="Share your thoughts about this product..." rows="4" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-family: inherit; resize: vertical;" onkeyup="document.getElementById('newCharCounter').innerText = this.value.length"></textarea>
                            <div style="font-size: 12px; color: #94a3b8; text-align: right; margin-top: 4px;"><span id="newCharCounter">0</span> / 1000</div>
                        </div>
                        <button type="submit" class="btn btn-dark" style="padding: 12px 24px;" id="submitNewBtn">Submit Review</button>
                    </form>
                </div>
            @else
                {{-- User has not purchased --}}
                <div class="review-form-card" style="text-align: center; color: var(--text-medium);">
                    <i data-lucide="shopping-bag" style="margin-bottom: 12px; color: #94a3b8;"></i>
                    <p style="margin: 0; font-size: 14px; font-weight: 600;">Purchase this product to leave a review</p>
                </div>
            @endif
        @else
            {{-- Guest --}}
            <div class="review-form-card" style="text-align: center;">
                <p style="margin-bottom: 16px; font-size: 14px; color: var(--text-medium); font-weight: 600;">Please sign in to leave a review</p>
                <button class="btn btn-outline" onclick="document.getElementById('openLoginModal').click()">Sign In</button>
            </div>
        @endauth

        <div id="reviewsList">
            {{-- Rendered via JS AJAX --}}
        </div>
        
        <div id="reviewsPagination" style="margin-top: 30px; display: flex; justify-content: center; gap: 10px;">
            {{-- Rendered via JS AJAX --}}
        </div>
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
    window.maxStock = {{ $product->stock }};
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

    // Limit quantity
    window.maxStock = stock;
    if (quantity > window.maxStock) {
        quantity = window.maxStock > 0 ? window.maxStock : 1;
        document.getElementById('qty').textContent = quantity;
        renderVariantRows();
    }

    // Stock badge
    var badge = document.getElementById('stockBadgeContainer');
    if (badge) {
        if (stock > 5)       badge.innerHTML = '<span class="stock-badge in">● In Stock</span>';
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
    var buyBtn = document.getElementById('buyNowBtn');
    if (!btn) return;
    var hasSizes   = (document.getElementById('sizeGroup') !== null);
    var hasColors  = NORM_VARIANTS.length > 0;
    var sizeOk     = !hasSizes  || selectedSize  !== null;
    var colorOk    = !hasColors || selectedColor !== null;
    var disabled   = !(sizeOk && colorOk);
    
    btn.disabled   = disabled;
    if (buyBtn) buyBtn.disabled = disabled;
}

/* ══════════════════════════════════════════════════════════
   QUANTITY
══════════════════════════════════════════════════════════ */
function updateQty(delta) {
    var max = window.maxStock !== undefined ? window.maxStock : 999;
    quantity = Math.max(1, Math.min(max, quantity + delta));
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

function handleBuyNow() {
    handleAddToCart();
    // Redirect to checkout immediately
    window.location.href = '/checkout';
}

/* ══════════════════════════════════════════════════════════
   WISHLIST
══════════════════════════════════════════════════════════ */
function toggleWishlist(id, btn) {
    var isActive = btn.classList.toggle('active');
    btn.innerHTML = isActive
        ? '<i data-lucide="heart" size="20" fill="currentColor"></i> Added to Wishlist'
        : '<i data-lucide="heart" size="20"></i> Add to Wishlist';
    showToast(isActive ? 'Added to wishlist' : 'Removed from wishlist', 'info');
}

/* ══════════════════════════════════════════════════════════
   REVIEWS LOGIC
══════════════════════════════════════════════════════════ */
// Star Rating Interaction
function hoverStars(btn, containerId) {
    let val = parseInt(btn.dataset.val);
    let btns = document.querySelectorAll(`#${containerId} .star-btn`);
    btns.forEach(b => {
        if (parseInt(b.dataset.val) <= val) {
            b.style.color = '#fbbf24';
        } else {
            b.style.color = '#cbd5e1';
        }
    });
}
function resetStars(containerId) {
    let inputId = containerId === 'newStarRating' ? 'newRatingValue' : 'editRatingValue';
    let val = parseInt(document.getElementById(inputId).value) || 0;
    let btns = document.querySelectorAll(`#${containerId} .star-btn`);
    btns.forEach(b => {
        if (parseInt(b.dataset.val) <= val) {
            b.style.color = '#fbbf24';
            b.classList.add('active');
        } else {
            b.style.color = '#cbd5e1';
            b.classList.remove('active');
        }
    });
}
function selectStar(btn, containerId) {
    let inputId = containerId === 'newStarRating' ? 'newRatingValue' : 'editRatingValue';
    document.getElementById(inputId).value = btn.dataset.val;
    if (inputId === 'newRatingValue') document.getElementById('ratingError').style.display = 'none';
    resetStars(containerId);
}

// Fetch Reviews
let reviewsPage = 1;
function fetchReviews(page = 1) {
    fetch(`/product/{{ $product->id }}/reviews?page=${page}`)
        .then(res => res.json())
        .then(data => {
            renderReviews(data.data);
            renderPagination(data);
        });
}

function renderReviews(reviews) {
    const list = document.getElementById('reviewsList');
    if (!reviews || reviews.length === 0) {
        if (reviewsPage === 1) {
            list.innerHTML = '<div style="padding: 40px; text-align: center; color: #94a3b8;">No reviews yet.</div>';
        }
        return;
    }
    
    list.innerHTML = reviews.map(r => {
        let starsHtml = '';
        for(let s=1; s<=5; s++) {
            starsHtml += `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="${s <= r.rating ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`;
        }
        
        // Hide edit/delete for their own review context if it's already in the top form, safely escaping comment
        const isOwnBadge = r.is_own ? '<span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; margin-left: 8px;">Your Review</span>' : '';

        // Safely escape comment text to prevent XSS
        const safeComment = document.createElement('div');
        safeComment.textContent = r.comment || '';
        const commentHtml = safeComment.innerHTML;
        
        return `
            <div class="review-item">
                <div class="review-meta">
                    <div class="review-stars">${starsHtml}</div>
                    <span style="font-weight:700; font-size:14px; color: #111;">${r.reviewer_name} ${isOwnBadge}</span>
                    <span style="color:var(--text-muted); font-size:13px; display: inline-flex; align-items: center; gap: 4px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Verified Buyer</span>
                    <span style="color:var(--text-light); font-size:12px; margin-left: auto;">${r.created_at}</span>
                </div>
                <p class="review-content">${commentHtml}</p>
            </div>
        `;
    }).join('');
}

function renderPagination(data) {
    const p = document.getElementById('reviewsPagination');
    if (data.last_page <= 1) { p.innerHTML = ''; return; }
    
    let html = '';
    if (data.current_page > 1) {
        html += `<button class="btn btn-outline" style="padding: 6px 14px; font-size: 13px;" onclick="fetchReviews(${data.current_page - 1})">Prev</button>`;
    }
    html += `<span style="font-size: 13px; font-weight: 600; padding: 8px 12px; color: #64748b;">Page ${data.current_page} of ${data.last_page}</span>`;
    if (data.current_page < data.last_page) {
        html += `<button class="btn btn-outline" style="padding: 6px 14px; font-size: 13px;" onclick="fetchReviews(${data.current_page + 1})">Next</button>`;
    }
    p.innerHTML = html;
}

// Submission Logic
function submitNewReview(e) {
    e.preventDefault();
    let val = document.getElementById('newRatingValue').value;
    if (!val) { document.getElementById('ratingError').style.display = 'block'; return; }
    
    let btn = document.getElementById('submitNewBtn');
    btn.disabled = true;
    btn.innerText = 'Submitting...';
    
    let payload = {
        rating: val,
        comment: document.getElementById('newReviewComment').value
    };
    
    fetch(`/product/{{ $product->id }}/review`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json().then(data => ({ status: r.status, body: data })))
    .then(res => {
        if (res.status === 200) {
            showToast(res.body.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(res.body.error || 'Failed to submit review', 'error');
            btn.disabled = false;
            btn.innerText = 'Submit Review';
        }
    });
}

function editReview() {
    document.getElementById('userReviewCard').style.display = 'none';
    document.getElementById('editReviewFormCard').style.display = 'block';
}

function cancelEditReview() {
    document.getElementById('userReviewCard').style.display = 'block';
    document.getElementById('editReviewFormCard').style.display = 'none';
    resetStars('editStarRating'); // return to existing rating on cancel
}

function submitEditReview(e, id) {
    e.preventDefault();
    let val = document.getElementById('editRatingValue').value;
    
    let btn = document.getElementById('submitEditBtn');
    btn.disabled = true;
    btn.innerText = 'Updating...';
    
    fetch(`/review/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            rating: val,
            comment: document.getElementById('editReviewComment').value
        })
    })
    .then(r => r.json().then(data => ({ status: r.status, body: data })))
    .then(res => {
        if (res.status === 200) {
            showToast(res.body.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(res.body.error || 'Failed to update review', 'error');
            btn.disabled = false;
            btn.innerText = 'Update Review';
        }
    });
}

function deleteReview(id) {
    if (!confirm('Are you sure you want to delete your review?')) return;
    
    fetch(`/review/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json().then(data => ({ status: r.status, body: data })))
    .then(res => {
        if (res.status === 200) {
            showToast(res.body.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast('Failed to delete review', 'error');
        }
    });
}

// Initial fetch
document.addEventListener('DOMContentLoaded', () => {
    fetchReviews(1);
});
</script>
@endsection
