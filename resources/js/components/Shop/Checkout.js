const React = require('react');
const e = React.createElement;

function Checkout() {
    const [cartItems, setCartItems] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [submitting, setSubmitting] = React.useState(false);
    const [form, setForm] = React.useState({
        full_name: '',
        address: '',
        city: '',
        postal_code: '',
        country: '',
        payment_method: 'cod',
    });
    const [error, setError] = React.useState('');

    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    React.useEffect(function () {
        fetch('/api/shop/cart')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setCartItems(data);
                setLoading(false);
            });
    }, []);

    function setField(field, value) {
        setForm(function (prev) {
            var next = {};
            Object.keys(prev).forEach(function (k) { next[k] = prev[k]; });
            next[field] = value;
            return next;
        });
    }

    function handleSubmit(evt) {
        evt.preventDefault();
        setSubmitting(true);
        setError('');
        fetch('/api/shop/checkout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(form),
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.order_id) {
                    window.location.href = '/order/' + data.order_id + '/confirmation';
                } else {
                    setError(data.message || 'Something went wrong. Please try again.');
                    setSubmitting(false);
                }
            })
            .catch(function () {
                setError('Network error. Please try again.');
                setSubmitting(false);
            });
    }

    if (loading) return e('p', { style: { padding: '2rem', textAlign: 'center' } }, 'Loading checkout...');

    var total = cartItems.reduce(function (sum, item) {
        var price = item.product.sale_price || item.product.price;
        return sum + parseFloat(price) * item.quantity;
    }, 0);

    return e('div', { style: { padding: '2rem', fontFamily: 'sans-serif', maxWidth: '800px', margin: '0 auto' } },
        e('h1', { style: { marginBottom: '2rem' } }, 'Checkout'),
        error ? e('p', { style: { color: 'red', marginBottom: '1rem' } }, error) : null,

        e('div', { style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '2rem' } },

            // Form
            e('form', { onSubmit: handleSubmit },
                e('h3', null, 'Shipping Information'),
                ['full_name', 'address', 'city', 'postal_code', 'country'].map(function (field) {
                    return e('div', { key: field, style: fGroup },
                        e('label', { style: fLabel }, field.replace('_', ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); })),
                        e('input', {
                            type: 'text', required: true,
                            value: form[field],
                            onChange: function (ev) { setField(field, ev.target.value); },
                            style: fInput,
                        })
                    );
                }),

                e('h3', { style: { marginTop: '1.5rem' } }, 'Payment Method'),
                e('div', { style: fGroup },
                    e('label', null,
                        e('input', {
                            type: 'radio', name: 'payment_method', value: 'cod',
                            checked: form.payment_method === 'cod',
                            onChange: function () { setField('payment_method', 'cod'); },
                            style: { marginRight: '8px' },
                        }),
                        'Cash on Delivery'
                    )
                ),
                e('div', { style: fGroup },
                    e('label', null,
                        e('input', {
                            type: 'radio', name: 'payment_method', value: 'card',
                            checked: form.payment_method === 'card',
                            onChange: function () { setField('payment_method', 'card'); },
                            style: { marginRight: '8px' },
                        }),
                        'Credit / Debit Card'
                    )
                ),

                e('button', {
                    type: 'submit', disabled: submitting,
                    style: { marginTop: '1.5rem', padding: '12px 28px', background: '#222', color: '#fff', border: 'none', borderRadius: '4px', cursor: 'pointer', fontWeight: '700', width: '100%' }
                }, submitting ? 'Placing Order...' : 'Place Order — $' + total.toFixed(2))
            ),

            // Order summary
            e('div', null,
                e('h3', null, 'Order Summary'),
                cartItems.map(function (item) {
                    var price = item.product.sale_price || item.product.price;
                    return e('div', { key: item.id, style: { display: 'flex', justifyContent: 'space-between', padding: '8px 0', borderBottom: '1px solid #eee' } },
                        e('span', null, item.product.name + ' × ' + item.quantity),
                        e('span', { style: { fontWeight: '600' } }, '$' + (parseFloat(price) * item.quantity).toFixed(2))
                    );
                }),
                e('div', { style: { display: 'flex', justifyContent: 'space-between', marginTop: '12px', fontWeight: '700', fontSize: '1.1rem' } },
                    e('span', null, 'Total'),
                    e('span', null, '$' + total.toFixed(2))
                )
            )
        )
    );
}

var fGroup = { marginBottom: '12px' };
var fLabel = { display: 'block', marginBottom: '4px', fontSize: '14px', fontWeight: '500' };
var fInput = { display: 'block', width: '100%', padding: '8px', boxSizing: 'border-box', border: '1px solid #ddd', borderRadius: '4px' };

module.exports = Checkout;
