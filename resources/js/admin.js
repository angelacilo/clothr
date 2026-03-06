/**
 * CLOTHR Admin Dashboard JavaScript
 */

import React from "react";
import ReactDOM from "react-dom";
import AdminDashboard from "./components/Admin/AdminDashboard";

/* =============================
   REACT MOUNT
============================= */

const rootElement = document.getElementById("admin-dashboard");

if (rootElement) {
    ReactDOM.render(
        <AdminDashboard />,
        rootElement
    );
}

/* =============================
   CSRF TOKEN
============================= */

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : "";
}

/* =============================
   NOTIFICATIONS
============================= */

function showNotification(message, type = "success") {

    const notification = document.createElement("div");

    notification.className = `alert alert-${type}`;
    notification.textContent = message;

    notification.style.position = "fixed";
    notification.style.top = "10px";
    notification.style.right = "10px";
    notification.style.zIndex = "9999";
    notification.style.maxWidth = "400px";

    document.body.prepend(notification);

    setTimeout(() => {
        notification.style.opacity = "0";
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/* =============================
   ORDER STATUS
============================= */

function updateOrderStatus(orderId, status) {

    if (!confirm("Update order status to " + status + "?")) return;

    fetch(`/admin/orders/${orderId}/status`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": getCsrfToken()
        },
        body: JSON.stringify({
            status: status
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            showNotification("Order updated successfully");
        } else {
            showNotification("Failed to update order", "danger");
        }

    })
    .catch(() => {
        showNotification("Server error", "danger");
    });
}

/* =============================
   USER ROLE
============================= */

function updateUserRole(userId, role) {

    if (!confirm("Change role to " + role + "?")) return;

    fetch(`/admin/users/${userId}/role`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": getCsrfToken()
        },
        body: JSON.stringify({
            role: role
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            showNotification("User role updated");
        } else {
            showNotification("Failed to update role", "danger");
        }

    })
    .catch(() => {
        showNotification("Server error", "danger");
    });
}

/* =============================
   BULK REVIEW APPROVE
============================= */

function bulkApprove() {

    const selected = document.querySelectorAll(".review-checkbox:checked");

    if (selected.length === 0) {
        alert("Select at least one review");
        return;
    }

    const ids = Array.from(selected).map(cb => cb.value);

    fetch("/admin/reviews/bulk-approve", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": getCsrfToken()
        },
        body: JSON.stringify({
            review_ids: ids
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            showNotification("Reviews approved");
            setTimeout(() => location.reload(), 1000);
        }

    })
    .catch(() => showNotification("Error", "danger"));
}

/* =============================
   CHECKBOX TOGGLE
============================= */

function toggleAllCheckboxes(checkbox) {

    const boxes = document.querySelectorAll(".review-checkbox, .product-checkbox");

    boxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

/* =============================
   PRODUCT SEARCH
============================= */

function searchProducts(query) {

    const cards = document.querySelectorAll(".product-card");
    const q = query.toLowerCase();

    cards.forEach(card => {

        const name = card.querySelector(".product-name")?.textContent.toLowerCase() || "";
        const category = card.querySelector(".product-category")?.textContent.toLowerCase() || "";

        if (name.includes(q) || category.includes(q)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }

    });
}

/* =============================
   FORMAT HELPERS
============================= */

function formatCurrency(value) {
    return "$" + parseFloat(value).toFixed(2);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

/* =============================
   SIDEBAR TOGGLE
============================= */

function toggleSidebar() {

    const sidebar = document.querySelector(".sidebar");

    if (sidebar) {
        sidebar.classList.toggle("active");
    }
}

/* =============================
   DOM READY
============================= */

document.addEventListener("DOMContentLoaded", function () {

    const selectAll = document.getElementById("selectAll");

    if (selectAll) {
        selectAll.addEventListener("change", function () {
            toggleAllCheckboxes(this);
        });
    }

});