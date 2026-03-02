@extends('admin.layouts.app')

@section('page-title', 'Edit User')
@section('page-subtitle', $user->name)

@section('content')
    <div class="form-card">
        <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3 class="section-title">User Information</h3>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') error @enderror" 
                           placeholder="Enter full name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') error @enderror" 
                           placeholder="Enter email address" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Role *</label>
                    <select id="role" name="role" class="form-control @error('role') error @enderror" required>
                        <option value="">Select role</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    @error('role')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
