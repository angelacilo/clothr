{{-- 
    FILE: checkout.blade.php
    WHAT IT DOES: This is the webpage where customers enter their details to buy items.
    WHY: It is the final step of the shopping experience.
    HOW IT WORKS: 
    - It uses the OFFICIAL Philippine PSGC API for a complete, accurate address system.
    - Regions, Cities, and Barangays are automatically loaded from the API.
--}}

@extends('layouts.shop')

@section('title', 'Checkout')

@section('extra_css')
<style>
    .checkout-layout { display: grid; grid-template-columns: 1fr 400px; gap: 40px; }
    
    .checkout-form { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 40px; box-shadow: var(--shadow-sm); }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px; }
    .form-group-full { grid-column: span 2; }
    
    .form-label { font-size: 11px; font-weight: 700; margin-bottom: 8px; display: block; text-transform: uppercase; color: var(--ink-muted); letter-spacing: 0.05em; }
    .form-input { width: 100%; padding: 14px 16px; border: 1px solid var(--border); border-radius: var(--radius-sm); outline: none; font-family: inherit; font-size: 14px; background: #fcfbf9; transition: 0.2s; }
    .form-input:focus { border-color: var(--ink); background: #fff; }
    
    .summary-card { background: #fff; padding: 32px; border-radius: var(--radius-md); border: 1px solid var(--border); position: sticky; top: 120px; box-shadow: var(--shadow-sm); }
    .summary-title { font-size: 20px; font-weight: 800; margin-bottom: 25px; color: var(--ink); }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 14px; color: var(--ink-soft); }
    .summary-total { display: flex; justify-content: space-between; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border); font-size: 20px; font-weight: 800; color: var(--ink); }
    
    .place-order-btn { background: var(--ink); color: #fff; width: 100%; padding: 18px; border-radius: var(--radius-sm); font-weight: 700; margin-top: 32px; font-size: 15px; transition: 0.3s; }
    .place-order-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .place-order-btn:disabled { opacity: 0.6; cursor: not-allowed; }

    .payment-option-card { border: 2px solid var(--border); border-radius: var(--radius-sm); padding: 20px; display: flex; gap: 16px; align-items: center; cursor: pointer; transition: 0.2s; }
    .payment-option-card.active { border-color: var(--ink); background: var(--sand); }
</style>
@endsection

@section('content')
<div class="container section">
    <h1 style="font-size: 36px; font-weight: 800; margin-bottom: 40px; color: var(--ink);">Checkout</h1>

    <div class="checkout-layout">
        {{-- LEFT SIDE: Shipping Information --}}
        <div class="checkout-form">
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 15px;">Shipping Address</h2>
            
            <form id="checkout-form-el">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-input" placeholder="e.g. Sheryn" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-input" placeholder="e.g. Acilo" required>
                    </div>
                    
                    <div class="form-group-full">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" name="phone" class="form-input" placeholder="e.g. 09123456789" required>
                    </div>

                    {{-- DYNAMIC DROPDOWNS (PSGC API) --}}
                    <div class="form-group">
                        <label class="form-label">Region</label>
                        <select name="region" id="region-select" class="form-input" required onchange="handleRegionChange(this)">
                            <option value="">Select Region</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">City / Municipality</label>
                        <select name="city" id="city-select" class="form-input" required disabled onchange="handleCityChange(this)">
                            <option value="">Select Region First</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Barangay</label>
                        <select name="barangay" id="barangay-select" class="form-input" required disabled>
                            <option value="">Select City First</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Zip Code</label>
                        <input type="text" name="zip_code" class="form-input" placeholder="e.g. 1100" required>
                    </div>

                    <div class="form-group-full">
                        <label class="form-label">Street Name, Building, House No.</label>
                        <input type="text" name="address_line_1" class="form-input" placeholder="e.g. 123 Aguinaldo St." required>
                    </div>
                </div>

                <div class="payment-options" style="margin-top: 40px;">
                    <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">Payment Method</h2>
                    <div class="payment-option-card active">
                        <i data-lucide="banknote" size="24"></i>
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">Cash on Delivery</div>
                            <div style="font-size: 12px; color: var(--ink-muted);">Pay when you receive the package.</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- RIGHT SIDE: Order Summary --}}
        <aside>
            <div class="summary-card">
                <h3 class="summary-title">Order Summary</h3>
                <div id="summary-items" style="max-height: 400px; overflow-y: auto;"></div>
                
                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border);">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="shipping">₱0.00</span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span id="total">₱0.00</span>
                    </div>
                </div>

                <button class="place-order-btn" onclick="placeOrder()">Confirm Order</button>
            </div>
        </aside>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    let isSubmittingOrder = false;

    /**
     * PHILIPPINE PSGC API INTEGRATION
     * Loads Regions, Cities, and Barangays dynamically.
     */
    async function initPSGC() {
        try {
            const res = await fetch('https://psgc.gitlab.io/api/regions/');
            const regions = await res.json();
            const select = document.getElementById('region-select');
            regions.sort((a,b) => a.name.localeCompare(b.name)).forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.code;
                opt.dataset.name = r.name;
                opt.textContent = r.name;
                select.appendChild(opt);
            });
        } catch(e) { console.error('PSGC Load Error', e); }
    }

    async function handleRegionChange(select) {
        const regionCode = select.value;
        const citySelect = document.getElementById('city-select');
        const brgySelect = document.getElementById('barangay-select');
        
        citySelect.innerHTML = '<option value="">Loading...</option>';
        citySelect.disabled = true;
        brgySelect.innerHTML = '<option value="">Select City First</option>';
        brgySelect.disabled = true;

        if (!regionCode) return;

        try {
            const res = await fetch(`https://psgc.gitlab.io/api/regions/${regionCode}/cities-municipalities/`);
            const cities = await res.json();
            citySelect.innerHTML = '<option value="">Select City / Municipality</option>';
            cities.sort((a,b) => a.name.localeCompare(b.name)).forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.code;
                opt.dataset.name = c.name;
                opt.textContent = c.name;
                citySelect.appendChild(opt);
            });
            citySelect.disabled = false;
        } catch(e) { console.error('City Load Error', e); }
    }

    async function handleCityChange(select) {
        const cityCode = select.value;
        const brgySelect = document.getElementById('barangay-select');
        
        brgySelect.innerHTML = '<option value="">Loading...</option>';
        brgySelect.disabled = true;

        if (!cityCode) return;

        try {
            const res = await fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`);
            const brgys = await res.json();
            brgySelect.innerHTML = '<option value="">Select Barangay</option>';
            brgys.sort((a,b) => a.name.localeCompare(b.name)).forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.name; // Store the name directly for the order
                opt.textContent = b.name;
                brgySelect.appendChild(opt);
            });
            brgySelect.disabled = false;
        } catch(e) { console.error('Barangay Load Error', e); }
    }

    function renderSummary() {
        const itemsList = document.getElementById('summary-items');
        if (!itemsList) return;
        itemsList.innerHTML = '';
        let subtotal = 0;
        const checkoutItems = cart.filter(item => item.is_selected !== false);
        checkoutItems.forEach((item) => {
            subtotal += item.price * item.quantity;
            itemsList.innerHTML += `
                <div class="summary-row" style="align-items: center; gap: 15px; margin-bottom: 12px;">
                    <img src="${item.image}" style="width: 50px; height: 65px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                    <div style="flex: 1;">
                        <div style="font-weight: 700; font-size: 13px;">${item.name}</div>
                        <div style="font-size: 11px; color: var(--ink-muted);">Qty: ${item.quantity} | ${item.size}</div>
                    </div>
                    <div style="font-weight: 800; font-size: 14px;">₱${(item.price * item.quantity).toFixed(2)}</div>
                </div>
            `;
        });
        let shipping = subtotal >= 2500 ? 0 : (subtotal > 0 ? 250 : 0);
        let total = subtotal + shipping;
        document.getElementById('subtotal').innerText = '₱' + subtotal.toFixed(2);
        document.getElementById('shipping').innerText = shipping === 0 ? 'FREE' : '₱' + shipping.toFixed(2);
        document.getElementById('total').innerText    = '₱' + total.toFixed(2);
    }

    async function placeOrder() {
        if (isSubmittingOrder) return;

        const form = document.getElementById('checkout-form-el');
        const formData = new FormData(form);
        const customerInfo = {};
        formData.forEach((value, key) => { customerInfo[key] = value; });

        // Get the NAMES instead of the CODES for the database
        const regSelect = document.getElementById('region-select');
        const citySelect = document.getElementById('city-select');
        if (regSelect.selectedIndex > 0) customerInfo.region = regSelect.options[regSelect.selectedIndex].dataset.name;
        if (citySelect.selectedIndex > 0) customerInfo.city = citySelect.options[citySelect.selectedIndex].dataset.name;

        if (!customerInfo.first_name || !customerInfo.phone || !customerInfo.region || !customerInfo.city || !customerInfo.barangay) {
            showToast('Please fill in all shipping details', 'error');
            return;
        }

        isSubmittingOrder = true;
        const btn = document.querySelector('.place-order-btn');
        btn.disabled = true;
        btn.textContent = 'Processing Order...';

        try {
            const response = await fetch('{{ route("place.order") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ items: cart.filter(i => i.is_selected), customer_info: customerInfo })
            });
            const result = await response.json();
            if (result.success) {
                localStorage.removeItem('clothr_cart');
                window.location.href = '/order-confirmation/' + result.order_id;
            } else {
                showToast(result.message || 'Error placing order', 'error');
                resetOrderButton(btn);
            }
        } catch (error) {
            showToast('Connection failed', 'error');
            resetOrderButton(btn);
        }
    }

    function resetOrderButton(btn) {
        isSubmittingOrder = false;
        btn.disabled = false;
        btn.textContent = 'Confirm Order';
    }

    window.addEventListener('load', () => {
        renderSummary();
        initPSGC();
    });
</script>
@endsection
