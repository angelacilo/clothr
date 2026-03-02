@extends('admin.layouts.app')

@section('page-title', 'Create Product')
@section('page-subtitle', 'Add a new product')

@section('content')
    <div class="form-card">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-section">
                <h3 class="section-title">Product Information</h3>

                <div class="form-group">
                    <label for="name" class="form-label">Product Name *</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') error @enderror" 
                           placeholder="Enter product name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                
                <div class="form-group">
                    <label for="slug" class="form-label">Slug (auto-generated)</label>
                    <input type="text" id="slug" name="slug" class="form-control" placeholder="leave blank to auto-generate" value="{{ old('slug') }}">
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description *</label>
                    <textarea id="description" name="description" class="form-control @error('description') error @enderror" 
                              rows="4" placeholder="Enter product description" required>{{ old('description') }}</textarea>
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
                                <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
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
                               placeholder="0.00" step="0.01" value="{{ old('price') }}" required>
                        @error('price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sale_price" class="form-label">Sale Price</label>
                        <input type="number" id="sale_price" name="sale_price" class="form-control" 
                               placeholder="0.00" step="0.01" value="{{ old('sale_price') }}">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Inventory</h3>

                <div class="form-group">
                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="form-control @error('stock_quantity') error @enderror" 
                           placeholder="0" value="{{ old('stock_quantity') }}" required>
                    @error('stock_quantity')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Media</h3>

                <div class="form-group">
                    <label for="images" class="form-label">Product Images</label>
                    <input type="file" id="images" name="images[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">You can upload multiple images</small>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Settings</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status" class="form-label">Status *</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group checkbox-group">
                        <label for="is_featured" class="form-label">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            Featured Product
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
