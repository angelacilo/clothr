import React from 'react';
import ReactDOM from 'react-dom';
import AdminApp from './components/Admin/AdminApp';

const root = document.getElementById('admin-root');
if (root) {
    ReactDOM.render(React.createElement(AdminApp), root);
}