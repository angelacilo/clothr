const React = require('react');
const e = React.createElement;

function OrderHistory() {
    const [orders, setOrders] = React.useState([]);
    const [loading, setLoading] = React.useState(true);

    React.useEffect(function () {
        fetch('/api/shop/orders')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setOrders(data.data || data);
                setLoading(false);
            });
    }, []);

    if (loading) return e('p', { style: { padding: '2rem', textAlign: 'center' } }, 'Loading orders...');

    if (!orders.length) {
        return e('div', { style: { padding: '4rem 2rem', textAlign: 'center', fontFamily: 'sans-serif' } },
            e('h2', null, 'No orders yet'),
            e('a', { href: '/products', style: { color: '#222', fontWeight: '600' } }, 'Start Shopping')
        );
    }

    return e('div', { style: { padding: '2rem', fontFamily: 'sans-serif', maxWidth: '900px', margin: '0 auto' } },
        e('h1', { style: { marginBottom: '2rem' } }, 'My Orders'),
        e('div', null,
            orders.map(function (order) {
                return e('div', {
                    key: order.id,
                    style: { border: '1px solid #eee', borderRadius: '8px', padding: '1.5rem', marginBottom: '1.5rem' }
                },
                    e('div', { style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' } },
                        e('div', null,
                            e('strong', null, 'Order #' + order.id),
                            e('span', { style: { marginLeft: '12px', color: '#888', fontSize: '13px' } }, new Date(order.created_at).toLocaleDateString())
                        ),
                        e('span', {
                            style: {
                                padding: '4px 12px', borderRadius: '12px', fontSize: '13px', fontWeight: '600',
                                background: statusBg(order.status),
                                color: statusColor(order.status),
                            }
                        }, order.status)
                    ),
                    order.items && order.items.map(function (item) {
                        var imgUrl = item.product && item.product.images && item.product.images[0]
                            ? '/storage/' + item.product.images[0].img_url
                            : '/images/placeholder.jpg';
                        return e('div', { key: item.id, style: { display: 'flex', gap: '1rem', alignItems: 'center', padding: '8px 0', borderTop: '1px solid #f5f5f5' } },
                            e('img', { src: imgUrl, alt: item.product ? item.product.name : '', style: { width: '50px', height: '60px', objectFit: 'cover', borderRadius: '4px' } }),
                            e('div', { style: { flex: 1 } },
                                e('p', { style: { margin: 0, fontWeight: '600' } }, item.product ? item.product.name : 'N/A'),
                                e('p', { style: { margin: 0, color: '#888', fontSize: '13px' } }, 'Qty: ' + item.quantity)
                            ),
                            e('span', { style: { fontWeight: '600' } }, '$' + parseFloat(item.price).toFixed(2))
                        );
                    }),
                    e('div', { style: { display: 'flex', justifyContent: 'space-between', marginTop: '12px' } },
                        e('a', { href: '/orders/' + order.id, style: { color: '#222', fontWeight: '600', fontSize: '14px' } }, 'View Details'),
                        e('strong', null, 'Total: $' + parseFloat(order.total_amount).toFixed(2))
                    )
                );
            })
        )
    );
}

function statusBg(status) {
    var map = { pending: '#fff3cd', processing: '#cce5ff', shipped: '#d4edda', delivered: '#d4edda', cancelled: '#f8d7da' };
    return map[status] || '#eee';
}
function statusColor(status) {
    var map = { pending: '#856404', processing: '#004085', shipped: '#155724', delivered: '#155724', cancelled: '#721c24' };
    return map[status] || '#333';
}

module.exports = OrderHistory;
