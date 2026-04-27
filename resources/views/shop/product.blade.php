{{-- 
    FILE: product.blade.php
    WHAT IT DOES: The "Showcase" page for a single product. 
    WHY: This is where customers pick their color, size, and quantity before buying.
    HOW IT WORKS:
    1. Dynamic Swatches: Click a color, and the sizes list updates automatically.
    2. Smart Cart: Aggregates items correctly before sending to DB.
    3. Proper Styling: Uses the correct theme variables (--ink, --border) so buttons are visible.
--}}

@extends('layouts.shop')

@section('title', $product->name)

@section('extra_css')
<style>
    /* ── LAYOUT ── */
    .product-detail   { display:grid; grid-template-columns:repeat(2, 1fr); gap:60px; align-items:start; max-width: 1200px; margin: 0 auto; padding: 40px 0; }
    .product-info     { position:sticky; top:120px; }
    .product-info h1  { font-size:32px; font-weight:800; color:var(--ink); margin:0 0 12px; line-height:1.2; }
    .category         { font-size:11px; font-weight:700; text-transform:uppercase; color:var(--ink-muted); letter-spacing:0.1em; display:block; margin-bottom:10px; }
    .price            { font-size:24px; font-weight:800; color:var(--ink); margin-bottom:24px; }

    /* ── GALLERY ── */
    .product-gallery  { max-width: 600px; }
    .product-main-img { border-radius:20px; overflow:hidden; background:var(--sand); border:1px solid var(--border); position:relative; aspect-ratio:3/4; max-height: 700px; }
    .product-main-img img { width:100%; height:100%; object-fit:cover; transition:opacity 0.3s ease; }
    .product-main-img img.swapping { opacity:0.3; }
    .thumb-strip      { display:flex; gap:12px; margin-top:16px; overflow-x:auto; padding-bottom:8px; }
    .thumb-item       { width:70px; height:85px; border-radius:12px; overflow:hidden; cursor:pointer; border:2px solid transparent; flex-shrink:0; background:var(--sand); transition:0.2s; }
    .thumb-item.active { border-color:var(--ink); }
    .thumb-item img   { width:100%; height:100%; object-fit:cover; }

    /* ── OPTIONS ── */
    .option-group     { margin-bottom:36px; }
    .option-row       { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
    .option-label     { font-size:13px; font-weight:700; color:var(--ink); text-transform:uppercase; letter-spacing:0.05em; }
    .option-value     { font-size:13px; font-weight:600; color:var(--ink-muted); }

    .color-btns       { display:flex; flex-wrap:wrap; gap:12px; }
    .color-btn        { width:44px; height:44px; border-radius:50%; border:2px solid var(--border); cursor:pointer; background:none; padding:4px; transition:0.2s; display:flex; align-items:center; justify-content:center; }
    .color-btn.active { border-color:var(--ink); transform:scale(1.1); }
    .color-swatch     { width:100%; height:100%; border-radius:50%; display:block; border:1px solid rgba(0,0,0,0.05); }

    .size-btns        { display:flex; flex-wrap:wrap; gap:10px; }
    .size-btn         { min-width:60px; height:44px; border-radius:12px; border:2px solid var(--border); font-size:14px; font-weight:700; color:var(--ink); background:none; cursor:pointer; transition:0.2s; }
    .size-btn.active  { background:var(--ink); color:var(--white); border-color:var(--ink); }
    .size-btn:disabled { opacity:0.3; cursor:not-allowed; background:var(--sand); }

    .qty-input        { display:flex; align-items:center; border:2px solid var(--border); border-radius:14px; width:fit-content; height:48px; overflow:hidden; }
    .qty-btn          { border:none; background:none; width:48px; height:100%; font-size:20px; font-weight:500; color:var(--ink); cursor:pointer; transition:0.2s; }
    .qty-btn:hover    { background:var(--sand); }
    .qty-val          { width:48px; text-align:center; font-size:16px; font-weight:800; color:var(--ink); }

    .variant-rows     { background:var(--sand); border-radius:20px; border:1px solid var(--border); overflow:hidden; margin-top:8px; }
    .variant-row      { display:grid; grid-template-columns:40px 1fr 1fr; border-bottom:1px solid var(--border); background:var(--white); }
    .variant-row:last-child { border-bottom:none; }
    .variant-row-num  { text-align:center; font-size:12px; font-weight:700; color:var(--ink-faint); padding:10px 6px; border-right:1px solid var(--border); background:var(--cream); display:flex; align-items:center; justify-content:center; }
    .variant-select   { width:100%; padding:10px 12px; border:none; border-right:1px solid var(--border); outline:none; font-family:inherit; font-size:13px; font-weight:600; background:var(--white); cursor:pointer; }
    .variant-select:last-child { border-right:none; }

    .stock-badge      { font-size:11px; font-weight:800; text-transform:uppercase; display:inline-block; margin-top:4px; }
    .stock-badge.in   { color:var(--emerald); }
    .stock-badge.low  { color:#f59e0b; }
    .stock-badge.out  { color:var(--ruby); }

    .add-to-cart-btn { background:var(--ink); color:var(--white); border:none; border-radius:16px; font-weight:800; font-size:15px; cursor:pointer; transition:0.3s; width: 100%; }
    .add-to-cart-btn:hover:not(:disabled) { transform:translateY(-2px); box-shadow:var(--shadow-md); }
    .add-to-cart-btn:disabled { opacity:0.3; cursor:not-allowed; }
    .wishlist-btn { background:none; border:2px solid var(--border); color:var(--ink); border-radius:16px; padding:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:0.2s; }
    .wishlist-btn:hover { background:var(--sand); }
    .wishlist-btn.active { color:var(--ruby); border-color:#fee2e2; background:#fef2f2; }

    @media (max-width:768px) {
        .product-detail { grid-template-columns:1fr; gap:40px; }
        .product-info h1 { font-size:28px; }
    }
</style>
@endsection

@section('content')
<div class="container section">
    {{-- 
        THE PRODUCT LAYOUT GRID
        We use a 2-column grid: 1.2 parts for the Image Gallery, 1 part for the Info Sidebar.
        Gap is set to 80px for a premium, spacious feel.
    --}}
    <div class="product-detail" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 80px; align-items: start;">

        {{-- 
            LEFT COLUMN: IMAGE GALLERY
            This contains the main product photo and the small thumbnails below it.
        --}}
        <div class="product-gallery">
            <div class="product-main-img" style="border-radius: 24px; overflow: hidden; background: #fcfbf9; border: 1px solid var(--border); aspect-ratio: 3/4; position: relative;">
                {{-- The ID 'mainProductImg' is used by JavaScript to swap images when a color is picked. --}}
                <img id="mainProductImg" src="{{ $product->images[0] ?? '/placeholder.png' }}" 
                     alt="{{ $product->name }}" 
                     style="width: 100%; height: 100%; object-fit: cover; transition: all 0.4s ease;">
            </div>
            {{-- Thumbnails strip (automatically populated by JS if multiple images exist) --}}
            <div class="thumb-strip" id="thumbStrip" style="display: flex; gap: 12px; margin-top: 20px; overflow-x: auto;"></div>
        </div>

        {{-- 
            RIGHT COLUMN: PRODUCT INFO SIDEBAR
            This uses 'sticky' so the buy buttons stay visible as the user scrolls.
        --}}
        <div class="product-info" style="position: sticky; top: 120px;">
            {{-- Breadcrumb/Category Label --}}
            <span class="category" style="font-size: 11px; font-weight: 800; color: var(--ink-muted); text-transform: uppercase; letter-spacing: 0.15em;">
                {{ $product->category->name ?? 'New Collection' }}
            </span>

            {{-- Product Title --}}
            <h1 style="font-size: 38px; font-weight: 800; color: var(--ink); margin: 8px 0 16px; line-height: 1.1;">
                {{ $product->name }}
            </h1>

            {{-- Price Tag --}}
            <p class="price" style="font-size: 28px; font-weight: 800; color: var(--ink); margin-bottom: 32px;">
                ₱{{ number_format($product->price, 2) }}
            </p>

            {{-- COLOR SELECTION SECTION --}}
            @if(!empty($product->variants) || !empty($product->colors))
            <div class="option-group" id="colorGroup" style="margin-bottom: 32px;">
                <div class="option-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="option-label" style="font-size: 13px; font-weight: 800; text-transform: uppercase;">Pick Color</span>
                    <span class="option-value" id="selectedColorLabel" style="font-size: 13px; color: var(--ink-muted); font-weight: 600;">—</span>
                </div>
                {{-- Color Swatches (Buttons) will be injected here by JS --}}
                <div class="color-btns" id="colorBtnsContainer" style="display: flex; flex-wrap: wrap; gap: 14px;"></div>
            </div>
            @endif

            {{-- SIZE SELECTION SECTION --}}
            @if(!empty($product->sizes))
            <div class="option-group" id="sizeGroup" style="margin-bottom: 32px;">
                <div class="option-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="option-label" style="font-size: 13px; font-weight: 800; text-transform: uppercase;">Select Size</span>
                    <span class="option-value" id="selectedSizeLabel" style="font-size: 13px; color: var(--ink-muted); font-weight: 600;">Choose your fit</span>
                </div>
                {{-- Size Buttons will be injected here by JS --}}
                <div class="size-btns" id="sizeBtnsContainer" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                <div id="stockBadgeContainer" style="margin-top: 12px;"></div>
            </div>
            @endif

            {{-- QUANTITY SELECTOR --}}
            <div class="option-group" style="margin-bottom: 40px;">
                <span class="option-label" style="font-size: 13px; font-weight: 800; text-transform: uppercase; display: block; margin-bottom: 16px;">Quantity</span>
                <div class="qty-input" style="display: flex; align-items: center; border: 2px solid var(--border); border-radius: 16px; width: fit-content; height: 52px; overflow: hidden; background: white;">
                    <button class="qty-btn" onclick="updateQty(-1)" style="width: 52px; border: none; background: none; font-size: 20px; cursor: pointer;">−</button>
                    <span class="qty-val" id="qty" style="width: 52px; text-align: center; font-weight: 800; font-size: 16px;">1</span>
                    <button class="qty-btn" onclick="updateQty(1)" style="width: 52px; border: none; background: none; font-size: 20px; cursor: pointer;">+</button>
                </div>
            </div>

            {{-- MULTI-ITEM CUSTOMIZATION (Shows up when Qty > 1) --}}
            <div id="variantRowsContainer" class="option-group" style="display: none; margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="option-label" style="font-size: 13px; font-weight: 800; text-transform: uppercase;">Item Personalization</span>
                    <span style="font-size: 11px; color: var(--ink-muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">Mix & Match</span>
                </div>
                <div class="variant-rows" id="variantRows"></div>
            </div>

            {{-- BUYING ACTIONS (Buttons) --}}
            <div class="action-group" style="display: grid; grid-template-columns: 1fr; gap: 14px; margin-top: 40px;">
                <button class="add-to-cart-btn" id="addToCartBtn" onclick="handleAddToCart()" 
                        style="padding: 22px; background: var(--ink); color: white; border: none; border-radius: 18px; font-weight: 800; font-size: 15px; cursor: pointer;">
                    Add to Bag
                </button>
                <button class="add-to-cart-btn" id="buyNowBtn" onclick="handleBuyNow()" 
                        style="padding: 22px; background: var(--cobalt); color: white; border: none; border-radius: 18px; font-weight: 800; font-size: 15px; cursor: pointer; box-shadow: 0 10px 25px rgba(30, 64, 175, 0.2);">
                    Buy It Now
                </button>
                <button class="wishlist-btn" onclick="toggleWishlist({{ $product->id }}, this)" 
                        style="padding: 16px; background: none; border: 2px solid var(--border); border-radius: 18px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i data-lucide="heart" size="20"></i> Add to Wishlist
                </button>
            </div>

            {{-- PRODUCT DESCRIPTION --}}
            @if($product->description)
            <div style="margin-top: 48px; padding-top: 36px; border-top: 1px solid var(--border);">
                <span class="option-label" style="font-size: 13px; font-weight: 800; text-transform: uppercase; display: block; margin-bottom: 16px;">About this product</span>
                <p style="font-size: 15px; color: var(--ink-soft); line-height: 1.8; letter-spacing: -0.01em;">
                    {!! nl2br(e($product->description)) !!}
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
// --- DATA INITIALIZATION ---
// We convert the Laravel variables into JavaScript constants so we can use them in our interactive UI.
const VARIANTS     = {!! json_encode($product->variants ?? []) !!}; // Holds colors, images, and sizes for each variant.
const FLAT_SIZES   = {!! json_encode($product->sizes   ?? []) !!}; // Simple list of sizes if no complex variants exist.
const FLAT_COLORS  = {!! json_encode($product->colors  ?? []) !!}; // Simple list of colors if no complex variants exist.
const DEFAULT_IMG  = '{{ $product->images[0] ?? "/placeholder.png" }}'; // The primary image to show on page load.

// --- STATE MANAGEMENT ---
// These variables track what the user has currently picked (Color, Size, Qty).
let selectedColor     = null; // The name of the picked color (e.g., 'Red').
let selectedColorHex  = null; // The CSS hex code for the swatch (e.g., '#ff0000').
let selectedColorImg  = null; // The image URL associated with this color.
let selectedSize      = null; // The size the user clicked (e.g., 'L').
let quantity          = 1;    // How many items the user wants.
let currentActiveIdx  = -1;   // The index in the NORM_VARIANTS array for the active color.

// --- UTILITY: COLOR MAP ---
// Maps common color names to Hex codes for the UI swatches.
const COLOR_MAP = {
    'white':'#ffffff','black':'#1a1a1a','red':'#e53e3e','blue':'#3182ce','navy':'#1a365d',
    'pink':'#f687b3','green':'#38a169','yellow':'#ecc94b','orange':'#ed8936','purple':'#805ad5',
    'grey':'#a0aec0','gray':'#a0aec0','brown':'#8b4513','beige':'#f5f0e8','cream':'#fffdd0',
};

// --- FUNCTION: nameToHex ---
// Converts a color name into a hex code. If unknown, returns a neutral grey.
function nameToHex(name) {
    return name ? (COLOR_MAP[name.toLowerCase().trim()] || '#cccccc') : '#cccccc';
}

// --- FUNCTION: buildNormalisedVariants ---
// Cleans up the data from the database into a standard format the JS can easily read.
function buildNormalisedVariants() {
    if (VARIANTS && VARIANTS.length > 0) {
        return VARIANTS.map(v => ({
            color:    v.color    || '',
            colorHex: v.colorHex || nameToHex(v.color),
            image:    v.image    || null,
            sizes:    v.sizes    || {},
        }));
    }
    // Fallback for simple products with just a list of colors.
    return FLAT_COLORS.map(c => ({ color:c, colorHex:nameToHex(c), image:null, sizes:{} }));
}

// Store the cleaned variant data.
const NORM_VARIANTS = buildNormalisedVariants();

// --- INITIALIZATION ---
// This runs as soon as the page loads to set up the gallery and swatches.
(function init() {
    window.maxStock = {{ $product->stock }}; // Set initial stock limit from the DB.
    renderThumbs();        // Show the small pictures below the main image.
    renderColorSwatches(); // Create the color picking buttons.
    // Auto-select the first color if available.
    if (NORM_VARIANTS.length > 0) activateColor(0);
    // If no colors, just show the sizes.
    else if (FLAT_SIZES.length > 0) renderSizeButtons(FLAT_SIZES, {});
})();

// --- FUNCTION: renderThumbs ---
// Generates the clickable thumbnail gallery.
function renderThumbs() {
    var strip = document.getElementById('thumbStrip'); // The container div.
    if (!strip) return; strip.innerHTML = '';          // Clear any existing content.
    var thumbImages = [];
    // Add the main product image as the first thumbnail.
    if (DEFAULT_IMG && DEFAULT_IMG !== '/placeholder.png') thumbImages.push({src:DEFAULT_IMG, label:'Main'});
    // Add images from each color variant to the gallery.
    NORM_VARIANTS.forEach(v => {
        if (v.image && v.image !== DEFAULT_IMG && v.image !== '/placeholder.png') thumbImages.push({src:v.image, label:v.color});
    });
    // Hide the thumbnails if there is only one image.
    if (thumbImages.length <= 1) return;
    // Create the HTML elements for each thumbnail.
    thumbImages.forEach((t, i) => {
        var div = document.createElement('div');
        div.className = 'thumb-item' + (i===0 ? ' active':''); // Highlight the first one.
        div.innerHTML = '<img src="'+t.src+'" alt="'+t.label+'">';
        div.onclick = function() {
            // When clicked, make this thumbnail 'active' and update the main photo.
            document.querySelectorAll('.thumb-item').forEach(d => d.classList.remove('active'));
            div.classList.add('active');
            changeMainImage(t.src);
        };
        strip.appendChild(div); // Add to the page.
    });
}

// --- FUNCTION: renderColorSwatches ---
// Creates the circular color selection buttons.
function renderColorSwatches() {
    var container = document.getElementById('colorBtnsContainer');
    if (!container) return; container.innerHTML = '';
    NORM_VARIANTS.forEach((v, idx) => {
        var btn = document.createElement('button');
        btn.className = 'color-btn';
        btn.dataset.idx = idx; // Store index for later.
        btn.innerHTML = '<span class="color-swatch" style="background:'+v.colorHex+';"></span>';
        btn.onclick = function() { activateColor(idx); }; // Click to select.
        container.appendChild(btn);
    });
}

// --- FUNCTION: activateColor ---
// Handles everything that happens when a color is picked.
function activateColor(idx) {
    var v = NORM_VARIANTS[idx];
    if (!v) return;
    currentActiveIdx = idx;
    selectedColor    = v.color;
    selectedColorHex = v.colorHex;
    selectedColorImg = v.image;
    selectedSize     = null; // Reset size when color changes.

    // Update the text label.
    document.getElementById('selectedColorLabel').textContent = v.color;
    // Update the 'active' styling of the buttons.
    document.querySelectorAll('.color-btn').forEach(b => b.classList.toggle('active', +b.dataset.idx === idx));

    // CRITICAL: Swap the main product image to the one belonging to this color.
    if (v.image) changeMainImage(v.image);

    // Filter which sizes are available for this specific color.
    var availableSizes = Object.keys(v.sizes);
    var hasAnyVariantData = NORM_VARIANTS.some(nv => Object.keys(nv.sizes).length > 0);
    if (availableSizes.length === 0 && !hasAnyVariantData && FLAT_SIZES.length > 0) {
        availableSizes = FLAT_SIZES; // Fallback if sizes aren't color-specific.
    }
    
    // Refresh the size buttons.
    renderSizeButtons(availableSizes, v.sizes);
    document.getElementById('selectedSizeLabel').textContent = 'Select a size';
    document.getElementById('stockBadgeContainer').innerHTML = '';
    renderVariantRows();          // Update the multi-item customization if needed.
    updateCartButtonState();      // Check if we can enable the 'Add to Bag' button.
}

// --- FUNCTION: renderSizeButtons ---
// Creates the clickable size boxes (S, M, L, etc.).
function renderSizeButtons(sizeList, stockMap) {
    var container = document.getElementById('sizeBtnsContainer');
    if (!container) return; container.innerHTML = '';
    sizeList.forEach(sz => {
        var stock = stockMap[sz] != null ? stockMap[sz] : 999;
        var btn   = document.createElement('button');
        btn.className   = 'size-btn';
        btn.textContent = sz;
        btn.disabled    = stock <= 0; // Disable button if out of stock.
        btn.onclick     = function() { selectSize(btn, sz, stock); };
        container.appendChild(btn);
    });
}

// --- FUNCTION: selectSize ---
// Tracks which size the user selected.
function selectSize(btn, size, stock) {
    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active'); // Style the selected button.
    selectedSize = size;
    document.getElementById('selectedSizeLabel').textContent = size;
    window.maxStock = stock; // Update the max allowed quantity based on stock.
    // If quantity was higher than new stock, lower it.
    if (quantity > window.maxStock) {
        quantity = Math.max(1, window.maxStock);
        document.getElementById('qty').textContent = quantity;
        renderVariantRows();
    }
    // Update the stock badge (In Stock, Low Stock, etc.).
    var badge = document.getElementById('stockBadgeContainer');
    if (badge) {
        if (stock > 5) badge.innerHTML = '<span class="stock-badge in">● In Stock</span>';
        else if (stock > 0) badge.innerHTML = '<span class="stock-badge low">⚠ Only '+ stock +' left</span>';
        else badge.innerHTML = '<span class="stock-badge out">✕ Out of Stock</span>';
    }
    updateCartButtonState();
}

// --- FUNCTION: updateCartButtonState ---
// Enables/Disables the Add to Bag button based on whether color/size are picked.
function updateCartButtonState() {
    var btn = document.getElementById('addToCartBtn');
    var buyBtn = document.getElementById('buyNowBtn');
    if (!btn) return;
    var sizeOk = !document.getElementById('sizeGroup') || selectedSize !== null;
    var colorOk = !document.getElementById('colorGroup') || selectedColor !== null;
    var disabled = !(sizeOk && colorOk); // Must have both to proceed.
    btn.disabled = disabled;
    if (buyBtn) buyBtn.disabled = disabled;
}

// --- FUNCTION: updateQty ---
// Handles the + and - buttons for quantity.
function updateQty(delta) {
    var max = window.maxStock !== undefined ? window.maxStock : 999;
    quantity = Math.max(1, Math.min(max, quantity + delta)); // Prevent going below 1 or above stock.
    document.getElementById('qty').textContent = quantity;
    renderVariantRows(); // Update multi-item customization rows.
}

// --- FUNCTION: renderVariantRows ---
// Creates specialized inputs if the user wants multiple items with different colors/sizes.
function renderVariantRows() {
    var container = document.getElementById('variantRowsContainer');
    var rowsEl    = document.getElementById('variantRows');
    var hasSizes  = !!document.getElementById('sizeGroup');
    var hasColors = NORM_VARIANTS.length > 0;

    if (quantity <= 1) { container.style.display = 'none'; return; }
    container.style.display = 'block';
    rowsEl.innerHTML = '';

    var currentV = NORM_VARIANTS[currentActiveIdx];
    var currentSizes = currentV ? Object.keys(currentV.sizes) : [];
    var hasAnyVariantData = NORM_VARIANTS.some(nv => Object.keys(nv.sizes).length > 0);
    if (currentSizes.length === 0 && !hasAnyVariantData) {
        currentSizes = FLAT_SIZES;
    }

    // Build the dropdown options.
    var sizeOpts  = hasSizes ? currentSizes.map(s => `<option value="${s}" ${s === selectedSize ? 'selected' : ''}>${s}</option>`).join('') : '';
    var colorOpts = hasColors ? NORM_VARIANTS.map(v => `<option value="${v.color}" ${v.color === selectedColor ? 'selected' : ''}>${v.color}</option>`).join('') : '';

    // Create a row for each item in the quantity.
    for (var i = 1; i <= quantity; i++) {
        var row = document.createElement('div');
        row.className = 'variant-row';
        row.innerHTML = `<div class="variant-row-num">${i}</div>`
            + (hasSizes  ? `<select class="variant-select" data-type="size" style="width:100%; padding:12px; border:none; border-right:1px solid var(--border);">${sizeOpts}</select>` : '')
            + (hasColors ? `<select class="variant-select" data-type="color" style="width:100%; padding:12px; border:none;">${colorOpts}</select>` : '');
        rowsEl.appendChild(row);
    }
}

// --- FUNCTION: changeMainImage ---
// Performs a smooth fade animation when swapping the primary product photo.
function changeMainImage(src) {
    var img = document.getElementById('mainProductImg');
    img.classList.add('swapping'); // Start fade out.
    setTimeout(() => { 
        img.src = src; // Change source.
        img.classList.remove('swapping'); // Fade back in.
    }, 150);
}

// --- FUNCTION: handleAddToCart ---
// Collects the user's choices and saves them to the shopping bag.
function handleAddToCart() {
    var baseProduct = {
        id: {{ $product->id }},
        name: '{{ addslashes($product->name) }}',
        price: {{ $product->price }},
        // Use the image belonging to the selected color, or the default one.
        image: selectedColorImg || '{{ addslashes($product->images[0] ?? "") }}',
    };

    var finalItems = [];
    if (quantity > 1) {
        // If multiple items, grab the choices from each row.
        document.querySelectorAll('#variantRows .variant-row').forEach(row => {
            var sVal = row.querySelector('[data-type="size"]')?.value || '';
            var cVal = row.querySelector('[data-type="color"]')?.value || '';
            var existing = finalItems.find(it => it.size === sVal && it.color === cVal);
            if (existing) existing.quantity++;
            else finalItems.push({ size: sVal, color: cVal, quantity: 1 });
        });
    } else {
        // Just a single item.
        finalItems.push({ size: selectedSize || '', color: selectedColor || '', quantity: 1 });
    }

    // Update the local storage 'cart' so it persists between pages.
    finalItems.forEach(fi => {
        var inCart = cart.find(it => it.id === baseProduct.id && it.size === fi.size && it.color === fi.color);
        if (inCart) inCart.quantity += fi.quantity;
        else cart.push(Object.assign({}, baseProduct, { size: fi.size, color: fi.color, quantity: fi.quantity, is_selected: true }));
    });

    localStorage.setItem('clothr_cart', JSON.stringify(cart));
    updateCartCount(); // Refresh the icon in the header.
    showToast('Product added to bag!');

    // If logged in, sync with the database too.
    if (isLoggedIn) {
        finalItems.forEach(fi => {
            fetch('/api/cart/update', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify(Object.assign({}, baseProduct, { size: fi.size, color: fi.color, quantity: fi.quantity }))
            });
        });
    }
}

// --- FUNCTION: handleBuyNow ---
// Adds to cart and immediately takes the user to the checkout screen.
function handleBuyNow() {
    handleAddToCart();
    setTimeout(() => window.location.href = '/checkout', 300);
}

// --- FUNCTION: toggleWishlist ---
// Saves or removes the item from the user's favorites.
function toggleWishlist(id, btn) {
    var active = btn.classList.toggle('active');
    btn.innerHTML = active ? '<i data-lucide="heart" size="20" fill="currentColor"></i> In Wishlist' : '<i data-lucide="heart" size="20"></i> Add to Wishlist';
    showToast(active ? 'Saved to wishlist' : 'Removed from wishlist', 'info');
}
</script>
@endsection
