@extends('admin.layouts.app')

@section('page-title', 'Products')
@section('page-subtitle', 'Manage inventory')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <form action="{{ route('admin.products.index') }}" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}" class="search-input">
                <select name="category" class="filter-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add Product
            </a>
        </div>
    </div>

    <div class="products-grid">
        @forelse ($products as $product)
            <div class="product-card">
                <div class="product-image">
                    @if ($product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}">
                    @else
                        <div class="no-image">
                            <i class="bi bi-image"></i>
                        </div>
                    @endif
                </div>
                <div class="product-info">
                    <h3 class="product-name">{{ $product->name }}</h3>
                    <p class="product-category">{{ $product->category->category_name ?? 'N/A' }}</p>
                    <div class="product-price">
                        <span class="price">${{ number_format($product->price, 2) }}</span>
                    </div>
                    <div class="product-meta">
                        <span class="stock">Stock: {{ $product->inventory->available_qty ?? 0 }}</span>
                    </div>
                </div>
                <div class="product-actions">
                    <a href="{{ route('admin.products.edit', $product->product_id) }}" class="action-btn edit-btn" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete-btn" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-muted">
                <p>No products found</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        {{ $products->links() }}
    </div>
@endsection
