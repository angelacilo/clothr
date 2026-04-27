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
                                    <button class="pro-btn-action-mini restock" onclick='openRestockModal({{ $alert['id'] }}, {{ json_encode($alert['name']) }})'>Restock</button>
                                    <button class="pro-btn-action-mini remove" onclick='removeSizePermanently({{ $alert['id'] }}, {{ json_encode($alert['color']) }}, {{ json_encode($alert['size']) }}, true)' title="Delete size permanently">
                                        <i data-lucide="trash-2" style="width: 14px; pointer-events: none;"></i>
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
                    <button class="pro-btn-main edit" onclick='openProductModal({{ json_encode($product) }})'>
                        <i data-lucide="edit-3" style="pointer-events: none;"></i> Edit
                    </button>
                    <button class="pro-btn-main stock" onclick='openRestockModal({{ $product->id }}, {{ json_encode($product->name) }})'>
                        <i data-lucide="package-plus" style="pointer-events: none;"></i> Stock
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
    <div class="pro-modal-container" style="width: 900px; height: auto; max-height: 85vh;">
        <div class="pro-modal-header" style="flex-direction: column; align-items: flex-start; gap: 4px;">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <h2 style="margin: 0;">Inventory Restock</h2>
                <button onclick="closeRestockModal()" class="pro-modal-close-btn">&times;</button>
            </div>
            <p id="restock_product_name" style="margin: 0; font-size: 14px; font-weight: 700; color: #64748b;"></p>
        </div>
        
        <!-- Info Alert -->
        <div style="padding: 15px 45px 0;">
            <div style="background: #eff6ff; border: 1.5px solid #dbeafe; border-radius: 14px; padding: 14px 20px; display: flex; align-items: center; gap: 12px;">
                <i data-lucide="info" style="color: #3b82f6; width: 20px;"></i>
                <p style="margin: 0; font-size: 13px; font-weight: 700; color: #1e40af;">
                    Enter how many units to <b>ADD</b> to each size. Current stock will not decrease.
                </p>
            </div>
        </div>

        <div class="pro-modal-scroller" style="padding: 25px 45px 45px;">
            <div id="restock_table_area" style="border: 2px solid #f1f5f9; border-radius: 20px; overflow: hidden; background: #fff;"></div>
        </div>

        <div class="pro-modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="font-size: 13px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em;">New Units Total</span>
                <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 6px 16px;">
                    <span id="r_sum_display" style="font-size: 24px; font-weight: 900; color: #111827;">0</span>
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <button onclick="closeRestockModal()" class="pro-btn-ghost" style="margin:0;">Cancel</button>
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
.pro-modal-scroller { flex: 1; overflow-y: auto; padding: 45px; min-height: 0; }
.pro-modal-footer { padding: 30px 45px; border-top: 2.5px solid #f1f5f9; background: #fafafa; flex-shrink: 0; text-align: right; }

/* The "Magic" Flex Fix for Scrolling Forms */
.pro-modal-form-logic {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow: hidden;
}

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
/* Restock Table */
.r-table {
    width: 100%;
    border-collapse: collapse;
}

/* Every header cell — identical */
.r-table thead th {
    padding: 12px 20px;
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    color: #94a3b8;
    letter-spacing: .1em;
    border-bottom: 2px solid #f1f5f9;
    text-align: left;
    background: #fafafa;
}

/* Every body row — identical height */
.r-table tbody tr {
    height: 64px;
    transition: background 0.1s;
}

/* Every body cell — identical padding */
.r-table tbody td {
    padding: 14px 20px;
    vertical-align: middle;
    border-bottom: 1px solid #f8fafc;
    font-size: 14px;
}

/* Remove bottom border from last row */
.r-table tbody tr:last-child td {
    border-bottom: none;
}

/* Hover effect on rows */
.r-table tbody tr:hover {
    background: #f8fafc;
}

.r-qty-field { border: none; background: none; width: 45px; text-align: center; font-size: 18px; font-weight: 900; color: var(--p-blue); outline: none; }

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
            <!-- SIZES SECTION: Replaces the old prompt() system -->
            <div style="margin-top: 25px; padding-top: 25px; border-top: 1.5px solid #f1f5f9;">
                <div style="font-size:11px;font-weight:900;text-transform:uppercase;color:#94a3b8;letter-spacing:.1em;margin-bottom:12px;">
                    Select Sizes — click to toggle on/off
                </div>

                <!-- PRESET SIZE BUTTONS -->
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
                    ${PRESET_SIZES.map(sz => {
                        const isSelected = sz in v.sizes;
                        return `
                            <button type="button"
                                onclick="togglePresetSize(${idx}, '${sz}')"
                                style="padding:8px 16px; border-radius:10px; font-size:13px; font-weight:800; cursor:pointer; transition:all .15s;
                                       ${isSelected 
                                         ? 'background:#111827;color:#fff;border:2px solid #111827;' 
                                         : 'background:#f8fafc;color:#374151;border:2px solid #e2e8f0;'
                                       }">
                                ${sz}
                                ${isSelected ? ' ✓' : ''}
                            </button>
                        `;
                    }).join('')}
                </div>

                <!-- CUSTOM SIZE INPUT -->
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:20px;">
                    <input type="text"
                           id="customSizeInput_${idx}"
                           placeholder="Custom size (e.g. 32, EU38, One Size)"
                           style="flex:1;padding:10px 14px; border:2px solid #f1f5f9; background:#f8fafc; border-radius:12px; font-size:13px; font-weight:700; outline:none;"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSize(${idx});}">
                    <button type="button"
                            onclick="addCustomSize(${idx})"
                            style="padding:10px 18px; background:#f1f5f9; border:none; border-radius:12px; font-size:13px; font-weight:800; cursor:pointer;">
                        + Add
                    </button>
                </div>

                <!-- STOCK INPUTS FOR SELECTED SIZES -->
                ${Object.keys(v.sizes).length > 0 ? `
                    <div style="font-size:11px;font-weight:900;text-transform:uppercase;color:#94a3b8;letter-spacing:.1em;margin-bottom:12px;">
                        Enter Stock Per Size
                    </div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        ${Object.entries(v.sizes).map(([sz, qty]) => `
                            <div style="position:relative;background:#f8fafc; border:2px solid #e2e8f0;border-radius:12px; padding:12px 16px;text-align:center; min-width:80px;">
                                <button type="button"
                                        onclick="deleteSizeFromStore(${idx}, '${sz}')"
                                        style="position:absolute;top:-8px;right:-8px; width:22px;height:22px; background:#ef4444;color:#fff; border:none;border-radius:50%; font-size:13px;cursor:pointer; display:flex;align-items:center; justify-content:center;">
                                    &times;
                                </button>
                                <div style="font-size:11px;font-weight:900; color:#94a3b8;margin-bottom:6px;">
                                    ${sz}
                                </div>
                                <input type="number"
                                       min="0"
                                       value="${qty}"
                                       oninput="updateSizeStock(${idx}, '${sz}', this.value)"
                                       style="border:none;background:none; width:50px;text-align:center; font-size:18px;font-weight:900; color:#111827;outline:none;">
                                <div style="font-size:9px;color:#94a3b8;margin-top:4px;">
                                    units
                                </div>
                            </div>
                        `).join('')}
                    </div>
                ` : `
                    <div style="text-align:center;padding:16px; color:#94a3b8;font-size:13px; border:2px dashed #e2e8f0; border-radius:12px;">
                        Click the size buttons above to add sizes
                    </div>
                `}
            </div>
        `;
        list.appendChild(card);
    });
}

/**
 * PRESET_SIZES
 * 
 * These are the standard clothing sizes that every 
 * product can use. Instead of typing sizes manually,
 * the admin just clicks these buttons.
 * 
 * "Free Size" means the item fits everyone 
 * regardless of size (like a scarf or bag).
 */
const PRESET_SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Free Size'];

/**
 * Toggles a preset size on or off for a color variant.
 * 
 * If the size is NOT in the variant: add it with stock = 0
 * If the size IS already in the variant: remove it
 * 
 * Then re-renders the variant list so the UI updates.
 * 
 * @param {number} idx  - which color variant (0, 1, 2...)
 * @param {string} size - the size name e.g. "S", "M", "XL"
 */
function togglePresetSize(idx, size) {
    // Check if this size already exists in this variant
    if (size in variantsStore[idx].sizes) {
        // Size exists — remove it
        // Confirm if it already has stock entered to prevent accidents
        if (variantsStore[idx].sizes[size] > 0) {
            if (!confirm('This size has stock entered. Remove it?')) {
                return; // Admin said no — do nothing
            }
        }
        delete variantsStore[idx].sizes[size];
    } else {
        // Size does not exist — add it with 0 stock
        variantsStore[idx].sizes[size] = 0;
    }
    
    // Re-render the whole variant list to show the change
    renderVariantList();
}

/**
 * Adds a custom size that is not in the preset list.
 * 
 * Admin types a size in the custom input box and 
 * clicks "+ Add". This function reads that input,
 * validates it, and adds it to the variant.
 * 
 * @param {number} idx - which color variant (0, 1, 2...)
 */
function addCustomSize(idx) {
    // Get the custom input field for this color variant
    const input = document.getElementById('customSizeInput_' + idx);
    
    // Read what the admin typed and remove extra spaces
    const size = input.value.trim().toUpperCase();
    
    // Do not add if empty
    if (!size) {
        alert('Please type a size name first.');
        return;
    }
    
    // Do not add if this size already exists
    if (size in variantsStore[idx].sizes) {
        alert(size + ' is already added to this color.');
        input.value = '';
        return;
    }
    
    // Add the custom size with 0 stock
    variantsStore[idx].sizes[size] = 0;
    
    // Clear the input field
    input.value = '';
    
    // Re-render to show the new size
    renderVariantList();
}

/**
 * Updates the stock number for a specific size 
 * when admin types in the stock input box.
 * 
 * @param {number} idx   - which color variant
 * @param {string} size  - which size (e.g. "M")
 * @param {number} value - the new stock number
 */
function updateSizeStock(idx, size, value) {
    // Make sure the value is a valid number, minimum 0
    const qty = Math.max(0, parseInt(value) || 0);
    variantsStore[idx].sizes[size] = qty;
    // No need to re-render — just update the stored value
}

function deleteSizeFromStore(idx, size) {
    if (variantsStore[idx].sizes[size] > 0) {
        if (!confirm('This size has stock entered. Remove it?')) {
            return;
        }
    }
    delete variantsStore[idx].sizes[size];
    renderVariantList();
}

document.getElementById('productForm').onsubmit = function() {
    document.getElementById('v_payload_json').value = JSON.stringify(variantsStore);
};

// 3. RESTOCK & CLEANUP
/**
 * openRestockModal()
 * 
 * Opens the restock popup for a specific product.
 * 
 * HOW IT WORKS:
 * 1. Shows the modal immediately with a loading message
 * 2. Fetches the current stock data from the server
 *    via AJAX (GET /admin/products/{id}/stock-data)
 * 3. Builds an HTML table showing every 
 *    color + size combination with its current stock
 * 4. Admin types how many units to ADD in each row
 * 5. The "New Units" total updates live
 * 6. Admin clicks "Update Stock" to save
 * 
 * WHY NO ROWSPAN:
 * We intentionally show the color name on EVERY row 
 * instead of using HTML rowspan. This is because 
 * rowspan in dynamically injected HTML (via innerHTML) 
 * causes inconsistent rendering across browsers, 
 * making rows appear different sizes. Every row 
 * being identical guarantees consistent appearance.
 * 
 * SECURITY:
 * The actual stock update happens on the server.
 * The server validates all quantities are >= 0.
 * The server uses a database transaction and 
 * row lock to prevent two restocks at the same time.
 * 
 * @param {number} id   - The product ID
 * @param {string} name - The product name for display
 */
function openRestockModal(id, name) {
    const modal = document.getElementById('restockModal');
    const area = document.getElementById('restock_table_area');
    document.getElementById('restock_product_name').innerText = name;
    area.innerHTML = '<div style="padding:100px 50px; text-align:center; color:#94a3b8;"><div class="pro-spinner" style="margin-bottom:20px;"></div><p style="font-weight:800; font-size:15px;">Loading Inventory Data...</p></div>';
    
    document.body.classList.remove('modal-open');
    document.body.classList.add('modal-open');

    // SECURITY: Include CSRF token in headers (Standard practice)
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';
    
    fetch('/admin/products/' + id + '/stock-data', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        let h = '';
        h += '<table class="r-table">';
        h += '<thead>';
        h += '<tr>';
        h += '<th>Color</th>';
        h += '<th>Size</th>';
        h += '<th style="text-align:center;">Current Stock</th>';
        h += '<th style="text-align:center;">+ Add Units</th>';
        h += '<th style="text-align:center; width:60px;"></th>';
        h += '</tr>';
        h += '</thead>';
        h += '<tbody>';
        
        if (data.hasVariants) {
            data.variants.forEach((v, variantIdx) => {
                const sizes = Object.keys(v.sizes);
                const colorHex = v.colorHex || '#ccc';
                const isLast = variantIdx === data.variants.length - 1;
                
                if (sizes.length === 0) return;
                
                sizes.forEach((sz, sizeIdx) => {
                    const qty = v.sizes[sz];
                    const key = v.color + '_' + sz;
                    const rowId = 'r-row-' + id + '-' + v.color.replace(/ /g,'_') + '-' + sz;
                    const stockColor = qty === 0 ? '#ef4444' : qty <= 5 ? '#d97706' : '#16a34a';
                    
                    const isFirstSizeOfColor = sizeIdx === 0;
                    const isLastSizeOfColor = sizeIdx === sizes.length - 1;
                    const topBorder = isFirstSizeOfColor && variantIdx > 0 ? 'border-top:2px solid #f1f5f9;' : '';
                    const bottomBorder = isLastSizeOfColor && !isLast ? '' : 'border-bottom:1px solid #f8fafc;';
                    
                    h += `<tr id="${rowId}" style="${topBorder}${bottomBorder}">`;
                    
                    // COLOR column
                    h += `<td><div style="display:flex;align-items:center;gap:10px;">`;
                    h += `<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:${colorHex};border:1.5px solid rgba(0,0,0,0.1);flex-shrink:0;"></span>`;
                    h += `<span style="font-size:13px;font-weight:800;color:#111827;">${v.color}</span>`;
                    h += `</div></td>`;
                    
                    // SIZE column
                    h += `<td><span style="background:#f1f5f9;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:900;color:#374151;">${sz}</span></td>`;
                    
                    // CURRENT STOCK column
                    h += `<td style="text-align:center;"><span style="font-size:16px;font-weight:900;color:${stockColor};">${qty}</span></td>`;
                    
                    // ADD UNITS column
                    h += `<td style="text-align:center;"><div style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;border:2px solid #bfdbfe;border-radius:12px;padding:8px 14px;">`;
                    h += `<span style="font-size:14px;font-weight:900;color:#3b82f6;">+</span>`;
                    h += `<input type="number" min="0" value="0" class="r-qty-field r-fld" data-key="${key}" style="border:none;background:none;width:40px;text-align:center;font-size:16px;font-weight:900;color:#3b82f6;outline:none;" oninput="recalcRestock()">`;
                    h += `</div></td>`;
                    
                    // ACTION column
                    h += `<td style="text-align:center;"><button type="button" onclick="removeSizePermanently(${id}, '${v.color.replace(/'/g, "\\'")}', '${sz.replace(/'/g, "\\'")}', false)" style="background:#fee2e2;border:none;color:#ef4444;width:36px;height:36px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;margin:0 auto;">`;
                    h += `<i data-lucide="trash-2" style="width:15px;height:15px;"></i></button></td>`;
                    
                    h += `</tr>`;
                });
            });
        } else {
            const stockColor = data.stock === 0 ? '#ef4444' : data.stock <= 5 ? '#d97706' : '#16a34a';
            h += `<tr>`;
            h += `<td style="font-size:13px;font-weight:800;color:#111827;">Standard Product</td>`;
            h += `<td><span style="background:#f1f5f9;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:900;color:#94a3b8;">—</span></td>`;
            h += `<td style="text-align:center;"><span style="font-size:16px;font-weight:900;color:${stockColor};">${data.stock}</span></td>`;
            h += `<td style="text-align:center;"><div style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;border:2px solid #bfdbfe;border-radius:12px;padding:8px 14px;">`;
            h += `<span style="font-size:14px;font-weight:900;color:#3b82f6;">+</span>`;
            h += `<input type="number" min="0" value="0" class="r-qty-field r-fld" data-key="default" style="border:none;background:none;width:40px;text-align:center;font-size:16px;font-weight:900;color:#3b82f6;outline:none;" oninput="recalcRestock()">`;
            h += `</div></td>`;
            h += `<td></td>`;
            h += `</tr>`;
        }
        
        area.innerHTML = h + '</tbody></table>';
        lucide.createIcons();
    })
    .catch(err => {
        area.innerHTML = '<div style="text-align:center;padding:100px 40px;color:#ef4444;font-size:14px;font-weight:700;">Failed to load stock data. Please check your connection and try again.</div>';
    });
    
    document.getElementById('restockForm').action = '/admin/products/' + id + '/restock';
    document.getElementById('r_sum_display').innerText = '0';
    document.getElementById('r_sum_display').parentElement.style.borderColor = '#e2e8f0';
    modal.style.display = 'flex';
}

