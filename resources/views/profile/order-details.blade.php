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

    /* Order Progress Timeline */
    .order-progress { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; padding: 25px 20px 20px; background: #fafbfc; border: 1px solid #eee; border-radius: 10px; position: relative; }
    .progress-step { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2; flex: 1; }
    .progress-dot { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; background: #f1f5f9; transition: all 0.3s; }
    .progress-dot.active { box-shadow: 0 0 0 5px rgba(0,0,0,0.05); }
    .progress-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; color: #94a3b8; }
    .progress-label.active { color: #111; }
    .progress-bar-bg { position: absolute; top: 43px; left: 15%; right: 15%; height: 3px; background: #e2e8f0; z-index: 1; border-radius: 2px; }
    .progress-bar-fill { height: 100%; background: #111; border-radius: 2px; transition: width 0.5s ease; }

    /* Delivery Info Card */
    .delivery-info { display: flex; gap: 20px; margin-bottom: 25px; }
    .delivery-card { flex: 1; padding: 18px; border: 1px solid #eee; border-radius: 10px; background: white; }
    .delivery-card h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #999; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
    .delivery-card .value { font-size: 16px; font-weight: 800; color: #111; }
    .delivery-card .sub { font-size: 12px; color: #888; margin-top: 4px; }

    /* Order History Timeline */
    .history-container { margin-bottom: 30px; padding: 20px 24px; background: white; border: 1px solid #eee; border-radius: 10px; }
    .history-title { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px; color: #111; }
    .history-event { display: flex; gap: 16px; position: relative; }
    .history-dot-col { display: flex; flex-direction: column; align-items: center; width: 28px; }
    .history-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .history-line { width: 2px; flex: 1; background: #e2e8f0; margin: 4px 0; }
    .history-info { padding-bottom: 20px; }
    .history-label { font-size: 14px; font-weight: 700; }
    .history-date { font-size: 12px; color: #888; margin-top: 2px; }

    /* Track Button */
    .track-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #111; color: white; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; transition: background 0.2s; }
    .track-btn:hover { background: #333; }
@endsection

@section('content')
<div class="order-details-container">
    <div class="header-bar">
        <h1>Order Details: #{{ $order->id }}</h1>
        <a href="{{ route('profile.orders') }}" class="back-btn"><i data-lucide="chevron-left" size="16"></i> BACK TO ORDERS</a>
    </div>

    <!-- Order Progress Timeline -->
    @php
        $steps = ['Pending', 'Processing', 'Shipped', 'Delivered'];
        $isCancelled = $order->status === 'Cancelled';
        $currentIdx = array_search($order->status, $steps);
        if ($currentIdx === false) $currentIdx = -1;
        $progressPercent = $currentIdx >= 0 ? ($currentIdx / (count($steps) - 1) * 100) : 0;
    @endphp
    <div class="order-progress">
        <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: {{ $isCancelled ? 0 : $progressPercent }}%"></div></div>
        @if($isCancelled)
            <div style="width: 100%; text-align: center; position: relative; z-index: 2;">
                <div class="progress-dot active" style="margin: 0 auto 8px; background: #fee2e2;">
                    <i data-lucide="x-circle" style="width: 18px; color: #ef4444;"></i>
                </div>
                <div class="progress-label active" style="color: #ef4444;">Cancelled</div>
            </div>
        @else
            @foreach($steps as $i => $step)
                @php
                    $isActive = $i <= $currentIdx;
                    $stepColors = [
                        'Pending' => '#f59e0b',
                        'Processing' => '#3b82f6',
                        'Shipped' => '#a855f7',
                        'Delivered' => '#10b981',
                    ];
                    $stepIcons = [
                        'Pending' => 'clock',
                        'Processing' => 'settings',
                        'Shipped' => 'truck',
                        'Delivered' => 'check-circle',
                    ];
                    $clr = $isActive ? $stepColors[$step] : '#cbd5e1';
                @endphp
                <div class="progress-step">
                    <div class="progress-dot {{ $isActive ? 'active' : '' }}" style="background: {{ $isActive ? $clr . '20' : '#f1f5f9' }};">
                        <i data-lucide="{{ $stepIcons[$step] }}" style="width: 16px; color: {{ $isActive ? $clr : '#94a3b8' }};"></i>
                    </div>
                    <div class="progress-label {{ $isActive ? 'active' : '' }}" style="{{ $isActive ? 'color: ' . $clr : '' }}">{{ $step }}</div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Delivery Info -->
    <div class="delivery-info">
        <div class="delivery-card">
            <h4><i data-lucide="truck" style="width: 13px;"></i> Courier Service</h4>
            <div class="value">{{ $order->courier_name ?? 'Not yet assigned' }}</div>
            @if(!$order->courier_name)
                <div class="sub">Courier will be assigned soon</div>
            @endif
        </div>
        <div class="delivery-card">
            <h4><i data-lucide="hash" style="width: 13px;"></i> Tracking Number</h4>
            <div class="value" style="font-family: monospace; letter-spacing: 1px;">{{ $order->tracking_number ?? '—' }}</div>
            @if($order->tracking_number && $order->tracking_url)
                <div style="margin-top: 10px;">
                    <a href="{{ $order->tracking_url }}" target="_blank" class="track-btn">
                        <i data-lucide="external-link" style="width: 14px;"></i> Track Package
                    </a>
                </div>
            @elseif(!$order->tracking_number)
                <div class="sub">Will be provided upon shipping</div>
            @endif
        </div>
        <div class="delivery-card">
            <h4><i data-lucide="calendar" style="width: 13px;"></i> Order Date</h4>
            <div class="value">{{ $order->created_at->format('M d, Y') }}</div>
            <div class="sub">{{ $order->created_at->format('g:i A') }}</div>
        </div>
    </div>

    <!-- Order History Timeline -->
    @php
        $events = [];
        $events[] = ['label' => 'Order Placed', 'date' => $order->created_at, 'icon' => 'shopping-bag', 'color' => '#3b82f6'];
        if ($order->processing_at) $events[] = ['label' => 'Processing', 'date' => $order->processing_at, 'icon' => 'settings', 'color' => '#1e40af'];
        if ($order->shipped_at) {
            $shippedLabel = 'Shipped';
            if ($order->tracking_number) $shippedLabel .= ' (Tracking: ' . $order->tracking_number . ')';
            $events[] = ['label' => $shippedLabel, 'date' => $order->shipped_at, 'icon' => 'truck', 'color' => '#7c3aed'];
        }
        if ($order->delivered_at) $events[] = ['label' => 'Delivered', 'date' => $order->delivered_at, 'icon' => 'check-circle', 'color' => '#10b981'];
        if ($order->cancelled_at) $events[] = ['label' => 'Cancelled', 'date' => $order->cancelled_at, 'icon' => 'x-circle', 'color' => '#ef4444'];
    @endphp
    <div class="history-container">
        <div class="history-title">Order Timeline</div>
        @foreach($events as $idx => $ev)
            @php $isLast = $idx === count($events) - 1; @endphp
            <div class="history-event">
                <div class="history-dot-col">
                    <div class="history-dot" style="background: {{ $ev['color'] }}15;">
                        <i data-lucide="{{ $ev['icon'] }}" style="width: 14px; color: {{ $ev['color'] }};"></i>
                    </div>
                    @if(!$isLast)
                        <div class="history-line"></div>
                    @endif
                </div>
                <div class="history-info" style="{{ $isLast ? 'padding-bottom: 0;' : '' }}">
                    <div class="history-label" style="color: {{ $ev['color'] }};">{{ $ev['label'] }}</div>
                    <div class="history-date">{{ $ev['date']->format('M d, Y — g:i A') }}</div>
                </div>
            </div>
        @endforeach
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
