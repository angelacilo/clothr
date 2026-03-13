# Clothr — Session Notes (What We Fixed)

This document explains, in simple words, what we changed in Clothr, what problems happened, how we fixed them, and what we recommend next.

---

## 1) Project structure (quick)

- **Laravel backend (PHP)**: routes, controllers, models
- **Admin panel**: React SPA loaded from a Blade page (`/admin`)
- **Customer storefront**: Blade pages (home, products, cart, checkout, orders)

Key folders:

- `routes/`
  - `web.php`: browser pages (session + cookies + CSRF)
  - `api.php`: API routes (API middleware group)
- `app/Http/Controllers/`
  - Customer controllers: `AuthController`, `ProductController`, `CartController`, `OrderController`
  - Admin controllers in `Admin/`
- `resources/views/`
  - Blade templates (admin shell + customer pages)

---

## 2) What we changed (high level)

### A) Security and cleanup for admin API logic

- We **moved inline “closure” API logic** out of `routes/api.php` and into a controller:
  - Created: `app/Http/Controllers/Admin/AdminApiController.php`
  - Result: routes became cleaner and logic is easier to maintain.

### B) Storefront fixes (customer side)

- We made the home page show **real products from the database** instead of hardcoded “Unsplash” demo cards.
- We added a `home()` method to `ProductController` and updated the home route to use it.
- We filtered customer product browsing to show only **active** products.

---

## 3) Problems we encountered and how we fixed them

### Problem 1: Admin panel logs out when navigating

**What we saw**
- Admin loads `/admin` but clicking other pages (Products, Categories, etc.) redirects to login.

**Why it happened**
- The admin API routes were protected in a way that caused **session conflicts**.
- In short: mixing the `web` stack into `routes/api.php` can mess with sessions/CSRF and make the user “look logged out”.

**Fix**
- We removed the problematic middleware approach and changed how admin API routes authenticate.

---

### Problem 2: Admin UI loads but API returns 401 (empty data)

**What we saw**
- `/api/admin/*` returned **401 Unauthorized**.
- React then crashed with errors like `categories.map is not a function` because it received an error JSON instead of an array.

**Why it happened**
- We used `auth:sanctum` in `routes/api.php`, but Sanctum “stateful SPA mode” was not enabled in the API middleware group:
  - In `app/Http/Kernel.php`, `EnsureFrontendRequestsAreStateful` was commented out.

**Fix we chose (most reliable for this project)**
- We **moved the `/api/admin/*` endpoints to `routes/web.php`**, so they use the same working web session as the admin page itself:
  - `Route::prefix('api/admin')->middleware(['auth','admin'])->group(...)`
- We removed the admin group from `routes/api.php` to avoid route conflicts.
- We kept `routes/web.php` admin SPA catch‑all route intact (must be last).

---

## 4) Files we changed (main)

- `routes/web.php`
  - Added `Route::prefix('api/admin')->middleware(['auth','admin'])->group(...)` with all admin API endpoints.
- `routes/api.php`
  - Removed the old `/api/admin/*` group so admin API is not duplicated in two places.
- `app/Http/Controllers/Admin/AdminApiController.php`
  - New controller holding the admin API “read” logic for products, categories, orders, users, reviews, reports.

Storefront-related:

- `app/Http/Controllers/ProductController.php`
- `resources/views/home.blade.php`

---

## 5) Commands we ran after changes

After route/middleware changes, we cleared caches:

```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

---

## 6) How to test

### Admin (as an admin user)

- Visit `/admin` and navigate to:
  - Products, Categories, Orders, Users, Reviews, Reports
- Open DevTools → Network tab:
  - `/api/admin/products` should return **200** with JSON data.

### Guest (incognito)

- Visit `/admin` → should redirect to `/login`.
- Visit `/api/admin/products` → should be blocked (not return data).

---

## 7) Future recommendations

- **Avoid duplicating routes**: keep `/api/admin/*` defined in one place only (we chose `web.php`).
- **Add safer React error handling**:
  - Use `Array.isArray(data) ? data.map(...) : []` to prevent UI crashes if the API fails.
- **If you want Sanctum in `api.php` later**:
  - Enable `EnsureFrontendRequestsAreStateful` in `app/Http/Kernel.php` for the `api` group.
  - Confirm Sanctum stateful domains and cookies are configured correctly.
  - This is more complex and easier to break, so only do it if you need it.

