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

    /* Location Modal Styles */
    .location-trigger { width: 100%; padding: 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: #fff; text-align: left; font-size: 14px; color: #666; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
    .location-trigger:focus { border-color: #000; }
    .location-trigger.has-value { color: #000; }

    .loc-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; padding: 20px; }
    .loc-modal-overlay.active { display: flex; }
    .loc-modal { background: #fff; width: 500px; max-width: 100%; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; height: 600px; max-height: 90vh; }
    .loc-header { padding: 20px; border-bottom: 1px solid #eee; position: relative; }
    .loc-title { font-size: 16px; font-weight: 700; text-align: center; }
    .loc-close { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #999; }
    
    .loc-tabs { display: flex; border-bottom: 1px solid #eee; padding: 0 10px; }
    .loc-tab { padding: 15px 10px; font-size: 13px; font-weight: 600; color: #999; cursor: pointer; position: relative; }
    .loc-tab.active { color: #ee4d2d; }
    .loc-tab.active::after { content: ''; position: absolute; bottom: 0; left: 10px; right: 10px; height: 2px; background: #ee4d2d; }
    .loc-tab.disabled { cursor: not-allowed; }

    .loc-search-box { padding: 15px; border-bottom: 1px solid #eee; }
    .loc-search-input { width: 100%; padding: 10px 15px; border: 1px solid #eee; border-radius: 40px; background: #f5f5f5; font-size: 14px; outline: none; }

    .loc-body { flex: 1; overflow-y: auto; display: flex; position: relative; }
    .loc-list { flex: 1; }
    .loc-group-header { padding: 10px 20px; background: #f9f9f9; font-size: 12px; font-weight: 700; color: #999; }
    .loc-item { padding: 12px 20px; font-size: 14px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #fafafa; }
    .loc-item:hover { background: #fdfdfd; }
    .loc-item.selected { color: #ee4d2d; font-weight: 600; }
    .loc-item.selected .check-icon { display: block; }
    .check-icon { display: none; color: #ee4d2d; }

    .loc-index { width: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; font-size: 10px; font-weight: 700; color: #666; background: #fff; border-left: 1px solid #eee; }
    .loc-index-item { cursor: pointer; padding: 2px 5px; }
    .loc-index-item:hover { color: #ee4d2d; }

    .loc-loading { position: absolute; inset: 0; background: rgba(255,255,255,0.8); display: none; align-items: center; justify-content: center; z-index: 10; }
    .loc-loading.active { display: flex; }
    .spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #ee4d2d; border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .payment-options { margin-top: 40px; }
    .payment-option-card { border: 1.5px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px; background: #fff; cursor: default; }
    .payment-option-card.active { border-color: #000; background: #f9fafb; }
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
                    <div class="form-group-full">
                        <label class="form-label">Location (Region, Province, City, Barangay)</label>
                        <div id="location-selector" class="location-trigger" onclick="openLocationModal()">
                            <span id="location-text">Select Region, Province, City, Barangay</span>
                            <i data-lucide="chevron-down" size="18"></i>
                        </div>
                        <input type="hidden" name="region" id="region_input">
                        <input type="hidden" name="province" id="province_input">
                        <input type="hidden" name="city" id="city_input">
                        <input type="hidden" name="barangay" id="barangay_input">
                        <input type="hidden" name="full_location" id="full_location_input">
                    </div>
                    <div class="form-group-full">
                        <label class="form-label">Street Name, Building, House No.</label>
                        <input type="text" name="address_line_1" class="form-input" placeholder="e.g. 123 Rizal St." required>
                    </div>
                </div>

                <div class="payment-options">
                    <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">Payment Method</h2>
                    <div class="payment-option-card active">
                        <i data-lucide="banknote" size="24"></i>
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">Cash on Delivery</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Pay when you receive the package.</div>
                        </div>
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
    });

    /* Shopee-style Location Selector Logic */
    const psgcBase = 'https://psgc.cloud/api';
    let currentState = 'region'; // region, province, city, barangay
    let selections = { region: null, province: null, city: null, barangay: null };
    let locationData = { regions: [], provinces: [], cities: [], barangays: [] };

    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'loc-modal-overlay';
    modalOverlay.innerHTML = `
        <div class="loc-modal">
            <div class="loc-header">
                <div class="loc-title">Select Location</div>
                <div class="loc-close" onclick="closeLocationModal()">&times;</div>
            </div>
            <div class="loc-tabs">
                <div class="loc-tab active" onclick="gotoStep('region')">Region</div>
                <div class="loc-tab disabled" id="tab-province" onclick="gotoStep('province')">Province</div>
                <div class="loc-tab disabled" id="tab-city" onclick="gotoStep('city')">City</div>
                <div class="loc-tab disabled" id="tab-barangay" onclick="gotoStep('barangay')">Barangay</div>
            </div>
            <div class="loc-search-box">
                <input type="text" class="loc-search-input" placeholder="Search..." oninput="filterLocations(this.value)">
            </div>
            <div class="loc-body">
                <div class="loc-loading"><div class="spinner"></div></div>
                <div class="loc-list" id="loc-items-list"></div>
                <div class="loc-index" id="loc-index-bar"></div>
            </div>
        </div>
    `;
    document.body.appendChild(modalOverlay);

    window.openLocationModal = () => {
        modalOverlay.classList.add('active');
        if (locationData.regions.length === 0) {
            fetchRegions();
        } else {
            renderLocations();
        }
    };

    window.closeLocationModal = () => {
        modalOverlay.classList.remove('active');
    };

    async function fetchData(url) {
        setLoading(true);
        try {
            const res = await fetch(url);
            return await res.json();
        } catch (e) {
            console.error(e);
            return [];
        } finally {
            setLoading(false);
        }
    }

    async function fetchRegions() {
        locationData.regions = await fetchData(`${psgcBase}/regions`);
        locationData.regions.sort((a,b) => a.name.localeCompare(b.name));
        renderLocations();
    }

    async function fetchProvinces(regionCode) {
        locationData.provinces = await fetchData(`${psgcBase}/regions/${regionCode}/provinces`);
        if (locationData.provinces.length === 0) {
            fetchCitiesForRegion(regionCode);
        } else {
            locationData.provinces.sort((a,b) => a.name.localeCompare(b.name));
            renderLocations();
        }
    }

    async function fetchCitiesForRegion(regionCode) {
         locationData.cities = await fetchData(`${psgcBase}/regions/${regionCode}/cities-municipalities`);
         locationData.cities.sort((a,b) => a.name.localeCompare(b.name));
         currentState = 'city';
         updateTabs();
         renderLocations();
    }

    async function fetchCities(provinceCode) {
        locationData.cities = await fetchData(`${psgcBase}/provinces/${provinceCode}/cities-municipalities`);
        locationData.cities.sort((a,b) => a.name.localeCompare(b.name));
        renderLocations();
    }

    async function fetchBarangays(cityCode) {
        locationData.barangays = await fetchData(`${psgcBase}/cities-municipalities/${cityCode}/barangays`);
        locationData.barangays.sort((a,b) => a.name.localeCompare(b.name));
        renderLocations();
    }

    function setLoading(active) {
        const loader = modalOverlay.querySelector('.loc-loading');
        if (loader) loader.classList.toggle('active', active);
    }

    window.gotoStep = (step) => {
        const tab = document.getElementById(`tab-${step}`) || (step === 'region' ? true : null);
        if (!tab || (tab !== true && tab.classList.contains('disabled'))) return;
        currentState = step;
        updateTabs();
        renderLocations();
    };

    function updateTabs() {
        const tabs = modalOverlay.querySelectorAll('.loc-tab');
        tabs.forEach(t => t.classList.remove('active'));
        
        const steps = ['region', 'province', 'city', 'barangay'];
        const activeIdx = steps.indexOf(currentState);
        if (tabs[activeIdx]) tabs[activeIdx].classList.add('active');

        if (document.getElementById('tab-province')) document.getElementById('tab-province').classList.toggle('disabled', !selections.region);
        if (document.getElementById('tab-city')) document.getElementById('tab-city').classList.toggle('disabled', !selections.province && !selections.region);
        if (document.getElementById('tab-barangay')) document.getElementById('tab-barangay').classList.toggle('disabled', !selections.city);
    }

    function renderLocations(filterStr = '') {
        const listEl = document.getElementById('loc-items-list');
        const indexEl = document.getElementById('loc-index-bar');
        listEl.innerHTML = '';
        indexEl.innerHTML = '';

        let data = [];
        if (currentState === 'region') data = locationData.regions;
        else if (currentState === 'province') data = locationData.provinces;
        else if (currentState === 'city') data = locationData.cities;
        else if (currentState === 'barangay') data = locationData.barangays;

        if (filterStr) {
            data = data.filter(item => item.name.toLowerCase().includes(filterStr.toLowerCase()));
        }

        const groups = {};
        data.forEach(item => {
            const firstLetter = item.name[0].toUpperCase();
            if (!groups[firstLetter]) groups[firstLetter] = [];
            groups[firstLetter].push(item);
        });

        const letters = Object.keys(groups).sort();
        letters.forEach(letter => {
            const header = document.createElement('div');
            header.className = 'loc-group-header';
            header.id = `group-${letter}`;
            header.innerText = letter;
            listEl.appendChild(header);

            const idxItem = document.createElement('div');
            idxItem.className = 'loc-index-item';
            idxItem.innerText = letter;
            idxItem.onclick = () => {
                header.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };
            indexEl.appendChild(idxItem);

            groups[letter].forEach(item => {
                const itemEl = document.createElement('div');
                itemEl.className = 'loc-item';
                if (selections[currentState] && selections[currentState].code === item.code) {
                    itemEl.classList.add('selected');
                }
                itemEl.innerHTML = `
                    <span>${item.name}</span>
                    <i data-lucide="check" class="check-icon" size="16"></i>
                `;
                itemEl.onclick = () => selectItem(item);
                listEl.appendChild(itemEl);
            });
        });

        lucide.createIcons();
    }

    window.filterLocations = (val) => {
        renderLocations(val);
    };

    function selectItem(item) {
        selections[currentState] = item;
        
        if (currentState === 'region') {
            selections.province = null;
            selections.city = null;
            selections.barangay = null;
            currentState = 'province';
            fetchProvinces(item.code);
        } else if (currentState === 'province') {
            selections.city = null;
            selections.barangay = null;
            currentState = 'city';
            fetchCities(item.code);
        } else if (currentState === 'city') {
            selections.barangay = null;
            currentState = 'barangay';
            fetchBarangays(item.code);
        } else if (currentState === 'barangay') {
            finalizeSelection();
        }
        
        updateTabs();
    }

    function finalizeSelection() {
        const fullAddr = [
            selections.barangay ? `Brgy. ${selections.barangay.name}` : '',
            selections.city ? selections.city.name : '',
            selections.province ? selections.province.name : '',
            selections.region ? selections.region.name : ''
        ].filter(Boolean).join(', ');

        const trigger = document.getElementById('location-selector');
        document.getElementById('location-text').innerText = fullAddr;
        trigger.classList.add('has-value');

        document.getElementById('region_input').value = selections.region ? selections.region.name : '';
        document.getElementById('province_input').value = selections.province ? selections.province.name : '';
        document.getElementById('city_input').value = selections.city ? selections.city.name : '';
        document.getElementById('barangay_input').value = selections.barangay ? selections.barangay.name : '';
        document.getElementById('full_location_input').value = fullAddr;

        closeLocationModal();
    }
</script>
@endsection