function closeRestockModal() { 
    document.getElementById('restockModal').style.display = 'none'; 
    document.body.classList.remove('modal-open');
}

/**
 * recalcRestock()
 * 
 * Runs every time admin types a number in 
 * any "+ Add Units" input field.
 * 
 * It adds up all the numbers across all rows 
 * and shows the total in the "New Units" display.
 * 
 * It also builds hidden form inputs so when 
 * the form submits, all the quantities are 
 * included in the POST request to the server.
 */
function recalcRestock() {
    let t = 0; let ins = '';
    document.querySelectorAll('.r-fld').forEach(f => {
        let v = parseInt(f.value) || 0; t += v;
        ins += `<input type="hidden" name="restock[${f.dataset.key}]" value="${v}">`;
    });
    document.getElementById('r_sum_display').innerText = t;
    const summaryBox = document.getElementById('r_sum_display').parentElement;
    if (t > 0) {
        summaryBox.style.background = '#f0fdf4';
        summaryBox.style.borderColor = '#bbf7d0';
        document.getElementById('r_sum_display').style.color = '#16a34a';
    } else {
        summaryBox.style.background = '#f8fafc';
        summaryBox.style.borderColor = '#e2e8f0';
        document.getElementById('r_sum_display').style.color = '#111827';
    }
    document.getElementById('r_inputs_hidden').innerHTML = ins;
}

