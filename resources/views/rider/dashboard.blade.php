@extends('layouts.admin')

@section('title', 'Rider Dashboard')
@section('subtitle', 'Manage your assigned deliveries')

@section('content')
<div class="rider-container">
    <!-- Filters -->
    <div class="card" style="margin-bottom: 24px; display: flex; gap: 12px; align-items: center; padding: 14px 20px;">
        <form method="GET" action="{{ route('rider.dashboard') }}" style="display: flex; gap: 12px; flex: 1; align-items: center;">
            <span style="font-size: 13px; font-weight: 700; color: var(--text-medium);">Filter Status:</span>
            <select name="status" onchange="this.form.submit()" style="padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 13px; color: var(--text-dark); background-color: white; cursor: pointer;">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Assigned</option>
                <option value="Processing" {{ $status == 'Processing' ? 'selected' : '' }}>To Prepare/Process</option>
                <option value="Shipped" {{ $status == 'Shipped' ? 'selected' : '' }}>In Transit / Shipped</option>
                <option value="Delivered" {{ $status == 'Delivered' ? 'selected' : '' }}>Completed</option>
            </select>
        </form>
    </div>

    <!-- Orders List -->
    <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
        @forelse($orders as $order)
            @php
                $customer = $order->customer_info;
                $isCourier = $order->delivery_type === 'courier';
                
                // Determine available actions based on status and delivery type
                $actions = [];
                if ($order->status === 'Pending') {
                    $actions[] = ['status' => 'Processing', 'label' => 'Accept Order', 'icon' => 'check', 'color' => '#3b82f6'];
                } elseif ($order->status === 'Processing') {
                    if ($isCourier) {
                        $actions[] = ['status' => 'Shipped', 'label' => 'Mark as Shipped', 'icon' => 'package', 'color' => '#a855f7'];
                    } else {
                        $actions[] = ['status' => 'Shipped', 'label' => 'Out for Delivery', 'icon' => 'truck', 'color' => '#a855f7'];
                    }
                } elseif ($order->status === 'Shipped' && !$isCourier) {
                    $actions[] = ['status' => 'Delivered', 'label' => 'Mark as Delivered', 'icon' => 'check-circle', 'color' => '#10b981'];
                }
            @endphp
            
            <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)'">
                <!-- Header -->
                <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: {{ $isCourier ? 'linear-gradient(to right, #faf5ff, white)' : 'linear-gradient(to right, #f0fdf4, white)' }};">
                    <div>
                        <span style="font-size: 14px; font-weight: 800; color: #111;">Order #{{ 1000 + $order->id }}</span>
                        <div style="font-size: 11px; color: var(--text-medium); margin-top: 2px;">{{ $order->created_at->format('M d, g:i A') }}</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 10px; padding: 3px 8px; border-radius: 20px; font-weight: 700; text-transform: uppercase; background: {{ $isCourier ? '#f3e8ff' : '#dcfce7' }}; color: {{ $isCourier ? '#6b21a8' : '#166534' }};">
                            {{ $isCourier ? 'Courier' : 'Direct Rider' }}
                        </span>
                        <div style="font-size: 12px; font-weight: 700; color: #3b82f6; margin-top: 4px;">{{ $order->status }}</div>
                    </div>
                </div>

                <!-- Body -->
                <div style="padding: 20px; flex: 1;">
                    <!-- Customer -->
                    <div style="margin-bottom: 16px; display: flex; gap: 12px; align-items: flex-start;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: #64748b;">
                            <i data-lucide="user" style="width: 20px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: #111;">{{ $customer['first_name'] ?? 'Guest' }} {{ $customer['last_name'] ?? '' }}</div>
                            <div style="font-size: 12px; color: var(--text-medium); margin-top: 2px;">{{ $customer['phone'] ?? 'No phone' }}</div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div style="margin-bottom: 16px; display: flex; gap: 12px; align-items: flex-start;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: #64748b;">
                            <i data-lucide="map-pin" style="width: 20px;"></i>
                        </div>
                        <div style="font-size: 12px; color: #475569; line-height: 1.5; flex: 1;">
                            {{ $customer['address_line_1'] ?? $customer['address'] ?? 'N/A' }}, 
                            {{ $customer['city'] ?? '' }}
                        </div>
                    </div>

                    @if($isCourier && $order->courier_name)
                        <!-- Courier Info -->
                        <div style="padding: 12px; border-radius: 10px; background: #fdf4ff; border: 1px solid #f5d0fe; margin-bottom: 16px;">
                            <div style="font-size: 11px; font-weight: 700; color: #a21caf; text-transform: uppercase; margin-bottom: 4px;">Courier Service</div>
                            <div style="font-size: 13px; font-weight: 700; color: #701a75;">{{ $order->courier_name }}</div>
                            @if($order->tracking_number)
                                <div style="font-size: 11px; color: #a21caf; margin-top: 2px; font-family: monospace;">Track: {{ $order->tracking_number }}</div>
                            @endif
                        </div>
                    @endif

                    <!-- Items Summary -->
                    <div style="font-size: 12px; color: var(--text-medium); border-top: 1px dashed #e2e8f0; padding-top: 12px;">
                        <strong>Items:</strong> {{ count($order->items) }} total (₱{{ number_format($order->total, 2) }})
                    </div>
                </div>

                <!-- Footer Actions -->
                @if(count($actions) > 0)
                    <div style="padding: 16px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; gap: 10px;">
                        @foreach($actions as $action)
                            <form method="POST" action="{{ route('rider.orders.status', $order->id) }}" style="flex: 1;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ $action['status'] }}">
                                <button type="submit" style="width: 100%; padding: 10px; border-radius: 8px; border: none; background: {{ $action['color'] }}; color: white; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.15s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">
                                    <i data-lucide="{{ $action['icon'] }}" style="width: 14px;"></i>
                                    {{ $action['label'] }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <div style="padding: 16px; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center;">
                        <span style="font-size: 12px; font-weight: 600; color: var(--text-medium);">No further actions available</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i data-lucide="package" style="width: 48px; height: 48px; color: var(--border-color); margin-bottom: 16px;"></i>
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">No Assigned Orders</h3>
                <p style="color: var(--text-medium); font-size: 14px;">You haven't been assigned any orders with the selected status.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection
