@extends('layouts.admin')

@section('title', 'Products')
@section('subtitle', 'Manage your product catalog')

@section('content')
<div class="products-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <span style="font-size: 14px; color: var(--text-medium); font-style: italic;">Add, edit, and archive products</span>
        <button class="btn btn-dark" onclick="openAddProductModal()">Add Product</button>
    </div>

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
                    <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 16px; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        {{ $product->description }}
                    </p>
                    <div style="margin-top: auto;">
                        <span style="font-size: 18px; font-weight: 800; color: var(--primary); display: block; margin-bottom: 20px;">₱{{ number_format($product->price, 2) }}</span>
                        <div style="display: flex; gap: 8px;">
                            <!-- Data-product attribute is much safer than JSON inside a string -->
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
</div>

<!-- ═══════════════════ ADD MODAL ═══════════════════ -->
<div id="addProductModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding:20px;">
    <div class="card" style="width:560px; padding:32px; max-height:90vh; overflow-y:auto;">
        <h2 style="margin-bottom:24px;">Add New Product</h2>
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">NAME</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">CATEGORY</label>
                <select name="category_id" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">PRODUCT IMAGE</label>
                <input type="file" name="image" accept="image/*" style="width:100%; padding:8px; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">PRICE (&#8369;)</label>
                    <input type="number" step="0.01" name="price" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;" required>
                </div>
                <div id="addStockField">
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">STOCK</label>
                    <input type="number" name="stock" id="addProdStock" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
            </div>

            {{-- VARIANTS --}}
            <div style="border:1px solid var(--border-color); border-radius:10px; padding:20px; margin-bottom:16px;">
                <p style="font-size:12px; font-weight:700; letter-spacing:.06em; margin:0 0 18px;">VARIANTS</p>
                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:11px; font-weight:700; color:#777; letter-spacing:.05em; text-transform:uppercase; margin-bottom:8px;">Colors</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="addColorInput" placeholder="Add a color..."
                               style="flex:1; padding:9px 12px; border:1px solid var(--border-color); border-radius:8px; font-size:13px; box-sizing:border-box;"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();addVariantColor();}">
                        <button type="button" class="btn btn-outline" style="padding:9px 16px; font-size:13px;" onclick="addVariantColor()">Add</button>
                    </div>
                    <div id="addColorTags" style="display:flex; flex-wrap:wrap; gap:6px; margin-top:10px;"></div>
                    <input type="hidden" name="variant_colors" id="addColorsHidden" value="[]">
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#777; letter-spacing:.05em; text-transform:uppercase; margin-bottom:8px;">Sizes</label>
                    <div id="addPresetSizes" style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px;">
                        @foreach(['XS','S','M','L','XL','XXL'] as $sz)
                            <button type="button" data-size="{{ $sz }}"
                                    style="padding:6px 14px; border:1px solid #ccc; border-radius:6px; font-size:12px; font-weight:600; background:#fff; color:#111; cursor:pointer;"
                                    onclick="togglePresetSize(this)">{{ $sz }}</button>
                        @endforeach
                    </div>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="addCustomSizeInput" placeholder="Custom size..."
                               style="flex:1; padding:9px 12px; border:1px solid var(--border-color); border-radius:8px; font-size:13px;"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomVariantSize();}">
                        <button type="button" class="btn btn-outline" onclick="addCustomVariantSize()">Add</button>
                    </div>
                    <div id="addCustomSizeTags" style="display:flex; flex-wrap:wrap; gap:6px; margin-top:10px;"></div>
                    <input type="hidden" name="variant_sizes" id="addSizesHidden" value="[]">
                </div>
            </div>

            {{-- VARIANT STOCK TABLE --}}
            <div id="addVariantStockSection" style="display:none; margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:10px;">VARIANT STOCK</label>
                <div style="border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">
                    <div id="addVariantRows"></div>
                </div>
                <input type="hidden" name="variant_stock" id="addVariantStockHidden" value="{}">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">DESCRIPTION</label>
                <textarea name="description" rows="3" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closeAddProductModal()">Cancel</button>
                <button type="submit" class="btn btn-dark">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════ EDIT MODAL ═══════════════════ -->
