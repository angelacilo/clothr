const React = require('react');

const e = React.createElement;

function AdminProducts() {
    const [products, setProducts] = React.useState([]);
    const [categories, setCategories] = React.useState([]);
    const [search, setSearch] = React.useState('');
    const [category, setCategory] = React.useState('');
    const [loading, setLoading] = React.useState(true);

    React.useEffect(() => {
        fetchProducts();
        fetchCategories();
    }, [search, category]);

    const fetchProducts = async () => {
        setLoading(true);
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (category) params.append('category', category);
        const res = await fetch(`/api/admin/products?${params}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        console.log('IMAGES:', data.data?.[0]?.images);
        setProducts(data.data || []);
        setLoading(false);
    };

    const fetchCategories = async () => {
        const res = await fetch('/api/admin/categories', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        setCategories(data || []);
    };

    const deleteProduct = async (id) => {
        if (!confirm('Are you sure?')) return;
        await fetch(`/api/admin/products/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        fetchProducts();
    };

    // Product Card
    const productCard = (product) => e('div', {
        key: product.product_id,
        style: { border: '1px solid #ddd', borderRadius: '8px', overflow: 'hidden', boxShadow: '0 1px 3px rgba(0,0,0,0.1)' }
    },
        // Image
        e('div', { style: { height: '180px', background: '#f3f4f6', display: 'flex', alignItems: 'center', justifyContent: 'center' } },
            product.images && product.images.length > 0
                ? e('img', {
                    src: `/storage/${product.images[0].image_path}`,
                    alt: product.name,
                    style: { height: '100%', width: '100%', objectFit: 'cover' }
                })
                : e('span', { style: { color: '#9ca3af' } }, 'No Image')
        ),
        // Info
        e('div', { style: { padding: '12px' } },
            e('h3', { style: { margin: '0 0 4px', fontWeight: '600' } }, product.name),
            e('p', { style: { margin: '0 0 4px', color: '#6b7280', fontSize: '14px' } },
                product.category ? product.category.category_name : 'N/A'
            ),
            e('p', { style: { margin: '0 0 4px', fontWeight: 'bold' } },
                `$${parseFloat(product.price).toFixed(2)}`
            ),
            e('p', { style: { margin: 0, fontSize: '14px' } },
                `Stock: ${product.inventory ? product.inventory.available_qty : 0}`
            )
        ),
        // Actions
        e('div', { style: { display: 'flex', justifyContent: 'flex-end', gap: '8px', padding: '12px', borderTop: '1px solid #ddd' } },
            e('a', {
                href: `/admin/products/${product.product_id}/edit`,
                style: { color: '#3b82f6', fontSize: '14px', textDecoration: 'none' }
            }, 'Edit'),
            e('button', {
                onClick: () => deleteProduct(product.product_id),
                style: { color: '#ef4444', fontSize: '14px', background: 'none', border: 'none', cursor: 'pointer' }
            }, 'Delete')
        )
    );

    return e('div', { style: { padding: '24px' } },
        // Header
        e('div', { style: { display: 'flex', justifyContent: 'space-between', marginBottom: '24px' } },
            e('div', { style: { display: 'flex', gap: '12px' } },
                e('input', {
                    type: 'text',
                    placeholder: 'Search products...',
                    value: search,
                    onChange: ev => setSearch(ev.target.value),
                    style: { border: '1px solid #ddd', borderRadius: '6px', padding: '8px 12px' }
                }),
                e('select', {
                    value: category,
                    onChange: ev => setCategory(ev.target.value),
                    style: { border: '1px solid #ddd', borderRadius: '6px', padding: '8px 12px' }
                },
                    e('option', { value: '' }, 'All Categories'),
                    ...categories.map(cat =>
                        e('option', { key: cat.category_id, value: cat.category_id }, cat.category_name)
                    )
                )
            ),
            e('a', {
                href: '/admin/products/create',
                style: { background: '#3b82f6', color: 'white', padding: '8px 16px', borderRadius: '6px', textDecoration: 'none' }
            }, '+ Add Product')
        ),

        // Content
        loading
            ? e('p', null, 'Loading...')
            : products.length === 0
                ? e('p', null, 'No products found.')
                : e('div', {
                    style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(220px, 1fr))', gap: '16px' }
                },
                    ...products.map(product => productCard(product))
                )
    );
}

module.exports = AdminProducts;