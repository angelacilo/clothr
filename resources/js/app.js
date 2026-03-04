require('./bootstrap');

const React = require('react');
const ReactDOM = require('react-dom');

// Admin components
const AdminProducts = require('./components/Admin/AdminProducts');
const AdminOrders = require('./components/Admin/AdminOrders');
const AdminCategories = require('./components/Admin/AdminCategories');
const AdminUsers = require('./components/Admin/AdminUsers');
const AdminReports = require('./components/Admin/AdminReports');
const AdminReviews = require('./components/Admin/AdminReviews');

// Shop components
const Home = require('./components/Shop/Home');
const ProductList = require('./components/Shop/ProductList');
const ProductDetail = require('./components/Shop/ProductDetail');
const Cart = require('./components/Shop/Cart');
const Checkout = require('./components/Shop/Checkout');
const OrderHistory = require('./components/Shop/OrderHistory');
const OrderSuccess = require('./components/Shop/OrderSuccess');

var mountPoints = [
    { id: 'admin-products-root', component: AdminProducts },
    { id: 'admin-orders-root', component: AdminOrders },
    { id: 'admin-categories-root', component: AdminCategories },
    { id: 'admin-users-root', component: AdminUsers },
    { id: 'admin-reports-root', component: AdminReports },
    { id: 'admin-reviews-root', component: AdminReviews },
    { id: 'shop-home-root', component: Home },
    { id: 'shop-products-root', component: ProductList },
    { id: 'shop-product-detail-root', component: ProductDetail },
    { id: 'shop-cart-root', component: Cart },
    { id: 'shop-checkout-root', component: Checkout },
    { id: 'shop-order-history-root', component: OrderHistory },
    { id: 'shop-order-success-root', component: OrderSuccess },
];

mountPoints.forEach(function (mount) {
    var el = document.getElementById(mount.id);
    if (el) {
        ReactDOM.render(React.createElement(mount.component), el);
    }
});
