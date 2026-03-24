@extends('layouts.admin')

@section('title', 'Products')
@section('subtitle', 'Manage your product catalog')

@section('content')
<div class="products-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <span style="font-size: 14px; color: var(--text-medium); font-style: italic;">Add, edit, and archive products</span>
        <button class="btn btn-dark" onclick="openAddProductModal()">Add Product</button>
    </div>

    @if(session('success'))
        <div style="background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:14px 18px; border-radius:8px; margin-bottom:20px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li style="font-size: 14px; font-weight: 500;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Product Grid -->
    <div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 24px;">
        @foreach($products as $product)
            <div class="card" style="padding: 0; overflow: hidden; position: relative; display: flex; flex-direction: column;">
                @if($product->isNew)
                    <span style="position: absolute; top: 12px; left: 12px; background-color: #3b82f6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; z-index: 2;">NEW</span>
                @endif
                @if($product->isOnSale)
                    <span style="position: absolute; top: 12px; right: 12px; background-color: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; z-index: 2;">SALE</span>
                @endif
                <div style="width: 100%; height: 280px; background: #f4f4f4; overflow: hidden;">
                    <img src="{{ $product->images[0] ?? '/placeholder.png' }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <h4 style="font-size: 15px; font-weight: 700;">{{ $product->name }}</h4>
                        <span style="font-size: 11px; font-weight: 600; color: var(--text-medium); text-transform: uppercase;">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    </div>
                    {{-- Variant color dots --}}
                    @if(!empty($product->variants))
                        <div style="display:flex; gap:5px; flex-wrap:wrap; margin-bottom:10px;">
                            @foreach($product->variants as $v)
                                <span title="{{ $v['color'] }}" style="width:14px;height:14px;border-radius:50%;border:1px solid rgba(0,0,0,.15);display:inline-block;background:{{ $v['colorHex'] ?? '#ccc' }};"></span>
                            @endforeach
                        </div>
                    @endif
                    <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 16px; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        {{ $product->description }}
                    </p>
                    <div style="margin-top: auto;">
                        <span style="font-size: 18px; font-weight: 800; color: var(--primary); display: block; margin-bottom: 20px;">₱{{ number_format($product->price, 2) }}</span>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-outline edit-product-btn"
                                    style="flex: 1; font-size: 12px; padding: 8px;"
                                    data-product-id="{{ $product->id }}"
                                    data-json="{{ json_encode($product) }}">Edit</button>
                            <form action="{{ route('admin.products.archive', $product->id) }}" method="POST" style="flex: 1;">
                                @csrf
                                <button class="btn btn-outline" style="width: 100%; font-size: 12px; padding: 8px; color: #f59e0b; border-color: #f59e0b;">Archive</button>
                            </form>
                            <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Really delete?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn" style="background-color: #fee2e2; color: #ef4444; padding: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="trash-2" style="width: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top: 24px;">
        {{ $products->withQueryString()->links() }}
    </div>
</div>

