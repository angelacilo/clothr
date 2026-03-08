@extends('layouts.admin')

@section('title', 'Products')
@section('subtitle', 'Add, edit, and manage products')

@section('content')
<div class="products-container">
    <!-- Top Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <span style="font-size: 14px; color: var(--text-medium); font-style: italic;">Manage your product catalog</span>
        <div style="display: flex; gap: 12px;">
            <button class="btn btn-outline" style="display: flex; align-items: center; gap: 8px; border-color: var(--border-color); color: var(--text-dark);">
                <i data-lucide="upload" style="width: 18px;"></i>
                Bulk Import
            </button>
            <button class="btn btn-dark" style="display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus" style="width: 18px;"></i>
                Add Product
            </button>
        </div>
    </div>

    <!-- Search + Filter -->
    <div class="card" style="display: flex; gap: 16px; align-items: center; margin-bottom: 32px; padding: 16px 24px;">
        <div style="position: relative; flex: 1;">
            <i data-lucide="search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 18px;"></i>
            <input type="text" placeholder="Search products..." 
                   style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px;">
        </div>
        
        <select style="padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px; color: var(--text-dark); background-color: white; width: 220px;">
            <option>All Categories</option>
            <option>Dresses</option>
            <option>Tops</option>
            <option>Bottoms</option>
        </select>
    </div>

    <!-- Product Grid -->
    <div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 24px;">
        <!-- Product 1 -->
        <div class="card" style="padding: 0; overflow: hidden; position: relative;">
            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=400" style="width: 100%; height: 280px; object-fit: cover;">
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 15px; font-weight: 700;">Floral Summer Dress</h4>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-medium); text-transform: uppercase;">Dresses</span>
                </div>
                <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 16px; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    Beautiful floral print dress perfect for sunny days and outdoor events.
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span style="font-size: 18px; font-weight: 800; color: var(--primary);">$49.99</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-outline" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px;">
                        <i data-lucide="edit-3" style="width: 16px;"></i> Edit
                    </button>
                    <button class="btn" style="background-color: #fee2e2; color: #ef4444; padding: 8px; width: 40px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="trash-2" style="width: 16px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Product 2 -->
        <div class="card" style="padding: 0; overflow: hidden; position: relative;">
            <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400" style="width: 100%; height: 280px; object-fit: cover;">
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 15px; font-weight: 700;">Chic Midi Dress</h4>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-medium); text-transform: uppercase;">Dresses</span>
                </div>
                <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 16px; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    Elegant midi dress with a modern silhouette for office or evening wear.
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span style="font-size: 18px; font-weight: 800; color: var(--primary);">$59.99</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-outline" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px;">
                        <i data-lucide="edit-3" style="width: 16px;"></i> Edit
                    </button>
                    <button class="btn" style="background-color: #fee2e2; color: #ef4444; padding: 8px; width: 40px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="trash-2" style="width: 16px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Product 3 (On Sale) -->
        <div class="card" style="padding: 0; overflow: hidden; position: relative;">
            <span style="position: absolute; top: 12px; right: 12px; background-color: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700;">SALE</span>
            <img src="https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=400" style="width: 100%; height: 280px; object-fit: cover;">
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 15px; font-weight: 700;">Floral Ruffle Top</h4>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-medium); text-transform: uppercase;">Tops</span>
                </div>
                <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 16px; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    Trendy floral top with ruffle details. Adds a feminine touch to any outfit.
                </p>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <span style="font-size: 18px; font-weight: 800; color: #ef4444;">$34.99</span>
                    <span style="font-size: 14px; color: var(--text-light); text-decoration: line-through;">$49.99</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-outline" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px;">
                        <i data-lucide="edit-3" style="width: 16px;"></i> Edit
                    </button>
                    <button class="btn" style="background-color: #fee2e2; color: #ef4444; padding: 8px; width: 40px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="trash-2" style="width: 16px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Product 4 -->
        <div class="card" style="padding: 0; overflow: hidden; position: relative;">
            <img src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400" style="width: 100%; height: 280px; object-fit: cover;">
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 15px; font-weight: 700;">Casual Silk Blouse</h4>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-medium); text-transform: uppercase;">Tops</span>
                </div>
                <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 16px; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    Luxurious silk blouse for everyday elegance and comfort.
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span style="font-size: 18px; font-weight: 800; color: var(--primary);">$44.99</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-outline" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px;">
                        <i data-lucide="edit-3" style="width: 16px;"></i> Edit
                    </button>
                    <button class="btn" style="background-color: #fee2e2; color: #ef4444; padding: 8px; width: 40px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="trash-2" style="width: 16px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Product 5 -->
        <div class="card" style="padding: 0; overflow: hidden; position: relative;">
            <img src="https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400" style="width: 100%; height: 280px; object-fit: cover;">
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 15px; font-weight: 700;">High-Waist Denim Jeans</h4>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-medium); text-transform: uppercase;">Bottoms</span>
                </div>
                <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 16px; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    Classic high-waist jeans with comfortable stretch and vintage wash.
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span style="font-size: 18px; font-weight: 800; color: var(--primary);">$64.99</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-outline" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px;">
                        <i data-lucide="edit-3" style="width: 16px;"></i> Edit
                    </button>
                    <button class="btn" style="background-color: #fee2e2; color: #ef4444; padding: 8px; width: 40px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="trash-2" style="width: 16px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Product 6 (New) -->
        <div class="card" style="padding: 0; overflow: hidden; position: relative;">
            <span style="position: absolute; top: 12px; left: 12px; background-color: #3b82f6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700;">NEW</span>
            <img src="https://images.unsplash.com/photo-1539109132314-d4a8c62e41dc?w=400" style="width: 100%; height: 280px; object-fit: cover;">
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 15px; font-weight: 700;">Tailored Trousers</h4>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-medium); text-transform: uppercase;">Bottoms</span>
                </div>
                <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 16px; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    Professional tailored trousers for a polished look.
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span style="font-size: 18px; font-weight: 800; color: var(--primary);">$54.99</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-outline" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px;">
                        <i data-lucide="edit-3" style="width: 16px;"></i> Edit
                    </button>
                    <button class="btn" style="background-color: #fee2e2; color: #ef4444; padding: 8px; width: 40px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="trash-2" style="width: 16px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
