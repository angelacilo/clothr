@extends('layouts.admin')

@section('title', 'Orders')
@section('subtitle', 'Manage customers orders')

@section('content')
<div class="orders-container">
    <!-- Row 1: Summary Cards -->
    <div class="grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 14px; color: var(--text-medium);">Today's Orders</span>
                <i data-lucide="arrow-up-right" style="color: #10b981; width: 16px;"></i>
            </div>
            <div style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">24</div>
            <div style="font-size: 12px; color: #10b981; font-weight: 600;">+12% from yesterday</div>
        </div>
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 14px; color: var(--text-medium);">Pending Actions</span>
                <i data-lucide="alert-circle" style="color: #f59e0b; width: 16px;"></i>
            </div>
            <div style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">7</div>
            <div style="font-size: 12px; color: #f97316; font-weight: 600;">Needs attention</div>
        </div>
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 14px; color: var(--text-medium);">Revenue Today</span>
                <i data-lucide="dollar-sign" style="color: #3b82f6; width: 16px;"></i>
            </div>
            <div style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">$4,521</div>
            <div style="font-size: 12px; color: #10b981; font-weight: 600;">+8% from yesterday</div>
        </div>
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="font-size: 14px; color: var(--text-medium);">Processing</span>
                <i data-lucide="refresh-cw" style="color: #3b82f6; width: 16px;"></i>
            </div>
            <div style="font-size: 28px; font-weight: 800; margin-bottom: 4px;">12</div>
            <div style="font-size: 12px; color: var(--text-medium);">Currently processing</div>
        </div>
    </div>

    <!-- Search + Filter Bar -->
    <div class="card" style="display: flex; gap: 16px; align-items: center; margin-bottom: 24px; padding: 16px 24px;">
        <div style="position: relative; flex: 1;">
            <i data-lucide="search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 18px;"></i>
            <input type="text" placeholder="Search by order ID, customer name, or email..." 
                   style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px;">
        </div>
        
        <select style="padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px; color: var(--text-dark); background-color: white; width: 180px;">
            <option>All Orders</option>
            <option>Processing</option>
            <option>Shipped</option>
            <option>Delivered</option>
        </select>
        
        <button class="btn btn-outline" style="display: flex; align-items: center; gap: 8px; border-color: var(--border-color); color: var(--text-dark);">
            <i data-lucide="filter" style="width: 18px;"></i>
            Filters
        </button>
        
        <button class="btn btn-outline" style="display: flex; align-items: center; gap: 8px; border-color: var(--border-color); color: var(--text-dark);">
            <i data-lucide="download" style="width: 18px;"></i>
            Export
        </button>
    </div>

    <!-- Orders Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox"></th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><a href="#" style="color: var(--primary); font-weight: 600; text-decoration: none;">#ORD-10234</a></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="avatar" style="width: 32px; height: 32px;">
                                <img src="https://i.pravatar.cc/150?u=sarah" alt="">
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600;">Sarah Johnson</span>
                                <span style="font-size: 12px; color: var(--text-medium);">sarah.j@email.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column;">
                            <span>Feb 08, 2026</span>
                            <span style="font-size: 12px; color: var(--text-medium);">2:34 PM</span>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                            <img src="https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                            <img src="https://images.unsplash.com/photo-1539109132333-68997f80521e?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                            <span style="font-size: 12px; color: var(--text-medium); margin-left: 4px;">+1</span>
                        </div>
                    </td>
                    <td style="font-weight: 700;">$247.50</td>
                    <td><span class="status-badge" style="background-color: #dcfce7; color: #166534;">Paid</span></td>
                    <td><span class="status-badge" style="background-color: #f3e8ff; color: #6b21a8; display: flex; align-items: center; gap: 4px; width: fit-content;">
                        <i data-lucide="truck" style="width: 12px;"></i> Shipped</span>
                    </td>
                    <td><i data-lucide="more-vertical" style="color: var(--text-medium); cursor: pointer;"></i></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><a href="#" style="color: var(--primary); font-weight: 600; text-decoration: none;">#ORD-10233</a></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="avatar" style="width: 32px; height: 32px;">
                                <img src="https://i.pravatar.cc/150?u=michael" alt="">
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600;">Michael Chen</span>
                                <span style="font-size: 12px; color: var(--text-medium);">m.chen@email.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column;">
                            <span>Feb 08, 2026</span>
                            <span style="font-size: 12px; color: var(--text-medium);">11:12 AM</span>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <img src="https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                            <img src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                        </div>
                    </td>
                    <td style="font-weight: 700;">$183.99</td>
                    <td><span class="status-badge" style="background-color: #dcfce7; color: #166534;">Paid</span></td>
                    <td><span class="status-badge" style="background-color: #dbeafe; color: #1e40af; display: flex; align-items: center; gap: 4px; width: fit-content;">
                        <i data-lucide="refresh-cw" style="width: 12px;"></i> Processing</span>
                    </td>
                    <td><i data-lucide="more-vertical" style="color: var(--text-medium); cursor: pointer;"></i></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><a href="#" style="color: var(--primary); font-weight: 600; text-decoration: none;">#ORD-10232</a></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="avatar" style="width: 32px; height: 32px;">
                                <img src="https://i.pravatar.cc/150?u=emma" alt="">
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600;">Emma Wilson</span>
                                <span style="font-size: 12px; color: var(--text-medium);">emma.w@email.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column;">
                            <span>Feb 07, 2026</span>
                            <span style="font-size: 12px; color: var(--text-medium);">8:45 PM</span>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <img src="https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                        </div>
                    </td>
                    <td style="font-weight: 700;">$89.50</td>
                    <td><span class="status-badge" style="background-color: #fef9c3; color: #854d0e;">Pending</span></td>
                    <td><span class="status-badge" style="background-color: #fef9c3; color: #854d0e; display: flex; align-items: center; gap: 4px; width: fit-content;">
                        <i data-lucide="clock" style="width: 12px;"></i> Pending</span>
                    </td>
                    <td><i data-lucide="more-vertical" style="color: var(--text-medium); cursor: pointer;"></i></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><a href="#" style="color: var(--primary); font-weight: 600; text-decoration: none;">#ORD-10231</a></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="avatar" style="width: 32px; height: 32px;">
                                <img src="https://i.pravatar.cc/150?u=david" alt="">
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600;">David Kim</span>
                                <span style="font-size: 12px; color: var(--text-medium);">d.kim@email.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column;">
                            <span>Feb 07, 2026</span>
                            <span style="font-size: 12px; color: var(--text-medium);">4:22 PM</span>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <img src="https://images.unsplash.com/photo-1544441893-675973e31985?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                            <img src="https://images.unsplash.com/photo-1548123380-197607a0a6fa?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                            <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=100" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                        </div>
                    </td>
                    <td style="font-weight: 700;">$312.00</td>
                    <td><span class="status-badge" style="background-color: #dcfce7; color: #166534;">Paid</span></td>
                    <td><span class="status-badge" style="background-color: #dcfce7; color: #166534; display: flex; align-items: center; gap: 4px; width: fit-content;">
                        <i data-lucide="check-circle" style="width: 12px;"></i> Delivered</span>
                    </td>
                    <td><i data-lucide="more-vertical" style="color: var(--text-medium); cursor: pointer;"></i></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