<div id="editProductModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding:20px;">
    <div class="card" style="width:560px; padding:32px; max-height:90vh; overflow-y:auto;">
        <h2 style="margin-bottom:24px;">Edit Product</h2>
        <form id="editProductForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">NAME</label>
                <input type="text" name="name" id="editProdName" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">CATEGORY</label>
                <select name="category_id" id="editProdCat" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">PRODUCT IMAGE</label>
                <input type="file" name="image" accept="image/*" style="width:100%;">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">PRICE (&#8369;)</label>
                    <input type="number" step="0.01" name="price" id="editProdPrice" style="width:100%; padding:10px;" required>
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">STOCK</label>
                    <input type="number" name="stock" id="editProdStock" style="width:100%; padding:10px;" required>
                </div>
            </div>

            {{-- VARIANTS --}}
            <div style="border:1px solid var(--border-color); border-radius:10px; padding:20px; margin-bottom:16px;">
                <p style="font-size:12px; font-weight:700; letter-spacing:.06em; margin:0 0 18px;">VARIANTS</p>
                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:11px; font-weight:700; color:#777; letter-spacing:.05em; text-transform:uppercase; margin-bottom:8px;">Colors</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="editColorInput" placeholder="Add a color..."
                               style="flex:1; padding:9px 12px; border:1px solid var(--border-color); border-radius:8px; font-size:13px;"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();editAddColor();}">
                        <button type="button" class="btn btn-outline" style="padding:9px 16px; font-size:13px;" onclick="editAddColor()">Add</button>
                    </div>
                    <div id="editColorTags" style="display:flex; flex-wrap:wrap; gap:6px; margin-top:10px;"></div>
                    <input type="hidden" name="variant_colors" id="editColorsHidden" value="[]">
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#777; letter-spacing:.05em; text-transform:uppercase; margin-bottom:8px;">Sizes</label>
                    <div id="editPresetSizes" style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px;">
                        @foreach(['XS','S','M','L','XL','XXL'] as $sz)
                            <button type="button" data-size="{{ $sz }}"
                                    style="padding:6px 14px; border:1px solid #ccc; border-radius:6px; font-size:12px; font-weight:600;"
                                    onclick="editTogglePresetSize(this)">{{ $sz }}</button>
                        @endforeach
                    </div>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="editCustomSizeInput" placeholder="Custom size..."
                               style="flex:1; padding:9px 12px; border:1px solid var(--border-color); border-radius:8px; font-size:13px;"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();editAddCustomSize();}">
                        <button type="button" class="btn btn-outline" onclick="editAddCustomSize()">Add</button>
                    </div>
                    <div id="editCustomSizeTags" style="display:flex; flex-wrap:wrap; gap:6px; margin-top:10px;"></div>
                    <input type="hidden" name="variant_sizes" id="editSizesHidden" value="[]">
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">DESCRIPTION</label>
                <textarea name="description" id="editProdDesc" rows="3" style="width:100%; border:1px solid var(--border-color); border-radius:8px;"></textarea>
            </div>
            <div style="margin-bottom:24px; display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <label style="display:flex; align-items:center; gap:8px; font-size:14px;">
                    <input type="checkbox" name="isFeatured" id="editProdFeatured"> Featured
                </label>
                <label style="display:flex; align-items:center; gap:8px; font-size:14px;">
                    <input type="checkbox" name="isOnSale" id="editProdSale"> On Sale
                </label>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closeEditProductModal()">Cancel</button>
                <button type="submit" class="btn btn-dark">Update Product</button>
            </div>
        </form>
    </div>
</div>

<script>
/* ═══ GLOBAL STATE ═══ */
var PRESET_SIZES = ['XS','S','M','L','XL','XXL'];
var varColors = [], varSizes = []; 
var editColors = [], editSizes = [];

