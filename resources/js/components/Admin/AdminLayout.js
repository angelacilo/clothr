import React from 'react';
const e = React.createElement;

function AdminLayout({ children }) {
    const user = JSON.parse(document.querySelector('meta[name="auth-user"]')?.content || '{}');
    const path = window.location.pathname;

    const navLinks = [
        { href: '/admin', label: 'Dashboard', icon: 'bi-speedometer2', match: /^\/admin$/ },
        { href: '/admin/orders', label: 'Orders', icon: 'bi-bag', match: /^\/admin\/orders/ },
        { href: '/admin/products', label: 'Products', icon: 'bi-box', match: /^\/admin\/products/ },
        { href: '/admin/categories', label: 'Categories', icon: 'bi-tag', match: /^\/admin\/categories/ },
        { href: '/admin/reports', label: 'Reports', icon: 'bi-graph-up', match: /^\/admin\/reports/ },
        { href: '/admin/reviews', label: 'Reviews', icon: 'bi-star', match: /^\/admin\/reviews/ },
        { href: '/admin/users', label: 'Users', icon: 'bi-people', match: /^\/admin\/users/ },
    ];

    const handleLogout = () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/logout';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    };

    return e('div', { className: 'admin-wrapper' },

        // Sidebar
        e('aside', { className: 'sidebar' },
            e('div', { className: 'sidebar-header' },
                e('h1', { className: 'sidebar-logo' }, 'CLOTHR'),
                e('div', { className: 'sidebar-divider' })
            ),
            e('nav', { className: 'sidebar-nav' },
                e('ul', { className: 'nav-links' },
                    ...navLinks.map(link =>
                        e('li', { key: link.href },
                            e('a', {
                                href: link.href,
                                className: 'nav-link' + (link.match.test(path) ? ' active' : '')
                            },
                                e('i', { className: 'bi ' + link.icon }),
                                e('span', null, link.label)
                            )
                        )
                    )
                ),
                e('div', { className: 'nav-footer' },
                    e('button', {
                        onClick: handleLogout,
                        className: 'nav-link logout-link',
                        style: { background: 'none', border: 'none', cursor: 'pointer', width: '100%', textAlign: 'left' }
                    },
                        e('i', { className: 'bi bi-box-arrow-right' }),
                        e('span', null, 'Logout')
                    )
                )
            )
        ),

        // Main Content
        e('main', { className: 'main-content' },

            // Topbar
            e('header', { className: 'topbar' },
                e('div', { className: 'topbar-left' },
                    e('h2', { className: 'topbar-title' }, 'CLOTHR Admin'),
                ),
                e('div', { className: 'topbar-right' },
                    e('a', {
                        href: '/home',
                        className: 'btn btn-outline-primary'
                    },
                        e('i', { className: 'bi bi-shop' }),
                        ' View Store'
                    ),
                    e('div', { className: 'user-info' },
                        e('div', { className: 'user-avatar' },
                            (user.name || 'A').charAt(0).toUpperCase()
                        ),
                        e('div', { className: 'user-details' },
                            e('p', { className: 'user-name' }, user.name || ''),
                            e('p', { className: 'user-email' }, user.email || '')
                        )
                    )
                )
            ),

            // Page Content
            e('section', { className: 'content' }, children)
        )
    );
}

export default AdminLayout;