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
                            <button class="btn btn-outline" style="flex: 1; font-size: 12px; padding: 8px;" 
                                    onclick="openEditProductModal({{ json_encode($product) }})">Edit</button>
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

<!-- Add Modal -->
<div id="addProductModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding:20px;">
    <div class="card" style="width:500px; padding:32px; max-height: 90vh; overflow-y: auto;">
        <h2 style="margin-bottom:24px;">Add New Product</h2>
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">NAME</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">CATEGORY</label>
                <select name="category_id" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">PRICE ($)</label>
                    <input type="number" step="0.01" name="price" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">STOCK</label>
                    <input type="number" name="stock" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
                </div>
            </div>
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">DESCRIPTION</label>
                <textarea name="description" rows="3" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closeAddProductModal()">Cancel</button>
                <button type="submit" class="btn btn-dark">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editProductModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding:20px;">
    <div class="card" style="width:500px; padding:32px; max-height: 90vh; overflow-y: auto;">
        <h2 style="margin-bottom:24px;">Edit Product</h2>
        <form id="editProductForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">NAME</label>
                <input type="text" name="name" id="editProdName" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">CATEGORY</label>
                <select name="category_id" id="editProdCat" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">PRICE ($)</label>
                    <input type="number" step="0.01" name="price" id="editProdPrice" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">STOCK</label>
                    <input type="number" name="stock" id="editProdStock" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">DESCRIPTION</label>
                <textarea name="description" id="editProdDesc" rows="3" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;"></textarea>
            </div>
            <div style="margin-bottom:24px; display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
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
    function openAddProductModal() {
        document.getElementById('addProductModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeAddProductModal() {
        document.getElementById('addProductModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    function openEditProductModal(product) {
        document.getElementById('editProductForm').action = '/admin/products/' + product.id;
        document.getElementById('editProdName').value = product.name;
        document.getElementById('editProdCat').value = product.category_id;
        document.getElementById('editProdPrice').value = product.price;
        document.getElementById('editProdStock').value = product.stock;
        document.getElementById('editProdDesc').value = product.description;
        document.getElementById('editProdFeatured').checked = !!product.isFeatured;
        document.getElementById('editProdSale').checked = !!product.isOnSale;
        
        document.getElementById('editProductModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeEditProductModal() {
        document.getElementById('editProductModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>
@endsection
