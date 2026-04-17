# CLOTHR: Rider & Courier System Documentation
**Date: April 17, 2026**

This document explains the new delivery features added to the CLOTHR e-commerce system.

---

## 1. Role Management (Admins Only)
Admins can now designate which users are allowed to handle deliveries.

*   **How to add a Rider**: 
    1.  Go to the **Users** menu in the Admin Sidebar.
    2.  Locate the user you want to promote.
    3.  Click the **"Make Rider"** icon (gray user with a `+`).
*   **Result**: The user will now have a purple "Rider" badge and can access the Rider Dashboard.

---

## 2. Order Assignment
Every order must now be assigned a rider and a delivery method.

*   **How to assign**:
    1.  Go to the **Orders** menu.
    2.  Click the **Truck Icon** on an order.
*   **Assignment Options**:
    *   **Assign Rider**: Select the rider responsible for this order.
    *   **Rider Delivery**: Choose this for local, direct deliveries by your rider.
    *   **Courier Delivery**: Choose this for shipping via third-party services. You will be asked to enter:
        *   **Courier Name** (e.g., LBC, J&T Express)
        *   **Tracking Number**

---

## 3. The Rider Portal
Riders have a dedicated area to manage their tasks.

*   **Login**: When a rider logs in, they are sent directly to `/rider/dashboard`.
*   **Sidebar**: They only see the **"Rider Portal"** section.
*   **Order Workflow**:
    *   **Accept Order**: Moves status from Pending to Processing.
    *   **Out for Delivery**: Moves status to Shipped.
    *   **Delivered**: Completes the order.
*   **Courier Orders**: If an order is set to Courier, the rider sees a **"Mark as Shipped"** button instead of "Out for Delivery."

---

## 4. Technical Changes
For developers, the following was implemented:

*   **Database**: Added `is_rider` to users; added delivery fields to orders.
*   **Middleware**: Created `RiderMiddleware` to protect rider routes.
*   **Controller**: Created `Rider\DashboardController` for rider logic.
*   **Auth**: Updated `AuthController` to handle role-based redirects after login.

---

**Note**: To enable these features, run the following command in your terminal:
`php artisan migrate`
