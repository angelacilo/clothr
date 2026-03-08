import React from 'react';
const e = React.createElement;

function AdminProductForm({ productId }) {
    const isEdit = !!productId;

    const [form, setForm] = React.useState({
        name: '',
        slug: '',
        description: '',
        category_id: '',
        price: '',
        sale_price: '',
        stock_quantity: '',
        status: 'active',
        is_featured: false,
    });

    const [categories, setCategories] = React.useState([]);
    const [existingImages, setExistingImages] = React.useState([]);
    const [newImages, setNewImages] = React.useState([]);
    const [previews, setPreviews] = React.useState([]);
    const [errors, setErrors] = React.useState({});
    const [loading, setLoading] = React.useState(false);
    const [fetching, setFetching] = React.useState(isEdit);

    // Load categories
    React.useEffect(() => {
        fetch('/api/admin/categories', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => setCategories(data || []));
    }, []);

    // Load product data if editing
    React.useEffect(() => {
        if (!isEdit) return;
        fetch('/api/admin/products/' + productId, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                setForm({
                    name: data.name || '',
                    slug: data.slug || '',
                    description: data.description || '',
                    category_id: data.category_id || '',
                    price: data.price || '',
                    sale_price: data.sale_price || '',
                    stock_quantity: data.inventory ? data.inventory.available_qty : '',
                    status: data.status || 'active',
                    is_featured: !!data.is_featured,
                });
                setExistingImages(data.images || []);
                setFetching(false);
            });
    }, [productId]);

    const handleChange = (field, value) => {
        setForm(prev => ({ ...prev, [field]: value }));
        setErrors(prev => ({ ...prev, [field]: null }));
        // Auto-generate slug from name
        if (field === 'name' && !isEdit) {
            const slug = value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            setForm(prev => ({ ...prev, name: value, slug }));
        }
    };

    const handleImages = (files) => {
        const arr = Array.from(files);
        setNewImages(arr);
        const urls = arr.map(f => URL.createObjectURL(f));
        setPreviews(urls);
    };

    const handleSubmit = async () => {
        setLoading(true);
        setErrors({});

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const formData = new FormData();

        formData.append('name', form.name);
        formData.append('slug', form.slug);
        formData.append('description', form.description);
        formData.append('category_id', form.category_id);
        formData.append('price', form.price);
        formData.append('sale_price', form.sale_price || '');
        formData.append('stock_quantity', form.stock_quantity);
        formData.append('status', form.status);
        if (form.is_featured) formData.append('is_featured', '1');
        if (isEdit) formData.append('_method', 'PUT');

        newImages.forEach(img => formData.append('images[]', img));

        const url = isEdit ? '/admin/products/' + productId : '/admin/products';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (res.redirected) {
                window.location.href = res.url;
                return;
            }

            const data = await res.json();

            if (res.status === 422) {
                setErrors(data.errors || {});
                setLoading(false);
                return;
            }

            if (res.ok) {
                window.location.href = '/admin/products';
            }
        } catch (err) {
            setErrors({ general: 'Something went wrong. Please try again.' });
        }

        setLoading(false);
    };

    // Styles
    const s = {
        container: { maxWidth: 800, margin: '0 auto', padding: '24px' },
        card: { background: '#fff', border: '1px solid #e5e7eb', borderRadius: 10, padding: 28, marginBottom: 20 },
        sectionTitle: { fontSize: 15, fontWeight: 700, color: '#374151', marginBottom: 18, paddingBottom: 10, borderBottom: '1px solid #f3f4f6' },
        formRow: { display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: 16 },
        formGroup: { marginBottom: 16 },
        label: { display: 'block', fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 6 },
        input: { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 6, fontSize: 14, outline: 'none', boxSizing: 'border-box' },
        inputError: { width: '100%', padding: '8px 12px', border: '1px solid #ef4444', borderRadius: 6, fontSize: 14, outline: 'none', boxSizing: 'border-box' },
        errorMsg: { color: '#ef4444', fontSize: 12, marginTop: 4 },
        select: { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 6, fontSize: 14, background: '#fff', boxSizing: 'border-box' },
        textarea: { width: '100%', padding: '8px 12px', border: '1px solid #d1d5db', borderRadius: 6, fontSize: 14, resize: 'vertical', boxSizing: 'border-box' },
        previewGrid: { display: 'flex', flexWrap: 'wrap', gap: 10, marginBottom: 16 },
        previewImg: { width: 100, height: 100, objectFit: 'cover', borderRadius: 6, border: '1px solid #e5e7eb' },
        actions: { display: 'flex', gap: 12, marginTop: 8 },
        btnPrimary: { background: '#3b82f6', color: '#fff', border: 'none', padding: '10px 24px', borderRadius: 6, fontSize: 14, fontWeight: 600, cursor: 'pointer' },
        btnSecondary: { background: '#fff', color: '#374151', border: '1px solid #d1d5db', padding: '10px 24px', borderRadius: 6, fontSize: 14, fontWeight: 600, cursor: 'pointer', textDecoration: 'none' },
        errorBanner: { background: '#fef2f2', border: '1px solid #fecaca', borderRadius: 6, padding: '12px 16px', color: '#dc2626', fontSize: 13, marginBottom: 16 },
        checkboxLabel: { display: 'flex', alignItems: 'center', gap: 8, fontSize: 14, cursor: 'pointer' },
        smallText: { fontSize: 12, color: '#6b7280', marginTop: 4 },
        loadingOverlay: { opacity: 0.6, pointerEvents: 'none' },
    };

    if (fetching) {
        return e('div', { style: { padding: 40, textAlign: 'center', color: '#6b7280' } }, 'Loading product...');
    }

    return e('div', { style: s.container },

        // Error banner
        errors.general && e('div', { style: s.errorBanner }, errors.general),

        e('div', { style: loading ? s.loadingOverlay : {} },

            // Product Information
            e('div', { style: s.card },
                e('h3', { style: s.sectionTitle }, 'Product Information'),

                // Name
                e('div', { style: s.formGroup },
                    e('label', { style: s.label }, 'Product Name *'),
                    e('input', {
                        type: 'text',
                        value: form.name,
                        onChange: ev => handleChange('name', ev.target.value),
                        placeholder: 'Enter product name',
                        style: errors.name ? s.inputError : s.input,
                    }),
                    errors.name && e('span', { style: s.errorMsg }, errors.name[0])
                ),

                // Slug
                e('div', { style: s.formGroup },
                    e('label', { style: s.label }, 'Slug (auto-generated)'),
                    e('input', {
                        type: 'text',
                        value: form.slug,
                        onChange: ev => handleChange('slug', ev.target.value),
                        placeholder: 'Leave blank to auto-generate',
                        style: s.input,
                    })
                ),

                // Description
                e('div', { style: s.formGroup },
                    e('label', { style: s.label }, 'Description *'),
                    e('textarea', {
                        value: form.description,
                        onChange: ev => handleChange('description', ev.target.value),
                        placeholder: 'Enter product description',
                        rows: 4,
                        style: errors.description ? { ...s.textarea, border: '1px solid #ef4444' } : s.textarea,
                    }),
                    errors.description && e('span', { style: s.errorMsg }, errors.description[0])
                ),

                // Category, Price, Sale Price
                e('div', { style: s.formRow },
                    e('div', { style: s.formGroup },
                        e('label', { style: s.label }, 'Category *'),
                        e('select', {
                            value: form.category_id,
                            onChange: ev => handleChange('category_id', ev.target.value),
                            style: errors.category_id ? { ...s.select, border: '1px solid #ef4444' } : s.select,
                        },
                            e('option', { value: '' }, 'Select a category'),
                            ...categories.map(cat =>
                                e('option', { key: cat.category_id, value: cat.category_id }, cat.category_name)
                            )
                        ),
                        errors.category_id && e('span', { style: s.errorMsg }, errors.category_id[0])
                    ),
                    e('div', { style: s.formGroup },
                        e('label', { style: s.label }, 'Price *'),
                        e('input', {
                            type: 'number',
                            value: form.price,
                            onChange: ev => handleChange('price', ev.target.value),
                            placeholder: '0.00',
                            step: '0.01',
                            style: errors.price ? s.inputError : s.input,
                        }),
                        errors.price && e('span', { style: s.errorMsg }, errors.price[0])
                    ),
                    e('div', { style: s.formGroup },
                        e('label', { style: s.label }, 'Sale Price'),
                        e('input', {
                            type: 'number',
                            value: form.sale_price,
                            onChange: ev => handleChange('sale_price', ev.target.value),
                            placeholder: '0.00',
                            step: '0.01',
                            style: s.input,
                        })
                    )
                )
            ),

            // Inventory
            e('div', { style: s.card },
                e('h3', { style: s.sectionTitle }, 'Inventory'),
                e('div', { style: s.formGroup },
                    e('label', { style: s.label }, 'Stock Quantity *'),
                    e('input', {
                        type: 'number',
                        value: form.stock_quantity,
                        onChange: ev => handleChange('stock_quantity', ev.target.value),
                        placeholder: '0',
                        style: errors.stock_quantity ? s.inputError : s.input,
                    }),
                    errors.stock_quantity && e('span', { style: s.errorMsg }, errors.stock_quantity[0])
                )
            ),

            // Media
            e('div', { style: s.card },
                e('h3', { style: s.sectionTitle }, 'Media'),

                // Existing images (edit mode)
                existingImages.length > 0 && e('div', { style: { marginBottom: 16 } },
                    e('p', { style: { fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 8 } }, 'Current Images'),
                    e('div', { style: s.previewGrid },
                        ...existingImages.map(img =>
                            e('img', {
                                key: img.id,
                                src: '/storage/' + img.image_path,
                                alt: 'Product image',
                                style: s.previewImg,
                            })
                        )
                    )
                ),

                // New image upload
                e('div', { style: s.formGroup },
                    e('label', { style: s.label }, isEdit ? 'Add More Images' : 'Product Images'),
                    e('input', {
                        type: 'file',
                        multiple: true,
                        accept: 'image/*',
                        onChange: ev => handleImages(ev.target.files),
                        style: s.input,
                    }),
                    e('small', { style: s.smallText }, 'You can upload multiple images')
                ),

                // New image previews
                previews.length > 0 && e('div', { style: s.previewGrid },
                    ...previews.map((url, i) =>
                        e('img', { key: i, src: url, style: s.previewImg })
                    )
                )
            ),

            // Settings
            e('div', { style: s.card },
                e('h3', { style: s.sectionTitle }, 'Settings'),
                e('div', { style: s.formRow },
                    e('div', { style: s.formGroup },
                        e('label', { style: s.label }, 'Status *'),
                        e('select', {
                            value: form.status,
                            onChange: ev => handleChange('status', ev.target.value),
                            style: s.select,
                        },
                            e('option', { value: 'active' }, 'Active'),
                            e('option', { value: 'inactive' }, 'Inactive')
                        )
                    ),
                    e('div', { style: s.formGroup },
                        e('label', { style: s.checkboxLabel },
                            e('input', {
                                type: 'checkbox',
                                checked: form.is_featured,
                                onChange: ev => handleChange('is_featured', ev.target.checked),
                            }),
                            'Featured Product'
                        )
                    )
                )
            ),

            // Actions
            e('div', { style: s.actions },
                e('button', {
                    onClick: handleSubmit,
                    disabled: loading,
                    style: s.btnPrimary,
                }, loading ? 'Saving...' : (isEdit ? 'Update Product' : 'Create Product')),
                e('a', {
                    href: '/admin/products',
                    style: s.btnSecondary,
                }, 'Cancel')
            )
        )
    );
}

export default AdminProductForm;