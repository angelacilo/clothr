@extends('layouts.admin')

@section('title', 'Courier Management')
@section('subtitle', 'Manage third-party courier partners and their access')

@section('content')
<div class="couriers-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div style="position: relative; width: 350px;">
            <i data-lucide="search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 18px;"></i>
            <input type="text" id="courierSearch" placeholder="Search courier or code..." oninput="filterCouriers()"
                   style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); outline: none; font-size: 14px; background-color: white;">
        </div>
        
        <button onclick="toggleModal('addCourierModal')" style="background: #111; color: white; border: none; padding: 12px 24px; font-weight: 800; border-radius: 8px; display: flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <i data-lucide="plus-circle" style="width: 18px; color: #10b981;"></i>
            ADD COURIER PARTNER
        </button>
    </div>

    <!-- Courier Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <div class="card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <div style="background: #f0fdf4; color: #166534; padding: 8px; border-radius: 10px;">
                    <i data-lucide="truck"></i>
                </div>
            </div>
            <div style="font-size: 28px; font-weight: 800; color: #111;">{{ $couriers->count() }}</div>
            <div style="font-size: 14px; color: #64748b; font-weight: 600; margin-top: 4px;">Total Logistics Partners</div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border-color);">
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Company Name</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Portal Code</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Manager Account</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase;">Partner Since</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 700; color: var(--text-medium); text-transform: uppercase; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($couriers as $courier)
                    <tr class="courier-row" data-name="{{ strtolower($courier->name) }}" data-code="{{ strtolower($courier->code) }}" style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #111;">
                                    {{ substr($courier->name, 0, 1) }}
                                </div>
                                <span style="font-weight: 700; color: var(--text-dark);">{{ $courier->name }}</span>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; background: #eff6ff; color: #2563eb; font-size: 13px;">
                                {{ $courier->code }}
                            </span>
                        </td>
                        <td style="padding: 16px; font-size: 14px;">
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600; color: #111;">{{ $courier->user->email }}</span>
                                <span style="font-size: 12px; color: #64748b;">Role: Courier Portal</span>
                            </div>
                        </td>
                        <td style="padding: 16px; font-size: 14px; color: var(--text-medium);">
                            {{ $courier->created_at->format('M d, Y') }}
                        </td>
                        <td style="padding: 16px; text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                                <form action="{{ route('admin.couriers.delete', $courier->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('WARNING: Deleting this courier will also remove their portal access. Continue?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px;" title="Terminate Partnership">
                                        <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($couriers->isEmpty())
            <div style="text-align: center; padding: 80px 0; color: var(--text-medium);">
                <i data-lucide="truck" style="width: 48px; height: 48px; color: var(--text-light); margin-bottom: 16px; opacity: 0.3;"></i>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">No Courier Partners</h3>
                <p style="font-size: 14px; max-width: 300px; margin: 0 auto;">Register your logistics partners to enable them to manage deliveries.</p>
            </div>
        @endif
    </div>
</div>

<!-- Add Courier Modal -->
<div id="addCourierModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; padding: 40px 20px;">
    <div style="max-width: 500px; background: white; margin: 40px auto; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <div style="padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 16px; font-weight: 800; text-transform: uppercase;">Register Courier Partner</h2>
            <button onclick="toggleModal('addCourierModal')" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
        <form action="{{ route('admin.couriers.store') }}" method="POST" style="padding: 24px;">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px;">COMPANY NAME</label>
                <input type="text" name="name" required placeholder="e.g. J&T Express" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px;">SYSTEM CODE (Short & Unique)</label>
                <input type="text" name="code" required placeholder="e.g. JT" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; text-transform: uppercase;">
                <small style="color: #64748b; font-size: 11px;">Used to link orders automatically.</small>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px;">MANAGER EMAIL</label>
                <input type="email" name="email" required placeholder="manager@company.com" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div style="position: relative;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px;">PASSWORD</label>
                    <input type="password" name="password" id="p1" required style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    <button type="button" onclick="togglePass('p1')" style="position: absolute; right: 12px; bottom: 10px; background:none; border:none; cursor:pointer; color:#94a3b8;"><i data-lucide="eye" style="width:16px;"></i></button>
                </div>
                <div style="position: relative;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px;">CONFIRM</label>
                    <input type="password" name="password_confirmation" id="p2" required style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    <button type="button" onclick="togglePass('p2')" style="position: absolute; right: 12px; bottom: 10px; background:none; border:none; cursor:pointer; color:#94a3b8;"><i data-lucide="eye" style="width:16px;"></i></button>
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #111; color: white; border: none; padding: 14px; border-radius: 10px; font-weight: 800; cursor: pointer; text-transform: uppercase;">
                Create Partner Account
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
        if(modal.style.display === 'block') lucide.createIcons();
    }

    function togglePass(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function filterCouriers() {
        const q = document.getElementById('courierSearch').value.toLowerCase();
        document.querySelectorAll('.courier-row').forEach(row => {
            const name = row.getAttribute('data-name');
            const code = row.getAttribute('data-code');
            row.style.display = (name.includes(q) || code.includes(q)) ? '' : 'none';
        });
    }

    window.onclick = function(event) {
        if (event.target.id === 'addCourierModal') toggleModal('addCourierModal');
    }
    // Listen for real-time updates to couriers
    if (window.Echo) {
        window.Echo.private('admin')
            .listen('.CourierCreated', (e) => {
                showToast("New Courier Partner Added: " + e.name);
                setTimeout(() => window.location.reload(), 1500);
            });
    }
</script>
@endsection
