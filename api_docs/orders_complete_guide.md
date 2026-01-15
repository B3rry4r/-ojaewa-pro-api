# Complete Orders API Guide

This document provides a comprehensive guide to the Orders API for the Oja Ewa mobile app, covering the entire order lifecycle from cart to delivery, including payment integration and notifications.

---

## Table of Contents
- [Order Status Lifecycle](#order-status-lifecycle)
- [Complete Order Flow](#complete-order-flow)
- [Cart Endpoints](#cart-endpoints)
- [Order Endpoints](#order-endpoints)
- [Payment Endpoints](#payment-endpoints)
- [Order Tracking](#order-tracking)
- [Notifications System](#notifications-system)
- [Admin Order Management](#admin-order-management)
- [Real-Life Simulation](#real-life-simulation)

---

## Order Status Lifecycle

```
┌─────────┐    ┌──────┐    ┌────────────┐    ┌─────────┐    ┌───────────┐
│ PENDING │───▶│ PAID │───▶│ PROCESSING │───▶│ SHIPPED │───▶│ DELIVERED │
└─────────┘    └──────┘    └────────────┘    └─────────┘    └───────────┘
     │                                             
     │         ┌───────────┐                      
     └────────▶│ CANCELLED │                      
               └───────────┘                      
```

| Status | Description | Changed By | Notification Sent |
|--------|-------------|------------|-------------------|
| `pending` | Order created, awaiting payment | System | ✅ Order Confirmed |
| `paid` | Payment confirmed via Paystack | System | ✅ Payment Successful |
| `processing` | Seller is preparing the order | Admin | ✅ Order Processing |
| `shipped` | Order has been shipped | Admin | ✅ Order Shipped |
| `delivered` | Order delivered to customer | Admin | ✅ Order Delivered |
| `cancelled` | Order cancelled | User or Admin | ✅ Order Cancelled |

---

## Complete Order Flow

### Flow Diagram

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  1. Add to   │────▶│  2. View     │────▶│  3. Place    │
│     Cart     │     │     Cart     │     │     Order    │
└──────────────┘     └──────────────┘     └──────────────┘
                                                 │
                                                 ▼
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  6. Verify   │◀────│  5. Complete │◀────│  4. Generate │
│    Payment   │     │    Payment   │     │  Payment Link│
└──────────────┘     └──────────────┘     └──────────────┘
       │
       ▼
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  7. Track    │────▶│  8. Receive  │────▶│  9. Order    │
│    Order     │     │   Updates    │     │   Delivered  │
└──────────────┘     └──────────────┘     └──────────────┘
```

---

## Cart Endpoints

All cart endpoints require authentication.

### Add Item to Cart

**POST** `/api/cart`

```json
// Request
{
  "product_id": 5,
  "quantity": 2,
  "selected_size": "L",
  "processing_time_type": "normal"
}

// Response
{
  "status": "success",
  "message": "Item added to cart",
  "data": {
    "cart_item": {
      "id": 1,
      "product_id": 5,
      "quantity": 2,
      "unit_price": "15000.00",
      "selected_size": "L",
      "product": {
        "id": 5,
        "name": "Traditional Agbada",
        "image": "https://...",
        "seller_profile": {
          "business_name": "Fashion House"
        }
      }
    },
    "cart_total": 30000,
    "items_count": 2
  }
}
```

### View Cart

**GET** `/api/cart`

```json
// Response
{
  "status": "success",
  "data": {
    "cart_id": 1,
    "items": [
      {
        "id": 1,
        "product_id": 5,
        "quantity": 2,
        "unit_price": "15000.00",
        "selected_size": "L",
        "product": {
          "id": 5,
          "name": "Traditional Agbada",
          "image": "https://..."
        }
      }
    ],
    "total": 30000,
    "items_count": 2
  }
}
```

### Update Cart Item

**PUT** `/api/cart/{cart_item_id}`

```json
// Request
{
  "quantity": 3
}

// Response
{
  "status": "success",
  "message": "Cart item updated",
  "data": {
    "cart_item": { ... },
    "cart_total": 45000,
    "items_count": 3
  }
}
```

### Remove Cart Item

**DELETE** `/api/cart/{cart_item_id}`

```json
// Response
{
  "status": "success",
  "message": "Item removed from cart"
}
```

### Clear Cart

**DELETE** `/api/cart/clear`

```json
// Response
{
  "status": "success",
  "message": "Cart cleared"
}
```

---

## Order Endpoints

All order endpoints require authentication.

### Create Order

**POST** `/api/orders`

Creates a new order from the provided items.

```json
// Request
{
  "items": [
    { "product_id": 5, "quantity": 2 },
    { "product_id": 8, "quantity": 1 }
  ]
}

// Response
{
  "message": "Order created successfully",
  "order": {
    "id": 123,
    "user_id": 1,
    "total_price": 45000,
    "status": "pending",
    "tracking_number": null,
    "cancellation_reason": null,
    "delivered_at": null,
    "payment_method": null,
    "payment_reference": null,
    "paid_at": null,
    "created_at": "2026-01-14T10:30:00.000000Z",
    "updated_at": "2026-01-14T10:30:00.000000Z",
    "orderItems": [
      {
        "id": 1,
        "order_id": 123,
        "product_id": 5,
        "quantity": 2,
        "unit_price": "15000.00"
      },
      {
        "id": 2,
        "order_id": 123,
        "product_id": 8,
        "quantity": 1,
        "unit_price": "15000.00"
      }
    ]
  }
}
```

**🔔 Notification:** User receives "Order Confirmed!" notification

---

### List User's Orders

**GET** `/api/orders`

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | int | Items per page (default: 10) |
| `page` | int | Page number |

```json
// Response
{
  "status": "success",
  "data": [
    {
      "id": 123,
      "total_price": "45000.00",
      "status": "paid",
      "tracking_number": null,
      "cancellation_reason": null,
      "delivered_at": null,
      "created_at": "2026-01-14T10:30:00.000000Z",
      "avg_rating": null,
      "orderItems": [
        {
          "id": 1,
          "product_id": 5,
          "quantity": 2,
          "unit_price": "15000.00",
          "product": {
            "id": 5,
            "name": "Traditional Agbada",
            "image": "https://...",
            "seller_profile": {
              "id": 1,
              "business_name": "Fashion House"
            }
          }
        }
      ]
    }
  ],
  "links": {
    "first": "https://api.../api/orders?page=1",
    "last": "https://api.../api/orders?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "https://api.../api/orders",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

---

### Get Order Details

**GET** `/api/orders/{id}`

```json
// Response
{
  "status": "success",
  "data": {
    "id": 123,
    "user_id": 1,
    "total_price": "45000.00",
    "status": "shipped",
    "tracking_number": "DHL-123456789",
    "cancellation_reason": null,
    "delivered_at": null,
    "payment_method": "paystack",
    "payment_reference": "ORDER_1705234567_abc123",
    "paid_at": "2026-01-14T10:35:00.000000Z",
    "created_at": "2026-01-14T10:30:00.000000Z",
    "orderItems": [ ... ],
    "user": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe",
      "email": "john@example.com"
    }
  }
}
```

---

### Cancel Order

**POST** `/api/orders/{id}/cancel`

⚠️ Users can only cancel orders with status `pending` or `processing`

```json
// Request
{
  "cancellation_reason": "Changed my mind about the purchase"
}

// Response
{
  "status": "success",
  "message": "Order cancelled successfully",
  "data": {
    "id": 123,
    "status": "cancelled",
    "cancellation_reason": "Changed my mind about the purchase",
    ...
  }
}
```

**🔔 Notification:** User receives "Order Cancelled" notification

---

## Payment Endpoints

### Generate Payment Link

**POST** `/api/payment/link`

Generates a Paystack payment link for an order.

```json
// Request
{
  "order_id": 123
}

// Response
{
  "status": "success",
  "message": "Payment link generated successfully",
  "data": {
    "payment_url": "https://checkout.paystack.com/abc123xyz",
    "access_code": "abc123xyz",
    "reference": "ORDER_1705234567_abc123",
    "amount": 45000,
    "currency": "NGN"
  }
}
```

**Mobile App Flow:**
1. Open `payment_url` in WebView or browser
2. User completes payment on Paystack
3. Paystack redirects to: `ojaewa://payment/callback?reference=ORDER_1705234567_abc123`
4. App catches deep link and extracts reference
5. App calls verify endpoint

---

### Verify Payment

**POST** `/api/payment/verify`

Verifies payment status with Paystack and updates order.

```json
// Request
{
  "reference": "ORDER_1705234567_abc123"
}

// Response (Success)
{
  "status": "success",
  "message": "Payment verified successfully",
  "data": {
    "order_id": 123,
    "payment_status": "success",
    "amount": 45000,
    "currency": "NGN",
    "paid_at": "2026-01-14T10:35:00.000000Z",
    "channel": "card",
    "reference": "ORDER_1705234567_abc123"
  }
}

// Response (Failed)
{
  "status": "error",
  "message": "Payment verification failed",
  "data": {
    "payment_status": "failed",
    "gateway_response": "Insufficient funds"
  }
}
```

---

## Order Tracking

### Get Order Tracking

**GET** `/api/orders/{id}/tracking`

```json
// Response
{
  "status": "success",
  "data": {
    "order_id": 123,
    "current_status": "shipped",
    "tracking_number": "DHL-123456789",
    "stages": [
      {
        "title": "Order Placed",
        "description": "Your order has been placed and is awaiting processing",
        "completed": true,
        "date": "Jan 14, 2026 10:30"
      },
      {
        "title": "Payment Confirmed",
        "description": "Payment received successfully",
        "completed": true,
        "date": "Jan 14, 2026 10:35"
      },
      {
        "title": "Processing",
        "description": "Your order is being prepared",
        "completed": true,
        "date": "Jan 14, 2026 14:00"
      },
      {
        "title": "Shipped",
        "description": "Your order has been shipped",
        "completed": true,
        "date": "Jan 15, 2026 09:00",
        "tracking_number": "DHL-123456789"
      },
      {
        "title": "Delivered",
        "description": "Your order has been delivered",
        "completed": false,
        "date": null
      }
    ],
    "estimated_delivery": "Jan 19, 2026"
  }
}
```

---

## Notifications System

The app uses a push notification system to keep users informed about their orders. Notifications are sent via:
1. **Push notifications** (in-app)
2. **Email notifications**

### Notification Events for Orders

| Event | Trigger | Notification Title | Message Example |
|-------|---------|-------------------|-----------------|
| `order_created` | Order placed | Order Confirmed! | Your order #123 has been confirmed. |
| `order_status_updated` | Status changes | Order Status Updated | Your order #123 status has been updated to shipped. |
| `order_status_updated` | Order delivered | Order Delivered | Your order #123 has been delivered. |
| `order_status_updated` | Order cancelled | Order Cancelled | Your order #123 has been cancelled. |

### Notification Payload Structure

```json
{
  "id": 1,
  "user_id": 5,
  "type": "push",
  "event": "order_status_updated",
  "title": "Order Status Updated",
  "message": "Your order #123 status has been updated to shipped.",
  "payload": {
    "order_id": 123,
    "status": "shipped",
    "deep_link": "/orders/123"
  },
  "read_at": null,
  "created_at": "2026-01-14T15:00:00.000000Z"
}
```

### Fetching Notifications

**GET** `/api/notifications`

```json
// Response
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "event": "order_status_updated",
        "title": "Order Shipped!",
        "message": "Your order #123 has been shipped and is on its way!",
        "payload": {
          "order_id": 123,
          "status": "shipped",
          "deep_link": "/orders/123"
        },
        "read_at": null,
        "created_at": "2026-01-14T15:00:00.000000Z"
      }
    ]
  }
}
```

### Get Unread Count

**GET** `/api/notifications/unread-count`

```json
{
  "status": "success",
  "data": {
    "unread_count": 5
  }
}
```

### Filter Notifications by Event

**GET** `/api/notifications/filter?event=order_status_updated`

### Mark as Read

**PATCH** `/api/notifications/{id}/read`

### Mark All as Read

**PATCH** `/api/notifications/read-all`

### Handling Deep Links

When a user taps a notification, use the `deep_link` from the payload to navigate:

```javascript
const handleNotification = (notification) => {
  const deepLink = notification.payload?.deep_link;
  
  // Navigate to order details
  if (deepLink?.startsWith('/orders/')) {
    const orderId = deepLink.split('/')[2];
    navigation.navigate('OrderDetails', { orderId });
  }
  
  // Mark notification as read
  markNotificationAsRead(notification.id);
};
```

---

## Admin Order Management

### List All Orders

**GET** `/api/admin/orders`

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | string | Filter by status |

```json
// Response
{
  "status": "success",
  "message": "Orders retrieved successfully",
  "data": {
    "data": [ ... ],
    "links": { ... },
    "meta": {
      "current_page": 1,
      "total": 50
    }
  }
}
```

### Get Order Details (Admin)

**GET** `/api/admin/order/{id}`

### Update Order Status

**PATCH** `/api/admin/order/{id}/status`

```json
// Request - Processing
{
  "status": "processing"
}

// Request - Shipped with tracking
{
  "status": "shipped",
  "tracking_number": "DHL-123456789"
}

// Request - Delivered
{
  "status": "delivered"
}

// Request - Cancelled
{
  "status": "canceled",
  "cancellation_reason": "Out of stock"
}

// Response
{
  "status": "success",
  "message": "Order status updated to shipped successfully",
  "data": {
    "id": 123,
    "status": "shipped",
    "tracking_number": "DHL-123456789",
    ...
  }
}
```

**🔔 Notification:** User automatically receives notification when admin updates status

---

## Real-Life Simulation

### Complete Journey: User Orders Traditional Agbada

#### Step 1: Add to Cart
```bash
POST /api/cart
Authorization: Bearer {token}
{
  "product_id": 5,
  "quantity": 1,
  "selected_size": "XL"
}
# ✅ Item added to cart
```

#### Step 2: Place Order
```bash
POST /api/orders
Authorization: Bearer {token}
{
  "items": [{ "product_id": 5, "quantity": 1 }]
}
# ✅ Order #123 created (status: pending)
# 🔔 Notification: "Order Confirmed!"
```

#### Step 3: Generate Payment Link
```bash
POST /api/payment/link
Authorization: Bearer {token}
{
  "order_id": 123
}
# ✅ Payment URL received
```

#### Step 4: User Pays on Paystack
```
User completes payment on Paystack checkout page
Paystack redirects to: ojaewa://payment/callback?reference=ORDER_xxx
```

#### Step 5: Verify Payment
```bash
POST /api/payment/verify
Authorization: Bearer {token}
{
  "reference": "ORDER_xxx"
}
# ✅ Payment verified (status: paid)
```

#### Step 6: Admin Processes Order
```bash
PATCH /api/admin/order/123/status
Authorization: Bearer {admin_token}
{
  "status": "processing"
}
# 🔔 Notification: "Order Processing"
```

#### Step 7: Admin Ships Order
```bash
PATCH /api/admin/order/123/status
Authorization: Bearer {admin_token}
{
  "status": "shipped",
  "tracking_number": "DHL-123456789"
}
# 🔔 Notification: "Order Shipped!"
```

#### Step 8: User Tracks Order
```bash
GET /api/orders/123/tracking
Authorization: Bearer {token}
# ✅ See tracking stages and tracking number
```

#### Step 9: Admin Marks Delivered
```bash
PATCH /api/admin/order/123/status
Authorization: Bearer {admin_token}
{
  "status": "delivered"
}
# 🔔 Notification: "Order Delivered!"
```

---

## Endpoints Summary

### User Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cart` | View cart |
| POST | `/api/cart` | Add item to cart |
| PUT | `/api/cart/{id}` | Update cart item |
| DELETE | `/api/cart/{id}` | Remove cart item |
| DELETE | `/api/cart/clear` | Clear cart |
| GET | `/api/orders` | List user's orders |
| POST | `/api/orders` | Create order |
| GET | `/api/orders/{id}` | Get order details |
| GET | `/api/orders/{id}/tracking` | Get tracking info |
| POST | `/api/orders/{id}/cancel` | Cancel order |
| POST | `/api/payment/link` | Generate payment link |
| POST | `/api/payment/verify` | Verify payment |
| GET | `/api/notifications` | Get all notifications |
| GET | `/api/notifications/unread-count` | Get unread count |
| PATCH | `/api/notifications/{id}/read` | Mark as read |

### Admin Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/orders` | List all orders |
| GET | `/api/admin/order/{id}` | Get order details |
| PATCH | `/api/admin/order/{id}/status` | Update order status |

---

## Error Responses

### Order Already Paid
```json
{
  "status": "error",
  "message": "Order has already been paid"
}
```

### Cannot Cancel Order
```json
{
  "status": "error",
  "message": "Order cannot be cancelled in current status"
}
```

### Payment Verification Failed
```json
{
  "status": "error",
  "message": "Payment verification failed",
  "data": {
    "payment_status": "failed",
    "gateway_response": "Card declined"
  }
}
```
