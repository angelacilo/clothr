@extends('admin.layouts.app')

@section('page-title', 'Reviews')
@section('page-subtitle', 'Manage customer reviews')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search reviews..." class="search-input">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="page-header-right">
            <button class="btn btn-outline-primary" onclick="bulkApprove()">
                <i class="bi bi-check-circle"></i> Bulk Approve
            </button>
        </div>
    </div>

    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)">
                    </th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>
                            <input type="checkbox" class="review-checkbox" value="{{ $review->review_id }}">
                        </td>
                        <td>{{ $review->user->name ?? 'Guest' }}</td>
                        <td>{{ $review->product->name ?? 'Product' }}</td>
                        <td>
                            <div class="rating">
                                @for ($i = 0; $i < $review->rating; $i++)
                                    <span class="star">⭐</span>
                                @endfor
                            </div>
                        </td>
                        <td>
                            <p class="comment-text">{{ Str::limit($review->comment, 60) }}</p>
                        </td>
                        <td>{{ $review->created_at->format('M d, Y') }}</td>
                        <td>
                            <form action="{{ route('admin.reviews.destroy', $review->review_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
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
                        <td colspan="7" class="text-center text-muted">No reviews found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $reviews->links() }}
    </div>

    @push('scripts')
        <script>
            function toggleAllCheckboxes(checkbox) {
                const checkboxes = document.querySelectorAll('.review-checkbox');
                checkboxes.forEach(cb => cb.checked = checkbox.checked);
            }

            function bulkApprove() {
                const selected = document.querySelectorAll('.review-checkbox:checked');
                if (selected.length === 0) {
                    alert('Please select reviews to approve');
                    return;
                }
                alert('Bulk approve functionality works with reviews table status field.');
            }
        </script>
    @endpush
@endsection
