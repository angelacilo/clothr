# Clothr — Sanctum Stateful Fix (What We Did)

This file documents the *second* fix we applied: making `/api/admin/*` work using `routes/api.php` + `auth:sanctum` **without** random logouts or 401 errors.

---

## 1) Problem we were seeing

### A) Logout problem (earlier)
- Admin UI loaded, but clicking other pages could redirect to login.
- Root cause was middleware/session conflicts caused by using the wrong middleware combination on API routes.

### B) 401 Unauthorized problem
- Admin UI loaded (so the user session worked for pages), **but all API calls failed**:
  - `GET /api/admin/products` → 401
  - `GET /api/admin/categories` → 401
  - etc.
- Because the API returned an error JSON, React sometimes crashed with messages like:
  - `categories.map is not a function`

---

## 2) Why it happened (simple explanation)

We protected `routes/api.php` with `auth:sanctum`, but Sanctum “SPA cookie authentication” was not fully enabled in the API middleware stack.

In `app/Http/Kernel.php`, the API group had this line **commented out**:

- `\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class`

When that middleware is missing, Sanctum behaves like “token auth only”, and cookie-based requests from the admin SPA are treated as unauthenticated → 401.

---

## 3) The fix (what we changed)

### Step 1: Enable Sanctum stateful requests for API routes

**File:** `app/Http/Kernel.php`

- We enabled Sanctum’s SPA middleware inside the `api` middleware group:
  - `\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class`

This allows the API to accept the logged-in browser session (cookies) for `/api/*` requests.

### Step 2: Move admin API endpoints back into `routes/api.php`

**File:** `routes/api.php`

- We put the admin API routes back under:
  - `Route::prefix('admin')->middleware(['auth:sanctum','admin'])->group(...)`

So:
- **Auth** is required (`auth:sanctum`)
- **Admin role** is required (`admin` middleware)

### Step 3: Remove duplicates from `routes/web.php`

**File:** `routes/web.php`

- We removed the temporary `/api/admin/*` block that was added earlier, to avoid conflicts/duplication.

---

## 4) How to test

### As admin

- Log in
- Open `/admin`
- Click Products / Categories / Orders / Users / Reviews / Reports
- In DevTools Network tab:
  - `/api/admin/products` should return **200** JSON

### As guest (incognito)

- Open `/api/admin/products`
  - should return **401** (or a block), not data

---

## 5) Notes / Recommendations

- Keep `/api/admin/*` **only in one place** (we chose `routes/api.php`).
- Keep `admin` middleware on admin APIs to prevent non-admin access.
- If React still crashes on bad responses, add safe guards in the components (`Array.isArray(...)` before `.map()`).