function submitRestockNow() { if(confirm('Update stock?')) document.getElementById('restockForm').submit(); }

/**
 * removeSizePermanently()
 * 
 * Permanently deletes a size from a product variant.
 * This is different from restocking — this REMOVES 
 * the size entirely from the product.
 * 
 * Example: If "Blue / XS" has 0 stock and is no 
 * longer being sold, admin can remove it so it 
 * does not appear in the restock table anymore.
 * 
 * This sends a POST request to the server which 
 * removes the size from the variants JSON in 
 * the database and recalculates the total stock.
 * 
 * @param {number}  id         - Product ID
 * @param {string}  color      - Color name e.g. "Blue"
 * @param {string}  size       - Size name e.g. "XS"
 * @param {boolean} fromBanner - true if called from 
 *                               the inventory alerts banner,
 *                               false if from restock modal
 */
function removeSizePermanently(id, color, size, fromBanner) {
    if (!confirm(`Delete "${size}" forever?`)) return;
    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');
    fd.append('color', color);
    fd.append('size', size);
    fetch(`/admin/products/${id}/remove-size`, { method: 'POST', body: fd }).then(res => res.json()).then(res => {
        if (res.success) {
            const rowId = `r-row-${id}-${color.replace(/ /g,'_')}-${size}`;
            const bannerId = `alert-row-${id}-${color.replace(/ /g,'_')}-${size}`;
            if (document.getElementById(rowId)) document.getElementById(rowId).style.display = 'none';
            if (document.getElementById(bannerId)) document.getElementById(bannerId).style.display = 'none';
        }
    });
}

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
