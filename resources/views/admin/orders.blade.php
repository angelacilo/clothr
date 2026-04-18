@extends('layouts.admin')

@section('title', 'Orders')
@section('subtitle', 'Manage customer orders & delivery')

@section('content')
<div class="orders-container">
    <!-- Row 1: Summary Cards -->
    <div class="grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 24px;">
        <div class="card" style="position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 50px; height: 50px; border-radius: 50%; background: rgba(59,130,246,0.08);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 13px; color: var(--text-medium); font-weight: 500;">Total Orders</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="shopping-bag" style="color: #fff; width: 16px;"></i>
                </div>
            </div>
            <div style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">{{ $orders->count() }}</div>
            <div style="font-size: 11px; color: #10b981; font-weight: 600;">All orders</div>
        </div>
        
        <div class="card" style="position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 50px; height: 50px; border-radius: 50%; background: rgba(245,158,11,0.08);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 13px; color: var(--text-medium); font-weight: 500;">Pending</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="clock" style="color: #fff; width: 16px;"></i>
                </div>
            </div>
            <div style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">{{ $orders->where('status', 'pending')->count() }}</div>
            <div style="font-size: 11px; color: #f97316; font-weight: 600;">Needs attention</div>
        </div>
        
        <div class="card" style="position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 50px; height: 50px; border-radius: 50%; background: rgba(168,85,247,0.08);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 13px; color: var(--text-medium); font-weight: 500;">Shipped</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #a855f7, #7c3aed); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="truck" style="color: #fff; width: 16px;"></i>
                </div>
            </div>
            <div style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">{{ $orders->where('status', 'shipped')->count() }}</div>
            <div style="font-size: 11px; color: #a855f7; font-weight: 600;">In transit</div>
        </div>
        
        <div class="card" style="position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 50px; height: 50px; border-radius: 50%; background: rgba(16,185,129,0.08);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 13px; color: var(--text-medium); font-weight: 500;">Delivered</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="check-circle" style="color: #fff; width: 16px;"></i>
                </div>
            </div>
            <div style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">{{ $orders->where('status', 'delivered')->count() }}</div>
            <div style="font-size: 11px; color: #10b981; font-weight: 600;">Completed</div>
        </div>
        
        <div class="card" style="position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 50px; height: 50px; border-radius: 50%; background: rgba(16,185,129,0.08);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 13px; color: var(--text-medium); font-weight: 500;">Revenue</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #14b8a6, #0d9488); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="trending-up" style="color: #fff; width: 16px;"></i>
                </div>
            </div>
            <div style="font-size: 24px; font-weight: 800; margin-bottom: 4px;">₱{{ number_format($orders->sum('total'), 2) }}</div>
            <div style="font-size: 11px; color: #14b8a6; font-weight: 600;">Total revenue</div>
        </div>
    </div>

    <!-- Search + Filter Bar -->
    <div class="card" style="display: flex; gap: 12px; align-items: center; margin-bottom: 24px; padding: 14px 20px;">
        <form method="GET" action="{{ route('admin.orders') }}" style="display: flex; gap: 12px; flex: 1; align-items: center;">
            <div style="position: relative; flex: 1;">
                <i data-lucide="search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 16px;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order ID, customer name, or tracking..." 
                       style="width: 100%; padding: 10px 10px 10px 42px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 13px; transition: border-color 0.2s;"
                       onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='var(--border-color)'">
            </div>
            
            <select name="status" onchange="this.form.submit()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 13px; color: var(--text-dark); background-color: white; width: 170px; cursor: pointer;">
                <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Orders</option>
                <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $statusFilter == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ $statusFilter == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ $statusFilter == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <button type="submit" class="btn" style="display: flex; align-items: center; gap: 6px; background: #3b82f6; color: white; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer;">
                <i data-lucide="search" style="width: 14px;"></i>
                Search
            </button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="table-container" style="border-radius: 12px; overflow: visible; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleAll(this)"></th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Courier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $customer = $order->customer_info;
                        $items = $order->items;
                        $firstItem = isset($items[0]) ? $items[0] : null;
                        $statusColors = [
                            'pending' => ['bg' => '#fef9c3', 'color' => '#854d0e', 'icon' => 'clock'],
                            'processing' => ['bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => 'settings'],
                            'shipped' => ['bg' => '#f3e8ff', 'color' => '#6b21a8', 'icon' => 'truck'],
                            'delivered' => ['bg' => '#dcfce7', 'color' => '#166534', 'icon' => 'check-circle'],
                            'cancelled' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'x-circle'],
                        ];
                        $sc = $statusColors[$order->status] ?? ['bg' => '#f3f4f6', 'color' => '#374151', 'icon' => 'circle'];
                    @endphp
                    <tr style="cursor: pointer; transition: background 0.15s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor=''">
                        <td onclick="event.stopPropagation()"><input type="checkbox" class="order-check"></td>
                        <td onclick="openOrderModal({{ $order->id }})"><span style="color: var(--primary); font-weight: 700;">#{{ 1000 + $order->id }}</span></td>
                        <td onclick="openOrderModal({{ $order->id }})">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 13px;">
                                    {{ strtoupper(substr($customer['first_name'] ?? ($customer['firstName'] ?? 'G'), 0, 1)) }}
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; font-size: 13px;">{{ $customer['first_name'] ?? ($customer['firstName'] ?? 'Guest') }} {{ $customer['last_name'] ?? ($customer['lastName'] ?? '') }}</span>
                                    <span style="font-size: 11px; color: var(--text-medium);">{{ $customer['email'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td onclick="openOrderModal({{ $order->id }})">
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-size: 13px; font-weight: 600;" title="{{ $order->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}">
                                    {{ $order->created_at->diffForHumans() }}
                                </span>
                                <span style="font-size: 11px; color: var(--text-medium);">
                                    {{ $order->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}
                                </span>
                            </div>
                        </td>
                        <td onclick="openOrderModal({{ $order->id }})">
                            <div style="display: flex; align-items: center; gap: 4px;">
                                @if($firstItem)
                                    @php
                                        $product = isset($firstItem['id']) ? \App\Models\Product::find($firstItem['id']) : null;
                                        $imgSrc = $firstItem['image'] ?? null;
                                        if (!$imgSrc && $product && !empty($product->images)) {
                                            $imgSrc = is_array($product->images) ? $product->images[0] : $product->images;
                                        }
                                        $svgPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' fill='%23f1f5f9'/%3E%3Cpath d='M22 26c0-2.2 1.8-4 4-4s4 1.8 4 4-1.8 4-4 4-4-1.8-4-4zm18 12H24c-1.8 0-3.3-1.2-3.8-2.9L24 30l6.5 8.5 5.5-7.5 7.8 11.2c-.8 1.1-2.1 1.8-3.8 1.8z' fill='%23cbd5e1'/%3E%3C/svg%3E";
                                        $imgSrc = $imgSrc ?: $svgPlaceholder;
                                    @endphp
                                    <img src="{{ is_array($imgSrc) ? ($imgSrc[0] ?? $svgPlaceholder) : $imgSrc }}" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid #eee;" onerror="this.src='{{ $svgPlaceholder }}'">
                                @endif
                                <span style="font-size: 12px; color: var(--text-medium); margin-left: 4px;">{{ count($items) }} item{{ count($items) > 1 ? 's' : '' }}</span>
                            </div>
                        </td>
                        <td onclick="openOrderModal({{ $order->id }})" style="font-weight: 700;">₱{{ number_format($order->total, 2) }}</td>
                        <td onclick="event.stopPropagation()">
                            <div style="position: relative;" class="status-dropdown-container">
                                <button onclick="toggleStatusDropdown(this, {{ $order->id }})" class="status-badge-btn" style="background-color: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; display: flex; align-items: center; gap: 5px; width: fit-content; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; border: none; cursor: pointer; transition: transform 0.15s;">
                                    <i data-lucide="{{ $sc['icon'] }}" style="width: 12px;"></i> {{ ucfirst($order->status) }}
                                    <i data-lucide="chevron-down" style="width: 10px; margin-left: 2px;"></i>
                                </button>
                                <div class="status-dropdown" id="statusDrop-{{ $order->id }}" style="display: none; position: absolute; top: 100%; left: 0; z-index: 100; background: white; border: 1px solid var(--border-color); border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,0.12); padding: 6px; min-width: 160px; margin-top: 4px;">
                                    @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
                                        @php $ssc = $statusColors[$s]; @endphp
                                        <form method="POST" action="{{ route('admin.orders.status', $order->id) }}" style="margin:0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="{{ $s }}">
                                            <button type="submit" style="display: flex; align-items: center; gap: 8px; width: 100%; padding: 8px 12px; border: none; background: {{ $order->status == $s ? $ssc['bg'] : 'transparent' }}; cursor: pointer; border-radius: 6px; font-size: 12px; color: {{ $ssc['color'] }}; font-weight: {{ $order->status == $s ? '700' : '500' }}; transition: background 0.15s;" onmouseover="this.style.backgroundColor='{{ $ssc['bg'] }}'" onmouseout="this.style.backgroundColor='{{ $order->status == $s ? $ssc['bg'] : 'transparent' }}'">
                                                <i data-lucide="{{ $ssc['icon'] }}" style="width: 13px;"></i>
                                                {{ ucfirst($s) }}
                                                @if($order->status == $s)
                                                    <i data-lucide="check" style="width: 12px; margin-left: auto;"></i>
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                @if($order->courier_name)
                                    <span style="font-size: 11px; font-weight: 600; color: #6b21a8;">{{ $order->courier_name }}</span>
                                @endif
                                @if($order->tracking_number)
                                    <span style="font-size: 10px; color: var(--text-medium); font-family: monospace;">{{ $order->tracking_number }}</span>
                                @endif
                                @if(!$order->courier_name && !$order->tracking_number)
                                    <span style="font-size: 11px; color: var(--text-light);">—</span>
                                @endif
                            </div>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div style="display: flex; gap: 6px;">
                                <button onclick="openOrderModal({{ $order->id }})" title="View Details" style="width: 30px; height: 30px; border: 1px solid var(--border-color); border-radius: 6px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s;" onmouseover="this.style.borderColor='#3b82f6'; this.style.color='#3b82f6'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='inherit'">
                                    <i data-lucide="eye" style="width: 14px;"></i>
                                </button>
                                <button onclick="openCourierModal({{ $order->id }}, '{{ addslashes($order->courier_name ?? '') }}', '{{ addslashes($order->tracking_number ?? '') }}')" title="Assign Courier" style="width: 30px; height: 30px; border: 1px solid var(--border-color); border-radius: 6px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s;" onmouseover="this.style.borderColor='#a855f7'; this.style.color='#a855f7'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='inherit'">
                                    <i data-lucide="truck" style="width: 14px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 60px 0; color: var(--text-medium);">
                            <i data-lucide="inbox" style="width: 40px; height: 40px; margin-bottom: 12px; color: var(--border-color);"></i>
                            <p style="font-size: 15px; font-weight: 600; margin-bottom: 4px;">No orders found</p>
                            <p style="font-size: 13px;">Try adjusting your search or filter criteria</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>

<!-- ========== ORDER DETAIL MODAL ========== -->
<div id="orderModal" style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.45); backdrop-filter: blur(4px); align-items: center; justify-content: center;" onclick="if(event.target===this)closeOrderModal()">
    <div style="background: white; border-radius: 16px; width: 680px; max-height: 85vh; overflow-y: auto; box-shadow: 0 25px 60px rgba(0,0,0,0.2); animation: modalSlideIn 0.25s ease;">
        <!-- Modal Header -->
        <div style="padding: 24px 28px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; border-radius: 16px 16px 0 0; z-index: 10;">
            <div>
                <h2 id="modalOrderId" style="font-size: 18px; font-weight: 800; margin-bottom: 2px;">Order #—</h2>
                <span id="modalOrderDate" style="font-size: 12px; color: var(--text-medium);"></span>
            </div>
            <button onclick="closeOrderModal()" style="width: 32px; height: 32px; border-radius: 8px; border: none; background: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.15s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                <i data-lucide="x" style="width: 16px;"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 24px 28px;">
            <!-- Order Status Timeline -->
            <div style="margin-bottom: 28px;">
                <h3 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-medium); margin-bottom: 16px;">Order Progress</h3>
                <div id="modalTimeline" style="display: flex; align-items: center; justify-content: space-between; position: relative; padding: 0 10px;"></div>
            </div>

            <!-- Customer Info -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div style="background: #f8fafc; border-radius: 10px; padding: 18px;">
                    <h4 style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-medium); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="user" style="width: 13px;"></i> Customer
                    </h4>
                    <p id="modalName" style="font-weight: 700; font-size: 14px; margin-bottom: 4px;"></p>
                    <p id="modalEmail" style="font-size: 12px; color: var(--text-medium); margin-bottom: 2px;"></p>
                    <p id="modalPhone" style="font-size: 12px; color: var(--text-medium);"></p>
                </div>
                <div style="background: #f8fafc; border-radius: 10px; padding: 18px;">
                    <h4 style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-medium); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="map-pin" style="width: 13px;"></i> Shipping Address
                    </h4>
                    <p id="modalAddress" style="font-size: 13px; line-height: 1.6;"></p>
                </div>
            </div>

            <!-- Courier / Tracking Info -->
            <div style="background: linear-gradient(135deg, #faf5ff, #f0f9ff); border-radius: 10px; padding: 18px; margin-bottom: 24px; border: 1px solid #e9d5ff;">
                <h4 style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #7c3aed; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="truck" style="width: 13px;"></i> Delivery Information
                </h4>
                <div style="display: flex; gap: 24px; align-items: flex-end;">
                    <div>
                        <span style="font-size: 11px; color: var(--text-medium); display: block; margin-bottom: 2px;">Courier</span>
                        <span id="modalCourier" style="font-weight: 700; font-size: 14px;">—</span>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: var(--text-medium); display: block; margin-bottom: 2px;">Tracking Number</span>
                        <span id="modalTracking" style="font-weight: 700; font-size: 14px; font-family: monospace;">—</span>
                    </div>
                    <div id="modalTrackBtn" style="margin-left: auto; display: none;">
                        <a id="modalTrackLink" href="#" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; transition: opacity 0.15s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">
                            <i data-lucide="external-link" style="width: 13px;"></i> Track Package
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order History Timeline -->
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-medium); margin-bottom: 14px;">Order History</h3>
                <div id="modalHistory" style="display: flex; flex-direction: column; gap: 0;"></div>
            </div>

            <!-- Items -->
            <h3 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-medium); margin-bottom: 12px;">Order Items</h3>
            <div id="modalItems" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;"></div>

            <!-- Total -->
            <div style="display: flex; justify-content: flex-end; padding-top: 16px; border-top: 2px dashed #e2e8f0;">
                <div style="text-align: right;">
                    <span style="font-size: 13px; color: var(--text-medium);">Order Total</span>
                    <div id="modalTotal" style="font-size: 24px; font-weight: 800; color: #111;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== COURIER ASSIGNMENT MODAL ========== -->
