@extends('admin.layouts.app')

@section('page-title', 'Edit Category')
@section('page-subtitle', $category->category_name)

@section('content')
    <div class="form-card">
        <form action="{{ route('admin.categories.update', $category->category_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3 class="section-title">Category Information</h3>

                <div class="form-group">
                    <label for="category_name" class="form-label">Category Name *</label>
                    <input type="text" id="category_name" name="category_name" class="form-control @error('category_name') error @enderror" 
                           placeholder="Enter category name" value="{{ old('category_name', $category->category_name) }}" required>
                    @error('category_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gender_type" class="form-label">Gender Type *</label>
                    <select id="gender_type" name="gender_type" class="form-control @error('gender_type') error @enderror" required>
                        <option value="">Select gender type</option>
                        <option value="male" {{ old('gender_type', $category->gender_type) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender_type', $category->gender_type) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="unisex" {{ old('gender_type', $category->gender_type) === 'unisex' ? 'selected' : '' }}>Unisex</option>
                    </select>
                    @error('gender_type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
