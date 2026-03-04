const React = require('react');
const e = React.createElement;

function OrderSuccess() {
    const [order, setOrder] = React.useState(null);
    const [loading, setLoading] = React.useState(true);

    React.useEffect(function () {
        var root = document.getElementById('shop-order-success-root');
        var orderId = root ? root.dataset.orderId : null;
        if (!orderId) { setLoading(false); return; }

        fetch('/api/shop/orders/' + orderId)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setOrder(data);
                setLoading(false);
            });
    }, []);

    if (loading) return e('p', { style: { padding: '2rem', textAlign: 'center' } }, 'Loading...');

    return e('div', { style: { padding: '4rem 2rem', textAlign: 'center', fontFamily: 'sans-serif', maxWidth: '600px', margin: '0 auto' } },

        // Success icon
        e('div', { style: { marginBottom: '1.5rem' } },
            e('svg', { width: '72', height: '72', viewBox: '0 0 24 24', fill: 'none', stroke: '#27ae60', strokeWidth: '2', style: { display: 'block', margin: '0 auto' } },
                e('circle', { cx: '12', cy: '12', r: '10' }),
                e('polyline', { points: '9 12 11 14 15 10' })
            )
        ),

        e('h1', { style: { fontSize: '2rem', marginBottom: '0.5rem' } }, 'Order Confirmed!'),
        e('p', { style: { color: '#555', marginBottom: '2rem' } }, 'Thank you for your purchase. Your order has been placed successfully.'),

        order ? e('div', { style: { border: '1px solid #eee', borderRadius: '8px', padding: '1.5rem', textAlign: 'left', marginBottom: '2rem' } },
            e('div', { style: { display: 'flex', justifyContent: 'space-between', marginBottom: '8px' } },
                e('span', { style: { color: '#888', fontSize: '14px' } }, 'Order Number'),
                e('strong', null, '#' + order.id)
            ),
            e('div', { style: { display: 'flex', justifyContent: 'space-between', marginBottom: '8px' } },
                e('span', { style: { color: '#888', fontSize: '14px' } }, 'Status'),
                e('strong', null, order.status)
            ),
            e('div', { style: { display: 'flex', justifyContent: 'space-between' } },
                e('span', { style: { color: '#888', fontSize: '14px' } }, 'Total'),
                e('strong', null, '$' + parseFloat(order.total_amount).toFixed(2))
            )
        ) : null,

        e('div', { style: { display: 'flex', gap: '1rem', justifyContent: 'center' } },
            e('a', { href: '/orders', style: outlineBtn }, 'View My Orders'),
            e('a', { href: '/products', style: filledBtn }, 'Continue Shopping')
        )
    );
}

var outlineBtn = { display: 'inline-block', padding: '12px 24px', border: '2px solid #222', color: '#222', textDecoration: 'none', borderRadius: '4px', fontWeight: '700' };
var filledBtn = { display: 'inline-block', padding: '12px 24px', background: '#222', color: '#fff', textDecoration: 'none', borderRadius: '4px', fontWeight: '700' };

module.exports = OrderSuccess;
