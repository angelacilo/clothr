{{-- 
    FILE: products.blade.php
    WHAT IT DOES: A high-performance, wide-screen dashboard with "Bulletproof" scrolling.
    WHY: To ensure the admin can always navigate the page, even when modals are active.
    HOW IT WORKS: 
    1. Smart Scroll: Background locks only when needed and unlocks instantly on close.
    2. Wide Management: 1000px wide interface for maximum visibility.
    3. Self-Cleaning: One-click removal of unnecessary variations.
--}}

@extends('layouts.admin')

@section('title', 'Product Manager')

@section('content')
<div class="pro-dashboard">
    
    <!-- ═══ TOP HEADER ═══ -->
    <div class="pro-header">
        <div class="pro-header-left">
            <h1>Product Inventory</h1>
            <p>Monitor stock levels and manage your premium catalog.</p>
        </div>
        <button class="pro-btn-add" onclick="openProductModal()">
            <i data-lucide="plus"></i>
            <span>Add New Product</span>
        </button>
    </div>

    <!-- ═══ STOCK STATUS BAR ═══ -->
    @if($totalAlerts > 0)
    <div class="pro-alert-banner">
        <div class="banner-summary" onclick="toggleAlerts()">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div class="alert-icon-box"><i data-lucide="alert-octagon"></i></div>
                <div>
                    <span class="banner-title">Inventory Alerts</span>
                    <p class="banner-desc"><strong>{{ $totalAlerts }}</strong> items are low or out of stock.</p>
                </div>
            </div>
            <button class="banner-toggle">
                <span id="alertToggleTxt">View Details</span>
                <i data-lucide="chevron-down" id="alertToggleIcon"></i>
            </button>
        </div>
        
        <div id="alertDetails" class="banner-details hidden">
            <div class="banner-table-wrapper">
                <table class="pro-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Variation</th>
                            <th>Stock</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($limitedAlerts as $alert)
                        <tr id="alert-row-{{ $alert['id'] }}-{{ str_replace(' ', '_', $alert['color']) }}-{{ $alert['size'] }}">
                            <td style="font-weight: 800; color: #111827;">{{ $alert['name'] }}</td>
                            <td><span class="pro-pill">{{ $alert['color'] }} / {{ $alert['size'] }}</span></td>
                            <td><span class="stock-badge {{ $alert['stock'] == 0 ? 'out' : 'low' }}">{{ $alert['stock'] }} units</span></td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <button class="pro-btn-action-mini restock" onclick="openRestockModal({{ $alert['id'] }}, '{{ addslashes($alert['name']) }}')">Restock</button>
                                    <button class="pro-btn-action-mini remove" onclick="removeSizePermanently({{ $alert['id'] }}, '{{ addslashes($alert['color']) }}', '{{ addslashes($alert['size']) }}', true)" title="Delete size permanently">
                                        <i data-lucide="trash-2" style="width: 14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- ═══ CATALOG GRID ═══ -->
    <div class="pro-grid">
        @foreach($products as $product)
        <div class="pro-card">
            <div class="pro-card-img">
                <img src="{{ $product->images[0] ?? '/placeholder.png' }}" alt="{{ $product->name }}">
                <div class="pro-card-category">{{ $product->category->name ?? 'Tops' }}</div>
                @if($product->isNew) <div class="pro-new-badge">NEW</div> @endif
            </div>
            <div class="pro-card-body">
                <h4 class="pro-card-title">{{ $product->name }}</h4>
                <div class="pro-card-stats">
                    <span class="pro-price">₱{{ number_format($product->price, 2) }}</span>
                    <span class="pro-stock">{{ $product->stock }} in stock</span>
                </div>
                <div class="pro-card-actions">
                    <button class="pro-btn-main edit" onclick="openProductModal({{ json_encode($product) }})">
                        <i data-lucide="edit-3"></i> Edit
                    </button>
                    <button class="pro-btn-main stock" onclick="openRestockModal({{ $product->id }}, '{{ addslashes($product->name) }}')">
                        <i data-lucide="package-plus"></i> Stock
                    </button>
                    <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Delete permanently?')" style="display: contents;">
                        @csrf @method('DELETE')
                        <button class="pro-btn-trash"><i data-lucide="trash-2"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pro-pagination">{{ $products->links() }}</div>
