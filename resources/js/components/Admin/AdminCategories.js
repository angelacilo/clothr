import React, { useEffect, useState } from 'react';

const GENDER_OPTIONS = ['Men', 'Women', 'Unisex', 'Kids'];

function AdminCategories() {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [editingCategory, setEditingCategory] = useState(null);
    const [form, setForm] = useState({ category_name: '', gender_type: '' });
    const [formError, setFormError] = useState('');
    const [saving, setSaving] = useState(false);
    const [deleteConfirm, setDeleteConfirm] = useState(null);
    const [successMsg, setSuccessMsg] = useState('');

    useEffect(() => {
        fetchCategories();
    }, []);

    function fetchCategories() {
        setLoading(true);
        fetch('/api/admin/categories', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                setCategories(Array.isArray(data) ? data : data.data || []);
                setLoading(false);
            })
            .catch(() => {
                setError('Failed to load categories.');
                setLoading(false);
            });
    }

    function openCreate() {
        setEditingCategory(null);
        setForm({ category_name: '', gender_type: '' });
        setFormError('');
        setShowModal(true);
    }

    function openEdit(cat) {
        setEditingCategory(cat);
        setForm({ category_name: cat.category_name, gender_type: cat.gender_type || '' });
        setFormError('');
        setShowModal(true);
    }

    function closeModal() {
        setShowModal(false);
        setEditingCategory(null);
        setFormError('');
    }

    function handleFormChange(e) {
        setForm(prev => ({ ...prev, [e.target.name]: e.target.value }));
    }

    function showSuccess(msg) {
        setSuccessMsg(msg);
        setTimeout(() => setSuccessMsg(''), 3000);
    }

    function handleSave() {
        if (!form.category_name.trim()) {
            setFormError('Category name is required.');
            return;
        }
        setSaving(true);
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const isEdit = !!editingCategory;
        const url = isEdit
            ? `/admin/categories/${editingCategory.category_id}`
            : `/admin/categories`;
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(form),
        })
            .then(r => r.json())
            .then(data => {
                setSaving(false);
                if (data.errors) {
                    setFormError(Object.values(data.errors).flat().join(' '));
                    return;
                }
                closeModal();
                fetchCategories();
                showSuccess(isEdit ? 'Category updated!' : 'Category created!');
            })
            .catch(() => {
                setSaving(false);
                setFormError('Something went wrong. Please try again.');
            });
    }

    function handleDelete(cat) {
        if (cat.products_count > 0) {
            alert(`Cannot delete "${cat.category_name}" — it has ${cat.products_count} product(s).`);
            return;
        }
        setDeleteConfirm(cat);
    }

    function confirmDelete() {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        fetch(`/admin/categories/${deleteConfirm.category_id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
            .then(() => {
                setDeleteConfirm(null);
                fetchCategories();
                showSuccess('Category deleted!');
            })
            .catch(() => setDeleteConfirm(null));
    }

    if (error) return <div style={{ color: '#dc2626', padding: 24 }}>{error}</div>;

    return (
        <div style={{ padding: 24 }}>
            {/* Success Toast */}
            {successMsg && (
                <div style={{
                    position: 'fixed', top: 20, right: 20, background: '#16a34a', color: '#fff',
                    padding: '12px 20px', borderRadius: 8, fontSize: 14, fontWeight: 500, zIndex: 9999,
                    boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                }}>
                    ✓ {successMsg}
                </div>
            )}

            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <h2 style={{ fontSize: 22, fontWeight: 700, color: '#1e293b' }}>Categories</h2>
                <button onClick={openCreate} style={{
                    padding: '9px 18px', background: '#2563eb', color: '#fff',
                    border: 'none', borderRadius: 8, fontSize: 14, fontWeight: 600, cursor: 'pointer',
                }}>
                    + Add Category
                </button>
            </div>

            {/* Stats */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16, marginBottom: 24 }}>
                {[
                    { label: 'Total Categories', value: categories.length, color: '#2563eb' },
                    { label: 'Total Products', value: categories.reduce((s, c) => s + (c.products_count || 0), 0), color: '#16a34a' },
                    { label: 'Empty Categories', value: categories.filter(c => !c.products_count).length, color: '#f59e0b' },
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
                            {['#', 'Category Name', 'Gender Type', 'Products', 'Actions'].map(h => (
                                <th key={h} style={{ padding: '12px 16px', textAlign: 'left', fontSize: 12, fontWeight: 600, color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr><td colSpan={5} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>Loading...</td></tr>
                        ) : categories.length === 0 ? (
                            <tr><td colSpan={5} style={{ padding: 40, textAlign: 'center', color: '#94a3b8' }}>No categories found.</td></tr>
                        ) : categories.map((cat, i) => (
                            <tr key={cat.category_id} style={{ borderBottom: '1px solid #f1f5f9' }}
                                onMouseEnter={e => e.currentTarget.style.background = '#f8fafc'}
                                onMouseLeave={e => e.currentTarget.style.background = '#fff'}>
                                <td style={{ padding: '12px 16px', fontSize: 13, color: '#94a3b8' }}>{i + 1}</td>
                                <td style={{ padding: '12px 16px', fontSize: 14, fontWeight: 600, color: '#1e293b' }}>
                                    {cat.category_name}
                                </td>
                                <td style={{ padding: '12px 16px' }}>
                                    {cat.gender_type ? (
                                        <span style={{
                                            background: '#eff6ff', color: '#2563eb', border: '1px solid #bfdbfe',
                                            padding: '2px 10px', borderRadius: 20, fontSize: 12, fontWeight: 600,
                                        }}>{cat.gender_type}</span>
                                    ) : <span style={{ color: '#94a3b8', fontSize: 13 }}>—</span>}
                                </td>
                                <td style={{ padding: '12px 16px', fontSize: 14, color: '#374151' }}>
                                    <span style={{
                                        background: cat.products_count > 0 ? '#f0fdf4' : '#f8fafc',
                                        color: cat.products_count > 0 ? '#16a34a' : '#94a3b8',
                                        padding: '2px 10px', borderRadius: 20, fontSize: 12, fontWeight: 600,
                                        border: `1px solid ${cat.products_count > 0 ? '#bbf7d0' : '#e2e8f0'}`,
                                    }}>
                                        {cat.products_count || 0} products
                                    </span>
                                </td>
                                <td style={{ padding: '12px 16px' }}>
                                    <div style={{ display: 'flex', gap: 8 }}>
                                        <button onClick={() => openEdit(cat)} style={{
                                            padding: '6px 14px', background: '#f1f5f9', color: '#374151',
                                            border: '1px solid #e2e8f0', borderRadius: 6, fontSize: 13, cursor: 'pointer', fontWeight: 500,
                                        }}>Edit</button>
                                        <button onClick={() => handleDelete(cat)} style={{
                                            padding: '6px 14px', background: '#fef2f2', color: '#dc2626',
                                            border: '1px solid #fecaca', borderRadius: 6, fontSize: 13, cursor: 'pointer', fontWeight: 500,
                                        }}>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Create/Edit Modal */}
            {showModal && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)', zIndex: 1000, display: 'flex', alignItems: 'center', justifyContent: 'center' }}
                    onClick={closeModal}>
                    <div style={{ background: '#fff', borderRadius: 16, width: 440, padding: 28 }}
                        onClick={e => e.stopPropagation()}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                            <h3 style={{ fontSize: 18, fontWeight: 700, color: '#1e293b' }}>
                                {editingCategory ? 'Edit Category' : 'Add Category'}
                            </h3>
                            <button onClick={closeModal} style={{ background: 'none', border: 'none', fontSize: 20, cursor: 'pointer', color: '#94a3b8' }}>✕</button>
                        </div>

                        {formError && (
                            <div style={{ background: '#fef2f2', color: '#dc2626', padding: '10px 14px', borderRadius: 8, fontSize: 13, marginBottom: 16 }}>
                                {formError}
                            </div>
                        )}

                        <div style={{ marginBottom: 16 }}>
                            <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 6 }}>
                                Category Name *
                            </label>
                            <input
                                type="text"
                                name="category_name"
                                value={form.category_name}
                                onChange={handleFormChange}
                                placeholder="e.g. T-Shirts"
                                style={{
                                    width: '100%', padding: '10px 14px', border: '1px solid #e2e8f0',
                                    borderRadius: 8, fontSize: 14, outline: 'none', boxSizing: 'border-box',
                                }}
                            />
                        </div>

                        <div style={{ marginBottom: 24 }}>
                            <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 6 }}>
                                Gender Type
                            </label>
                            <select
                                name="gender_type"
                                value={form.gender_type}
                                onChange={handleFormChange}
                                style={{
                                    width: '100%', padding: '10px 14px', border: '1px solid #e2e8f0',
                                    borderRadius: 8, fontSize: 14, outline: 'none', background: '#fff', boxSizing: 'border-box',
                                }}
                            >
                                <option value="">Select gender type</option>
                                {GENDER_OPTIONS.map(g => <option key={g} value={g}>{g}</option>)}
                            </select>
                        </div>

                        <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                            <button onClick={closeModal} style={{
                                padding: '9px 18px', background: '#f1f5f9', color: '#374151',
                                border: '1px solid #e2e8f0', borderRadius: 8, fontSize: 14, cursor: 'pointer',
                            }}>Cancel</button>
                            <button onClick={handleSave} disabled={saving} style={{
                                padding: '9px 18px', background: '#2563eb', color: '#fff',
                                border: 'none', borderRadius: 8, fontSize: 14, fontWeight: 600, cursor: 'pointer',
                                opacity: saving ? 0.7 : 1,
                            }}>
                                {saving ? 'Saving...' : (editingCategory ? 'Update' : 'Create')}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Delete Confirm Modal */}
            {deleteConfirm && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)', zIndex: 1000, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <div style={{ background: '#fff', borderRadius: 16, width: 380, padding: 28 }}>
                        <h3 style={{ fontSize: 18, fontWeight: 700, color: '#1e293b', marginBottom: 12 }}>Delete Category</h3>
                        <p style={{ fontSize: 14, color: '#64748b', marginBottom: 24 }}>
                            Are you sure you want to delete <strong>"{deleteConfirm.category_name}"</strong>? This cannot be undone.
                        </p>
                        <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                            <button onClick={() => setDeleteConfirm(null)} style={{
                                padding: '9px 18px', background: '#f1f5f9', color: '#374151',
                                border: '1px solid #e2e8f0', borderRadius: 8, fontSize: 14, cursor: 'pointer',
                            }}>Cancel</button>
                            <button onClick={confirmDelete} style={{
                                padding: '9px 18px', background: '#dc2626', color: '#fff',
                                border: 'none', borderRadius: 8, fontSize: 14, fontWeight: 600, cursor: 'pointer',
                            }}>Delete</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default AdminCategories;