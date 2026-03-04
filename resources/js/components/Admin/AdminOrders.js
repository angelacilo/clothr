const React = require('react');
const e = React.createElement;

function AdminOrders() {
    const [orders, setOrders] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [view, setView] = React.useState('list'); // 'list' | 'detail'
    const [selected, setSelected] = React.useState(null);
    const [statusMsg, setStatusMsg] = React.useState('');

    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    function loadOrders() {
        setLoading(true);
        fetch('/api/admin/orders')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setOrders(data.data || data);
                setLoading(false);
            });
    }

    React.useEffect(function () { loadOrders(); }, []);

    function openDetail(order) {
        setSelected(order);
        setView('detail');
        setStatusMsg('');
    }

    function updateStatus(orderId, status) {
        fetch('/api/admin/orders/' + orderId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ status: status }),
        })
            .then(function (res) { return res.json(); })
            .then(function (updated) {
                setStatusMsg('Status updated to "' + status + '".');
                setSelected(Object.assign({}, selected, { status: updated.status || status }));
                loadOrders();
            });
    }

    if (loading) return e('p', { style: { padding: '2rem' } }, 'Loading orders...');

    if (view === 'detail' && selected) {
        return e('div', { style: { padding: '2rem' } },
            e('button', { onClick: function () { setView('list'); setSelected(null); } }, '← Back to Orders'),
            e('h2', null, 'Order #' + selected.id),
            e('p', null, 'Customer: ' + (selected.user ? selected.user.name : 'N/A')),
            e('p', null, 'Status: ' + selected.status),
            e('p', null, 'Total: $' + parseFloat(selected.total_amount || 0).toFixed(2)),
            e('p', null, 'Date: ' + new Date(selected.created_at).toLocaleDateString()),
            statusMsg ? e('p', { style: { color: 'green' } }, statusMsg) : null,
            e('div', { style: { marginTop: '1rem' } },
                ['pending', 'processing', 'shipped', 'delivered', 'cancelled'].map(function (s) {
                    return e('button', {
                        key: s,
                        onClick: function () { updateStatus(selected.id, s); },
                        style: {
                            marginRight: '8px',
                            padding: '6px 12px',
                            background: selected.status === s ? '#333' : '#f0f0f0',
                            color: selected.status === s ? '#fff' : '#333',
                            border: '1px solid #ccc',
                            borderRadius: '4px',
                            cursor: 'pointer',
                        }
                    }, s.charAt(0).toUpperCase() + s.slice(1));
                })
            )
        );
    }

    return e('div', { style: { padding: '2rem' } },
        e('h2', null, 'Orders'),
        e('table', { style: { width: '100%', borderCollapse: 'collapse' } },
            e('thead', null,
                e('tr', null,
                    e('th', { style: tableTh }, 'Order ID'),
                    e('th', { style: tableTh }, 'Customer'),
                    e('th', { style: tableTh }, 'Status'),
                    e('th', { style: tableTh }, 'Total'),
                    e('th', { style: tableTh }, 'Date'),
                    e('th', { style: tableTh }, 'Actions'),
                )
            ),
            e('tbody', null,
                orders.map(function (order) {
                    return e('tr', { key: order.id },
                        e('td', { style: tableTd }, '#' + order.id),
                        e('td', { style: tableTd }, order.user ? order.user.name : 'N/A'),
                        e('td', { style: tableTd }, order.status),
                        e('td', { style: tableTd }, '$' + parseFloat(order.total_amount || 0).toFixed(2)),
                        e('td', { style: tableTd }, new Date(order.created_at).toLocaleDateString()),
                        e('td', { style: tableTd },
                            e('button', { onClick: function () { openDetail(order); } }, 'View')
                        )
                    );
                })
            )
        )
    );
}

var tableTh = { textAlign: 'left', padding: '10px 12px', borderBottom: '2px solid #e0e0e0', fontWeight: '600' };
var tableTd = { padding: '10px 12px', borderBottom: '1px solid #eee' };

module.exports = AdminOrders;