</div>

<!-- ══════════════════════════════════════════════════════
   WIDE PRODUCT MODAL (Bulletproof Scrolling)
══════════════════════════════════════════════════════ -->
<div id="productModal" class="pro-modal-overlay" onclick="handleOutsideClick(event, 'productModal')">
    <div class="pro-modal-container">
        <div class="pro-modal-header">
            <h2 id="modal_header">Product Management</h2>
            <button onclick="closeProductModal()" class="pro-modal-close-btn">&times;</button>
        </div>
        <form id="productForm" method="POST" enctype="multipart/form-data" class="pro-modal-form-logic">
            @csrf <div id="modal_method"></div>
            
            <div class="pro-modal-scroller">
                
                <!-- Section 1: General -->
                <div class="pro-form-section">
                    <p class="pro-section-title">Item Details</p>
                    <div class="pro-form-row">
                        <div style="flex: 2;">
                            <label class="pro-input-label">Product Name</label>
                            <input type="text" name="name" id="p_name" class="pro-form-input" placeholder="e.g. Linen Blouse" required>
                        </div>
                        <div style="flex: 1;">
                            <label class="pro-input-label">Price (₱)</label>
                            <input type="number" step="0.01" name="price" id="p_price" class="pro-form-input" placeholder="0.00" required>
                        </div>
                        <div style="flex: 1;">
                            <label class="pro-input-label">Category</label>
                            <select name="category_id" id="p_category" class="pro-form-input" required>
                                @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Description & Photo -->
                <div class="pro-form-section">
                    <p class="pro-section-title">Visuals & Description</p>
                    <div class="pro-form-row">
                        <div style="flex: 1;">
                            <label class="pro-input-label">Short Description</label>
                            <textarea name="description" id="p_desc" class="pro-form-input" style="height: 120px; resize: none;" placeholder="Details about fabric and fit..."></textarea>
                        </div>
                        <div style="width: 250px;">
                            <label class="pro-input-label">Primary Photo</label>
                            <div class="pro-img-dropzone" onclick="document.getElementById('main_photo_input').click()">
                                <input type="file" name="image" id="main_photo_input" style="display: none;" onchange="previewMainImg(this)">
                                <div id="drop_hint_box"><i data-lucide="image-plus" style="width: 32px;"></i><p>Upload</p></div>
                                <img id="drop_img_preview" src="" style="display: none; width: 100%; border-radius: 12px;">
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 25px; display: flex; gap: 25px;">
                        <label class="pro-input-checkbox"><input type="checkbox" name="isNew" id="p_new"> Mark as New Arrival</label>
                        <label class="pro-input-checkbox"><input type="checkbox" name="isOnSale" id="p_sale"> Mark as On Sale</label>
                    </div>
                </div>

                <!-- Section 3: Colors & Inventory -->
                <div class="pro-form-section" style="border: none; margin-bottom: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <p class="pro-section-title" style="margin: 0;">Stock Sizing (Colors & Sizes)</p>
                        <button type="button" class="pro-btn-add-variant" onclick="addNewColorGroup()">+ Add Color Group</button>
                    </div>
                    <div id="v_list_container"></div>
                    <div id="v_empty_msg" class="pro-empty-state">
                        <p>No colors added. Default Stock: <input type="number" name="stock" id="p_stock" value="0" class="pro-form-input" style="width: 80px; text-align: center; margin-left: 10px;"></p>
                    </div>
                </div>

            </div>

            <!-- Footer (Pinned) -->
            <div class="pro-modal-footer">
                <button type="button" onclick="closeProductModal()" class="pro-btn-ghost">Discard</button>
                <button type="submit" class="pro-btn-solid">Save Product</button>
            </div>
            <input type="hidden" name="variants_data" id="v_payload_json">
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
   WIDE RESTOCK MODAL (Consistent & Simple)
