@extends('admin.layouts.app')

@section('page-title', 'Categories')
@section('page-subtitle', 'Manage product categories')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search categories..." class="search-input">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add Category
            </a>
        </div>
    </div>

    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Gender Type</th>
                    <th>Products Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->category_name }}</td>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($category->gender_type) }}</span>
                        </td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category->category_id) }}" class="action-btn edit-btn" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->category_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No categories found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $categories->links() }}
    </div>
@endsection
