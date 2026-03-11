import React, { useEffect, useState } from 'react';

function AdminUsers() {
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [filterRole, setFilterRole] = useState('');
    const [deleteConfirm, setDeleteConfirm] = useState(null);
    const [successMsg, setSuccessMsg] = useState('');

    useEffect(() => { fetchUsers(); }, [page, search, filterRole]);

    function fetchUsers() {
        setLoading(true);
        let url = `/api/admin/users?page=${page}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (filterRole) url += `&role=${filterRole}`;
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                setUsers(data.data || []);
                setLastPage(data.last_page || 1);
                setTotal(data.total || 0);
                setLoading(false);
            })
            .catch(() => { setError('Failed to load users.'); setLoading(false); });
    }

    function handleSearch(e) {
        e.preventDefault();
        setPage(1);
        setSearch(searchInput);
    }

    function showSuccess(msg) {
        setSuccessMsg(msg);
        setTimeout(() => setSuccessMsg(''), 3000);
    }

    function confirmDelete() {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        fetch(`/api/admin/users/${deleteConfirm.user_id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
            .then(r => r.json())
            .then(() => { setDeleteConfirm(null); fetchUsers(); showSuccess('User deleted!'); })
            .catch(() => setDeleteConfirm(null));
    }

    const adminCount = users.filter(u => u.role === 'admin').length;
    const customerCount = users.filter(u => u.role === 'customer').length;

    if (error) return <div style={{ color: '#dc2626', padding: 24 }}>{error}</div>;

    return (
        <div style={{ padding: 24 }}>
            {successMsg && (
                <div style={{
                    position: 'fixed', top: 20, right: 20, background: '#16a34a', color: '#fff',
                    padding: '12px 20px', borderRadius: 8, fontSize: 14, fontWeight: 500, zIndex: 9999,
                    boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                }}>
                    {successMsg}
                </div>
            )}

            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <h2 style={{ fontSize: 22, fontWeight: 700, color: '#1e293b' }}>Users</h2>
                <div style={{ display: 'flex', gap: 10 }}>
                    <select value={filterRole} onChange={e => { setFilterRole(e.target.value); setPage(1); }}
                        style={{ padding: '8px 14px', border: '1px solid #e2e8f0', borderRadius: 8, fontSize: 14, background: '#fff' }}>
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
            </div>

            {/* Search */}
            <form onSubmit={handleSearch} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input
                    type="text"
                    value={searchInput}
                    onChange={e => setSearchInput(e.target.value)}
                    placeholder="Search by name or email..."
                    style={{
                        flex: 1, padding: '10px 14px', border: '1px solid #e2e8f0',
                        borderRadius: 8, fontSize: 14, outline: 'none',
                    }}
                />
                <button type="submit" style={{
                    padding: '10px 20px', background: '#2563eb', color: '#fff',
                    border: 'none', borderRadius: 8, fontSize: 14, fontWeight: 600, cursor: 'pointer',
                }}>Search</button>
                {search && (
                    <button type="button" onClick={() => { setSearch(''); setSearchInput(''); setPage(1); }} style={{
                        padding: '10px 16px', background: '#f1f5f9', color: '#374151',
                        border: '1px solid #e2e8f0', borderRadius: 8, fontSize: 14, cursor: 'pointer',
                    }}>Clear</button>
                )}
            </form>

            {/* Stats */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16, marginBottom: 24 }}>
                {[
                    { label: 'Total Users', value: total, color: '#2563eb' },
                    { label: 'Admins', value: adminCount, color: '#7c3aed' },
                    { label: 'Customers', value: customerCount, color: '#16a34a' },
                ].map(stat => (
                    <div key={stat.label} style={{ background: '#fff', border: '1px solid #e2e8f0', borderRadius: 12, padding: 20 }}>
                        <div style={{ fontSize: 13, color: '#94a3b8', marginBottom: 6 }}>{stat.label}</div>
                        <div style={{ fontSize: 28, fontWeight: 700, color: stat.color }}>{stat.value}</div>
                    </div>
                ))}
            </div>

            {/* Table */}
            <div style={{ background: '#fff', borderRadius: 12, border: '1px solid #e2e8f0', overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                    <thead>
                        <tr style={{ background: '#f8fafc', borderBottom: '1px solid #e2e8f0' }}>
                            {['#', 'Name', 'Email', 'Phone', 'Role', 'Joined', 'Actions'].map(h => (
                                <th key={h} style={{ padding: '12px 16px', textAlign: 'left', fontSize: 12, fontWeight: 600, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr><td colSpan={7} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>Loading...</td></tr>
                        ) : users.length === 0 ? (
                            <tr><td colSpan={7} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>No users found.</td></tr>
                        ) : users.map((user, i) => (
                            <tr key={user.user_id} style={{ borderBottom: '1px solid #f1f5f9' }}
                                onMouseEnter={e => e.currentTarget.style.background = '#f8fafc'}
                                onMouseLeave={e => e.currentTarget.style.background = '#fff'}>
                                <td style={{ padding: '12px 16px', fontSize: 13, color: '#94a3b8' }}>{(page-1)*15+i+1}</td>
                                <td style={{ padding: '12px 16px' }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                                        <div style={{
                                            width: 36, height: 36, borderRadius: '50%',
                                            background: user.role === 'admin' ? '#ede9fe' : '#dbeafe',
                                            display: 'flex', alignItems: 'center', justifyContent: 'center',
                                            fontSize: 14, fontWeight: 700,
                                            color: user.role === 'admin' ? '#7c3aed' : '#2563eb',
                                            flexShrink: 0,
                                        }}>
                                            {user.name ? user.name.charAt(0).toUpperCase() : '?'}
                                        </div>
                                        <span style={{ fontSize: 14, fontWeight: 600, color: '#1e293b' }}>{user.name}</span>
                                    </div>
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 13, color: '#374151' }}>{user.email}</td>
                                <td style={{ padding: '12px 16px', fontSize: 13, color: '#374151' }}>{user.phone_num || '-'}</td>
                                <td style={{ padding: '12px 16px' }}>
                                    <span style={{
                                        background: user.role === 'admin' ? '#ede9fe' : '#dbeafe',
                                        color: user.role === 'admin' ? '#7c3aed' : '#2563eb',
                                        border: `1px solid ${user.role === 'admin' ? '#ddd6fe' : '#bfdbfe'}`,
                                        padding: '2px 10px', borderRadius: 20, fontSize: 12, fontWeight: 600,
                                    }}>
                                        {user.role}
                                    </span>
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 12, color: '#94a3b8', whiteSpace: 'nowrap' }}>
                                    {user.created_at ? new Date(user.created_at).toLocaleDateString() : '-'}
                                </td>
                                <td style={{ padding: '12px 16px' }}>
                                    {user.role !== 'admin' && (
                                        <button onClick={() => setDeleteConfirm(user)} style={{
                                            padding: '6px 14px', background: '#fef2f2', color: '#dc2626',
                                            border: '1px solid #fecaca', borderRadius: 6, fontSize: 13, cursor: 'pointer', fontWeight: 500,
                                        }}>Delete</button>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {lastPage > 1 && (
                <div style={{ display: 'flex', justifyContent: 'center', gap: 8, marginTop: 20 }}>
                    <button onClick={() => setPage(p => Math.max(1,p-1))} disabled={page===1}
                        style={{ padding: '7px 16px', border: '1px solid #e2e8f0', borderRadius: 8, background: page===1?'#f8fafc':'#fff', cursor: page===1?'default':'pointer', color: page===1?'#94a3b8':'#374151', fontSize: 13 }}>Prev</button>
                    <span style={{ fontSize: 13, color: '#64748b', lineHeight: '34px' }}>Page {page} of {lastPage}</span>
                    <button onClick={() => setPage(p => Math.min(lastPage,p+1))} disabled={page===lastPage}
                        style={{ padding: '7px 16px', border: '1px solid #e2e8f0', borderRadius: 8, background: page===lastPage?'#f8fafc':'#fff', cursor: page===lastPage?'default':'pointer', color: page===lastPage?'#94a3b8':'#374151', fontSize: 13 }}>Next</button>
                </div>
            )}

            {/* Delete Modal */}
            {deleteConfirm && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)', zIndex: 1000, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <div style={{ background: '#fff', borderRadius: 16, width: 380, padding: 28 }}>
                        <h3 style={{ fontSize: 18, fontWeight: 700, color: '#1e293b', marginBottom: 12 }}>Delete User</h3>
                        <p style={{ fontSize: 14, color: '#64748b', marginBottom: 24 }}>
                            Are you sure you want to delete <strong>{deleteConfirm.name}</strong>? This cannot be undone.
                        </p>
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

export default AdminUsers;