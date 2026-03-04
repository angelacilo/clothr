const React = require('react');
const e = React.createElement;

function ProductDetail() {
    const [product, setProduct] = React.useState(null);
    const [loading, setLoading] = React.useState(true);
    const [qty, setQty] = React.useState(1);
    const [msg, setMsg] = React.useState('');
    const [activeImg, setActiveImg] = React.useState(0);

    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    React.useEffect(function () {
        // Slug is embedded in the page via a data attribute on the root element
        var root = document.getElementById('shop-product-detail-root');
        var slug = root ? root.dataset.slug : null;
        var url = slug ? '/api/shop/products/' + slug : null;
        if (!url) { setLoading(false); return; }

        fetch(url)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setProduct(data);
                setLoading(false);
            });
    }, []);

    function addToCart() {
        fetch('/api/shop/cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ product_id: product.product_id, quantity: qty }),
        })
            .then(function (res) { return res.json(); })
            .then(function () { setMsg('Added to cart!'); });
    }

    if (loading) return e('p', { style: { padding: '2rem', textAlign: 'center' } }, 'Loading product...');
    if (!product) return e('p', { style: { padding: '2rem', textAlign: 'center' } }, 'Product not found.');

    var images = product.images || [];
    var mainImg = images[activeImg]
        ? '/storage/' + images[activeImg].img_url
        : '/images/placeholder.jpg';

    var price = product.sale_price
        ? e('div', null,
            e('span', { style: { textDecoration: 'line-through', color: '#999', marginRight: '10px', fontSize: '1.1rem' } }, '$' + parseFloat(product.price).toFixed(2)),
            e('span', { style: { color: '#c0392b', fontWeight: '700', fontSize: '1.5rem' } }, '$' + parseFloat(product.sale_price).toFixed(2))
        )
        : e('span', { style: { fontWeight: '700', fontSize: '1.5rem' } }, '$' + parseFloat(product.price).toFixed(2));

    var stock = product.inventory ? product.inventory.available_qty : 0;

    return e('div', { style: { padding: '2rem', fontFamily: 'sans-serif', maxWidth: '1100px', margin: '0 auto' } },
        e('div', { style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '3rem' } },

            // Images
            e('div', null,
                e('img', { src: mainImg, alt: product.name, style: { width: '100%', borderRadius: '8px', objectFit: 'cover', maxHeight: '500px' } }),
                images.length > 1 ? e('div', { style: { display: 'flex', gap: '8px', marginTop: '12px' } },
                    images.map(function (img, idx) {
                        return e('img', {
                            key: img.image_id,
                            src: '/storage/' + img.img_url,
                            alt: 'thumb',
                            onClick: function () { setActiveImg(idx); },
                            style: {
                                width: '60px', height: '60px', objectFit: 'cover', borderRadius: '4px',
                                cursor: 'pointer', border: activeImg === idx ? '2px solid #222' : '2px solid transparent',
                            }
                        });
                    })
                ) : null
            ),

            // Info
            e('div', null,
                product.category ? e('p', { style: { color: '#888', fontSize: '13px', margin: '0 0 8px' } }, product.category.category_name) : null,
                e('h1', { style: { margin: '0 0 12px', fontSize: '2rem' } }, product.name),
                price,
                e('p', { style: { margin: '16px 0', color: '#555' } }, product.description || ''),
                e('p', { style: { margin: '8px 0', color: stock > 0 ? '#27ae60' : '#e74c3c', fontWeight: '600' } },
                    stock > 0 ? 'In Stock (' + stock + ' available)' : 'Out of Stock'
                ),

                stock > 0 ? e('div', { style: { marginTop: '1.5rem' } },
                    e('div', { style: { display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '1rem' } },
                        e('label', null, 'Qty:'),
                        e('input', {
                            type: 'number',
                            min: '1',
                            max: String(stock),
                            value: qty,
                            onChange: function (ev) { setQty(parseInt(ev.target.value, 10) || 1); },
                            style: { width: '60px', padding: '6px', border: '1px solid #ddd', borderRadius: '4px' },
                        })
                    ),
                    e('button', {
                        onClick: addToCart,
                        style: { padding: '14px 36px', background: '#222', color: '#fff', border: 'none', borderRadius: '4px', cursor: 'pointer', fontWeight: '700', fontSize: '1rem' }
                    }, 'Add to Cart')
                ) : null,

                msg ? e('p', { style: { color: 'green', marginTop: '1rem' } }, msg) : null,

                // Reviews
                product.reviews && product.reviews.length ? e('div', { style: { marginTop: '3rem' } },
                    e('h3', null, 'Customer Reviews (' + product.reviews.length + ')'),
                    product.reviews.map(function (rev) {
                        return e('div', { key: rev.id, style: { borderTop: '1px solid #eee', padding: '16px 0' } },
                            e('p', { style: { margin: '0 0 4px', fontWeight: '600' } }, (rev.user ? rev.user.name : 'Anonymous') + ' — ' + '★'.repeat(rev.rating)),
                            e('p', { style: { margin: 0, color: '#555' } }, rev.comment)
                        );
                    })
                ) : null
            )
        )
    );
}

module.exports = ProductDetail;
