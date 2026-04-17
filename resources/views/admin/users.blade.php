@extends('layouts.admin')

@section('title', 'Users')
@section('subtitle', 'All user accounts in the system')

@section('content')
<div class="users-container">
    <!-- Search Bar -->
    <div class="card" style="margin-bottom: 32px; padding: 16px 24px;">
        <div style="position: relative; width: 100%;">
            <i data-lucide="search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 18px;"></i>
            <input type="text" id="userSearch" placeholder="Search users by name or email..." oninput="filterUsers()"
                   style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px; background-color: white;">
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border-color);">
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">ID</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">User</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">Phone</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">Role</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px;">Joined Date</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; letter-spacing: 0.5px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="user-row" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 16px; font-size: 14px; color: var(--text-medium);">#{{ $user->id }}</td>
                    <td style="padding: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            @if($user->avatar)
                                <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#94a3b8" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                    </svg>
                                </div>
                            @endif
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 700; color: var(--text-dark); font-size: 14px;">{{ $user->name }}</span>
                                <span style="font-size: 12px; color: var(--text-light);">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px; font-size: 14px; color: var(--text-dark);">{{ $user->phone ?? '—' }}</td>
                    <td style="padding: 16px;">
                        @if($user->is_admin)
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #dbeafe; color: #1e40af; text-transform: uppercase;">Admin</span>
                        @elseif($user->is_rider)
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #f3e8ff; color: #6b21a8; text-transform: uppercase;">Rider</span>
                        @else
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #dcfce7; color: #166534; text-transform: uppercase;">Customer</span>
                        @endif
                    </td>
                    <td style="padding: 16px; font-size: 14px; color: var(--text-medium);">{{ $user->created_at->format('M d, Y') }}</td>
                    <td style="padding: 16px; text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                            @if(!$user->is_admin)
                                <form action="{{ route('admin.users.toggle-rider', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" style="background: none; border: none; color: {{ $user->is_rider ? '#6b21a8' : '#64748b' }}; cursor: pointer; padding: 4px;" title="{{ $user->is_rider ? 'Remove Rider Role' : 'Make Rider' }}">
                                        <i data-lucide="{{ $user->is_rider ? 'user-minus' : 'user-plus' }}" style="width: 18px; height: 18px;"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px;" title="Delete User">
                                        <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                                    </button>
                                </form>
                            @else
                                <span style="color: var(--text-light); cursor: not-allowed; padding: 4px;" title="Admins cannot be modified">
                                    <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->isEmpty())
        <div style="text-align: center; padding: 60px 0; color: var(--text-medium);">
            <i data-lucide="users" style="width: 48px; height: 48px; color: var(--text-light); margin-bottom: 16px;"></i>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">No users found</h3>
        </div>
    @endif

    <div style="padding: 24px; border-top: 1px solid var(--border-color);">
        {{ $users->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterUsers() {
        const q = document.getElementById('userSearch').value.toLowerCase();
        document.querySelectorAll('.user-row').forEach(row => {
            const name = row.getAttribute('data-name');
            const email = row.getAttribute('data-email');
            row.style.display = (name.includes(q) || email.includes(q)) ? '' : 'none';
        });
    }
</script>
@endsection