<!-- ═══ SHARED MODAL STYLES ═══ -->
<style>
.vb-table { width:100%; border-collapse:collapse; font-size:13px; }
.vb-table th { background:#f9fafb; padding:8px 12px; text-align:left; font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#555; border-bottom:1px solid #e5e7eb; }
.vb-table td { padding:8px 12px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.vb-table tr:last-child td { border-bottom:none; }
.vb-row-img { width:52px; height:52px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb; }
.vb-size-chip { display:inline-flex; align-items:center; gap:6px; background:#f3f4f6; border:1px solid #e5e7eb; border-radius:6px; padding:3px 8px; font-size:12px; font-weight:600; }
.vb-size-chip input { width:50px; padding:2px 4px; border:1px solid #d1d5db; border-radius:4px; font-size:12px; text-align:center; }
.color-hex-preview { width:28px; height:28px; border-radius:50%; border:2px solid #e5e7eb; display:inline-block; vertical-align:middle; margin-left:6px; }
.add-variant-row-btn { background:#f8fafc; border:1.5px dashed #cbd5e1; border-radius:10px; width:100%; padding:10px; font-size:13px; font-weight:600; color:#64748b; cursor:pointer; transition:.2s; margin-top:10px; }
.add-variant-row-btn:hover { background:#f1f5f9; border-color:#94a3b8; color:#334155; }
.variant-block { border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:12px; position:relative; background:#fafafa; }
.variant-block-header { display:flex; align-items:center; gap:10px; margin-bottom:14px; flex-wrap:wrap; }
.variant-remove-btn { position:absolute; top:12px; right:12px; background:none; border:none; font-size:18px; cursor:pointer; color:#9ca3af; line-height:1; }
.variant-remove-btn:hover { color:#ef4444; }
.size-grid { display:flex; gap:8px; flex-wrap:wrap; }
.size-stock-item { display:flex; flex-direction:column; align-items:center; gap:4px; }
.size-stock-item label { font-size:11px; font-weight:700; color:#374151; }
.size-stock-item input { width:56px; padding:5px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; text-align:center; }
.preset-size-toggle { padding:5px 12px; border:1.5px solid #e5e7eb; border-radius:6px; font-size:12px; font-weight:700; background:#fff; cursor:pointer; transition:.15s; }
.preset-size-toggle.on { background:#111; color:#fff; border-color:#111; }
</style>

<!-- ═══════════════════ ADD MODAL ═══════════════════ -->
<div id="addProductModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding:20px;">
    <div class="card" style="width:640px; padding:32px; max-height:92vh; overflow-y:auto;">
        <h2 style="margin-bottom:24px;">Add New Product</h2>
        <form id="addProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Basic Info -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div style="grid-column:1/-1;">
                    <label class="form-lbl">NAME</label>
                    <input type="text" name="name" class="form-inp" required>
                </div>
                <div>
                    <label class="form-lbl">CATEGORY</label>
                    <select name="category_id" class="form-inp" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-lbl">PRICE (₱)</label>
                    <input type="number" step="0.01" name="price" class="form-inp" required>
                </div>
            </div>

            <!-- General / fallback product image -->
            <div style="margin-bottom:16px;">
                <label class="form-lbl">PRODUCT IMAGE (fallback / no-variant)</label>
                <input type="file" name="image" accept="image/*" class="form-inp">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-lbl">DESCRIPTION</label>
                <textarea name="description" rows="2" class="form-inp"></textarea>
            </div>

            <!-- ── VARIANT BUILDER ── -->
            <div style="margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <label style="font-size:12px; font-weight:700; letter-spacing:.06em;">COLOR VARIANTS</label>
                    <span style="font-size:11px; color:#9ca3af;">Each variant can have its own image & stock per size</span>
                </div>

                <!-- Global sizes selector -->
                <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px; padding:14px; margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:10px;">Available Sizes</label>
                    <div id="addGlobalSizes" style="display:flex; flex-wrap:wrap; gap:7px;">
                        @foreach(['XS','S','M','L','XL','XXL'] as $sz)
                            <button type="button" class="preset-size-toggle" data-size="{{ $sz }}" onclick="addToggleGlobalSize(this)">{{ $sz }}</button>
                        @endforeach
                    </div>
                    <input type="text" id="addCustomSizeInput" placeholder="Custom size (e.g. 28x30)…" class="form-inp" style="margin-top:10px; font-size:13px;" onkeydown="if(event.key==='Enter'){event.preventDefault();addPushCustomSize();}">
                </div>

                <div id="addVariantBlocks"></div>
                <button type="button" class="add-variant-row-btn" onclick="addVariantBlock()">+ Add Color Variant</button>
            </div>

            <!-- Stock fallback (shown only when no variants) -->
            <div id="addStockFallback" style="margin-bottom:16px;">
                <label class="form-lbl">STOCK (when no variants)</label>
                <input type="number" name="stock" id="addStockInput" class="form-inp" value="0">
            </div>

            <!-- Flags -->
            <div style="display:flex; gap:20px; margin-bottom:24px; flex-wrap:wrap;">
                <label style="display:flex; align-items:center; gap:8px; font-size:14px;"><input type="checkbox" name="isFeatured"> Featured</label>
                <label style="display:flex; align-items:center; gap:8px; font-size:14px;"><input type="checkbox" name="isOnSale"> On Sale</label>
                <label style="display:flex; align-items:center; gap:8px; font-size:14px;"><input type="checkbox" name="isNew"> New Arrival</label>
            </div>

            <!-- Hidden payload -->
            <input type="hidden" name="variants_data" id="addVariantsDataHidden" value="[]">
            <input type="hidden" name="variant_colors" id="addColorsHidden" value="[]">
            <input type="hidden" name="variant_sizes"  id="addSizesHidden"  value="[]">

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closeAddProductModal()">Cancel</button>
                <button type="submit" class="btn btn-dark" onclick="serializeAddForm()">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════ EDIT MODAL ═══════════════════ -->
<div id="editProductModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding:20px;">
    <div class="card" style="width:640px; padding:32px; max-height:92vh; overflow-y:auto;">
        <h2 style="margin-bottom:24px;">Edit Product</h2>
        <form id="editProductForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div style="grid-column:1/-1;">
                    <label class="form-lbl">NAME</label>
                    <input type="text" name="name" id="editProdName" class="form-inp" required>
                </div>
                <div>
                    <label class="form-lbl">CATEGORY</label>
                    <select name="category_id" id="editProdCat" class="form-inp">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-lbl">PRICE (₱)</label>
                    <input type="number" step="0.01" name="price" id="editProdPrice" class="form-inp" required>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-lbl">PRODUCT IMAGE (fallback / no-variant)</label>
                <div id="editProdImgPreview" style="margin-bottom:8px; display:none;"><img style="height:60px; border-radius:6px; object-fit:cover;"></div>
                <input type="file" name="image" accept="image/*" class="form-inp">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-lbl">DESCRIPTION</label>
                <textarea name="description" id="editProdDesc" rows="2" class="form-inp"></textarea>
            </div>

            <!-- ── VARIANT BUILDER ── -->
            <div style="margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <label style="font-size:12px; font-weight:700; letter-spacing:.06em;">COLOR VARIANTS</label>
                    <span style="font-size:11px; color:#9ca3af;">Upload a new image to replace an existing one</span>
                </div>
                <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px; padding:14px; margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:10px;">Available Sizes</label>
                    <div id="editGlobalSizes" style="display:flex; flex-wrap:wrap; gap:7px;">
                        @foreach(['XS','S','M','L','XL','XXL'] as $sz)
                            <button type="button" class="preset-size-toggle" data-size="{{ $sz }}" onclick="editToggleGlobalSize(this)">{{ $sz }}</button>
                        @endforeach
                    </div>
                    <input type="text" id="editCustomSizeInput" placeholder="Custom size…" class="form-inp" style="margin-top:10px; font-size:13px;" onkeydown="if(event.key==='Enter'){event.preventDefault();editPushCustomSize();}">
                </div>
                <div id="editVariantBlocks"></div>
                <button type="button" class="add-variant-row-btn" onclick="editVariantBlock()">+ Add Color Variant</button>
            </div>

            <!-- Stock fallback -->
            <div id="editStockFallback" style="margin-bottom:16px; display:none;">
                <label class="form-lbl">STOCK (when no variants)</label>
                <input type="number" name="stock" id="editProdStock" class="form-inp">
            </div>

            <div style="margin-bottom:24px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; flex-wrap:wrap;">
                <label style="display:flex; align-items:center; gap:8px; font-size:14px;"><input type="checkbox" name="isFeatured" id="editProdFeatured"> Featured</label>
                <label style="display:flex; align-items:center; gap:8px; font-size:14px;"><input type="checkbox" name="isOnSale"   id="editProdSale">     On Sale</label>
                <label style="display:flex; align-items:center; gap:8px; font-size:14px;"><input type="checkbox" name="isNew"      id="editProdNew">      New Arrival</label>
            </div>

            <input type="hidden" name="variants_data" id="editVariantsDataHidden" value="[]">
            <input type="hidden" name="variant_colors" id="editColorsHidden" value="[]">
            <input type="hidden" name="variant_sizes"  id="editSizesHidden"  value="[]">

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closeEditProductModal()">Cancel</button>
                <button type="submit" class="btn btn-dark" onclick="serializeEditForm()">Update Product</button>
            </div>
        </form>
    </div>
</div>

<style>
.form-lbl { display:block; font-size:12px; font-weight:700; letter-spacing:.05em; margin-bottom:7px; color:#374151; }
.form-inp  { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box; background:#fff; }
.form-inp:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
select.form-inp { appearance:none; }
</style>

<script>
/* ══════════════════════════════════════════════════════
   SHARED STATE
══════════════════════════════════════════════════════ */
var addGlobalSizes  = [];   // e.g. ['S','M','L']
var addVariants     = [];   // [{color,colorHex,image,sizes:{S:0,M:0},_newImg:File|null}]

var editGlobalSizes = [];
var editVariants    = [];

/* ══════════════════════════════════════════════════════
   MODAL OPEN / CLOSE
══════════════════════════════════════════════════════ */
function openAddProductModal() {
    addGlobalSizes = []; addVariants = [];
    document.querySelectorAll('#addGlobalSizes .preset-size-toggle').forEach(function(b){ b.classList.remove('on'); });
    document.getElementById('addVariantBlocks').innerHTML = '';
    document.getElementById('addStockFallback').style.display = 'block';
    document.getElementById('addProductModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeAddProductModal() {
    document.getElementById('addProductModal').style.display = 'none';
    document.body.style.overflow = '';
}
function closeEditProductModal() {
    document.getElementById('editProductModal').style.display = 'none';
    document.body.style.overflow = '';
}

/* ══════════════════════════════════════════════════════
   GLOBAL SIZE TOGGLES
══════════════════════════════════════════════════════ */
function addToggleGlobalSize(btn) {
    var sz = btn.dataset.size;
    if (btn.classList.contains('on')) {
        btn.classList.remove('on');
        addGlobalSizes = addGlobalSizes.filter(function(s){return s!==sz;});
    } else {
        btn.classList.add('on');
        addGlobalSizes.push(sz);
    }
    refreshAllAddSizeGrids();
}
function addPushCustomSize() {
    var inp = document.getElementById('addCustomSizeInput'), v = inp.value.trim();
    if(!v || addGlobalSizes.indexOf(v)!==-1) { inp.value=''; return; }
    addGlobalSizes.push(v); inp.value='';
    refreshAllAddSizeGrids();
}
function refreshAllAddSizeGrids() {
    document.querySelectorAll('#addVariantBlocks .size-grid').forEach(function(grid) {
        var idx = +grid.dataset.variantIdx;
        renderSizeGrid(grid, addGlobalSizes, addVariants[idx] ? addVariants[idx].sizes : {});
    });
    document.getElementById('addStockFallback').style.display = (addVariants.length===0) ? 'block' : 'none';
}

function editToggleGlobalSize(btn) {
    var sz = btn.dataset.size;
    if (btn.classList.contains('on')) {
        btn.classList.remove('on');
        editGlobalSizes = editGlobalSizes.filter(function(s){return s!==sz;});
    } else {
        btn.classList.add('on');
        editGlobalSizes.push(sz);
    }
    refreshAllEditSizeGrids();
}
function editPushCustomSize() {
    var inp = document.getElementById('editCustomSizeInput'), v = inp.value.trim();
    if(!v || editGlobalSizes.indexOf(v)!==-1) { inp.value=''; return; }
    editGlobalSizes.push(v); inp.value='';
    refreshAllEditSizeGrids();
}
function refreshAllEditSizeGrids() {
    document.querySelectorAll('#editVariantBlocks .size-grid').forEach(function(grid) {
        var idx = +grid.dataset.variantIdx;
        renderSizeGrid(grid, editGlobalSizes, editVariants[idx] ? editVariants[idx].sizes : {});
    });
    document.getElementById('editStockFallback').style.display = (editVariants.length===0) ? 'block' : 'none';
}

/* ══════════════════════════════════════════════════════
   SIZE GRID RENDERER
══════════════════════════════════════════════════════ */
function renderSizeGrid(gridEl, sizes, existingSizes) {
    gridEl.innerHTML = '';
    if (sizes.length === 0) {
        gridEl.innerHTML = '<span style="font-size:12px;color:#9ca3af;">Select sizes above first.</span>';
        return;
    }
    sizes.forEach(function(sz) {
        var stock = (existingSizes && existingSizes[sz] != null) ? existingSizes[sz] : 0;
        var item = document.createElement('div');
        item.className = 'size-stock-item';
        item.innerHTML = '<label>' + sz + '</label><input type="number" min="0" value="' + stock + '" data-size="' + sz + '" onchange="updateStock(this)">';
        gridEl.appendChild(item);
    });
}

function updateStock(inp) {
    var grid = inp.closest('.size-grid');
    var idx  = +grid.dataset.variantIdx;
    var mode = grid.dataset.mode; // 'add' or 'edit'
    var arr  = mode === 'edit' ? editVariants : addVariants;
    if (arr[idx]) {
        arr[idx].sizes[inp.dataset.size] = parseInt(inp.value) || 0;
    }
}

/* ══════════════════════════════════════════════════════
   VARIANT BLOCK BUILDER — ADD FORM
══════════════════════════════════════════════════════ */
function addVariantBlock(preset) {
    var idx = addVariants.length;
    var v = preset || { color:'', colorHex:'#cccccc', image: null, sizes:{} };
    addVariants.push(v);

    var block = document.createElement('div');
    block.className = 'variant-block';
    block.id = 'add-vblock-' + idx;

    var imgPreviewHtml = v.image
        ? '<img id="add-img-prev-'+idx+'" src="'+v.image+'" style="height:56px;border-radius:8px;margin-top:8px;object-fit:cover;" onerror="this.style.display=\'none\'">'
        : '<img id="add-img-prev-'+idx+'" src="" style="height:56px;border-radius:8px;margin-top:8px;object-fit:cover;display:none;">';

    block.innerHTML = `
        <button type="button" class="variant-remove-btn" onclick="removeAddVariant(${idx})" title="Remove">×</button>
        <div class="variant-block-header">
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Color Name</label>
                <input type="text" value="${v.color}" placeholder="e.g. Midnight Blue"
                    style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;width:170px;"
                    oninput="addVariants[${idx}].color=this.value">
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Hex Color</label>
                <div style="display:flex;align-items:center;gap:8px;">
                    <input type="color" value="${v.colorHex}" style="width:40px;height:36px;padding:2px;border:1px solid #e5e7eb;border-radius:7px;cursor:pointer;"
                        oninput="addVariants[${idx}].colorHex=this.value">
                    <span style="font-size:12px;color:#6b7280;">Pick swatch color</span>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Variant Image</label>
                <input type="file" name="color_images[${idx}]" accept="image/*"
                    style="font-size:12px;padding:4px;"
                    onchange="previewAddVariantImg(this,${idx})">
                ${imgPreviewHtml}
            </div>
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:8px;">Stock Per Size</label>
            <div class="size-grid" data-variant-idx="${idx}" data-mode="add"></div>
        </div>
    `;

    document.getElementById('addVariantBlocks').appendChild(block);
    var grid = block.querySelector('.size-grid');
    renderSizeGrid(grid, addGlobalSizes, v.sizes);
    document.getElementById('addStockFallback').style.display = 'none';
}

function previewAddVariantImg(input, idx) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var prev = document.getElementById('add-img-prev-'+idx);
            if (prev) { prev.src=e.target.result; prev.style.display='block'; }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeAddVariant(idx) {
    // Mark as removed (don't splice to keep indices stable)
    addVariants[idx] = null;
    var block = document.getElementById('add-vblock-' + idx);
    if (block) block.remove();
    var active = addVariants.filter(function(v){return v!==null;});
    document.getElementById('addStockFallback').style.display = (active.length===0) ? 'block' : 'none';
}

/* ══════════════════════════════════════════════════════
   VARIANT BLOCK BUILDER — EDIT FORM
══════════════════════════════════════════════════════ */
function editVariantBlock(preset) {
    var idx = editVariants.length;
    var v = preset || { color:'', colorHex:'#cccccc', image: null, sizes:{} };
    editVariants.push(v);

    var block = document.createElement('div');
    block.className = 'variant-block';
    block.id = 'edit-vblock-' + idx;

    var imgPreviewHtml = v.image
        ? '<img id="edit-img-prev-'+idx+'" src="'+v.image+'" style="height:56px;border-radius:8px;margin-top:8px;object-fit:cover;" onerror="this.style.display=\'none\'">'
        : '<img id="edit-img-prev-'+idx+'" src="" style="height:56px;border-radius:8px;margin-top:8px;object-fit:cover;display:none;">';

    block.innerHTML = `
        <button type="button" class="variant-remove-btn" onclick="removeEditVariant(${idx})" title="Remove">×</button>
        <div class="variant-block-header">
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Color Name</label>
                <input type="text" value="${escHtml(v.color || '')}" placeholder="e.g. Midnight Blue"
                    style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;width:170px;"
                    oninput="editVariants[${idx}].color=this.value">
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Hex Color</label>
                <div style="display:flex;align-items:center;gap:8px;">
                    <input type="color" value="${escHtml(v.colorHex || '#cccccc')}" style="width:40px;height:36px;padding:2px;border:1px solid #e5e7eb;border-radius:7px;cursor:pointer;"
                        oninput="editVariants[${idx}].colorHex=this.value">
                    <span style="font-size:12px;color:#6b7280;">Pick swatch color</span>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Variant Image</label>
                <input type="file" name="color_images[${idx}]" accept="image/*"
                    style="font-size:12px;padding:4px;"
                    onchange="previewEditVariantImg(this,${idx})">
                ${imgPreviewHtml}
            </div>
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:8px;">Stock Per Size</label>
            <div class="size-grid" data-variant-idx="${idx}" data-mode="edit"></div>
        </div>
    `;

    document.getElementById('editVariantBlocks').appendChild(block);
    var grid = block.querySelector('.size-grid');
    renderSizeGrid(grid, editGlobalSizes, v.sizes || {});
    document.getElementById('editStockFallback').style.display = 'none';
}

function previewEditVariantImg(input, idx) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var prev = document.getElementById('edit-img-prev-'+idx);
            if (prev) { prev.src=e.target.result; prev.style.display='block'; }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeEditVariant(idx) {
    editVariants[idx] = null;
    var block = document.getElementById('edit-vblock-' + idx);
    if (block) block.remove();
    var active = editVariants.filter(function(v){return v!==null;});
    document.getElementById('editStockFallback').style.display = (active.length===0) ? 'block' : 'none';
}

/* ══════════════════════════════════════════════════════
   OPEN EDIT MODAL (from product card)
══════════════════════════════════════════════════════ */
function handleEditClick(btn) {
    try {
        openEditProductModal(JSON.parse(btn.getAttribute('data-json')));
    } catch(e) { console.error('Parse error', e); }
}

function openEditProductModal(product) {
    // Reset
    editVariants    = [];
    editGlobalSizes = [];
    document.getElementById('editVariantBlocks').innerHTML = '';
    document.querySelectorAll('#editGlobalSizes .preset-size-toggle').forEach(function(b){ b.classList.remove('on'); });
    document.getElementById('editCustomSizeInput').value = '';

    // Populate basic fields
    document.getElementById('editProductForm').action = '/admin/products/' + product.id;
    document.getElementById('editProdName').value   = product.name;
    document.getElementById('editProdCat').value    = product.category_id;
    document.getElementById('editProdPrice').value  = product.price;
    document.getElementById('editProdStock').value  = product.stock;
    document.getElementById('editProdDesc').value   = product.description || '';
    document.getElementById('editProdFeatured').checked = !!product.isFeatured;
    document.getElementById('editProdSale').checked     = !!product.isOnSale;
    document.getElementById('editProdNew').checked      = !!product.isNew;

    // Existing image preview
    var imgWrap = document.getElementById('editProdImgPreview');
    if (product.images && product.images[0] && product.images[0] !== '/placeholder.png') {
        imgWrap.querySelector('img').src = product.images[0];
        imgWrap.style.display = 'block';
    } else {
        imgWrap.style.display = 'none';
    }

    // Load existing variants
    var variants = Array.isArray(product.variants) ? product.variants : [];

    // Collect all sizes from variants
    var allSizes = [];
    variants.forEach(function(v) {
        Object.keys(v.sizes || {}).forEach(function(sz) {
            if (allSizes.indexOf(sz) === -1) allSizes.push(sz);
        });
    });

    // Also pick up legacy flat sizes
    if (Array.isArray(product.sizes)) {
        product.sizes.forEach(function(sz){
            if(allSizes.indexOf(sz)===-1) allSizes.push(sz);
        });
    }

    editGlobalSizes = allSizes;

    // Highlight preset size buttons
    document.querySelectorAll('#editGlobalSizes .preset-size-toggle').forEach(function(b) {
        if (editGlobalSizes.indexOf(b.dataset.size) !== -1) b.classList.add('on');
    });

    // Build variant blocks
    if (variants.length > 0) {
        variants.forEach(function(v) { editVariantBlock(v); });
    } else if (Array.isArray(product.colors) && product.colors.length > 0) {
        // Upgrade legacy flat colors
        product.colors.forEach(function(c) { editVariantBlock({color:c,colorHex:'#cccccc',image:null,sizes:{}}); });
    }

    document.getElementById('editStockFallback').style.display = (editVariants.filter(Boolean).length===0) ? 'block' : 'none';

    document.getElementById('editProductModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

/* ══════════════════════════════════════════════════════
   SERIALISE BEFORE SUBMIT
══════════════════════════════════════════════════════ */
function collectSizeStocks(idx, mode) {
    var grid = document.querySelector((mode==='edit'?'#editVariantBlocks':'#addVariantBlocks') + ' [data-variant-idx="'+idx+'"]');
    var sizes = {};
    if (grid) {
        grid.querySelectorAll('input[data-size]').forEach(function(inp) {
            sizes[inp.dataset.size] = parseInt(inp.value) || 0;
        });
    }
    return sizes;
}

function serializeAddForm() {
    var out = [];
    addVariants.forEach(function(v, idx) {
        if (!v) return;
        out.push({
            color:    v.color,
            colorHex: v.colorHex,
            image:    v.image,
            sizes:    collectSizeStocks(idx, 'add'),
        });
    });
    var colors = out.map(function(v){return v.color;});
    var sizes  = addGlobalSizes;
    document.getElementById('addVariantsDataHidden').value = JSON.stringify(out);
    document.getElementById('addColorsHidden').value       = JSON.stringify(colors);
    document.getElementById('addSizesHidden').value        = JSON.stringify(sizes);
}

function serializeEditForm() {
    var out = [];
    editVariants.forEach(function(v, idx) {
        if (!v) return;
        out.push({
            color:    v.color,
            colorHex: v.colorHex,
            image:    v.image,
            sizes:    collectSizeStocks(idx, 'edit'),
        });
    });
    var colors = out.map(function(v){return v.color;});
    var sizes  = editGlobalSizes;
    document.getElementById('editVariantsDataHidden').value = JSON.stringify(out);
    document.getElementById('editColorsHidden').value       = JSON.stringify(colors);
    document.getElementById('editSizesHidden').value        = JSON.stringify(sizes);
}

/* ══════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════ */
function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

/* ══════════════════════════════════════════════════════
   BOOTSTRAP
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-product-btn').forEach(function(btn) {
        btn.addEventListener('click', function(){ handleEditClick(this); });
    });
});
</script>
@endsection
