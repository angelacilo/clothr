@extends('layouts.shop')

@section('title', 'Checkout')

@section('extra_css')
    .checkout-layout { display: grid; grid-template-columns: 1fr 380px; gap: 40px; }
    
    .checkout-form { background: #fff; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 40px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
    .form-group-full { grid-column: span 2; }
    
    .form-label { font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block; text-transform: uppercase; }
    .form-input { width: 100%; padding: 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; font-family: inherit; }
    .form-input:focus { border-color: #000; }
    
    .summary-card { background: #f8f9fa; padding: 30px; border-radius: var(--radius-md); position: sticky; top: 120px; }
    .summary-title { font-size: 18px; font-weight: 700; margin-bottom: 25px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; }
    .summary-total { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 18px; font-weight: 800; }
    
    .place-order-btn { background: #000; color: #fff; width: 100%; padding: 18px; border-radius: var(--radius-sm); font-weight: 700; margin-top: 30px; }
    
    .address-card { border: 1.5px solid var(--border-color); border-radius: 12px; padding: 20px; cursor: pointer; transition: 0.2s; position: relative; }
    .address-card.active { border-color: #000; background: #f9fafb; }
    .address-card__check { position: absolute; top: 15px; right: 15px; color: #000; display: none; }
    .address-card.active .address-card__check { display: block; }
    .address-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
    
    .checkout-item { border-bottom: 1px solid var(--border-color); padding: 15px 0; display: flex; gap: 15px; }
    .checkout-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    .checkout-item__info { flex: 1; }
    .checkout-item__actions { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
    .edit-select { padding: 4px 8px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 12px; }
@endsection

@section('content')
<div class="container section">
    <h1 style="font-size: 40px; font-weight: 800; margin-bottom: 40px;">Checkout</h1>

    <div class="checkout-layout">
        <div class="checkout-form">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 15px;">Shipping Address</h2>
            
            @if(count($addresses) > 0)
                <div class="address-grid">
                    @foreach($addresses as $addr)
                        <div class="address-card {{ $addr->is_default ? 'active' : '' }}" onclick="selectAddress(this, {{ json_encode($addr) }})">
                            <i data-lucide="check-circle" class="address-card__check" size="20"></i>
                            <div style="font-weight: 700; margin-bottom: 5px;">{{ $addr->label }}</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">
                                {{ $addr->first_name }} {{ $addr->last_name }}<br>
                                {{ $addr->address_line_1 }}<br>
                                {{ $addr->city }}, {{ $addr->zip_code }}<br>
                                {{ $addr->country }}
                            </div>
                        </div>
                    @endforeach
                    <div class="address-card" onclick="showNewAddressForm()">
                        <div style="height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted);">
                            <i data-lucide="plus" size="24" style="margin-bottom: 8px;"></i>
                            <span style="font-size: 14px; font-weight: 600;">Add New Address</span>
                        </div>
                    </div>
                </div>
            @endif

            <form id="checkout-form-el" style="{{ count($addresses) > 0 ? 'display: none;' : '' }}">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" value="{{ auth()->user()->email }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <select name="country" id="country_select" class="form-input" required onchange="loadRegions(this.value)">
                            <option value="">Select Country</option>
                            <!-- Loaded via JS -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Region / State</label>
                        <select name="region" id="region_select" class="form-input" required disabled onchange="loadCities(this.value)">
                            <option value="">Select Region</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">City / Municipality</label>
                        <select name="city" id="city_select" class="form-input" required disabled>
                            <option value="">Select City</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ZIP / Postal Code</label>
                        <input type="text" name="zip_code" class="form-input" required>
                    </div>
                    <div class="form-group-full">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" name="address_line_1" class="form-input" placeholder="House number, Street name" required>
                    </div>
                    <div class="form-group-full">
                        <label class="form-label">Address Line 2 (Optional)</label>
                        <input type="text" name="address_line_2" class="form-input" placeholder="Apartment, Studio, Floor">
                    </div>
                </div>
            </form>
        </div>

        <aside>
            <div class="summary-card">
                <h3 class="summary-title">Order Summary</h3>
                <div id="summary-items">
                    <!-- Items list -->
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="shipping">₱250.00</span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span id="total">₱0.00</span>
                    </div>
                </div>

                <button class="place-order-btn" onclick="placeOrder()">Place Order</button>
            </div>
        </aside>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    let selectedAddressId = null;
    let shipping = 250;
    let total = 0;

    async function loadCountries() {
        const res = await fetch('/api/countries');
        const countries = await res.json();
        const select = document.getElementById('country_select');
        countries.forEach(c => {
            select.innerHTML += `<option value="${c.id}">${c.name}</option>`;
        });
    }

    async function loadRegions(countryId) {
        const select = document.getElementById('region_select');
        const citySelect = document.getElementById('city_select');
        select.innerHTML = '<option value="">Select Region</option>';
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;

        if (!countryId) {
            select.disabled = true;
            return;
        }

        const res = await fetch(`/api/regions/${countryId}`);
        const regions = await res.json();
        regions.forEach(r => {
            select.innerHTML += `<option value="${r.id}">${r.name}</option>`;
        });
        select.disabled = false;
    }

    async function loadCities(regionId) {
        const select = document.getElementById('city_select');
        select.innerHTML = '<option value="">Select City</option>';

        if (!regionId) {
            select.disabled = true;
            return;
        }

        const res = await fetch(`/api/cities/${regionId}`);
        const cities = await res.json();
        cities.forEach(c => {
            select.innerHTML += `<option value="${c.id}">${c.name}</option>`;
        });
        select.disabled = false;
    }

    function renderSummary() {
        const itemsList = document.getElementById('summary-items');
        let subtotal = 0;
        
        itemsList.innerHTML = '';
        cart.forEach((item, index) => {
            subtotal += item.price * item.quantity;
            itemsList.innerHTML += `
                <div class="checkout-item">
                    <img src="${item.image}" alt="${item.name}">
                    <div class="checkout-item__info">
                        <div style="font-weight: 700; font-size: 14px;">${item.name}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">₱${item.price.toFixed(2)}</div>
                        <div class="checkout-item__actions">
                            <select class="edit-select" onchange="updateItemQuantity(${index}, this.value)">
                                ${[1,2,3,4,5,6,7,8,9,10].map(q => `<option value="${q}" ${q == item.quantity ? 'selected' : ''}>Qty: ${q}</option>`).join('')}
                            </select>
                            <select class="edit-select" onchange="updateItemSize(${index}, this.value)">
                                ${['XS','S','M','L','XL'].map(s => `<option value="${s}" ${s == item.size ? 'selected' : ''}>Size: ${s}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div style="font-weight: 700; font-size: 14px;">₱${(item.price * item.quantity).toFixed(2)}</div>
                </div>
            `;
        });

        shipping = subtotal >= 2500 ? 0 : 250;
        total = subtotal + shipping;

        document.getElementById('subtotal').innerText = '₱' + subtotal.toFixed(2);
        document.getElementById('shipping').innerText = shipping === 0 ? '₱0.00' : '₱' + shipping.toFixed(2);
        document.getElementById('total').innerText = '₱' + total.toFixed(2);
    }

    window.updateItemQuantity = function(index, qty) {
        cart[index].quantity = parseInt(qty);
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        renderSummary();
    }

    window.updateItemSize = function(index, size) {
        cart[index].size = size;
        localStorage.setItem('clothr_cart', JSON.stringify(cart));
        renderSummary();
    }

    function selectAddress(card, address) {
        document.querySelectorAll('.address-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        selectedAddressId = address.id;
        document.getElementById('checkout-form-el').style.display = 'none';
        
        // Fill form as hidden data or just use selectedAddressId on submit
    }

    function showNewAddressForm() {
        document.querySelectorAll('.address-card').forEach(c => c.classList.remove('active'));
        selectedAddressId = 'new';
        document.getElementById('checkout-form-el').style.display = 'block';
    }

    async function placeOrder() {
        let customer_info = {};
        
        if (selectedAddressId && selectedAddressId !== 'new') {
            customer_info.address_id = selectedAddressId;
        } else {
            const form = document.getElementById('checkout-form-el');
            if (!form.reportValidity()) return;
            const formData = new FormData(form);
            formData.forEach((value, key) => customer_info[key] = value);
        }

        const orderData = {
            customer_info: customer_info,
            items: cart,
            total: total,
            _token: '{{ csrf_token() }}'
        };

        try {
            const response = await fetch('{{ route('place.order') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();
            if (result.success) {
                localStorage.removeItem('clothr_cart');
                window.location.href = '/order-confirmation/' + result.order_id;
            } else {
                showToast(result.message || 'Something went wrong', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error placing order.', 'error');
        }
    }

    window.addEventListener('load', () => {
        renderSummary();
        loadCountries();
    });
</script>
@endsection
