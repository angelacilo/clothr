import React, { useEffect, useState } from 'react';

const STAR_COLORS = { 5: '#16a34a', 4: '#2563eb', 3: '#f59e0b', 2: '#f97316', 1: '#dc2626' };

function Stars({ rating }) {
    return (
        <span style={{ color: STAR_COLORS[rating] || '#94a3b8', fontSize: 15, fontWeight: 700 }}>
            {rating}/5
        </span>
    );
}

function AdminReviews() {
    const [reviews, setReviews] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [filterRating, setFilterRating] = useState('');
    const [deleteConfirm, setDeleteConfirm] = useState(null);
    const [successMsg, setSuccessMsg] = useState('');

    useEffect(() => { fetchReviews(); }, [page, filterRating]);

    function fetchReviews() {
        setLoading(true);
        let url = `/api/admin/reviews?page=${page}`;
        if (filterRating) url += `&rating=${filterRating}`;
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                setReviews(data.data || []);
                setLastPage(data.last_page || 1);
                setTotal(data.total || 0);
                setLoading(false);
            })
            .catch(() => { setError('Failed to load reviews.'); setLoading(false); });
    }

    function showSuccess(msg) {
        setSuccessMsg(msg);
        setTimeout(() => setSuccessMsg(''), 3000);
    }

    function confirmDelete() {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        fetch(`/api/admin/reviews/${deleteConfirm.review_id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
            .then(r => r.json())
            .then(() => { setDeleteConfirm(null); fetchReviews(); showSuccess('Review deleted!'); })
            .catch(() => setDeleteConfirm(null));
    }

    const avgRating = reviews.length
        ? (reviews.reduce((s, r) => s + r.rating, 0) / reviews.length).toFixed(1)
        : null;

    if (error) return <div style={{ color: '#dc2626', padding: 24 }}>{error}</div>;

    return (
        <div style={{ padding: 24 }}>
            {successMsg && (
                <div style={{
                    position: 'fixed', top: 20, right: 20, background: '#16a34a', color: '#fff',
                    padding: '12px 20px', borderRadius: 8, fontSize: 14, fontWeight: 500, zIndex: 9999,
                }}>
                    {successMsg}
                </div>
            )}

            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <h2 style={{ fontSize: 22, fontWeight: 700, color: '#1e293b' }}>Reviews</h2>
                <select value={filterRating} onChange={e => { setFilterRating(e.target.value); setPage(1); }}
                    style={{ padding: '8px 14px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: 14, background: '#fff' }}>
                    <option value="">All Ratings</option>
                    {[5,4,3,2,1].map(n => <option key={n} value={n}>{n} Star{n!==1?'s':''}</option>)}
                </select>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16, marginBottom: 24 }}>
                {[
                    { label: 'Total Reviews', value: total, color: '#2563eb' },
                    { label: 'Average Rating', value: avgRating ? `${avgRating} / 5` : 'N/A', color: '#f59e0b' },
                    { label: 'This Page', value: reviews.length, color: '#16a34a' },
                ].map(stat => (
                    <div key={stat.label} style={{ background: '#fff', border: '1px solid #e2e8f0', borderRadius: 12, padding: 20 }}>
                        <div style={{ fontSize: 13, color: '#94a3b8', marginBottom: 6 }}>{stat.label}</div>
                        <div style={{ fontSize: 28, fontWeight: 700, color: stat.color }}>{stat.value}</div>
                    </div>
                ))}
            </div>

            <div style={{ background: '#fff', borderRadius: 12, border: '1px solid #e2e8f0', overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                    <thead>
                        <tr style={{ background: '#f8fafc', borderBottom: '1px solid #e2e8f0' }}>
                            {['#','Product','User','Rating','Comment','Date','Actions'].map(h => (
                                <th key={h} style={{ padding: '12px 16px', textAlign: 'left', fontSize: 12, fontWeight: 600, color: '#64748b', textTransform: 'uppercase' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr><td colSpan={7} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>Loading...</td></tr>
                        ) : reviews.length === 0 ? (
                            <tr><td colSpan={7} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>No reviews found.</td></tr>
                        ) : reviews.map((review, i) => (
                            <tr key={review.review_id} style={{ borderBottom: '1px solid #f1f5f9' }}
                                onMouseEnter={e => e.currentTarget.style.background = '#f8fafc'}
                                onMouseLeave={e => e.currentTarget.style.background = '#fff'}>
                                <td style={{ padding: '12px 16px', fontSize: 13, color: '#94a3b8' }}>{(page-1)*15+i+1}</td>
                                <td style={{ padding: '12px 16px', fontSize: 14, fontWeight: 600, color: '#1e293b' }}>
                                    {review.product?.name || '-'}
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 13, color: '#374151' }}>
                                    <div style={{ fontWeight: 500 }}>
                                        {review.user ? `${review.user.first_name} ${review.user.last_name}` : '-'}
                                    </div>
                                    <div style={{ fontSize: 12, color: '#94a3b8' }}>{review.user?.email}</div>
                                </td>
                                <td style={{ padding: '12px 16px' }}><Stars rating={review.rating} /></td>
                                <td style={{ padding: '12px 16px', fontSize: 13, color: '#374151', maxWidth: 220 }}>
                                    {review.comment
                                        ? (review.comment.length > 80 ? review.comment.slice(0,80)+'...' : review.comment)
                                        : <span style={{ color: '#94a3b8' }}>No comment</span>}
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 12, color: '#94a3b8', whiteSpace: 'nowrap' }}>
                                    {review.created_at ? new Date(review.created_at).toLocaleDateString() : '-'}
                                </td>
                                <td style={{ padding: '12px 16px' }}>
                                    <button onClick={() => setDeleteConfirm(review)} style={{
                                        padding: '6px 14px', background: '#fef2f2', color: '#dc2626',
                                        border: '1px solid #fecaca', borderRadius: 6, fontSize: 13, cursor: 'pointer', fontWeight: 500,
                                    }}>Delete</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {lastPage > 1 && (
                <div style={{ display: 'flex', justifyContent: 'center', gap: 8, marginTop: 20 }}>
                    <button onClick={() => setPage(p => Math.max(1,p-1))} disabled={page===1}
                        style={{ padding: '7px 16px', border: '1px solid #e2e8f0', borderRadius: 8, background: page===1?'#f8fafc':'#fff', cursor: page===1?'default':'pointer', color: page===1?'#94a3b8':'#374151', fontSize: 13 }}>Prev</button>
                    <span style={{ fontSize: 13, color: '#64748b', lineHeight: '34px' }}>Page {page} of {lastPage}</span>
                    <button onClick={() => setPage(p => Math.min(lastPage,p+1))} disabled={page===lastPage}
                        style={{ padding: '7px 16px', border: '1px solid #e2e8f0', borderRadius: 8, background: page===lastPage?'#f8fafc':'#fff', cursor: page===lastPage?'default':'pointer', color: page===lastPage?'#94a3b8':'#374151', fontSize: 13 }}>Next</button>
                </div>
            )}

            {deleteConfirm && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)', zIndex: 1000, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <div style={{ background: '#fff', borderRadius: 16, width: 380, padding: 28 }}>
                        <h3 style={{ fontSize: 18, fontWeight: 700, color: '#1e293b', marginBottom: 12 }}>Delete Review</h3>
                        <p style={{ fontSize: 14, color: '#64748b', marginBottom: 24 }}>Are you sure you want to delete this review? This cannot be undone.</p>
                        <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                            <button onClick={() => setDeleteConfirm(null)} style={{ padding: '9px 18px', background: '#f1f5f9', color: '#374151', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: 14, cursor: 'pointer' }}>Cancel</button>
                            <button onClick={confirmDelete} style={{ padding: '9px 18px', background: '#dc2626', color: '#fff', border: 'none', borderRadius: 8, fontSize: 14, fontWeight: 600, cursor: 'pointer' }}>Delete</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default AdminReviews;