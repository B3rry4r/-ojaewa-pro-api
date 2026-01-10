# User Notification Management Endpoints

## User Notification System

### 1. Get User Notifications
**GET** `/api/notifications`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": "uuid-notification-1",
        "type": "App\\Notifications\\OrderStatusNotification",
        "notifiable_type": "App\\Models\\User",
        "notifiable_id": 1,
        "data": {
          "title": "Order Status Updated",
          "message": "Your order #123 has been shipped",
          "order_id": 123,
          "deep_link": "/orders/123"
        },
        "read_at": null,
        "created_at": "2025-01-20T14:30:00.000000Z",
        "updated_at": "2025-01-20T14:30:00.000000Z"
      },
      {
        "id": "uuid-notification-2",
        "type": "App\\Notifications\\ProductApprovedNotification",
        "notifiable_type": "App\\Models\\User",
        "notifiable_id": 1,
        "data": {
          "title": "Product Approved",
          "message": "Your product 'Traditional Agbada' has been approved",
          "product_id": 1,
          "deep_link": "/products/1"
        },
        "read_at": "2025-01-20T15:00:00.000000Z",
        "created_at": "2025-01-20T13:00:00.000000Z",
        "updated_at": "2025-01-20T15:00:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 15
  }
}
```

**Test Coverage:** ✅ Tested

---

### 2. Get Unread Notifications Count
**GET** `/api/notifications/unread-count`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "unread_count": 5
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 3. Mark Notification as Read
**PATCH** `/api/notifications/{id}/read`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Notification marked as read",
  "data": {
    "id": "uuid-notification-1",
    "type": "App\\Notifications\\OrderStatusNotification",
    "notifiable_type": "App\\Models\\User",
    "notifiable_id": 1,
    "data": {
      "title": "Order Status Updated",
      "message": "Your order #123 has been shipped"
    },
    "read_at": "2025-01-20T16:00:00.000000Z",
    "created_at": "2025-01-20T14:30:00.000000Z",
    "updated_at": "2025-01-20T16:00:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 4. Mark All Notifications as Read
**PATCH** `/api/notifications/mark-all-read`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Marked 3 notifications as read"
}
```

**Test Coverage:** ✅ Tested

---

### 5. Delete Notification
**DELETE** `/api/notifications/{id}`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Notification deleted"
}
```

**Error Response (404):**
```json
{
  "status": "error",
  "message": "Notification not found"
}
```

**Test Coverage:** ✅ Tested

---

### 6. Filter Notifications
**GET** `/api/notifications/filter`
**Middleware:** `auth:sanctum`

**Query Parameters:**
```
GET /api/notifications/filter?type=push&read=false&event=order_status
```

**Validation Rules:**
- `type`: nullable|in:push,email
- `event`: nullable|string
- `read`: nullable|boolean

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": "uuid-notification-3",
        "type": "push",
        "event": "order_status",
        "data": {
          "title": "Order Delivered",
          "message": "Your order has been delivered successfully"
        },
        "read_at": null,
        "created_at": "2025-01-20T16:30:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 1
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Notification Preferences Management

### 7. Get Notification Preferences
**GET** `/api/notifications/preferences`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Notification preferences retrieved successfully",
  "data": {
    "email_notifications": true,
    "push_notifications": true,
    "sms_notifications": false,
    "order_updates": true,
    "promotional_emails": true,
    "security_alerts": true
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 8. Update Notification Preferences
**PUT** `/api/notifications/preferences`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "email_notifications": true,
  "push_notifications": false,
  "sms_notifications": false,
  "order_updates": true,
  "promotional_emails": false,
  "security_alerts": true
}
```

**Validation Rules:**
- `email_notifications`: sometimes|boolean
- `push_notifications`: sometimes|boolean
- `sms_notifications`: sometimes|boolean
- `order_updates`: sometimes|boolean
- `promotional_emails`: sometimes|boolean
- `security_alerts`: sometimes|boolean

**Response (200):**
```json
{
  "status": "success",
  "message": "Notification preferences updated successfully",
  "data": {
    "email_notifications": true,
    "push_notifications": false,
    "sms_notifications": false,
    "order_updates": true,
    "promotional_emails": false,
    "security_alerts": true
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Notification Types & Events

### Notification Types:
- **Order Notifications** - Order status updates, delivery confirmations
- **Product Notifications** - Product approval/rejection, inventory updates
- **Business Notifications** - Business profile status, payment updates
- **System Notifications** - Maintenance alerts, feature announcements
- **Marketing Notifications** - Promotions, new collections, sales
- **Security Notifications** - Login alerts, password changes

### Notification Events:
- `order_created` - New order placed
- `order_status_updated` - Order status changed
- `product_approved` - Product approved by admin
- `product_rejected` - Product rejected by admin
- `business_approved` - Business profile approved
- `payment_received` - Payment confirmed
- `system_maintenance` - System maintenance notice
- `promotion_alert` - New promotion available

### Notification Channels:
- **Push Notifications** - Mobile app push notifications
- **Email Notifications** - Email delivery
- **In-App Notifications** - Dashboard notifications
- **SMS Notifications** - Text message delivery (future feature)

## Test Coverage Summary

- **User Notifications:** ✅ 4/6 endpoints tested (67%)
- **Notification Preferences:** 🔴 0/2 endpoints tested (0%)

**Total Notification Management:** ✅ 4/8 endpoints tested (50%)

---
*Last Updated: January 2025*