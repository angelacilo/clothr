@extends('layouts.admin')

@section('title', 'Reviews')
@section('subtitle', 'Customer reviews & ratings')

@section('content')
<div class="card">
    <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 16px; font-weight: 700;">All Customer Reviews</h3>
    </div>
    <div class="table-responsive">
        <table class="admin-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid var(--border-color);">
                    <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Customer</th>
                    <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Product</th>
                    <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Review</th>
                    <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Status</th>
                    <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 16px;">
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600; color: var(--text-dark); font-size: 14px;">{{ $review->user->name ?? 'Unknown' }}</span>
                                <span style="font-size: 12px; color: var(--text-light);">{{ $review->user->email ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @if($review->product)
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 6px; overflow: hidden; background: #f3f4f6;">
                                    @php $imgArr = is_string($review->product->images) ? json_decode($review->product->images, true) : $review->product->images; @endphp
                                    <img src="{{ !empty($imgArr) ? url($imgArr[0]) : '/placeholder.png' }}" alt="{{ $review->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <span style="font-weight: 500; color: var(--text-dark); font-size: 13px;">{{ Str::limit($review->product->name, 25) }}</span>
                            </div>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; gap: 2px; color: #fbbf24; margin-bottom: 6px;">
                                @for($i=1; $i<=5; $i++)
                                    <i data-lucide="star" {{ $i<=$review->rating ? 'fill="currentColor"' : '' }} style="width: 14px;"></i>
                                @endfor
                            </div>
                            <div style="font-size: 13px; color: var(--text-secondary); max-width: 300px; white-space: normal; line-height: 1.5;">
                                {{ Str::limit($review->comment, 80) }}
                            </div>
                            <div style="font-size: 11px; color: var(--text-light); margin-top: 6px;">{{ $review->created_at->format('M d, Y') }}</div>
                        </td>
                        <td style="padding: 16px;">
                            @if($review->is_visible)
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #d1fae5; color: #065f46;">Published</span>
                            @else
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #fee2e2; color: #991b1b;">Hidden</span>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: right;">
                            <form action="{{ route('admin.reviews.toggle', $review->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn {{ $review->is_visible ? 'btn-outline' : 'btn-dark' }}" style="padding: 6px 12px; font-size: 12px;">
                                    {{ $review->is_visible ? 'Hide' : 'Publish' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="reviews-container" style="display: flex; align-items: center; justify-content: center; padding: 60px 0;">
                                <div style="text-align: center; max-width: 450px;">
                                    <div style="width: 90px; height: 90px; border: 2px dashed #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 32px;">
                                        <i data-lucide="star" style="width: 44px; height: 44px; color: #94a3b8;"></i>
                                    </div>
                                    <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 16px; color: var(--text-dark);">No Reviews Yet</h2>
                                    <p style="font-size: 15px; color: var(--text-medium); line-height: 1.6; padding: 0 20px;">
                                        Customer reviews and ratings will appear here once customers start reviewing your products.
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(count($reviews) > 0)
    <div style="padding: 24px; border-top: 1px solid var(--border-color);">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection
