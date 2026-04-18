# CLOTHR — Delivery Fulfillment Implementation Plan

> **Status:** 🟢 APPROVED — Ready to Implement  
> **Last Updated:** 2026-04-18  
> **Real-Time:** ✅ Laravel WebSockets (beyondcode) — Self-hosted, no account needed  
> **Security:** ✅ Enforced at every layer  
> **Laravel:** 8.x | **PHP:** 7.3+

---

## 1. Overview

This document describes the full order fulfillment and delivery flow for CLOTHR.
It covers status transitions, role-based security, real-time updates via self-hosted
WebSockets, and the lost package handling process.

---

## 2. The Complete Status Flow

```
[Customer places order]
         │
         ▼
     ┌─────────┐
     │ pending │  ◄── Admin sees new order (real-time notification)
     └────┬────┘
          │  Admin packs & prepares
          ▼
   ┌────────────┐
   │ processing │  ◄── Admin controls
   └─────┬──────┘
         │  Admin assigns courier company + tracking number
         ▼
   ┌─────────┐
   │ shipped │  ◄── Admin controls
   └────┬────┘
        │  Courier assigns a Rider (must assign rider FIRST)
        ▼
  ┌──────────────────┐
  │ out_for_delivery │  ◄── Courier controls
  └────────┬─────────┘
           │  Rider marks delivered
           ▼
     ┌───────────┐
     │ delivered │  ◄── Rider triggers → real-time sync to Courier + Admin + Customer
     └───────────┘

  ──── Alternate path ────────────────────────────────────────────────
  At "out_for_delivery": Courier can report the package as LOST
         │
         ▼
   ┌──────┐
   │ lost │  ◄── Courier reports → Admin notified (real-time) → Admin notifies customer
   └──────┘
         │  Admin manually cancels
         ▼
   ┌───────────┐
   │ cancelled │
   └───────────┘
```

---

## 3. Role Responsibilities & Permissions

| Role | Allowed Status Transitions | What They See | Other Actions |
|---|---|---|---|
| **Admin** | `pending → processing` | All orders, all statuses | Notify customer if lost |
| **Admin** | `processing → shipped` | Real-time new order alerts | Full oversight dashboard |
| **Admin** | `any → cancelled` | Lost package reports | — |
| **Courier** | `shipped → out_for_delivery` | Only orders for their courier code | Assign rider, Report lost |
| **Rider** | `out_for_delivery → delivered` | Only their own assigned deliveries | — |
| **Customer** | ❌ None (read-only) | Their own orders only | Receives real-time notifications |

### Hard Security Rules (backend enforced — not just UI)
- **Rider:** Can ONLY update a delivery where `deliveries.rider_id = auth()->user()->rider->id`
- **Courier:** Can ONLY update an order where `orders.courier_service = courier->code`
- **Courier:** Can ONLY assign a rider who belongs to their courier (`riders.courier_id = courier->id`)
- **Admin:** Only role that can move an order to `cancelled`
- **No role** can skip steps (e.g., `pending → delivered` is rejected)
- All transitions validated server-side in `OrderService` — frontend is cosmetic only

---

## 4. Real-Time Architecture

### Technology: Laravel WebSockets (self-hosted)
**Package:** `beyondcode/laravel-websockets ^1.12`
**JS Client:** Laravel Echo + Pusher JS (as WebSocket client)

| Factor | Detail |
|---|---|
| External account | ❌ None needed |
| Cost | Free, self-hosted |
| Laravel 8.x compatible | ✅ Yes |
| PHP 7.3+ compatible | ✅ Yes |
| Extra process needed | ✅ `php artisan websockets:serve` (2nd terminal) |
| Debug dashboard | ✅ Built-in at `/laravel-websockets` |

### Running the App (Two Terminals)
```bash
# Terminal 1 — Web server
php artisan serve

# Terminal 2 — WebSocket server
php artisan websockets:serve
```

### Events to Broadcast

| Event Class | Channel Type | Channel Name | Who Listens | Triggered When |
|---|---|---|---|---|
| `NewOrderPlaced` | Private | `admin` | Admin | Customer places order |
| `OrderStatusUpdated` | Private | `admin` | Admin | Any order status changes |
| `OrderStatusUpdated` | Private | `user.{userId}` | Customer | Their order changes |
| `OrderStatusUpdated` | Private | `courier.{code}` | Courier | Their order changes |
| `RiderAssigned` | Private | `rider.{riderId}` | Rider | Courier assigns them |
| `PackageLostReported` | Private | `admin` | Admin | Courier reports lost |

### Real-Time Flow Example (Rider marks Delivered)

```
Rider taps "Mark Delivered" in their portal
        │
        ▼
POST /rider/deliveries/{id}/status  [CSRF + auth + ownership check]
        │
        ▼
RiderController::updateStatus()
  ├── abort_if rider doesn't own delivery → 403
  ├── validate status = 'delivered' only
  ├── DB::transaction { update delivery + order + timestamps }
  ├── UserNotification::notify(...) for customer, admin, courier
  └── broadcast(new OrderStatusUpdated($order))
        │
        ▼
Laravel WebSockets server receives event
        │
        ├──► private-admin channel         → Admin dashboard updates row
        ├──► private-courier.{code} channel → Courier portal updates row
        └──► private-user.{userId} channel  → Customer order page updates timeline
```

### Frontend Listening (per portal)

**Admin Layout:**
```javascript
Echo.private('admin')
    .listen('OrderStatusUpdated', (e) => { updateOrderRow(e.order); showToast(e.message); })
    .listen('PackageLostReported', (e) => { showLostAlert(e); })
    .listen('NewOrderPlaced',      (e) => { prependOrderToTable(e.order); playSound(); });
```

**Courier Layout (in portal.blade.php):**
```javascript
Echo.private(`courier.${courierCode}`)
    .listen('OrderStatusUpdated', (e) => { updateOrderRow(e.order); });
```

**Rider Layout (in portal.blade.php):**
```javascript
Echo.private(`rider.${riderId}`)
    .listen('RiderAssigned', (e) => { prependDeliveryCard(e.delivery); showToast('New delivery assigned!'); });
```

**Customer (in their order page):**
```javascript
Echo.private(`user.${userId}`)
    .listen('OrderStatusUpdated', (e) => { updateTimeline(e.order); });
```

---

## 5. The Real-Time System (Simple Explanation)

We installed a **Self-Hosted WebSocket Server**. This means your website talks to itself in real-time without needing any external accounts (like Pusher.com).

### What we installed:
1.  **Laravel WebSockets**: This is the "Engine" running in your terminal. It handles the live connection.
2.  **Laravel Echo**: This is the "Listener" on your website. It waits for updates and refreshes the page automatically.
3.  **Pusher JS**: A small library that allows Echo to talk to the WebSockets Engine.

### Why this is better:
*   ✅ **Zero Cost**: You don't need to pay for a Pusher account.
*   ✅ **Unlimited**: No limits on how many messages or users can be live.
*   ✅ **Private**: All data stays on your own server.

---

## 6. Security Enforcement

### 5.1 Route-Level Middleware

```
/admin/*    → ['auth', 'role:admin']
/courier/*  → ['auth', 'role:courier']
/rider/*    → ['auth', 'role:rider']
/profile/*  → ['auth'] (customers)
```

### 5.2 Controller-Level Ownership Checks

```php
// In RiderController — rider can't touch another rider's delivery
abort_if($delivery->rider_id !== auth()->user()->rider->id, 403);

// In CourierController — courier can't touch another courier's order
abort_if($order->courier_service !== $courier->code, 403);

// In CourierController — courier can only assign their own riders
abort_if($rider->courier_id !== $courier->id, 403);
```

### 5.3 OrderService Transition Guard

```php
$validTransitions = [
    // [currentStatus] => [roles allowed to transition] => [allowed next statuses]
    'pending'          => ['admin'   => ['processing', 'cancelled']],
    'processing'       => ['admin'   => ['shipped', 'cancelled']],
    'shipped'          => ['courier' => ['out_for_delivery']],
    'out_for_delivery' => ['rider'   => ['delivered']],
    'delivered'        => [], // terminal — no further transitions
    'cancelled'        => [], // terminal
];
```

### 5.4 WebSocket Channel Authorization (routes/channels.php)

