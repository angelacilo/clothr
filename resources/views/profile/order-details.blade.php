@extends('layouts.shop')

@section('title', 'Order Details')

@section('extra_css')
    .order-details-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header-bar h1 { font-size: 24px; font-weight: 800; text-transform: uppercase; }
    .back-btn { font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 4px; color: #000; text-decoration: none; }
    .back-btn:hover { text-decoration: underline; }

    .order-table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-color); margin-bottom: 20px; }
    .order-table th { background: #f9fafb; padding: 15px; font-size: 14px; font-weight: 700; color: #111; text-align: center; border-bottom: 1px solid var(--border-color); }
    .order-table td { padding: 20px; text-align: center; vertical-align: top; border-bottom: 1px solid var(--border-color); border-right: 1px solid #f0f0f0; }
    .order-table td:last-child { border-right: none; }
    
    .product-cell { display: flex; gap: 15px; text-align: left; }
    .product-img { width: 80px; height: 80px; object-fit: cover; background: #f8f9fa; border: 1px solid #eee; border-radius: 4px; }
    .product-info h3 { font-size: 14px; font-weight: 600; margin-bottom: 4px; color: #111; }
    .product-var { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; }
    .return-policy { font-size: 12px; color: #666; display: flex; align-items: flex-start; gap: 4px; }
    .return-policy i { margin-top: 2px; }
    .return-policy a { color: #2563eb; }

    .order-footer { background: #f9fafb; padding: 15px 20px; font-size: 13px; color: #333; border: 1px solid var(--border-color); border-top: none; display: flex; justify-content: space-between; align-items: center; }

@endsection

@section('content')
<div class="order-details-container">
    <div class="header-bar">
        <h1>Order Details: #{{ $order->id }}</h1>
        <a href="{{ route('profile.orders') }}" class="back-btn"><i data-lucide="chevron-left" size="16"></i> BACK TO ORDERS</a>
    </div>

    <table class="order-table">
        <thead>
            <tr>
                <th style="text-align: left; width: 40%;">Products</th>
                <th>Quantity</th>
                <th>SKU</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="product-cell">
                            <img src="{{ $item['image'] ?? '/placeholder.png' }}" class="product-img">
                            <div class="product-info">
                                <h3>{{ $item['name'] }}</h3>
                                <div class="product-var">{{ $item['size'] ?? 'one-size' }} / {{ $item['color'] ?? 'Standard' }}</div>
                                <div class="return-policy">
                                    <i data-lucide="info" size="14"></i>
                                    <span>Items in this category cannot be returned or exchanged, please refer to <a href="{{ route('info', 'returns') }}">Return Policy</a></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="vertical-align: middle;">{{ $item['quantity'] ?? 1 }}</td>
                    <td style="vertical-align: middle; color: #555; font-size: 13px;">SKU: {{ 'sj' . str_pad($order->id . rand(1000, 9999), 10, '0', STR_PAD_LEFT) }}</td>
                    <td style="vertical-align: middle;">
                        <span style="color: #ef4444; font-weight: 700;">₱{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</span>
                    </td>
                    <td style="vertical-align: middle; color: #333;">{{ $order->status }}</td>
                    <td style="vertical-align: middle;">
                        @if($order->status == 'Delivered')
                            <a href="{{ route('profile.reviews') }}" style="display: inline-block; padding: 6px 12px; border: 1px solid #111; color: #111; font-size: 12px; font-weight: 600; text-decoration: none; border-radius: 4px;">Review</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="order-footer">
        <div>Return Period: Returnable time: before {{ \Carbon\Carbon::parse($order->created_at)->addDays(30)->format('m/d/Y') }}</div>
        <div style="font-weight: 800; font-size: 16px;">Total Paid: ₱{{ number_format($order->total, 2) }}</div>
    </div>
</div>
@endsection
