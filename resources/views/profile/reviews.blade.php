@extends('layouts.shop')

@section('title', 'Review Center')

@section('extra_css')
    .review-center { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header-bar h1 { font-size: 28px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
    
    .tabs-wrap { display: flex; border-bottom: 2px solid #eee; margin-bottom: 20px; }
    .tab { flex: 1; text-align: center; padding: 15px; font-size: 16px; font-weight: 700; color: #555; text-decoration: none; position: relative; }
    .tab.active { color: #000; }
    .tab.active::after { content: ''; position: absolute; bottom: -2px; left: 20%; right: 20%; height: 2px; background: #000; }

    .notice { background: #fff5f5; color: #e53e3e; text-align: center; padding: 12px; font-size: 14px; font-weight: 600; margin-bottom: 20px; border-radius: 4px; }
    
    .table-header { display: grid; grid-template-columns: 1fr 1fr 1fr; background: #f9fafb; padding: 15px 0; font-weight: 700; font-size: 13px; text-align: center; color: #555; border-bottom: 1px solid #eee; }
    
    .order-box { border: 1px solid #eee; margin-bottom: 20px; }
    .order-header { background: #f9fafb; padding: 10px 15px; font-size: 13px; color: #666; border-bottom: 1px solid #eee; }
    .order-body { display: grid; grid-template-columns: 1fr 1fr 1fr; align-items: center; padding: 20px 0; text-align: center; }
    
    .product-col { position: relative; }
    .product-col img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
    .product-col-inner { display: flex; align-items: center; justify-content: center; gap: 15px; }
    
    .order-details-link { color: #2563eb; text-decoration: none; font-size: 14px; }
    .order-details-link:hover { text-decoration: underline; }
    
    .action-col { border-left: 1px solid #eee; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .review-btn { background: #000; color: #fff; border: none; padding: 10px 40px; font-size: 14px; font-weight: 700; cursor: pointer; }
    .review-points { color: #d97706; font-size: 12px; }

    .empty-state { text-align: center; padding: 60px 0; color: #888; font-size: 14px; }

    .back-btn { font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 4px; }
    .back-btn:hover { text-decoration: underline; }
@endsection

@section('content')
<div class="review-center">
    <div class="header-bar">
        <div style="width: 80px;"></div> <!-- Spacer -->
        <h1>Review Center</h1>
        <a href="{{ route('profile.orders') }}" class="back-btn"><i data-lucide="chevron-left" size="16"></i> BACK</a>
    </div>

    <div class="tabs-wrap">
        <a href="?status=awaiting" class="tab {{ $status == 'awaiting' ? 'active' : '' }}">Awaiting Review({{ $deliveredOrders->count() }})</a>
        <a href="?status=reviewed" class="tab {{ $status == 'reviewed' ? 'active' : '' }}">Reviewed</a>
    </div>

    <div class="notice">
        Follow review guide to earn more points
    </div>

    <div class="table-header">
        <div>Products</div>
        <div>Order</div>
        <div>Order operation</div>
    </div>

    @if($status == 'awaiting')
        @if($deliveredOrders->count() > 0)
            @foreach($deliveredOrders as $order)
                <div class="order-box">
                    <div class="order-header">
                        Order NO. {{ str_pad($order->id, 10, '0', STR_PAD_LEFT) }}LX
                    </div>
                    <div class="order-body">
                        <div class="product-col">
                            <div class="product-col-inner">
                                <i data-lucide="chevron-left" size="16" style="color: #ccc;"></i>
                                <div style="text-align: center;">
                                    <img src="{{ $order->items[0]['image'] ?? '/placeholder.png' }}">
                                    <div style="font-size: 12px; margin-top: 8px;">Review {{ count($order->items) }} Items</div>
                                </div>
                                <i data-lucide="chevron-right" size="16" style="color: #666;"></i>
                            </div>
                        </div>
                        <div style="border-left: 1px solid #eee;">
                            <a href="{{ route('profile.order', $order->id) }}" class="order-details-link">Order details</a>
                        </div>
                        <div class="action-col">
                            <button class="review-btn" onclick="openReviewModal({{ $order->items[0]['id'] }}, '{{ addslashes($order->items[0]['name']) }}')">Review</button>
                            <div class="review-points">Comment to get the highest 36 points.</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">No more orders pending review.</div>
        @endif
    @else
        <!-- Reviewed Tab -->
        @if(isset($reviews) && $reviews->count() > 0)
            @foreach($reviews as $review)
                <div class="order-box">
                    <div class="order-body" style="grid-template-columns: 1fr 2fr;">
                        <div class="product-col">
                            <div class="product-col-inner">
                                <img src="{{ $review->product->images[0] ?? '/placeholder.png' }}" style="width:80px; height:80px;">
                                <div style="text-align:left;">
                                    <div style="font-weight:700; font-size:14px;">{{ $review->product->name }}</div>
                                    <div style="color:#fbbf24; margin-top:5px;">
                                        @for($i=1; $i<=5; $i++)
                                            <i data-lucide="star" size="14" {{ $i <= $review->rating ? 'fill="currentColor"' : '' }}></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 0 20px; text-align:left; font-size:14px; color:#555;">
                            <div style="font-weight:600; margin-bottom:5px; color:#333;">Your Review:</div>
                            "{{ $review->comment ?? 'No comment provided.' }}"
                            <div style="font-size:12px; color:#999; margin-top:10px;">Submitted on {{ $review->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">You haven't written any reviews yet.</div>
        @endif
    @endif
</div>

<!-- Review Submission Modal -->
<div class="modal-overlay" id="reviewModal">
    <div class="login-modal" style="width: 500px;">
        <div class="modal-header">
            <span class="modal-title">Write a Review</span>
            <button class="modal-close" onclick="closeReviewModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="reviewProductInfo" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding: 10px; background: #f9fafb; border-radius: 8px;">
                <span id="reviewProductName" style="font-weight: 700; font-size: 14px;"></span>
            </div>
            
            <form id="reviewForm" onsubmit="submitReview(event)">
                @csrf
                <input type="hidden" id="reviewProductId">
                
                <div style="margin-bottom: 20px;">
                    <label class="form-label">Rating</label>
                    <div style="display: flex; gap: 8px; color: #fbbf24;" id="starRating">
                        <i data-lucide="star" class="star-btn" data-value="1" onclick="setRating(1)"></i>
                        <i data-lucide="star" class="star-btn" data-value="2" onclick="setRating(2)"></i>
                        <i data-lucide="star" class="star-btn" data-value="3" onclick="setRating(3)"></i>
                        <i data-lucide="star" class="star-btn" data-value="4" onclick="setRating(4)"></i>
                        <i data-lucide="star" class="star-btn" data-value="5" onclick="setRating(5)"></i>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" value="5">
                </div>

                <div style="margin-bottom: 20px;">
                    <label class="form-label">Your Comment</label>
                    <textarea name="comment" id="reviewComment" class="form-input" style="height: 120px; resize: none;" placeholder="Share your thoughts about this product..."></textarea>
                </div>

                <button type="submit" class="btn-blue" id="submitReviewBtn">Submit Review</button>
            </form>
        </div>
    </div>
</div>

<style>
    .star-btn { cursor: pointer; transition: transform 0.2s; }
    .star-btn:hover { transform: scale(1.2); }
    .star-btn.active { fill: currentColor; }
</style>

<script>
    let currentRating = 5;

    function openReviewModal(productId, productName) {
        document.getElementById('reviewProductId').value = productId;
        document.getElementById('reviewProductName').innerText = productName;
        document.getElementById('reviewModal').classList.add('show');
        setRating(5); // Default to 5 stars
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.remove('show');
        document.getElementById('reviewForm').reset();
    }

    function setRating(val) {
        currentRating = val;
        document.getElementById('ratingInput').value = val;
        const stars = document.querySelectorAll('.star-btn');
        stars.forEach(star => {
            if (parseInt(star.dataset.value) <= val) {
                star.setAttribute('fill', 'currentColor');
            } else {
                star.removeAttribute('fill');
            }
        });
    }

    function submitReview(e) {
        e.preventDefault();
        const productId = document.getElementById('reviewProductId').value;
        const rating = document.getElementById('ratingInput').value;
        const comment = document.getElementById('reviewComment').value;
        const btn = document.getElementById('submitReviewBtn');

        btn.disabled = true;
        btn.innerText = 'Submitting...';

        fetch(`/product/${productId}/review`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ rating, comment })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                closeReviewModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.error || 'Failed to submit review', 'error');
                btn.disabled = false;
                btn.innerText = 'Submit Review';
            }
        })
        .catch(err => {
            showToast('An error occurred. Please try again.', 'error');
            btn.disabled = false;
            btn.innerText = 'Submit Review';
        });
    }
</script>
</div>
@endsection