/* ═══ MODAL CONTROLS ═══ */
function openAddProductModal() {
    resetAddVariants();
    document.getElementById('addProductModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeAddProductModal() {
    document.getElementById('addProductModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function handleEditClick(btn) {
    var raw = btn.getAttribute('data-json');
    try {
        var product = JSON.parse(raw);
        openEditProductModal(product);
    } catch(e) { console.error("Could not parse product JSON", e); }
}

function openEditProductModal(product) {
    document.getElementById('editProductForm').action = '/admin/products/' + product.id;
    document.getElementById('editProdName').value    = product.name;
    document.getElementById('editProdCat').value     = product.category_id;
    document.getElementById('editProdPrice').value   = product.price;
    document.getElementById('editProdStock').value   = product.stock;
    document.getElementById('editProdDesc').value    = product.description || '';
    document.getElementById('editProdFeatured').checked = !!product.isFeatured;
    document.getElementById('editProdSale').checked     = !!product.isOnSale;

    // Load arrays properly
    editColors = Array.isArray(product.colors) ? JSON.parse(JSON.stringify(product.colors)) : [];
    editSizes  = Array.isArray(product.sizes)  ? JSON.parse(JSON.stringify(product.sizes))  : [];

    editRenderColorTags();
    editRenderCustomSizeTags();
    
    // Reset buttons
    document.querySelectorAll('#editPresetSizes button').forEach(function(btn) {
        var on = editSizes.indexOf(btn.dataset.size) !== -1;
        btn.style.background = on ? '#111' : '#fff';
        btn.style.color      = on ? '#fff' : '#111';
        btn.style.borderColor= on ? '#111' : '#ccc';
    });

    // Populate hidden inputs immediately
    document.getElementById('editColorsHidden').value = JSON.stringify(editColors);
    document.getElementById('editSizesHidden').value  = JSON.stringify(editSizes);

    document.getElementById('editProductModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditProductModal() {
    document.getElementById('editProductModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

/* ═══ ADD MODAL LOGIC ═══ */
function addVariantColor() {
    var inp = document.getElementById('addColorInput'), val = inp.value.trim();
    if(!val) return;
    if(varColors.indexOf(val) !== -1) { inp.value=''; return; }
    varColors.push(val); inp.value=''; renderColorTags(); refreshVariantTable();
}
function removeVariantColor(val) {
    varColors = varColors.filter(function(c){return c!==val;});
    renderColorTags(); refreshVariantTable();
}
function renderColorTags() {
    var box = document.getElementById('addColorTags'); box.innerHTML = '';
    varColors.forEach(function(c) {
        var t = document.createElement('span');
        t.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:#f0f0f0;padding:4px 11px;border-radius:20px;font-size:12px;font-weight:600;';
        t.innerHTML = c + ' <button type="button" onclick="removeVariantColor(\''+c+'\')" style="border:none;background:none;cursor:pointer;">&times;</button>';
        box.appendChild(t);
    });
}
function togglePresetSize(btn) {
    var val = btn.dataset.size, idx = varSizes.indexOf(val);
    if(idx!==-1) { varSizes.splice(idx,1); btn.style.background='#fff'; btn.style.color='#111'; }
    else { varSizes.push(val); btn.style.background='#111'; btn.style.color='#fff'; }
    refreshVariantTable();
}
function addCustomVariantSize() {
    var inp = document.getElementById('addCustomSizeInput'), val = inp.value.trim();
    if(!val) return;
    if(varSizes.indexOf(val) !== -1) { inp.value=''; return; }
    varSizes.push(val); inp.value=''; renderCustomSizeTags(); refreshVariantTable();
}
function removeCustomVariantSize(val) {
    varSizes = varSizes.filter(function(s){return s!==val;});
    renderCustomSizeTags(); refreshVariantTable();
}
function renderCustomSizeTags() {
    var customs = varSizes.filter(function(s){return PRESET_SIZES.indexOf(s)===-1;});
    var box = document.getElementById('addCustomSizeTags'); box.innerHTML = '';
    customs.forEach(function(s) {
        var t = document.createElement('span');
        t.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:#f0f0f0;padding:4px 11px;border-radius:20px;font-size:12px;font-weight:600;';
        t.innerHTML = s + ' <button type="button" onclick="removeCustomVariantSize(\''+s+'\')" style="border:none;background:none;cursor:pointer;">&times;</button>';
        box.appendChild(t);
    });
}
function refreshVariantTable() {
    var s = document.getElementById('addVariantStockSection'), f = document.getElementById('addStockField'), r = document.getElementById('addVariantRows');
    if(varColors.length===0 || varSizes.length===0) { s.style.display='none'; f.style.display='block'; return; }
    s.style.display='block'; f.style.display='none'; r.innerHTML = '';
    varColors.forEach(function(c, ci) {
        varSizes.forEach(function(sz, si) {
            var id = 'vs_' + (c+'__'+sz).replace(/[^a-zA-Z0-9]/g,'_');
            var div = document.createElement('div');
            div.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 100px;padding:8px 14px;border-bottom:1px solid #eee;';
            div.innerHTML = '<span>'+c+'</span><span>'+sz+'</span><input type="number" value="0" id="'+id+'" style="width:70px;padding:4px;">';
            r.appendChild(div);
        });
    });
}
function resetAddVariants() {
    varColors = []; varSizes = []; 
    document.getElementById('addColorTags').innerHTML = '';
    document.getElementById('addCustomSizeTags').innerHTML = '';
    document.querySelectorAll('#addPresetSizes button').forEach(function(b){ b.style.background='#fff'; b.style.color='#111'; });
    refreshVariantTable();
}

/* ═══ EDIT MODAL LOGIC ═══ */
function editAddColor() {
    var inp = document.getElementById('editColorInput'), val = inp.value.trim();
    if(!val) return;
    if(editColors.indexOf(val) !== -1) { inp.value=''; return; }
    editColors.push(val); inp.value=''; editRenderColorTags();
    document.getElementById('editColorsHidden').value = JSON.stringify(editColors);
}
function editRemoveColor(val) {
    editColors = editColors.filter(function(c){return c!==val;});
    editRenderColorTags();
    document.getElementById('editColorsHidden').value = JSON.stringify(editColors);
}
function editRenderColorTags() {
    var box = document.getElementById('editColorTags'); box.innerHTML = '';
    editColors.forEach(function(c) {
        var t = document.createElement('span');
        t.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:#f0f0f0;padding:4px 11px;border-radius:20px;font-size:12px;font-weight:600;';
        t.innerHTML = c + ' <button type="button" onclick="editRemoveColor(\''+c+'\')" style="border:none;background:none;cursor:pointer;">&times;</button>';
        box.appendChild(t);
    });
}
function editTogglePresetSize(btn) {
    var val = btn.dataset.size, idx = editSizes.indexOf(val);
    if(idx!==-1) { editSizes.splice(idx,1); btn.style.background='#fff'; btn.style.color='#111'; }
    else { editSizes.push(val); btn.style.background='#111'; btn.style.color='#fff'; }
    document.getElementById('editSizesHidden').value = JSON.stringify(editSizes);
}
function editAddCustomSize() {
    var inp = document.getElementById('editCustomSizeInput'), val = inp.value.trim();
    if(!val) return;
    if(editSizes.indexOf(val) !== -1) { inp.value=''; return; }
    editSizes.push(val); inp.value=''; editRenderCustomSizeTags();
    document.getElementById('editSizesHidden').value = JSON.stringify(editSizes);
}
function editRemoveCustomSize(val) {
    editSizes = editSizes.filter(function(s){return s!==val;});
    editRenderCustomSizeTags();
    document.getElementById('editSizesHidden').value = JSON.stringify(editSizes);
    document.querySelectorAll('#editPresetSizes button').forEach(function(b){
        if(b.dataset.size===val){ b.style.background='#fff'; b.style.color='#111'; }
    });
}
function editRenderCustomSizeTags() {
    var customs = editSizes.filter(function(s){return PRESET_SIZES.indexOf(s)===-1;});
    var box = document.getElementById('editCustomSizeTags'); box.innerHTML = '';
    customs.forEach(function(s) {
        var t = document.createElement('span');
        t.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:#f0f0f0;padding:4px 11px;border-radius:20px;font-size:12px;font-weight:600;';
        t.innerHTML = s + ' <button type="button" onclick="editRemoveCustomSize(\''+s+'\')" style="border:none;background:none;cursor:pointer;">&times;</button>';
        box.appendChild(t);
    });
}

function serializeAll() {
    // Add form
    document.getElementById('addColorsHidden').value = JSON.stringify(varColors);
    document.getElementById('addSizesHidden').value  = JSON.stringify(varSizes);
    if(varColors.length && varSizes.length) {
        var st = {};
        varColors.forEach(function(c){
            varSizes.forEach(function(sz){
                var id = 'vs_' + (c+'__'+sz).replace(/[^a-zA-Z0-9]/g,'_');
                var inp = document.getElementById(id);
                st[c+'__'+sz] = inp ? (parseInt(inp.value)||0) : 0;
            });
        });
        document.getElementById('addVariantStockHidden').value = JSON.stringify(st);
    }

    // Edit form
    document.getElementById('editColorsHidden').value = JSON.stringify(editColors);
    document.getElementById('editSizesHidden').value  = JSON.stringify(editSizes);
}

/* ═══ LISTENERS ═══ */
document.addEventListener('DOMContentLoaded', function() {
    // Grid buttons
    document.querySelectorAll('.edit-product-btn').forEach(function(btn) {
        btn.addEventListener('click', function(){ handleEditClick(this); });
    });

    // Form submits
    document.querySelectorAll('form').forEach(function(f){
        f.addEventListener('submit', serializeAll);
    });
});
</script>
@endsection