<div id="courierModal" style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.45); backdrop-filter: blur(4px); align-items: center; justify-content: center;" onclick="if(event.target===this)closeCourierModal()">
    <div style="background: white; border-radius: 16px; width: 440px; box-shadow: 0 25px 60px rgba(0,0,0,0.2); animation: modalSlideIn 0.25s ease;">
        <div style="padding: 24px 28px 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #a855f7, #7c3aed); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="truck" style="color: white; width: 18px;"></i>
                </div>
                <div>
                    <h2 style="font-size: 16px; font-weight: 800;">Assign Courier</h2>
                    <span id="courierModalOrderId" style="font-size: 12px; color: var(--text-medium);"></span>
                </div>
            </div>
            <button onclick="closeCourierModal()" style="width: 32px; height: 32px; border-radius: 8px; border: none; background: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                <i data-lucide="x" style="width: 16px;"></i>
            </button>
        </div>
        <form id="courierForm" method="POST" style="padding: 24px 28px;">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 8px;">Courier Service</label>
                <select id="courierSelect" name="courier_name" onchange="updateTrackingPrefix(this)" style="width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid var(--border-color); outline: none; font-size: 14px; background: white; appearance: none; cursor: pointer; background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23666%22 stroke-width=%222%22><path d=%22m6 9 6 6 6-6%22/></svg>'); background-repeat: no-repeat; background-position: right 12px center;">
                    <option value="">Select Courier</option>
                    @foreach($couriers as $c)
                        <option value="{{ $c->name }}" data-code="{{ $c->code }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 24px;">
                <label style="font-size: 13px; font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 8px;">Tracking Number</label>
                <input type="text" id="trackingInput" name="tracking_number" placeholder="e.g. JT3849201832" 
                       style="width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid var(--border-color); outline: none; font-size: 14px; font-family: monospace; letter-spacing: 0.5px; transition: border-color 0.2s;"
                       onfocus="this.style.borderColor='#a855f7'" onblur="this.style.borderColor='var(--border-color)'">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeCourierModal()" style="flex: 1; padding: 12px; border-radius: 10px; border: 1px solid var(--border-color); background: white; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">Cancel</button>
                <button type="submit" style="flex: 1; padding: 12px; border-radius: 10px; border: none; background: linear-gradient(135deg, #a855f7, #7c3aed); color: white; font-weight: 700; font-size: 14px; cursor: pointer; transition: opacity 0.15s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">Save Courier Info</button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<script>
    window.addEventListener('load', function() {
        showToast('{{ session('success') }}');
    });
