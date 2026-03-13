# Testing & Troubleshooting Guide

---

## 🧪 Testing Checklist

### Phase 1: Basic Authentication
- [ ] Can log in with admin credentials
- [ ] After login, can access `/admin` (doesn't redirect)
- [ ] Non-admin users are redirected from `/admin`
- [ ] Logout removes session

### Phase 2: API Authentication
- [ ] Dashboard loads (no 401 errors)
- [ ] Charts display with data
- [ ] Loading spinners disappear
- [ ] Browser console shows no errors

### Phase 3: CRUD Operations
- [ ] **Products**: Can view, create, edit, delete
- [ ] **Categories**: Can view, create, edit, delete
- [ ] **Orders**: Can view list, update status
- [ ] **Users**: Can view list, delete users
- [ ] **Reviews**: Can view list, delete reviews
- [ ] **Reports**: Can generate and view reports

### Phase 4: Security
- [ ] Direct API call without login gets 401
- [ ] Non-admin user API calls get 403
- [ ] Session cookie is HttpOnly (cannot access via JS)
- [ ] Logging out invalidates session

---

## 🔍 Debugging Steps

### Step 1: Verify Login Works
```bash
# In browser console while on login page:
fetch('/login', {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'admin@clothr.com',
        password: 'password'
    })
})
.then(r => r.status === 302 ? 'Redirect (login works!)' : 'No redirect')
.then(console.log)
```

Expected: `Redirect (login works!)`

### Step 2: Check Session Cookie
```bash
# In browser console (after login):
fetch('/admin', { credentials: 'include' })
    .then(r => console.log(r.status === 200 ? '✅ Admin page accessible' : '❌ ' + r.status))
```

Expected: `✅ Admin page accessible`

### Step 3: Verify Session Cookie Exists
```bash
# Open DevTools (F12) → Application tab → Storage → Cookies → 127.0.0.1:8000
# Look for:
# LARAVEL_SESSION | (value) | 127.0.0.1:8000 | ✓ HttpOnly | ✓ SameSite=Lax
```

### Step 4: Test API Endpoint
```bash
# In browser console:
fetch('/api/admin/stats', { 
    credentials: 'include',
    headers: { 'Accept': 'application/json' }
})
    .then(r => {
        console.log('Status:', r.status);
        return r.json();
    })
    .then(data => console.log('Data received:', data))
    .catch(e => console.error('Error:', e))
```

Expected status: `200`  
Expected data: `{ totalRevenue: ..., todaysOrders: ..., ... }`

### Step 5: Check Network Requests
**Chrome DevTools → Network tab**:

1. Refresh `/admin`
2. Look for request to `/api/admin/stats`
3. Click on it, then go to **Headers**:
   - **Request Headers** should include:
     ```
     Cookie: LARAVEL_SESSION=abc123...
     Accept: application/json
     ```
   - **Response Status** should be: `200 OK`

### Step 6: Review Network Response
**In same Network panel**:
1. Click `/api/admin/stats` request
2. Go to **Response** tab
3. Should see JSON with stats:
   ```json
   {
     "totalRevenue": 15000,
     "todaysOrders": 5,
     "orderStatus": {...},
     "salesTrend": [...]
   }
   ```

---

## 🐛 Common Issues & Solutions

### Issue #1: Dashboard shows "Failed to load dashboard data"

**Possible Cause #1A: Missing credentials**
```javascript
// ❌ WRONG
fetch('/api/admin/stats', { headers: { 'Accept': 'application/json' } })

// ✅ CORRECT  
fetch('/api/admin/stats', { 
    credentials: 'include',  // ← This is required
    headers: { 'Accept': 'application/json' } 
})
```

**Check**: In React component, search for all `fetch(` calls. Each should have `credentials: 'include'`.

**Cause #1B: Not logged in**
1. Check if you're logged in (check top navigation)
2. If not, log in with `admin@clothr.com` / `password`
3. Refresh the page

**Cause #1C: Not admin user**
1. Check database: `SELECT * FROM users WHERE email='admin@clothr.com'`
2. Verify `role` column = 'admin'
3. If not, update: `UPDATE users SET role='admin' WHERE email='admin@clothr.com'`

---

### Issue #2: Browser console shows "401 Unauthorized"

**Step 1: Check what's being sent**
```javascript
// In console:
fetch('/api/admin/stats', {
    credentials: 'include',
    headers: { 'Accept': 'application/json' }
})
    .then(r => {
        console.log('Status:', r.status);
        console.log('Status Text:', r.statusText);
        return r.text();  // Read as text, not JSON
    })
    .then(text => {
        console.log('Response:', text);  // See what server actually returned
    })
```

**Step 2: Check session is active**
```javascript
// Session endpoint (should return empty {} or user data)
fetch('/api/user', { credentials: 'include' })
    .then(r => r.json())
    .then(d => console.log('User:', d))
    .catch(e => console.error('Error:', e))
```

**Step 3: Check if session cookie is being sent**
1. Open DevTools → Network tab
2. Refresh page or make a request
3. Click on `/api/admin/stats` request
4. Go to **Headers** tab
5. Look for `Cookie:` in **Request Headers**
6. Should show `LARAVEL_SESSION=...`

If NOT showing:
- Session might be expired
- Try logging in again
- Clear cookies and refresh

---

### Issue #3: "Unexpected token < in JSON at position 0"

**This means**: Server returned HTML instead of JSON (probably an error page)

**Debugging**:
```javascript
fetch('/api/admin/stats', {
    credentials: 'include',
    headers: { 'Accept': 'application/json' }
})
    .then(r => r.text())  // Read as text, not JSON
    .then(text => {
        console.log('Raw response:', text);  // Will show HTML if error
        // If it starts with <html or <!, there's a server error
        // If it starts with {, it's valid JSON (just parse it)
    })
```

**Check Laravel logs**:
```bash
# Terminal
tail -f storage/logs/laravel.log
# Or on Windows:
type storage\logs\laravel.log
```

Look for error messages in the log.

---

### Issue #4: CORS Error (if frontend is separate)

**Error**: `Access to XMLHttpRequest has been blocked by CORS policy`

**Solution**: Not applicable if frontend and backend are same domain (127.0.0.1:8000)

If using different domains (frontend on different port):
```php
// app/Http/Middleware/HandleCors.php (add this)
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    // Allow React frontend
    $response->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    $response->header('Access-Control-Allow-Credentials', 'true');
    
    return $response;
}
```

---

### Issue #5: Products/Orders/etc not loading in admin panel

**Check each component**:

**AdminProducts.js**:
```javascript
fetch('/api/admin/products', {
    credentials: 'include',
    headers: { 'Accept': 'application/json' }
})
```

**AdminOrders.js**:
```javascript
fetch('/api/admin/orders', {
    credentials: 'include',
    headers: { 'Accept': 'application/json' }
})
```

If all components need credentials, they were probably added. Check in DevTools → Network that requests are successful (200).

---

## 📊 Manual Testing with Curl

### Test 1: Login and Get Session
```bash
# Note: Save cookies to file for next requests
curl -X POST http://127.0.0.1:8000/login \
  -d "email=admin@clothr.com&password=password" \
  -c cookies.txt \
  -L
  
# Response: Should redirect to /admin (200)
```

### Test 2: Access Admin Page
```bash
# Use saved cookies
curl http://127.0.0.1:8000/admin \
  -b cookies.txt
  
# Response: Should return HTML of React app (200)
```

### Test 3: Access API (with auth)
```bash
curl http://127.0.0.1:8000/api/admin/stats \
  -b cookies.txt \
  -H "Accept: application/json"
  
# Response: Should return JSON (200)
# {
#   "totalRevenue": ...,
#   "todaysOrders": ...
# }
```

### Test 4: Access API (without auth)
```bash
curl http://127.0.0.1:8000/api/admin/stats \
  -H "Accept: application/json"
  
# Response: Should be 401 Unauthorized
# {"error": "Unauthenticated"}
```

---

## 🔧 Quick Fixes

### If Nothing Works: Clear Everything
```bash
# Terminal
php artisan cache:clear
php artisan config:clear
php artisan auth:clear-resets
php artisan session:clear

# Then refresh browser
# Ctrl+Shift+Delete → Clear cookies for 127.0.0.1:8000
# Refresh /admin and log in again
```

### If 500 Server Error
```bash
# Check logs
tail -f storage/logs/laravel.log

# Common issues:
# - Missing database columns
# - Controller method doesn't exist
# - Query syntax error
# - Missing package

# Check database
php artisan tinker
# > User::first()
# > User::where('role', 'admin')->first()
```

### If Routes Not Found
```bash
# Verify routes are registered
php artisan route:list | grep "admin"

# Should show:
# GET|HEAD /api/admin/stats ... AdminDashboardController@apiStats
# POST /admin/products ... AdminProductController@store
# etc.
```

### If Middleware Not Applied
```bash
# routes/api.php check:
Route::prefix("admin")->middleware(['auth:sanctum', 'admin'])->group(...)
                       ↑ This must be present

# If missing, add it:
// Find this:
Route::prefix("admin")->group(function () {

// Change to:
Route::prefix("admin")->middleware(['auth:sanctum', 'admin'])->group(function () {
```

---

## 📱 Browser Developer Tools Workflow

### Complete Testing Flow
1. **Open DevTools**: F12
2. **Go to Network tab**
3. **Refresh `/admin` page**:
   - Look for 302 redirects (login)
   - Look for HTML response (React app)
4. **Check Cookies tab**:
   - Should see `LARAVEL_SESSION`
   - Should see `HttpOnly: ✓`
5. **Make API call** (in Console):
   ```javascript
   fetch('/api/admin/stats', {
       credentials: 'include',
       headers: { 'Accept': 'application/json' }
   }).then(r => r.json()).then(console.log)
   ```
6. **Check Network tab again**:
   - `/api/admin/stats` should show 200
   - Request Headers should show Cookie
   - Response should show JSON data

---

## ✅ Success Criteria

### Dashboard Working When:
- ✅ Page loads without errors
- ✅ No 401/403 in console
- ✅ Charts display with data
- ✅ All sections (products, orders, users, etc.) load
- ✅ Can perform CRUD operations
- ✅ Network tab shows all 200 responses

### Security Verified When:
- ✅ Logging out prevents API access
- ✅ Direct API call without login gets 401
- ✅ Non-admin user API calls get 403
- ✅ Refreshing page keeps you logged in
- ✅ Multiple admin tabs share same session

---

## 📞 Getting Help

**If tests fail, check in this order**:
1. Run testing checklist above
2. Check debugging steps
3. Review solution for matching issue
4. Check Laravel logs: `storage/logs/laravel.log`
5. Check database: `php artisan tinker` → verify data exists
6. Check routes: `php artisan route:list`
7. Ask for help with error messages and steps taken
