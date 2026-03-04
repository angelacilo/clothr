const React = require('react');
const e = React.createElement;

function Cart() {
    const [items, setItems] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [msg, setMsg] = React.useState('');

    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    function loadCart() {
        setLoading(true);
        fetch('/api/shop/cart')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setItems(data);
                setLoading(false);
            });
    }

    React.useEffect(function () { loadCart(); }, []);

    function removeItem(id) {
        fetch('/api/shop/cart/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf },
        })
            .then(function () { loadCart(); });
    }

    function updateQty(id, qty) {
        if (qty < 1) return;
        fetch('/api/shop/cart/' + id, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ quantity: qty }),
        })
            .then(function () { loadCart(); });
    }

    if (loading) return e('p', { style: { padding: '2rem', textAlign: 'center' } }, 'Loading cart...');

    if (!items.length) {
        return e('div', { style: { padding: '4rem 2rem', textAlign: 'center', fontFamily: 'sans-serif' } },
            e('h2', null, 'Your cart is empty'),
            e('a', { href: '/products', style: { color: '#222', fontWeight: '600' } }, 'Continue Shopping')
        );
    }

    var total = items.reduce(function (sum, item) {
        var price = item.product.sale_price || item.product.price;
        return sum + parseFloat(price) * item.quantity;
    }, 0);

    return e('div', { style: { padding: '2rem', fontFamily: 'sans-serif', maxWidth: '900px', margin: '0 auto' } },
        e('h1', { style: { marginBottom: '2rem' } }, 'Shopping Cart'),
        msg ? e('p', { style: { color: 'green' } }, msg) : null,

        e('div', null,
            items.map(function (item) {
                var product = item.product;
                var imgUrl = product.images && product.images[0]
                    ? '/storage/' + product.images[0].img_url
                    : '/images/placeholder.jpg';
                var unitPrice = product.sale_price || product.price;

                return e('div', {
                    key: item.id,
                    style: { display: 'flex', gap: '1.5rem', borderBottom: '1px solid #eee', padding: '1.5rem 0', alignItems: 'center' }
                },
                    e('img', { src: imgUrl, alt: product.name, style: { width: '90px', height: '110px', objectFit: 'cover', borderRadius: '6px' } }),
                    e('div', { style: { flex: 1 } },
                        e('p', { style: { margin: '0 0 4px', fontWeight: '600', fontSize: '1rem' } }, product.name),
                        e('p', { style: { margin: '0 0 12px', color: '#888', fontSize: '13px' } }, product.category ? product.category.category_name : ''),
                        e('div', { style: { display: 'flex', alignItems: 'center', gap: '10px' } },
                            e('button', { onClick: function () { updateQty(item.id, item.quantity - 1); }, style: qtyBtn }, '−'),
                            e('span', null, item.quantity),
                            e('button', { onClick: function () { updateQty(item.id, item.quantity + 1); }, style: qtyBtn }, '+'),
                        )
                    ),
                    e('div', { style: { textAlign: 'right' } },
                        e('p', { style: { fontWeight: '700', margin: '0 0 8px' } }, '$' + (parseFloat(unitPrice) * item.quantity).toFixed(2)),
                        e('button', {
                            onClick: function () { removeItem(item.id); },
                            style: { color: '#e74c3c', background: 'none', border: 'none', cursor: 'pointer', fontSize: '13px' }
                        }, 'Remove')
                    )
                );
            })
        ),

        e('div', { style: { textAlign: 'right', marginTop: '2rem' } },
            e('p', { style: { fontSize: '1.2rem', fontWeight: '700' } }, 'Total: $' + total.toFixed(2)),
            e('a', { href: '/checkout', style: { display: 'inline-block', padding: '12px 32px', background: '#222', color: '#fff', textDecoration: 'none', borderRadius: '4px', fontWeight: '700', marginTop: '1rem' } }, 'Proceed to Checkout')
        )
    );
}

var qtyBtn = { width: '28px', height: '28px', border: '1px solid #ddd', borderRadius: '4px', cursor: 'pointer', background: '#f5f5f5' };

module.exports = Cart;
