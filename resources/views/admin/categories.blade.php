@extends('layouts.admin')

@section('title', 'Categories')
@section('subtitle', 'Manage product categories')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="font-size: 18px; font-weight: 700;">All Categories</h3>
        <button class="btn btn-dark" onclick="openAddModal()">Add Category</button>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                <th style="padding: 12px; font-size: 14px; color: var(--text-medium);">NAME</th>
                <th style="padding: 12px; font-size: 14px; color: var(--text-medium);">SLUG</th>
                <th style="padding: 12px; font-size: 14px; color: var(--text-medium);">VISIBLE</th>
                <th style="padding: 12px; font-size: 14px; color: var(--text-medium);">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 12px; font-weight: 600;">{{ $category->name }}</td>
                    <td style="padding: 12px; color: var(--text-medium);">{{ $category->slug }}</td>
                    <td style="padding: 12px;">
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; background: {{ $category->isVisible ? '#dcfce7' : '#fee2e2' }}; color: {{ $category->isVisible ? '#166534' : '#991b1b' }};">
                            {{ $category->isVisible ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <button class="btn btn-outline" style="padding: 4px 8px; font-size: 12px;" onclick="openEditModal({{ $category->id }}, '{{ $category->name }}', {{ $category->isVisible ? 'true' : 'false' }})">Edit</button>
                        <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn" style="padding: 4px 8px; font-size: 12px; color: #ef4444;">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div id="addModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="card" style="width:400px; padding:32px;">
        <h2 style="margin-bottom:24px;">Add Category</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">NAME</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn btn-dark">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="card" style="width:400px; padding:32px;">
        <h2 style="margin-bottom:24px;">Edit Category</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; margin-bottom:8px;">NAME</label>
                <input type="text" name="name" id="editName" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px;" required>
            </div>
            <div style="margin-bottom:24px;">
                <label style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="isVisible" id="editVisible"> Visible on Storefront
                </label>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-dark">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }
    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }
    function openEditModal(id, name, visible) {
        document.getElementById('editForm').action = '/admin/categories/' + id;
        document.getElementById('editName').value = name;
        document.getElementById('editVisible').checked = visible;
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
@endsection