══════════════════════════════════════════════════════ -->
<div id="restockModal" class="pro-modal-overlay" onclick="handleOutsideClick(event, 'restockModal')">
    <div class="pro-modal-container" style="width: 800px; height: auto; max-height: 85vh;">
        <div class="pro-modal-header">
            <h2>Inventory Restock</h2>
            <button onclick="closeRestockModal()" class="pro-modal-close-btn">&times;</button>
        </div>
        <div class="pro-modal-scroller" style="padding: 0;">
            <div id="restock_table_area"></div>
        </div>
        <div class="pro-modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; color: #64748b;">New Units: <span id="r_sum_display" style="font-size: 26px; color: #111827; margin-left: 15px;">0</span></div>
            <div style="display: flex; gap: 15px;">
                <button onclick="closeRestockModal()" class="pro-btn-ghost">Cancel</button>
                <button onclick="submitRestockNow()" class="pro-btn-solid">Update Stock</button>
            </div>
        </div>
        <form id="restockForm" method="POST">@csrf<div id="r_inputs_hidden"></div></form>
    </div>
</div>

<style>
/* --- PRO DESIGN SYSTEM --- */
:root { --p-dark: #0f172a; --p-blue: #2563eb; --p-red: #ef4444; --p-bg: #f8fafc; --p-border: #e2e8f0; }

.pro-dashboard { padding: 30px 0; max-width: 1400px; margin: 0 auto; }

/* Header */
.pro-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; }
.pro-header h1 { font-size: 38px; font-weight: 900; color: var(--p-dark); margin: 0; }
.pro-header p { font-size: 16px; color: #64748b; margin-top: 6px; }
.pro-btn-add { background: var(--p-dark); color: #fff; border: none; padding: 15px 30px; border-radius: 14px; font-weight: 800; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
.pro-btn-add:hover { transform: translateY(-4px); box-shadow: 0 20px 30px rgba(0,0,0,0.15); }

/* Alert Banner */
.pro-alert-banner { background: #fff; border: 1.5px solid var(--p-border); border-left: 6px solid var(--p-red); border-radius: 20px; margin-bottom: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow: hidden; }
.banner-summary { padding: 22px 30px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
.alert-icon-box { width: 44px; height: 44px; background: #fff1f2; color: var(--p-red); border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.banner-title { font-size: 16px; font-weight: 800; }
.banner-toggle { background: #f8fafc; border: 1px solid var(--p-border); padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px; }
.pro-table { width: 100%; border-collapse: collapse; }
.pro-table th { text-align: left; padding: 15px 30px; font-size: 11px; text-transform: uppercase; color: #94a3b8; border-bottom: 1.5px solid #f1f5f9; }
.pro-table td { padding: 18px 30px; border-bottom: 1px solid #f8fafc; font-size: 14px; }
.pro-pill { background: #f1f5f9; padding: 3px 10px; border-radius: 8px; font-weight: 800; font-size: 11px; }
.stock-badge { padding: 4px 12px; border-radius: 100px; font-size: 11px; font-weight: 900; }
.stock-badge.out { background: #fef2f2; color: var(--p-red); }
.stock-badge.low { background: #fffbeb; color: #d97706; }
.pro-btn-action-mini { border: none; padding: 6px 14px; border-radius: 8px; font-size: 11px; font-weight: 800; cursor: pointer; }
.pro-btn-action-mini.restock { background: var(--p-dark); color: #fff; }
.pro-btn-action-mini.remove { background: #fff; border: 1px solid #fee2e2; color: var(--p-red); }

/* Grid */
.pro-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; }
.pro-card { background: #fff; border: 1.5px solid var(--p-border); border-radius: 28px; overflow: hidden; transition: 0.3s; }
.pro-card:hover { transform: translateY(-8px); box-shadow: 0 40px 60px -20px rgba(0,0,0,0.12); border-color: var(--p-dark); }
.pro-card-img { height: 260px; position: relative; background: #f1f5f9; }
.pro-card-img img { width: 100%; height: 100%; object-fit: cover; }
.pro-card-category { position: absolute; bottom: 15px; right: 15px; background: rgba(255,255,255,0.9); padding: 4px 12px; border-radius: 100px; font-size: 10px; font-weight: 900; color: #94a3b8; border: 1px solid var(--p-border); }
.pro-new-badge { position: absolute; top: 15px; left: 15px; background: var(--p-blue); color: #fff; padding: 4px 12px; border-radius: 8px; font-size: 9px; font-weight: 900; }
.pro-card-body { padding: 25px; }
.pro-card-title { font-size: 18px; font-weight: 800; color: var(--p-dark); margin: 0 0 10px; }
.pro-card-stats { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 25px; }
.pro-price { font-size: 24px; font-weight: 900; color: var(--p-dark); }
.pro-stock { font-size: 12px; font-weight: 700; color: #94a3b8; }
.pro-card-actions { display: flex; gap: 8px; padding-top: 20px; border-top: 1.5px solid #f8fafc; }
.pro-btn-main { flex: 2; border: 2px solid var(--p-border); background: #fff; border-radius: 14px; padding: 10px; font-size: 12px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
.pro-btn-main.edit { background: var(--p-dark); color: #fff; border: none; }
.pro-btn-main.stock { background: #eff6ff; color: var(--p-blue); border-color: #dbeafe; }
.pro-btn-trash { flex: 1; border: 2px solid var(--p-border); background: #fff; color: #94a3b8; border-radius: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.pro-btn-trash:hover { background: #fef2f2; border-color: #fecaca; color: var(--p-red); }

/* MODALS */
.pro-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
.pro-modal-container { background: #fff; width: 1000px; height: 90vh; border-radius: 36px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 60px 120px -30px rgba(0,0,0,0.5); }
.pro-modal-header { padding: 30px 45px; border-bottom: 2.5px solid #f1f5f9; flex-shrink: 0; display: flex; justify-content: space-between; align-items: center; }
.pro-modal-header h2 { margin: 0; font-size: 26px; font-weight: 900; }
.pro-modal-close-btn { background: #f1f5f9; border: none; width: 44px; height: 44px; border-radius: 50%; font-size: 32px; color: #94a3b8; cursor: pointer; line-height: 1; }
.pro-modal-scroller { flex: 1; overflow-y: auto; padding: 45px; }
.pro-modal-footer { padding: 30px 45px; border-top: 2.5px solid #f1f5f9; background: #fafafa; flex-shrink: 0; text-align: right; }

.pro-form-section { border-bottom: 2px solid #f1f5f9; margin-bottom: 45px; padding-bottom: 45px; }
.pro-section-title { font-size: 11px; font-weight: 900; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.12em; margin-bottom: 25px; }
.pro-form-row { display: flex; gap: 25px; }
.pro-input-label { display: block; font-size: 14px; font-weight: 800; color: var(--p-dark); margin-bottom: 10px; }
.pro-form-input { width: 100%; padding: 15px 20px; border: 2.5px solid #f1f5f9; background: #f8fafc; border-radius: 16px; font-size: 15px; font-weight: 700; outline: none; transition: 0.2s; box-sizing: border-box; }
.pro-form-input:focus { border-color: var(--p-dark); background: #fff; }
.pro-input-checkbox { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 800; cursor: pointer; }
.pro-img-dropzone { border: 3px dashed #e2e8f0; border-radius: 24px; background: #f8fafc; padding: 30px; text-align: center; cursor: pointer; display: flex; align-items: center; justify-content: center; min-height: 120px; }

.pro-btn-solid { background: var(--p-dark); color: #fff; border: none; padding: 16px 36px; border-radius: 16px; font-weight: 800; font-size: 16px; cursor: pointer; }
.pro-btn-ghost { background: #fff; border: 2.5px solid var(--p-border); color: #64748b; padding: 16px 36px; border-radius: 16px; font-weight: 800; margin-right: 15px; cursor: pointer; }

/* Restock Table */
.r-table { width: 100%; border-collapse: collapse; }
.r-table th { text-align: left; padding: 20px 35px; font-size: 11px; text-transform: uppercase; color: #94a3b8; border-bottom: 2px solid #f1f5f9; }
.r-table td { padding: 20px 35px; border-bottom: 1px solid #f8fafc; font-size: 15px; vertical-align: middle; }
.r-color-label { font-weight: 900; color: var(--p-dark); vertical-align: top !important; padding-top: 25px !important; width: 120px; }
.r-input-pill { background: #eff6ff; border: 2.5px solid #dbeafe; border-radius: 14px; padding: 10px 18px; display: inline-flex; align-items: center; gap: 10px; }
.r-input-pill span { font-weight: 900; color: var(--p-blue); font-size: 16px; }
.r-qty-field { border: none; background: none; width: 45px; text-align: center; font-size: 18px; font-weight: 900; color: var(--p-blue); outline: none; }
.r-trash-btn { background: #fff; border: 1.5px solid #fee2e2; color: var(--p-red); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; }
.r-trash-btn:hover { background: #fef2f2; }

/* Variants */
.pro-v-card { background: #fff; border: 2.5px solid #f1f5f9; border-radius: 28px; padding: 25px; margin-bottom: 25px; position: relative; }
.pro-s-grid { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; padding-top: 20px; border-top: 1.5px solid #f1f5f9; }
.pro-s-box { display: flex; flex-direction: column; align-items: center; background: #f8fafc; border: 1.5px solid #f1f5f9; border-radius: 14px; padding: 8px 14px; position: relative; }
.pro-s-box span { font-size: 10px; font-weight: 900; color: #94a3b8; margin-bottom: 4px; }
.pro-s-box input { border: none; background: none; width: 45px; text-align: center; font-size: 16px; font-weight: 900; outline: none; }
.pro-s-del { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; background: var(--p-red); color: #fff; border: none; border-radius: 50%; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.pro-btn-add-variant { background: #f1f5f9; color: var(--p-dark); border: none; padding: 10px 20px; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer; }

.hidden { display: none; }
body.modal-open { overflow: hidden !important; padding-right: 0 !important; }
</style>

<script>
/**
 * CLOTHR PREMIUM DASHBOARD ENGINE
 */

var variantsStore = [];

// 1. MODAL MANAGEMENT
function openProductModal(p = null) {
    const modal = document.getElementById('productModal');
    const form = document.getElementById('productForm');
    document.getElementById('drop_img_preview').style.display = 'none';
    document.getElementById('drop_hint_box').style.display = 'block';
    
    // Safety: Always remove the class before adding it
    document.body.classList.remove('modal-open');
    document.body.classList.add('modal-open');

    if (p) {
        document.getElementById('modal_header').innerText = 'Edit Collection Item';
        form.action = "/admin/products/" + p.id;
        document.getElementById('modal_method').innerHTML = '@method("PUT")';
        document.getElementById('p_name').value = p.name;
        document.getElementById('p_price').value = p.price;
        document.getElementById('p_category').value = p.category_id;
        document.getElementById('p_desc').value = p.description;
        document.getElementById('p_stock').value = p.stock;
        document.getElementById('p_new').checked = !!p.isNew;
        document.getElementById('p_sale').checked = !!p.isOnSale;
        if (p.images && p.images[0]) {
            document.getElementById('drop_img_preview').src = p.images[0];
            document.getElementById('drop_img_preview').style.display = 'block';
            document.getElementById('drop_hint_box').style.display = 'none';
        }
        variantsStore = p.variants || [];
    } else {
        document.getElementById('modal_header').innerText = 'Add New Collection Item';
        form.action = "{{ route('admin.products.store') }}";
        document.getElementById('modal_method').innerHTML = '';
        form.reset();
        variantsStore = [];
    }
    renderVariantList();
    modal.style.display = 'flex';
}

function closeProductModal() { 
    document.getElementById('productModal').style.display = 'none'; 
    document.body.classList.remove('modal-open');
}

// 2. VARIANT SYSTEM
function addNewColorGroup() {
    variantsStore.push({ color: '', colorHex: '#000000', image: null, sizes: {} });
    renderVariantList();
}

function removeColorGroup(idx) {
    if(confirm('Delete this color and all its sizes?')) { variantsStore.splice(idx, 1); renderVariantList(); }
}

function renderVariantList() {
    const list = document.getElementById('v_list_container');
    const empty = document.getElementById('v_empty_msg');
    list.innerHTML = '';
    
    if (variantsStore.length === 0) { empty.style.display = 'block'; return; }
    empty.style.display = 'none';
    
    variantsStore.forEach((v, idx) => {
        let card = document.createElement('div');
        card.className = 'pro-v-card';
        card.innerHTML = `
            <button type="button" onclick="removeColorGroup(${idx})" style="position:absolute; top:20px; right:20px; border:none; background:none; color:#94a3b8; font-size:28px; cursor:pointer;">&times;</button>
            <div class="pro-form-row">
                <div style="flex:2;">
                    <label class="pro-input-label">Color Name</label>
                    <input type="text" placeholder="e.g. Ivory White" class="pro-form-input" value="${v.color}" oninput="variantsStore[${idx}].color=this.value">
                </div>
                <div style="width:65px;">
                    <label class="pro-input-label">Hex</label>
                    <div style="height:55px; border-radius:14px; border:2.5px solid #f1f5f9; overflow:hidden;">
                        <input type="color" value="${v.colorHex}" oninput="variantsStore[${idx}].colorHex=this.value" style="width:150%; height:150%; border:none; cursor:pointer; transform:translate(-15%, -15%);">
                    </div>
                </div>
                <div style="flex:1.5;">
                    <label class="pro-input-label">Color Photo</label>
                    <input type="file" name="color_images[${idx}]" class="pro-form-input" style="font-size:11px; padding:13px;">
                </div>
            </div>
            <div class="pro-s-grid">
                ${Object.keys(v.sizes).map(sz => `
                    <div class="pro-s-box">
                        <span>${sz}</span>
                        <input type="number" min="0" value="${v.sizes[sz]}" oninput="variantsStore[${idx}].sizes['${sz}']=this.value">
                        <button type="button" class="pro-s-del" onclick="deleteSizeFromStore(${idx}, '${sz}')">&times;</button>
                    </div>
                `).join('')}
                <button type="button" class="pro-btn-add-variant" style="height:54px; margin-left:auto;" onclick="addSizeToStore(${idx})">+ Add Size</button>
            </div>
        `;
        list.appendChild(card);
    });
}

function addSizeToStore(idx) {
    let s = prompt("Enter Size Name:");
    if(s) { variantsStore[idx].sizes[s] = 0; renderVariantList(); }
}

function deleteSizeFromStore(idx, size) {
    delete variantsStore[idx].sizes[size];
    renderVariantList();
}

document.getElementById('productForm').onsubmit = function() {
    document.getElementById('v_payload_json').value = JSON.stringify(variantsStore);
};

// 3. RESTOCK & CLEANUP
function openRestockModal(id, name) {
    const modal = document.getElementById('restockModal');
    const area = document.getElementById('restock_table_area');
    area.innerHTML = '<div style="padding:50px; text-align:center; color:#94a3b8;">Processing Catalog Data...</div>';
    
    document.body.classList.remove('modal-open');
    document.body.classList.add('modal-open');

    fetch('/admin/products/' + id + '/stock-data').then(res => res.json()).then(data => {
        let h = '<table class="r-table"><thead><tr><th>Color</th><th>Size</th><th>Stock</th><th style="text-align:right;">+ Add Units</th><th style="width:50px;"></th></tr></thead><tbody>';
        if (data.hasVariants) {
            data.variants.forEach(v => {
                let sizes = Object.keys(v.sizes);
                if (sizes.length === 0) return;
                sizes.forEach((sz, i) => {
                    let k = v.color + '_' + sz;
                    h += `<tr id="r-row-${id}-${v.color.replace(' ','_')}-${sz}">
                        ${i===0?`<td rowspan="${sizes.length}" class="r-color-label">${v.color}</td>`:''}
                        <td><span class="pro-pill">${sz}</span></td>
                        <td style="font-weight:700; color:#64748b;">${v.sizes[sz]}</td>
                        <td style="text-align:right;"><div class="r-input-pill"><span>+</span><input type="number" min="0" value="0" class="r-qty-field r-fld" data-key="${k}" oninput="recalcRestock()"></div></td>
                        <td><button type="button" class="r-trash-btn" onclick="removeSizePermanently(${id}, '${v.color}', '${sz}', false)"><i data-lucide="trash-2" style="width:14px;"></i></button></td>
                    </tr>`;
                });
            });
        } else {
            h += `<tr><td class="r-color-label">Global</td><td>-</td><td>${data.stock}</td><td style="text-align:right;"><div class="r-input-pill"><span>+</span><input type="number" min="0" value="0" class="r-qty-field r-fld" data-key="default" oninput="recalcRestock()"></div></td><td></td></tr>`;
        }
        area.innerHTML = h + '</tbody></table>';
        lucide.createIcons();
    });
    
    document.getElementById('restockForm').action = '/admin/products/' + id + '/restock';
    document.getElementById('r_sum_display').innerText = '0';
    modal.style.display = 'flex';
}

function closeRestockModal() { 
    document.getElementById('restockModal').style.display = 'none'; 
    document.body.classList.remove('modal-open');
}

function removeSizePermanently(id, color, size, fromBanner) {
    if (!confirm(`Delete "${size}" forever?`)) return;
    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');
    fd.append('color', color);
    fd.append('size', size);
    fetch(`/admin/products/${id}/remove-size`, { method: 'POST', body: fd }).then(res => res.json()).then(res => {
        if (res.success) {
            const rowId = `r-row-${id}-${color.replace(' ','_')}-${size}`;
            const bannerId = `alert-row-${id}-${color.replace(' ','_')}-${size}`;
            if (document.getElementById(rowId)) document.getElementById(rowId).style.display = 'none';
            if (document.getElementById(bannerId)) document.getElementById(bannerId).style.display = 'none';
        }
    });
}

function recalcRestock() {
    let t = 0; let ins = '';
    document.querySelectorAll('.r-fld').forEach(f => {
        let v = parseInt(f.value) || 0; t += v;
        ins += `<input type="hidden" name="restock[${f.dataset.key}]" value="${v}">`;
    });
    document.getElementById('r_sum_display').innerText = t;
    document.getElementById('r_inputs_hidden').innerHTML = ins;
}

function submitRestockNow() { if(confirm('Update stock?')) document.getElementById('restockForm').submit(); }

// 4. UTILS
function toggleAlerts() {
    const div = document.getElementById('alertDetails');
    const label = document.getElementById('alertToggleTxt');
    const icon = document.getElementById('alertToggleIcon');
    div.classList.toggle('hidden');
    if (div.classList.contains('hidden')) {
        label.innerText = 'View Details';
        icon.style.transform = 'rotate(0deg)';
    } else {
        label.innerText = 'Hide Details';
        icon.style.transform = 'rotate(180deg)';
    }
}

function previewMainImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('drop_img_preview').src = e.target.result;
            document.getElementById('drop_img_preview').style.display = 'block';
            document.getElementById('drop_hint_box').style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Safety: Handle Escape Key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeProductModal();
        closeRestockModal();
    }
});

// Safety: Handle Outside Click
function handleOutsideClick(e, modalId) {
    if (e.target.id === modalId) {
        if (modalId === 'productModal') closeProductModal();
        else closeRestockModal();
    }
}

document.addEventListener('DOMContentLoaded', () => { 
    if (window.lucide) lucide.createIcons(); 
    // Always ensure body is unlocked on load
    document.body.classList.remove('modal-open');
});
</script>
@endsection
