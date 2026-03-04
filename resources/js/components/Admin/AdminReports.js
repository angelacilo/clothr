const React = require('react');
const e = React.createElement;

function AdminReports() {
    const [report, setReport] = React.useState(null);
    const [loading, setLoading] = React.useState(true);

    React.useEffect(function () {
        fetch('/api/admin/reports')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setReport(data);
                setLoading(false);
            });
    }, []);

    if (loading) return e('p', { style: { padding: '2rem' } }, 'Loading reports...');

    var r = report || {};

    return e('div', { style: { padding: '2rem' } },
        e('h2', null, 'Reports & Analytics'),

        e('div', { style: { display: 'flex', gap: '1.5rem', flexWrap: 'wrap', marginBottom: '2rem' } },
            statCard('Total Revenue', '$' + parseFloat(r.total_revenue || 0).toFixed(2)),
            statCard('Total Orders', r.total_orders || 0),
            statCard('Total Users', r.total_users || 0),
            statCard('Total Products', r.total_products || 0),
        ),

        r.top_products && r.top_products.length
            ? e('div', null,
                e('h3', null, 'Top Selling Products'),
                e('table', { style: { width: '100%', borderCollapse: 'collapse' } },
                    e('thead', null,
                        e('tr', null,
                            e('th', { style: tableTh }, 'Product'),
                            e('th', { style: tableTh }, 'Units Sold'),
                            e('th', { style: tableTh }, 'Revenue'),
                        )
                    ),
                    e('tbody', null,
                        r.top_products.map(function (p, idx) {
                            return e('tr', { key: idx },
                                e('td', { style: tableTd }, p.name),
                                e('td', { style: tableTd }, p.units_sold),
                                e('td', { style: tableTd }, '$' + parseFloat(p.revenue || 0).toFixed(2)),
                            );
                        })
                    )
                )
            )
            : null,

        r.recent_orders && r.recent_orders.length
            ? e('div', { style: { marginTop: '2rem' } },
                e('h3', null, 'Recent Orders'),
                e('table', { style: { width: '100%', borderCollapse: 'collapse' } },
                    e('thead', null,
                        e('tr', null,
                            e('th', { style: tableTh }, 'Order #'),
                            e('th', { style: tableTh }, 'Customer'),
                            e('th', { style: tableTh }, 'Status'),
                            e('th', { style: tableTh }, 'Total'),
                        )
                    ),
                    e('tbody', null,
                        r.recent_orders.map(function (o) {
                            return e('tr', { key: o.id },
                                e('td', { style: tableTd }, '#' + o.id),
                                e('td', { style: tableTd }, o.user ? o.user.name : 'N/A'),
                                e('td', { style: tableTd }, o.status),
                                e('td', { style: tableTd }, '$' + parseFloat(o.total_amount || 0).toFixed(2)),
                            );
                        })
                    )
                )
            )
            : null
    );
}

function statCard(label, value) {
    return e('div', {
        key: label,
        style: {
            background: '#f7f7f7',
            borderRadius: '8px',
            padding: '1.5rem 2rem',
            minWidth: '160px',
            flex: '1',
            boxShadow: '0 1px 4px rgba(0,0,0,0.07)',
        }
    },
        e('p', { style: { margin: 0, fontSize: '13px', color: '#888' } }, label),
        e('p', { style: { margin: '6px 0 0', fontSize: '24px', fontWeight: '700' } }, String(value))
    );
}

var tableTh = { textAlign: 'left', padding: '10px 12px', borderBottom: '2px solid #e0e0e0', fontWeight: '600' };
var tableTd = { padding: '10px 12px', borderBottom: '1px solid #eee' };

module.exports = AdminReports;
