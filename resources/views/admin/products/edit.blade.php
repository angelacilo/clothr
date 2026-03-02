@extends('admin.layouts.app')

@section('page-title', 'Edit Product')
@section('page-subtitle', $product->name)

@section('content')
    <div class="form-card">
        <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3 class="section-title">Product Information</h3>

                <div class="form-group">
                    <label for="name" class="form-label">Product Name *</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') error @enderror" 
                           placeholder="Enter product name" value="{{ old('name', $product->name) }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" id="slug" name="slug" class="form-control" placeholder="leave blank to auto-generate" 
                           value="{{ old('slug', $product->slug) }}">
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description *</label>
                    <textarea id="description" name="description" class="form-control @error('description') error @enderror" 
                              rows="4" placeholder="Enter product description" required>{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Category *</label>
                        <select id="category_id" name="category_id" class="form-control @error('category_id') error @enderror" required>
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price" class="form-label">Price *</label>
                        <input type="number" id="price" name="price" class="form-control @error('price') error @enderror" 
                               placeholder="0.00" step="0.01" value="{{ old('price', $product->price) }}" required>
                        @error('price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sale_price" class="form-label">Sale Price</label>
                        <input type="number" id="sale_price" name="sale_price" class="form-control" 
                               placeholder="0.00" step="0.01" value="{{ old('sale_price', $product->sale_price) }}">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Inventory</h3>

                <div class="form-group">
                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="form-control @error('stock_quantity') error @enderror" 
                           placeholder="0" value="{{ old('stock_quantity', $product->inventory->available_qty ?? 0) }}" required>
                    @error('stock_quantity')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Media</h3>

                @if ($product->images->count() > 0)
                    <div class="images-preview">
                        <h4>Current Images</h4>
                        <div class="preview-grid">
                            @foreach ($product->images as $image)
                                <div class="preview-item">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label for="images" class="form-label">Add More Product Images</label>
                    <input type="file" id="images" name="images[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">You can upload additional images</small>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Settings</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status" class="form-label">Status *</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" {{ old('status', $product->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $product->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group checkbox-group">
                        <label for="is_featured" class="form-label">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured ? 1 : 0) ? 'checked' : '' }}>
                            Featured Product
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
