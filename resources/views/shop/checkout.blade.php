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
@endsection

@section('content')
<div class="container section">
    <h1 style="font-size: 40px; font-weight: 800; margin-bottom: 40px;">Checkout</h1>

    <div class="checkout-layout">
        <div class="checkout-form">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 30px;">Shipping Address</h2>
            <form id="checkout-form-el">
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
                        <input type="email" name="email" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" required>
                    </div>
                    <div class="form-group-full">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <select name="city" class="form-input" required>
                            <option value="">Select City</option>
                            <option value="New York">New York</option>
                            <option value="London">London</option>
                            <option value="Paris">Paris</option>
                            <option value="Tokyo">Tokyo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ZIP Code</label>
                        <input type="text" name="zip" class="form-input" required>
                    </div>
                    <div class="form-group-full">
                        <label class="form-label">Description / Notes</label>
                        <textarea name="notes" class="form-input" rows="4" style="resize: none;"></textarea>
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
    let subtotal = 0;
    let shipping = 5.99;
    let total = 0;

    function renderSummary() {
        const itemsList = document.getElementById('summary-items');
        subtotal = 0;
        
        itemsList.innerHTML = '';
        cart.forEach(item => {
            subtotal += item.price * item.quantity;
            itemsList.innerHTML += `
                <div class="summary-row" style="color: var(--text-secondary);">
                    <span>${item.name} x${item.quantity}</span>
                    <span>₱${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            `;
        });

        shipping = subtotal >= 2500 ? 0 : 250;
        total = subtotal + shipping;

        document.getElementById('subtotal').innerText = '₱' + subtotal.toFixed(2);
        document.getElementById('shipping').innerText = shipping === 0 ? '₱0.00' : '₱' + shipping.toFixed(2);
        document.getElementById('total').innerText = '₱' + total.toFixed(2);
    }

    async function placeOrder() {
        const form = document.getElementById('checkout-form-el');
        if (!form.reportValidity()) return;

        const formData = new FormData(form);
        const customer_info = {};
        formData.forEach((value, key) => customer_info[key] = value);

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
                alert('Something went wrong. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error placing order.');
        }
    }

    window.addEventListener('load', renderSummary);
</script>
@endsection
