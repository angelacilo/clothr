const React = require('react');
const e = React.createElement;

function ProductList() {
    const [products, setProducts] = React.useState([]);
    const [categories, setCategories] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [search, setSearch] = React.useState('');
    const [category, setCategory] = React.useState('');
    const [page, setPage] = React.useState(1);
    const [lastPage, setLastPage] = React.useState(1);

    function loadProducts(s, c, p) {
        setLoading(true);
        var params = new URLSearchParams({ page: p });
        if (s) params.set('search', s);
        if (c) params.set('category', c);
        fetch('/api/shop/products?' + params.toString())
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setProducts(data.data || data);
                setLastPage(data.last_page || 1);
                setLoading(false);
            });
    }

    React.useEffect(function () {
        fetch('/api/shop/categories').then(function (r) { return r.json(); }).then(setCategories);
    }, []);

    React.useEffect(function () {
        loadProducts(search, category, page);
    }, [search, category, page]);

    if (loading) return e('p', { style: { padding: '2rem', textAlign: 'center' } }, 'Loading products...');

    return e('div', { style: { padding: '2rem', fontFamily: 'sans-serif' } },
        e('h1', { style: { marginBottom: '1.5rem' } }, 'All Products'),

        // Filters
        e('div', { style: { display: 'flex', gap: '1rem', marginBottom: '2rem', flexWrap: 'wrap' } },
            e('input', {
                type: 'text',
                placeholder: 'Search products...',
                value: search,
                onChange: function (ev) { setSearch(ev.target.value); setPage(1); },
                style: { padding: '8px 12px', flex: '1', minWidth: '200px', border: '1px solid #ddd', borderRadius: '4px' },
            }),
            e('select', {
                value: category,
                onChange: function (ev) { setCategory(ev.target.value); setPage(1); },
                style: { padding: '8px 12px', border: '1px solid #ddd', borderRadius: '4px' },
            },
                e('option', { value: '' }, 'All Categories'),
                categories.map(function (cat) {
                    return e('option', { key: cat.category_id, value: cat.category_id }, cat.category_name);
                })
            )
        ),

        // Grid
        e('div', { style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(220px, 1fr))', gap: '1.5rem' } },
            products.map(function (product) {
                var imgUrl = product.images && product.images[0]
                    ? '/storage/' + product.images[0].img_url
                    : '/images/placeholder.jpg';
                var price = product.sale_price
                    ? e('span', null,
                        e('span', { style: { textDecoration: 'line-through', color: '#999', marginRight: '6px' } }, '$' + parseFloat(product.price).toFixed(2)),
                        e('span', { style: { color: '#c0392b', fontWeight: '700' } }, '$' + parseFloat(product.sale_price).toFixed(2))
                    )
                    : e('span', { style: { fontWeight: '700' } }, '$' + parseFloat(product.price).toFixed(2));

                return e('a', {
                    key: product.product_id,
                    href: '/products/' + product.slug,
                    style: { textDecoration: 'none', color: 'inherit', display: 'block', border: '1px solid #eee', borderRadius: '8px', overflow: 'hidden' },
                },
                    e('img', { src: imgUrl, alt: product.name, style: { width: '100%', height: '260px', objectFit: 'cover', display: 'block' } }),
                    e('div', { style: { padding: '12px' } },
                        e('p', { style: { margin: '0 0 4px', fontWeight: '600' } }, product.name),
                        price
                    )
                );
            })
        ),

        // Pagination
        lastPage > 1 ? e('div', { style: { display: 'flex', gap: '8px', marginTop: '2rem', justifyContent: 'center' } },
            Array.from({ length: lastPage }, function (_, i) { return i + 1; }).map(function (p) {
                return e('button', {
                    key: p,
                    onClick: function () { setPage(p); },
                    style: {
                        padding: '6px 12px', cursor: 'pointer',
                        background: page === p ? '#222' : '#f0f0f0',
                        color: page === p ? '#fff' : '#333',
                        border: '1px solid #ccc', borderRadius: '4px',
                    }
                }, p);
            })
        ) : null
    );
}

module.exports = ProductList;
