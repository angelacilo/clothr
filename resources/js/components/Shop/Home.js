const React = require('react');
const e = React.createElement;

function Home() {
    const [featuredProducts, setFeaturedProducts] = React.useState([]);
    const [categories, setCategories] = React.useState([]);
    const [loading, setLoading] = React.useState(true);

    React.useEffect(function () {
        Promise.all([
            fetch('/api/shop/products?featured=1').then(function (r) { return r.json(); }),
            fetch('/api/shop/categories').then(function (r) { return r.json(); }),
        ]).then(function (results) {
            setFeaturedProducts(results[0].data || results[0]);
            setCategories(results[1]);
            setLoading(false);
        });
    }, []);

    if (loading) return e('p', { style: { padding: '2rem', textAlign: 'center' } }, 'Loading...');

    return e('div', { style: { fontFamily: 'sans-serif' } },

        // Hero
        e('section', { style: { background: '#111', color: '#fff', padding: '80px 2rem', textAlign: 'center' } },
            e('h1', { style: { fontSize: '3rem', margin: '0 0 1rem', letterSpacing: '2px' } }, 'CLOTHR'),
            e('p', { style: { fontSize: '1.2rem', color: '#ccc', marginBottom: '2rem' } }, 'Modern Women\'s Fashion'),
            e('a', { href: '/products', style: ctaBtn }, 'Shop Now')
        ),

        // Categories
        categories.length ? e('section', { style: sectionPad },
            e('h2', { style: sectionTitle }, 'Shop by Category'),
            e('div', { style: { display: 'flex', gap: '1rem', flexWrap: 'wrap' } },
                categories.map(function (cat) {
                    return e('a', {
                        key: cat.category_id,
                        href: '/products?category=' + cat.category_id,
                        style: catCard,
                    }, cat.category_name);
                })
            )
        ) : null,

        // Featured Products
        featuredProducts.length ? e('section', { style: sectionPad },
            e('h2', { style: sectionTitle }, 'Featured Products'),
            e('div', { style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(220px, 1fr))', gap: '1.5rem' } },
                featuredProducts.slice(0, 8).map(function (product) {
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
                        style: { textDecoration: 'none', color: 'inherit', display: 'block', border: '1px solid #eee', borderRadius: '8px', overflow: 'hidden', transition: 'box-shadow 0.2s' },
                    },
                        e('img', { src: imgUrl, alt: product.name, style: { width: '100%', height: '260px', objectFit: 'cover', display: 'block' } }),
                        e('div', { style: { padding: '12px' } },
                            e('p', { style: { margin: '0 0 4px', fontWeight: '600' } }, product.name),
                            price
                        )
                    );
                })
            )
        ) : null
    );
}

var sectionPad = { padding: '3rem 2rem' };
var sectionTitle = { fontSize: '1.5rem', fontWeight: '700', marginBottom: '1.5rem' };
var ctaBtn = {
    display: 'inline-block', padding: '14px 36px', background: '#fff', color: '#111',
    textDecoration: 'none', fontWeight: '700', letterSpacing: '1px', borderRadius: '4px',
};
var catCard = {
    display: 'inline-block', padding: '10px 20px', border: '1px solid #ddd',
    borderRadius: '20px', textDecoration: 'none', color: '#333',
    background: '#f7f7f7', fontWeight: '500',
};

module.exports = Home;
