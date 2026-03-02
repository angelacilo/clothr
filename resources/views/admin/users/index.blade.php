@extends('admin.layouts.app')

@section('page-title', 'Users')
@section('page-subtitle', 'Manage users')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <form action="{{ route('admin.users.index') }}" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" class="search-input">
                <select name="role" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add User
            </a>
        </div>
    </div>

    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <span>{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <div class="role-select" data-user-id="{{ $user->user_id }}">
                                <select class="user-role-select" onchange="updateUserRole({{ $user->user_id }}, this.value)">
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                    <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->user_id) }}" class="action-btn edit-btn" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" style="display:inline;" 
                                  onsubmit="return {{ auth()->user()->user_id !== $user->user_id ? 'confirm(\'Are you sure?\')' : 'alert(\'Cannot delete your own account\'); return false;' }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete" {{ auth()->user()->user_id === $user->user_id ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $users->links() }}
    </div>

    @push('scripts')
        <script>
            function updateUserRole(userId, role) {
                fetch(`/admin/users/${userId}/role`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ role: role })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User role updated successfully!', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to update user role', 'error');
                });
            }

            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type}`;
                notification.textContent = message;
                document.querySelector('.content').prepend(notification);
                setTimeout(() => notification.remove(), 3000);
            }
        </script>
    @endpush
@endsection