```php
Broadcast::channel('admin', fn($user) => $user->role === 'admin');

Broadcast::channel('courier.{code}', function ($user, $code) {
    return $user->courierAccount?->code === $code;
});

Broadcast::channel('rider.{riderId}', function ($user, $riderId) {
    return (int) $user->rider?->id === (int) $riderId;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

### 5.5 Additional Security Measures

| Measure | Applied To |
|---|---|
| CSRF tokens | All POST/PUT/DELETE forms |
| `$request->validate()` | All controller inputs |
| `in:` validation rule | All status values — no arbitrary strings accepted |
| Rate limiting `throttle:30,1` | Status update + assign + report-lost endpoints |
| DB transactions | All multi-step database operations |
| `abort_if` ownership checks | Every rider/courier controller method |

---

## 6. Database Changes

### New Migration: `add_lost_fields_to_deliveries_table`
```php
$table->text('lost_reason')->nullable();
$table->timestamp('lost_at')->nullable();
```

### No other schema changes needed.

---

## 7. New Files to Create

| File | Purpose |
|---|---|
| `app/Events/NewOrderPlaced.php` | Broadcast when customer places order |
| `app/Events/OrderStatusUpdated.php` | Broadcast on any status change |
| `app/Events/RiderAssigned.php` | Broadcast to rider when assigned |
| `app/Events/PackageLostReported.php` | Broadcast to admin when lost |
| `app/Http/Middleware/RoleMiddleware.php` | Enforce role-based route access |

---

## 8. Files to Modify

### Backend
| File | Change |
|---|---|
| `.env` | Add WebSocket config (local, no external credentials) |
| `config/broadcasting.php` | Set driver to `pusher` (compatible with Laravel WebSockets) |
| `routes/web.php` | Add `reportLost`, `notifyCustomerLost` routes + rate limiting |
| `routes/channels.php` | Private channel authorization |
| `app/Kernel.php` | Register `RoleMiddleware` |
| `app/Services/OrderService.php` | Role-aware transitions, fire broadcast events |
| `app/Http/Controllers/CourierController.php` | `assignRider()` → fires `RiderAssigned`, add `reportLost()` |
| `app/Http/Controllers/RiderController.php` | Lock to `out_for_delivery → delivered`, fire events |
| `app/Http/Controllers/Admin/OrderController.php` | `notifyCustomerLost()`, show lost badge |
| `app/Models/UserNotification.php` | Add `order_lost`, `order_rider_assigned` types |

### Frontend (Blade)
| File | Change |
|---|---|
| `layouts/admin.blade.php` | Echo + WebSocket listeners, live toast alerts |
| `layouts/portal.blade.php` | Echo + WebSocket listeners for courier & rider |
| `courier/orders/show.blade.php` | "Assign Rider" modal, "Report Lost" button |
| `courier/orders.blade.php` | Rider name column, "lost" badge, real-time row update |
| `rider/dashboard.blade.php` | Real-time new task card, `out_for_delivery → delivered` only |
| `rider/deliveries/show.blade.php` | Mark delivered button only |
| `admin/orders.blade.php` | Rider info in modal, lost report badge, notify-customer action |

---

## 9. Implementation Phases

| Phase | Task |
|---|---|
| **1** | Install packages: `composer require beyondcode/laravel-websockets pusher/pusher-php-server` |
| **2** | Publish WebSocket config, update `.env` + `broadcasting.php` |
| **3** | Run migration: add `lost_at` + `lost_reason` to `deliveries` |
| **4** | Create `RoleMiddleware`, register in `Kernel.php`, apply to all routes |
| **5** | Create all 4 broadcast Event classes |
| **6** | Update `routes/channels.php` with private channel authorization |
| **7** | Update `OrderService` — role-aware transitions + event firing |
| **8** | Update `RiderController` — lock transitions, fire events |
| **9** | Update `CourierController` — assign rider + report lost + fire events |
| **10** | Update `Admin/OrderController` — lost report handling + notify customer |
| **11** | Update `UserNotification` model with new types |
| **12** | Update `routes/web.php` — new routes + rate limiting |
| **13** | Add Echo + WebSocket JS to `layouts/admin.blade.php` + `layouts/portal.blade.php` |
| **14** | Update all Blade views (Courier, Rider, Admin) |
| **15** | End-to-end test: full flow from order placed → delivered |

---

## 10. Approval Checklist ✅

- [x] Status flow: `pending → processing → shipped → out_for_delivery → delivered`
- [x] Role permissions confirmed
- [x] Lost package: Courier → Admin (real-time) → Admin notifies Customer
- [x] Rider: `out_for_delivery → delivered` only
- [x] Courier: must assign rider before setting `out_for_delivery`
- [x] All notifications fire simultaneously to all relevant parties
- [x] Real-time: **Laravel WebSockets** (self-hosted, no external account)
- [x] Security: Role middleware + ownership checks + channel auth + CSRF + rate limiting

---

*This document is the single source of truth. Implementation starts at Phase 1.*
