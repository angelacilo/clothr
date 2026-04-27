# 👗 CLOTHR - Ecommerce Management System
### *A Complete Guide for the Presentation Panel*

---

## 1. Project Overview
**CLOTHR** is a modern e-commerce platform specifically designed for clothing brands. Unlike simple online stores, CLOTHR handles the entire lifecycle of a product: from the moment it is added to the inventory, to the second it is delivered to the customer’s doorstep.

The system is built to be **fast, secure, and professional**, ensuring that both the store owners and the customers have a seamless experience.

---

## 2. The Four Key Roles (System Portals)
The system is divided into four distinct portals. Each person only sees what they need to see to do their job.

### 🛡️ Admin Portal (The Store Owner)
*   **What they do:** Manage the catalog, track stock, and start the delivery process.
*   **Key Tools:**
    *   **Product Manager:** Add new clothes with multiple colors and sizes.
    *   **Inventory Restock:** A specialized tool to add new stock without accidentally changing product details.
    *   **Order Control:** Monitor all sales and hand over packages to a Courier service.

### 🚚 Courier Portal (The Shipping Company)
*   **What they do:** They act as the middleman (like J&T or LBC).
*   **Key Tools:**
    *   **Rider Management:** Register and manage their own fleet of delivery riders.
    *   **Order Assignment:** Receive packages from the Admin and assign them to a specific Rider.

### 🏍️ Rider Portal (The Delivery Driver)
*   **What they do:** Use their mobile phone to pick up and deliver packages.
*   **Key Tools:**
    *   **Delivery Tracking:** See the customer's address and phone number.
    *   **Proof of Delivery:** Take a photo of the received package to prove the delivery was successful.

### 👤 Customer Portal (The Shopper)
*   **What they do:** Browse, buy, and track.
*   **Key Tools:**
    *   **Smart Shopping Bag:** A cart that handles different colors and sizes perfectly.
    *   **Real-time Tracking:** See exactly where their order is (Pending -> Shipped -> Out for Delivery -> Delivered).

---

## 3. Core Features & "Why They Matter"

### 📦 Advanced Inventory & Variants
*   **The Feature:** Clothes come in many variations (Red/Small, Blue/Large, etc.).
*   **Why it matters:** Most systems get confused with stock when colors and sizes are mixed. Our system uses a **JSON-based variant engine** that tracks every single piece of clothing individually. You will never sell a "Red Small" if only "Blue Large" is in stock.

### 💰 Intelligent Shipping Fees
*   **The Feature:** Automated shipping calculation.
*   **Why it matters:** To encourage higher sales, we implemented a rule: **FREE shipping for orders over ₱2,500.** Otherwise, a flat fee of **₱250** is added. This calculation happens on the server, so it cannot be cheated by users.

### 📸 Proof of Delivery (POD) Transparency
*   **The Feature:** Riders must upload a photo when they deliver.
*   **Why it matters:** This photo is instantly visible to the **Customer, the Courier, and the Admin.** It prevents "lost package" disputes and builds trust.

### 🔔 Real-Time Notification System
*   **The Feature:** Instant alerts across all roles.
*   **Why it matters:** When a rider delivers a package, the Admin and Courier get a notification immediately without refreshing their page. This is powered by **WebSockets**, making the system feel alive and professional.

---

## 4. How the "Order Flow" Works (Step-by-Step)
1.  **Checkout:** The Customer picks their items. The system calculates the Subtotal + Shipping.
2.  **Processing:** The Admin prepares the box and selects a Courier service (e.g., LBC).
3.  **Hand-off:** The Courier receives the order in their portal and assigns it to an available Rider.
4.  **Transit:** The Rider clicks "Picked Up" and later "Out for Delivery." The Customer gets an alert.
5.  **Completion:** The Rider delivers the item, takes a photo, and clicks "Delivered." All parties are notified, and the proof of delivery is saved.

---

## 5. Security & Stability
*   **CSRF Protection:** Every form is locked with a unique "security token" to prevent hackers from submitting fake data.
*   **Database Transactions:** When a sale happens, the system locks the database for a millisecond. This ensures that two people cannot buy the "very last item" at the exact same time.
*   **Role-Based Access (Middleware):** A Rider cannot access the Admin panel, and a Customer cannot access the Courier dashboard. The system checks your "ID Badge" (Role) before letting you into any page.

---

## 6. Technical Stack (The Engine)
*   **Framework:** Laravel 8 (PHP) — Chosen for its robust security and speed.
*   **Database:** MySQL — Stores all orders, products, and user accounts.
*   **Frontend:** Vanilla CSS & JavaScript — Custom-coded for a "Premium" look that is unique to CLOTHR.
*   **Real-time:** Laravel Echo & WebSockets — Powers the live notifications.

---

## 7. Conclusion for the Panel
CLOTHR is not just a website; it is a **complete business solution.** It solves the real-world problems of inventory tracking, shipping logistics, and customer trust. By combining a beautiful user interface with a complex backend "engine," we have created a platform ready for the modern digital economy.
