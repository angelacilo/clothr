# Clothr Inventory System Documentation

This document explains exactly how the new inventory stock system works for the **Clothr Application**. It covers how stock is deducted when customers buy products, and how admins can view and manage that stock.

---

## 1. How Stock Deduction Works
Previously, when a customer placed an order, the system recorded the order but did not decrease the available stock. We have completely rewritten this process.

### Placing an order
When a customer clicks "Checkout":
1. The system checks exactly how many items are left for the specific **Size** and **Color** they chose.
2. If there is enough stock, the order is created.
3. The system then **subtracts the quantity ordered** from the global product list and the specific variant (Color/Size).
4. If there is **not enough stock**, the system cancels the checkout and shows an "Out of Stock" error to prevent overselling.

*File modified: `app/Services/OrderService.php`*

### Cancelling an order
If an admin cancels an order from the Admin Dashboard, the system will **add the stock back** into the inventory so another customer can buy it. If the order is "un-cancelled" (restored), the stock is deducted again.

*File modified: `app/Services/OrderService.php`*

---

## 2. Admin Inventory Dashboard
To stop admins from having to guess what is out of stock, we redesigned the "Products" page in the Admin Dashboard.

### Global Inventory Alerts
At the very top of the **Products** page, there is now an **Inventory Alerts** red box. This box will only appear when items are "Low Stock" (1 to 5 units left) or "Out of Stock" (0 units).
- It lists the **exact Product Name**, **Color**, and **Size** that needs restocking.
- It provides a quick "Restock" button next to each missing item.

### Stock Filters
You can easily filter the entire catalog. Next to the "Add Product" button, we added three sort buttons:
1. **All** 
2. **Low Stock**
3. **Out of Stock**

### Variant Breakdowns on Product Cards
Instead of just showing "Total Stock: 20", every product card on the admin page now has a dedicated **Variant Status** box.
- It lists every single Color and Size combination you sell for that product.
- It has color-coded tags indicating stock health:
  - 🔴 **Red**: 0 units left (Out of Stock)
  - 🟠 **Orange**: 1 to 5 units left (Low Stock)
  - 🟢 **Green**: 6+ units left (Healthy)

*Files modified:*
- *`app/Http/Controllers/Admin/ProductController.php` (for calculating the low stock items)*
- *`resources/views/admin/products.blade.php` (for drawing the new dashboard and filters)*

---

This system ensures that customers only buy what you actually have in your warehouse, and gives you a crystal-clear view on exactly what inventory you need to order next!
