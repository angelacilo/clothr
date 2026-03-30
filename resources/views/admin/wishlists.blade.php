@extends('layouts.admin')

@section('title', 'Wishlists')
@section('subtitle', 'Customer favorites and saved items')

@section('content')
<div class="wishlists-container">
    <!-- Top 10 Wishlisted Products -->
    <div class="card" style="margin-bottom: 32px;">
        <div style="padding: 24px; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 16px; font-weight: 700;">Top 10 Most Wishlisted Products</h3>
        </div>
        <div class="table-responsive">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border-color);">
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Product</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Category</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Price</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; text-align: right;">Total Saves</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $top)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; background: #f3f4f6;">
                                        @php $imgArr = is_string($top->product->images) ? json_decode($top->product->images, true) : $top->product->images; @endphp
                                        <img src="{{ !empty($imgArr) ? url($imgArr[0]) : '/placeholder.png' }}" alt="{{ $top->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <span style="font-weight: 600; color: var(--text-dark); font-size: 14px;">{{ $top->product->name }}</span>
                                </div>
                            </td>
                            <td style="padding: 16px; font-size: 14px; color: var(--text-medium);">{{ $top->product->category->name ?? 'Uncategorized' }}</td>
                            <td style="padding: 16px; font-size: 14px; color: var(--text-dark); font-weight: 600;">₱{{ number_format($top->product->price, 2) }}</td>
                            <td style="padding: 16px; font-size: 15px; font-weight: 800; color: var(--text-dark); text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 6px; background: #fef2f2; color: #ef4444; padding: 4px 12px; border-radius: 20px;">
                                    <i data-lucide="heart" style="width: 14px; fill: currentColor;"></i> {{ $top->total }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 24px; padding: 16px 24px;">
        <form action="{{ route('admin.wishlists') }}" method="GET" style="display: flex; gap: 16px; align-items: center;">
            <div style="position: relative; flex: 1;">
                <i data-lucide="search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 18px;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by customer name, email or product..." style="width: 100%; padding: 10px 12px 10px 48px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px;">
            </div>
            <div>
                <select name="category_id" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px; background: white; cursor: pointer;" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-dark" style="padding: 10px 24px;">Search</button>
            @if(request()->hasAny(['search', 'category_id']))
                <a href="{{ route('admin.wishlists') }}" class="btn btn-outline" style="text-decoration: none;">Clear</a>
            @endif
        </form>
    </div>

    <!-- All Wishlist Entries -->
    <div class="card">
        <div style="padding: 24px; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 16px; font-weight: 700;">All Customer Wishlists</h3>
        </div>
        <div class="table-responsive">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border-color);">
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Customer</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Product</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Category</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Price</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; text-align: right;">Date Saved</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wishlists as $wishlist)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; color: var(--text-dark); font-size: 14px;">{{ $wishlist->user->name }}</span>
                                    <span style="font-size: 12px; color: var(--text-light);">{{ $wishlist->user->email }}</span>
                                </div>
                            </td>
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 6px; overflow: hidden; background: #f3f4f6;">
                                        @php $imgArr = is_string($wishlist->product->images) ? json_decode($wishlist->product->images, true) : $wishlist->product->images; @endphp
                                        <img src="{{ !empty($imgArr) ? url($imgArr[0]) : '/placeholder.png' }}" alt="{{ $wishlist->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <span style="font-weight: 500; color: var(--text-dark); font-size: 13px;">{{ $wishlist->product->name }}</span>
                                </div>
                            </td>
                            <td style="padding: 16px; font-size: 13px; color: var(--text-medium);">{{ $wishlist->product->category->name ?? 'Uncategorized' }}</td>
                            <td style="padding: 16px; font-size: 13px; color: var(--text-dark); font-weight: 600;">₱{{ number_format($wishlist->product->price, 2) }}</td>
                            <td style="padding: 16px; font-size: 13px; color: var(--text-medium); text-align: right;">{{ $wishlist->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px 16px; text-align: center; color: var(--text-medium);">
                                <i data-lucide="heart" style="width: 32px; height: 32px; color: var(--text-light); margin-bottom: 12px;"></i>
                                <div style="font-size: 14px;">No wishlist entries found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 24px; border-top: 1px solid var(--border-color);">
            {{ $wishlists->links() }}
        </div>
    </div>
</div>
@endsection