</script>
@endif

@if(session('error'))
<script>
    window.addEventListener('load', function() {
        const t = document.getElementById('toast');
        if (t) {
            t.style.background = '#ef4444'; // Red color for error
            showToast('{{ session('error') }}');
            setTimeout(() => {
                t.style.background = '#111'; // Reset back to default black
            }, 3500);
        } else {
            alert('{{ session('error') }}');
        }
    });
</script>
@endif

<style>
@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.timeline-step { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2; }
.timeline-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 6px; transition: all 0.3s; }
.timeline-dot.active { box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15); }
.timeline-dot.cancelled { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15); }
.timeline-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
.timeline-bar { position: absolute; top: 14px; left: 10%; right: 10%; height: 3px; background: #e2e8f0; z-index: 1; border-radius: 2px; }
.timeline-bar-fill { height: 100%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 2px; transition: width 0.4s ease; }
</style>
@endsection

@section('scripts')
<script>
    // Close status dropdowns on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.status-dropdown-container')) {
            document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');
        }
    });

    function toggleAll(master) {
        document.querySelectorAll('.order-check').forEach(cb => cb.checked = master.checked);
    }

    function toggleStatusDropdown(btn, orderId) {
        const drop = document.getElementById('statusDrop-' + orderId);
        const isVisible = drop.style.display === 'block';
        document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');
        drop.style.display = isVisible ? 'none' : 'block';
    }

    // Courier tracking URLs
    const courierTrackUrls = {
        'J&T Express': 'https://www.jtexpress.ph/trajectoryQuery?billcode=',
        'LBC Express': 'https://www.lbcexpress.com/track/?tracking_no=',
        'Ninja Van': 'https://www.ninjavan.co/en-ph/tracking?id=',
        'Flash Express': 'https://www.flashexpress.ph/fle/tracking?se=',
        'GoGo Xpress': 'https://gogoxpress.com/track?tracking_number=',
    };

    function formatDate(dateStr) {
        if (!dateStr) return null;
        return new Date(dateStr).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    // ========== ORDER DETAIL MODAL ==========
    function openOrderModal(orderId) {
        fetch('/admin/orders/' + orderId)
            .then(r => r.json())
            .then(order => {
                const ci = order.customer_info || {};
                document.getElementById('modalOrderId').textContent = 'Order #' + (1000 + order.id);
                document.getElementById('modalOrderDate').textContent = new Date(order.created_at).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                document.getElementById('modalName').textContent = (ci.first_name || ci.firstName || 'Guest') + ' ' + (ci.last_name || ci.lastName || '');
                document.getElementById('modalEmail').textContent = ci.email || 'N/A';
                document.getElementById('modalPhone').textContent = ci.phone || '';
                
                // Address
                let addr = '';
                if (ci.address) addr += ci.address;
                if (ci.barangay) addr += (addr ? ', ' : '') + ci.barangay;
                if (ci.city || ci.city_name) addr += (addr ? ', ' : '') + (ci.city_name || ci.city);
                if (ci.region || ci.region_name) addr += (addr ? ', ' : '') + (ci.region_name || ci.region);
                if (ci.zip) addr += ' ' + ci.zip;
                document.getElementById('modalAddress').textContent = addr || 'N/A';

                // Courier info
                document.getElementById('modalCourier').textContent = order.courier_name || '—';
                document.getElementById('modalTracking').textContent = order.tracking_number || '—';

                // Track Package button
                const trackBtn = document.getElementById('modalTrackBtn');
                const trackLink = document.getElementById('modalTrackLink');
                const baseUrl = courierTrackUrls[order.courier_name];
                if (baseUrl && order.tracking_number) {
                    trackLink.href = baseUrl + order.tracking_number;
                    trackBtn.style.display = 'block';
                } else {
                    trackBtn.style.display = 'none';
                }

                // Progress Timeline
                buildTimeline(order.status);

                // Order History Timeline
                buildHistory(order);

                // Items
                const itemsContainer = document.getElementById('modalItems');
                itemsContainer.innerHTML = '';
                const items = order.items || [];
                const svgPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' fill='%23f1f5f9'/%3E%3Cpath d='M22 26c0-2.2 1.8-4 4-4s4 1.8 4 4-1.8 4-4 4-4-1.8-4-4zm18 12H24c-1.8 0-3.3-1.2-3.8-2.9L24 30l6.5 8.5 5.5-7.5 7.8 11.2c-.8 1.1-2.1 1.8-3.8 1.8z' fill='%23cbd5e1'/%3E%3C/svg%3E";
                items.forEach(item => {
                    const qty = item.quantity || 1;
                    const price = item.price || 0;
                    itemsContainer.innerHTML += `
                        <div style="display: flex; align-items: center; gap: 14px; padding: 12px; background: #f8fafc; border-radius: 10px;">
                            <img src="${item.image || svgPlaceholder}" style="width: 52px; height: 52px; border-radius: 8px; object-fit: cover; border: 1px solid #eee;" onerror="this.src='${svgPlaceholder}'">
                            <div style="flex: 1;">
                                <div style="font-weight: 700; font-size: 13px;">${item.name || 'Product'}</div>
                                <div style="font-size: 11px; color: var(--text-medium); margin-top: 2px;">Size: ${item.size || 'N/A'} &bull; Qty: ${qty}</div>
                            </div>
                            <div style="font-weight: 700; font-size: 14px;">₱${(price * qty).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                        </div>
                    `;
                });

                // Total
                document.getElementById('modalTotal').textContent = '₱' + Number(order.total).toLocaleString('en-PH', {minimumFractionDigits: 2});

                document.getElementById('orderModal').style.display = 'flex';
                lucide.createIcons();
            });
    }

    function buildHistory(order) {
        const container = document.getElementById('modalHistory');
        const events = [];

        events.push({ label: 'Order Placed', date: order.created_at, icon: 'shopping-bag', color: '#3b82f6' });
        if (order.processing_at) events.push({ label: 'Processing', date: order.processing_at, icon: 'settings', color: '#1e40af' });
        if (order.shipped_at) {
            let shippedLabel = 'Shipped';
            if (order.tracking_number) shippedLabel += ' (Tracking: ' + order.tracking_number + ')';
            events.push({ label: shippedLabel, date: order.shipped_at, icon: 'truck', color: '#7c3aed' });
        }
        if (order.delivered_at) events.push({ label: 'Delivered', date: order.delivered_at, icon: 'check-circle', color: '#10b981' });
        if (order.cancelled_at) events.push({ label: 'Cancelled', date: order.cancelled_at, icon: 'x-circle', color: '#ef4444' });

        let html = '';
        events.forEach((ev, idx) => {
            const isLast = idx === events.length - 1;
            const dateStr = formatDate(ev.date);
            html += `
                <div style="display: flex; gap: 14px; position: relative;">
                    <div style="display: flex; flex-direction: column; align-items: center; width: 24px;">
                        <div style="width: 24px; height: 24px; border-radius: 50%; background: ${ev.color}15; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i data-lucide="${ev.icon}" style="width: 12px; color: ${ev.color};"></i>
                        </div>
                        ${!isLast ? '<div style="width: 2px; flex: 1; background: #e2e8f0; margin: 4px 0;"></div>' : ''}
                    </div>
                    <div style="padding-bottom: ${isLast ? '0' : '16px'};">
                        <div style="font-size: 13px; font-weight: 700; color: ${ev.color};">${ev.label}</div>
                        <div style="font-size: 11px; color: var(--text-medium);">${dateStr}</div>
                    </div>
                </div>`;
        });

        container.innerHTML = html;
    }

    function closeOrderModal() {
        document.getElementById('orderModal').style.display = 'none';
    }

    function buildTimeline(currentStatus) {
        const steps = ['pending', 'processing', 'shipped', 'delivered'];
        const isCancelled = currentStatus === 'cancelled';
        const currentIdx = steps.indexOf(currentStatus);
        const container = document.getElementById('modalTimeline');
        
        const colors = {
            'pending': '#f59e0b',
            'processing': '#3b82f6',
            'shipped': '#a855f7',
            'delivered': '#10b981'
        };
        const icons = {
            'pending': 'clock',
            'processing': 'settings',
            'shipped': 'truck',
            'delivered': 'check-circle'
        };

        let html = '<div class="timeline-bar"><div class="timeline-bar-fill" style="width: ' + (isCancelled ? '0%' : (currentIdx >= 0 ? (currentIdx / (steps.length - 1) * 100) + '%' : '0%')) + '"></div></div>';

        if (isCancelled) {
            html += `
                <div class="timeline-step" style="width: 100%; text-align: center;">
                    <div class="timeline-dot cancelled" style="background: #fee2e2; width: 36px; height: 36px;">
                        <i data-lucide="x-circle" style="width: 18px; color: #ef4444;"></i>
                    </div>
                    <span class="timeline-label" style="color: #ef4444;">Cancelled</span>
                </div>`;
        } else {
            steps.forEach((step, i) => {
                const isActive = i <= currentIdx;
                const isCurrent = i === currentIdx;
                const color = isActive ? colors[step] : '#cbd5e1';
                html += `
                    <div class="timeline-step">
                        <div class="timeline-dot ${isCurrent ? 'active' : ''}" style="background: ${isActive ? color + '20' : '#f1f5f9'};">
                            <i data-lucide="${icons[step]}" style="width: 14px; color: ${isActive ? color : '#94a3b8'};"></i>
                        </div>
                        <span class="timeline-label" style="color: ${isActive ? color : '#94a3b8'};">${step.charAt(0).toUpperCase() + step.slice(1)}</span>
                    </div>`;
            });
        }

        container.innerHTML = html;
    }

    // ========== COURIER MODAL ==========
    function openCourierModal(orderId, courierName, trackingNum) {
        document.getElementById('courierModalOrderId').textContent = 'Order #' + (1000 + orderId);
        document.getElementById('courierForm').action = '/admin/orders/' + orderId + '/courier';
        document.getElementById('courierSelect').value = courierName || '';
        document.getElementById('trackingInput').value = trackingNum || '';
        document.getElementById('courierModal').style.display = 'flex';
        lucide.createIcons();
    }

    function closeCourierModal() {
        document.getElementById('courierModal').style.display = 'none';
    }

    function updateTrackingPrefix(select) {
        const selectedOption = select.options[select.selectedIndex];
        const code = selectedOption.getAttribute('data-code');
        const trackingInput = document.getElementById('trackingInput');
        
        if (code && !trackingInput.value.startsWith(code)) {
            // If the input is empty or contains another prefix, we replace/set it
            // We keep the numeric part if it exists
            const currentVal = trackingInput.value;
            const numericPart = currentVal.replace(/^[A-Z]+/i, '');
            trackingInput.value = code + numericPart;
        } else if (!code && trackingInput.value) {
            // Optional: clear if "Select Courier" is chosen? Maybe not.
        }
    }

    // ESC key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeOrderModal();
            closeCourierModal();
        }
    });
</script>
@endsection
