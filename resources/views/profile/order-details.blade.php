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
        $status = strtolower($order->status);
        $steps = ['pending', 'processing', 'shipped', 'out_for_delivery', 'delivered'];
        $isCancelled = $status === 'cancelled';
        $currentIdx = array_search($status, $steps);
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
            @foreach($steps as $i => $stepKey)
                @php
                    $isActive = $i <= $currentIdx;
                    $stepNames = [
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'out_for_delivery' => 'Out for delivery',
                        'delivered' => 'Delivered',
                    ];
                    $stepColors = [
                        'pending' => '#f59e0b',
                        'processing' => '#3b82f6',
                        'shipped' => '#a855f7',
                        'out_for_delivery' => '#06b6d4',
                        'delivered' => '#10b981',
                    ];
                    $stepIcons = [
                        'pending' => 'clock',
                        'processing' => 'settings',
                        'shipped' => 'truck',
                        'out_for_delivery' => 'map-pin',
                        'delivered' => 'check-circle',
                    ];
                    $clr = $isActive ? $stepColors[$stepKey] : '#cbd5e1';
                @endphp
                <div class="progress-step" id="progress-step-{{ $stepKey }}" data-index="{{ $i }}">
                    <div class="progress-dot {{ $isActive ? 'active' : '' }}" style="background: {{ $isActive ? $clr . '20' : '#f1f5f9' }};">
                        <i data-lucide="{{ $stepIcons[$stepKey] }}" style="width: 16px; color: {{ $isActive ? $clr : '#94a3b8' }};"></i>
                    </div>
                    <div class="progress-label {{ $isActive ? 'active' : '' }}" style="{{ $isActive ? 'color: ' . $clr : '' }}">{{ $stepNames[$stepKey] }}</div>
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
        // Ordered Steps from top to bottom
        $orderedSteps = [];
        
        // Step 1: Placed
        $orderedSteps[] = [
            'key' => 'Order Placed',
            'is_complete' => true,
            'is_active' => $order->status === 'Pending',
            'icon' => 'shopping-bag',
            'title' => 'Order Placed',
            'desc' => 'Your order has been placed and is waiting for confirmation',
            'date' => $order->created_at,
            'color' => '#f59e0b'
        ];
        
        // Step 2: Processing
        if ($order->processing_at || in_array($status, ['processing', 'shipped', 'out_for_delivery', 'delivered'])) {
            $orderedSteps[] = [
                'key' => 'processing',
                'is_complete' => in_array($status, ['processing', 'shipped', 'out_for_delivery', 'delivered']),
                'is_active' => $status === 'processing',
                'icon' => 'clock',
                'title' => 'Processing',
                'desc' => 'Your order is being prepared by our team',
                'date' => $order->processing_at,
                'color' => '#3b82f6'
            ];
        } else {
            $orderedSteps[] = [
                'key' => 'processing',
                'is_complete' => false,
                'is_active' => false,
                'icon' => 'clock',
                'title' => 'Processing',
                'desc' => 'Your order is being prepared by our team',
                'date' => null,
                'color' => '#94a3b8'
            ];
        }
        
        // Step 3: Shipped
        if ($order->shipped_at || in_array($status, ['shipped', 'out_for_delivery', 'delivered'])) {
            $orderedSteps[] = [
                'key' => 'shipped',
                'is_complete' => in_array($status, ['shipped', 'out_for_delivery', 'delivered']),
                'is_active' => $status === 'shipped',
                'icon' => 'truck',
                'title' => 'Shipped',
                'desc' => 'Your order is on its way!',
                'date' => $order->shipped_at,
                'color' => '#a855f7'
            ];
        } else {
            $orderedSteps[] = [
                'key' => 'shipped',
                'is_complete' => false,
                'is_active' => false,
                'icon' => 'truck',
                'title' => 'Shipped',
                'desc' => 'Your order is on its way!',
                'date' => null,
                'color' => '#94a3b8'
            ];
        }

        // Step 3.5: Out For Delivery
        if ($order->out_for_delivery_at || in_array($status, ['out_for_delivery', 'delivered'])) {
            $orderedSteps[] = [
                'key' => 'out_for_delivery',
                'is_complete' => in_array($status, ['out_for_delivery', 'delivered']),
                'is_active' => $status === 'out_for_delivery',
                'icon' => 'map-pin',
                'title' => 'Out for delivery',
                'desc' => 'Our rider is on the way to your location!',
                'date' => $order->out_for_delivery_at ?? ($order->status === 'out_for_delivery' ? $order->updated_at : null),
                'color' => '#06b6d4'
            ];
        }
        
        // Step 4: Delivered
        if ($order->delivered_at || $status === 'delivered') {
            $orderedSteps[] = [
                'key' => 'delivered',
                'is_complete' => $status === 'delivered',
                'is_active' => $status === 'delivered',
                'icon' => 'check-circle',
                'title' => 'Delivered',
                'desc' => 'Your order has been delivered. Thank you for shopping with CLOTHR!',
                'date' => $order->delivered_at,
                'color' => '#10b981'
            ];
        } else {
            $orderedSteps[] = [
                'key' => 'delivered',
                'is_complete' => false,
                'is_active' => false,
                'icon' => 'check-circle',
                'title' => 'Delivered',
                'desc' => 'Your order has been delivered. Thank you for shopping with CLOTHR!',
                'date' => null,
                'color' => '#94a3b8'
            ];
        }
        
        // Step 5: Cancelled
        if ($order->status === 'Cancelled') {
            // override the others for cancelled state cleanly
            $orderedSteps = [];
            $orderedSteps[] = [
                'key' => 'Order Placed',
                'is_complete' => true,
                'is_active' => false,
                'icon' => 'shopping-bag',
                'title' => 'Order Placed',
                'desc' => 'Your order was placed successfully',
                'date' => $order->created_at,
                'color' => '#111'
            ];
            $orderedSteps[] = [
                'key' => 'Cancelled',
                'is_complete' => true,
                'is_active' => true,
                'icon' => 'x-circle',
                'title' => 'Cancelled',
                'desc' => 'Your order has been cancelled. Contact us if you have questions.',
                'date' => $order->cancelled_at,
                'color' => '#ef4444'
            ];
        }
    @endphp

    <style>
        .timeline-container { padding: 30px; background: white; border: 1px solid #eee; border-radius: 12px; margin-bottom: 30px; box-shadow: var(--shadow-sm); }
        .timeline-title { font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 30px; color: #111; }
        .timeline-item { display: flex; position: relative; gap: 20px; padding-bottom: 35px; }
        .timeline-item:last-child { padding-bottom: 0; }
        
        .timeline-item::before { content: ''; position: absolute; left: 19px; top: 40px; bottom: 0; width: 2px; }
        .timeline-item:last-child::before { display: none; }
        .timeline-item.completed::before { background: currentColor; opacity: 0.3; }
        .timeline-item.pending::before { background: #e2e8f0; border-right: 2px dashed #cbd5e1; width: 0; left: 18px; }

        .timeline-icon-wrap { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative; z-index: 2; transition: all 0.3s; }
        .timeline-item.completed .timeline-icon-wrap { background: currentColor; color: white !important; }
        .timeline-item.pending .timeline-icon-wrap { background: white; border: 2px solid #e2e8f0; color: #94a3b8 !important; }

        .timeline-item.active .timeline-icon-wrap {
            box-shadow: 0 0 0 4px rgba(0,0,0,0.05);
            animation: pulse-active 2s infinite;
        }

        @keyframes pulse-active {
            0% { box-shadow: 0 0 0 0 currentColor; }
            70% { box-shadow: 0 0 0 8px rgba(0,0,0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0,0,0, 0); }
        }

        .timeline-content { padding-top: 8px; flex-grow: 1; }
        .timeline-header { font-size: 15px; font-weight: 800; color: #111; }
        .timeline-item.pending .timeline-header { color: #94a3b8; }
        
        .timeline-desc { font-size: 13px; color: #64748b; margin-top: 4px; line-height: 1.5; }
        .timeline-time { font-size: 12px; color: #94a3b8; margin-top: 6px; font-weight: 500; }
        
        .timeline-item.cancelled .timeline-header { color: #ef4444; }
        .timeline-item.cancelled .timeline-icon-wrap { background: #ef4444; color: white !important; }
        .timeline-item.cancelled .timeline-desc { color: #ef4444; opacity: 0.8; }
    </style>

    <div class="timeline-container">
        <div class="timeline-title">Order Timeline</div>
        
        @foreach($orderedSteps as $step)
            @php
                $statusClass = 'pending';
                if ($step['is_complete']) $statusClass = 'completed';
                if ($step['key'] === 'Cancelled') $statusClass = 'cancelled';
                if ($step['is_active']) $statusClass .= ' active';
                $stepId = $step['key'] === 'Order Placed' ? 'placed' : $step['key'];
            @endphp
            <div class="timeline-item {{ $statusClass }}" id="timeline-{{ strtolower($stepId) }}" style="color: {{ $step['color'] }};" data-step="{{ $step['key'] }}">
                <div class="timeline-icon-wrap" style="{{ $step['key'] === 'Cancelled' ? '' : ($step['is_complete'] ? 'background:' . $step['color'] . ';' : '') }}">
                    <i data-lucide="{{ $step['icon'] }}" style="width: 18px; {{ $step['is_complete'] && $step['key'] !== 'Cancelled' ? 'color:white;' : '' }}"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-header">{{ $step['title'] }}</div>
                    <div class="timeline-desc" id="desc-{{ strtolower($stepId) }}">
                        {{ $step['desc'] }}
                        @if($step['key'] === 'shipped' && $step['is_complete'])
                            @if($order->courier_name)
                                <br><span style="font-weight:600; color:#111;">Courier:</span> {{ $order->courier_name }}
                            @endif
                            @if($order->tracking_number)
                                <br><span style="font-weight:600; color:#111;">Tracking:</span> {{ $order->tracking_number }}
                            @endif
                            @if($order->tracking_url)
                                <br><a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; gap:4px; margin-top:8px; font-size:12px; font-weight:700; color:#8b5cf6; text-decoration:none;">Track your package <i data-lucide="arrow-right" style="width:12px;"></i></a>
                            @endif
                        @endif
                    </div>
                    @if($step['date'])
                        <div class="timeline-time">{{ $step['date']->format('F j, Y \a\t g:i A') }}</div>
                    @else
                        <div class="timeline-time" style="display: none;"></div>
                    @endif

                    @if($step['key'] === 'delivered' && $order->delivery && $order->delivery->proof_of_delivery)
                        <div id="pod-container" style="margin-top: 15px; max-width: 300px;">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #999; margin-bottom: 5px;">Proof of Delivery</div>
                            <img src="{{ asset('storage/' . $order->delivery->proof_of_delivery) }}" alt="Proof of Delivery" style="width: 100%; border-radius: 8px; border: 1px solid #eee; box-shadow: 0 4px 12px rgba(0,0,0,0.05); cursor: pointer;" onclick="window.open(this.src)">
                        </div>
                    @endif
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
@section('extra_js')
<script>
    window.refreshShop = function() {
        setTimeout(() => { window.location.reload(); }, 1500);
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.Echo !== 'undefined') {
            window.Echo.private('user.{{ auth()->id() }}')
                .listen('.App\\Events\\OrderStatusUpdated', (e) => {
                    console.log('Order update received:', e);
                    if (e.order.id == {{ $order->id }}) {
                        updateRealTimeTimeline(e.order);
                    }
                });
        }
    });

    function updateRealTimeTimeline(order) {
        const status = order.status.toLowerCase();
        const steps = ['pending', 'processing', 'shipped', 'out_for_delivery', 'delivered'];
        const currentIdx = steps.indexOf(status);
        if (currentIdx === -1) return;

        // 1. Update Progress Bar
        const fill = document.querySelector('.progress-bar-fill');
        const percent = (currentIdx / (steps.length - 1)) * 100;
        fill.style.width = percent + '%';

        const stepColors = {
            'pending': '#f59e0b',
            'processing': '#3b82f6',
            'shipped': '#a855f7',
            'out_for_delivery': '#06b6d4',
            'delivered': '#10b981'
        };

        // 2. Update Progress Steps
        steps.forEach((stepKey, i) => {
            const stepEl = document.getElementById('progress-step-' + stepKey);
            if (!stepEl) return;
            
            const dot = stepEl.querySelector('.progress-dot');
            const label = stepEl.querySelector('.progress-label');
            const icon = dot.querySelector('i');
            const isActive = i <= currentIdx;
            const clr = isActive ? stepColors[stepKey] : '#cbd5e1';

            if (isActive) {
                dot.classList.add('active');
                dot.style.background = clr + '20';
                label.classList.add('active');
                label.style.color = clr;
                icon.style.color = clr;
            } else {
                dot.classList.remove('active');
                dot.style.background = '#f1f5f9';
                label.classList.remove('active');
                label.style.color = '';
                icon.style.color = '#94a3b8';
            }
        });

        // 3. Update Timeline Items
        steps.forEach((stepKey, i) => {
            const itemEl = document.getElementById('timeline-' + stepKey);
            if (!itemEl) return;

            const isActive = i === currentIdx;
            const isComplete = i <= currentIdx;
            const clr = stepColors[stepKey];
            
            itemEl.classList.remove('pending', 'completed', 'active');
            if (isComplete) itemEl.classList.add('completed');
            else itemEl.classList.add('pending');
            if (isActive) itemEl.classList.add('active');

            const iconWrap = itemEl.querySelector('.timeline-icon-wrap');
            const icon = iconWrap.querySelector('i');
            const timeEl = itemEl.querySelector('.timeline-time');

            if (isComplete) {
                iconWrap.style.background = clr;
                icon.style.color = 'white';
                if (isActive && !timeEl.innerText) {
                    const now = new Date();
                    timeEl.innerText = now.toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true }).replace(',', '') + ' at ' + now.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
                    // More precise format matching PHP
                    const options = { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
                    const formattedDate = new Intl.DateTimeFormat('en-US', options).format(now);
                    timeEl.innerText = formattedDate.replace(' at', ' at'); // Intl puts 'at' sometimes
                    timeEl.style.display = 'block';
                }
            }
        });

        // 4. Update Header and Table
        document.querySelectorAll('td').forEach(td => {
            if (td.innerText.toLowerCase() === '{{ strtolower($order->status) }}') {
                td.innerText = order.status;
            }
        });

        // Optional: Trigger a toast
        if (window.showToast) {
            window.showToast('Order status updated to ' + order.status, 'success');
        }
    }
</script>
@endsection
@endsection
