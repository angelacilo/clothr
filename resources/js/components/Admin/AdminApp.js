import React from 'react';
import { BrowserRouter, Routes, Route, Navigate, useParams } from 'react-router-dom';
import AdminLayout from './AdminLayout';
import AdminDashboard from './AdminDashboard';
import AdminProducts from './AdminProducts';
import AdminProductForm from './AdminProductForm';
import AdminOrders from './AdminOrders';
import AdminCategories from './AdminCategories';
const e = React.createElement;

function AdminProductFormWrapper() {
    const { id } = useParams();
    return e(AdminProductForm, { productId: id });
}

function AdminApp() {
    return e(BrowserRouter, null,
        e(AdminLayout, null,
            e(Routes, null,
                e(Route, { path: '/admin', element: e(AdminDashboard) }),
                e(Route, { path: '/admin/orders', element: e(AdminOrders) }),
                e(Route, { path: '/admin/products', element: e(AdminProducts) }),
                e(Route, { path: '/admin/products/create', element: e(AdminProductForm, {}) }),
                e(Route, { path: '/admin/products/:id/edit', element: e(AdminProductFormWrapper) }),
                e(Route, { path: '/admin/categories', element: e(AdminCategories) }),
                e(Route, { path: '*', element: e(Navigate, { to: '/admin', replace: true }) })
            )
        )
    );
}
export default AdminApp;