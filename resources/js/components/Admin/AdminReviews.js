const React = require('react');
const e = React.createElement;

function AdminReviews() {
    const [reviews, setReviews] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [msg, setMsg] = React.useState('');

    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    function loadReviews() {
        setLoading(true);
        fetch('/api/admin/reviews')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setReviews(data.data || data);
                setLoading(false);
            });
    }

    React.useEffect(function () { loadReviews(); }, []);

    function handleDelete(reviewId) {
        if (!confirm('Delete this review?')) return;
        fetch('/api/admin/reviews/' + reviewId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf },
        })
            .then(function () {
                setMsg('Review deleted.');
                loadReviews();
            });
    }

    function handleApprove(reviewId) {
        fetch('/api/admin/reviews/' + reviewId + '/approve', {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': csrf },
        })
            .then(function (res) { return res.json(); })
            .then(function () {
                setMsg('Review approved.');
                loadReviews();
            });
    }

    if (loading) return e('p', { style: { padding: '2rem' } }, 'Loading reviews...');

    return e('div', { style: { padding: '2rem' } },
        e('h2', null, 'Product Reviews'),
        msg ? e('p', { style: { color: 'green', marginBottom: '1rem' } }, msg) : null,
        e('table', { style: { width: '100%', borderCollapse: 'collapse' } },
            e('thead', null,
                e('tr', null,
                    e('th', { style: tableTh }, 'ID'),
                    e('th', { style: tableTh }, 'Product'),
                    e('th', { style: tableTh }, 'User'),
                    e('th', { style: tableTh }, 'Rating'),
                    e('th', { style: tableTh }, 'Comment'),
                    e('th', { style: tableTh }, 'Approved'),
                    e('th', { style: tableTh }, 'Actions'),
                )
            ),
            e('tbody', null,
                reviews.map(function (review) {
                    return e('tr', { key: review.id },
                        e('td', { style: tableTd }, review.id),
                        e('td', { style: tableTd }, review.product ? review.product.name : 'N/A'),
                        e('td', { style: tableTd }, review.user ? review.user.name : 'N/A'),
                        e('td', { style: tableTd }, '★ ' + review.rating),
                        e('td', { style: Object.assign({}, tableTd, { maxWidth: '200px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }) }, review.comment),
                        e('td', { style: tableTd }, review.is_approved ? 'Yes' : 'No'),
                        e('td', { style: tableTd },
                            !review.is_approved
                                ? e('button', { onClick: function () { handleApprove(review.id); }, style: { marginRight: '6px' } }, 'Approve')
                                : null,
                            e('button', { onClick: function () { handleDelete(review.id); }, style: { color: 'red' } }, 'Delete')
                        )
                    );
                })
            )
        )
    );
}

var tableTh = { textAlign: 'left', padding: '10px 12px', borderBottom: '2px solid #e0e0e0', fontWeight: '600' };
var tableTd = { padding: '10px 12px', borderBottom: '1px solid #eee' };

module.exports = AdminReviews;
