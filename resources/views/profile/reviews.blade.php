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

    @if($deliveredOrders->count() > 0 && $status == 'awaiting')
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
                        <button class="review-btn" onclick="showToast('Thank you for reviewing!', 'success'); this.innerText='Reviewed'; this.disabled=true; this.style.opacity=0.5;">Review</button>
                        <div class="review-points">Comment to get the highest 36 points.</div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            No more orders pending review.
        </div>
    @endif
</div>
@endsection
