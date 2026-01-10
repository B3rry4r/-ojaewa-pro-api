# Order & Payment Management Endpoints

## Order Management

### 1. Get User Orders
**GET** `/api/orders`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "total_price": 45000.00,
      "status": "shipped",
      "tracking_number": "TRK123456789",
      "payment_reference": "ORDER_202501201234_REF",
      "payment_data": null,
      "cancellation_reason": null,
      "delivered_at": null,
      "created_at": "2025-01-19T10:00:00.000000Z",
      "updated_at": "2025-01-20T14:00:00.000000Z",
      "order_items": [
        {
          "id": 1,
          "order_id": 1,
          "product_id": 1,
          "quantity": 1,
          "unit_price": 25000.00,
          "created_at": "2025-01-19T10:00:00.000000Z",
          "updated_at": "2025-01-19T10:00:00.000000Z",
          "product": {
            "id": 1,
            "name": "Traditional Yoruba Agbada",
            "gender": "male",
            "style": "traditional",
            "tribe": "yoruba",
            "price": 25000.00,
            "image": "https://example.com/agbada.jpg"
          }
        },
        {
          "id": 2,
          "order_id": 1,
          "product_id": 2,
          "quantity": 1,
          "unit_price": 20000.00,
          "product": {
            "id": 2,
            "name": "Hausa Kaftan",
            "gender": "female",
            "style": "traditional",
            "tribe": "hausa",
            "price": 20000.00,
            "image": "https://example.com/kaftan.jpg"
          }
        }
      ]
    }
  ],
  "links": {
    "first": "http://localhost/api/orders?page=1",
    "last": "http://localhost/api/orders?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://localhost/api/orders",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

**Test Coverage:** ✅ Tested

---

### 2. Create Order
**POST** `/api/orders`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 1
    },
    {
      "product_id": 3,
      "quantity": 2
    }
  ]
}
```

**Validation Rules:**
- `items`: required|array|min:1
- `items.*.product_id`: required|exists:products,id,status,approved
- `items.*.quantity`: required|integer|min:1

**Response (201):**
```json
{
  "message": "Order created successfully",
  "order": {
    "id": 2,
    "user_id": 1,
    "total_price": 65000.00,
    "status": "pending",
    "tracking_number": null,
    "payment_reference": null,
    "payment_data": null,
    "cancellation_reason": null,
    "delivered_at": null,
    "created_at": "2025-01-20T15:00:00.000000Z",
    "updated_at": "2025-01-20T15:00:00.000000Z",
    "order_items": [
      {
        "id": 3,
        "order_id": 2,
        "product_id": 1,
        "quantity": 1,
        "unit_price": 25000.00,
        "product": {
          "id": 1,
          "name": "Traditional Yoruba Agbada",
          "price": 25000.00
        }
      },
      {
        "id": 4,
        "order_id": 2,
        "product_id": 3,
        "quantity": 2,
        "unit_price": 20000.00,
        "product": {
          "id": 3,
          "name": "Modern Dashiki",
          "price": 20000.00
        }
      }
    ]
  }
}
```

**Error Response (500) - Transaction Failed:**
```json
{
  "message": "Failed to create order",
  "error": "Product not found or not approved"
}
```

**Test Coverage:** ✅ Tested

---

### 3. Get Single Order
**GET** `/api/orders/{id}`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "total_price": 45000.00,
  "status": "delivered",
  "tracking_number": "TRK123456789",
  "payment_reference": "ORDER_202501201234_REF",
  "payment_data": "{\"status\":\"success\",\"reference\":\"ORDER_202501201234_REF\",\"amount\":4500000,\"currency\":\"NGN\"}",
  "cancellation_reason": null,
  "delivered_at": "2025-01-22T16:30:00.000000Z",
  "created_at": "2025-01-19T10:00:00.000000Z",
  "updated_at": "2025-01-22T16:30:00.000000Z",
  "order_items": [
    {
      "id": 1,
      "order_id": 1,
      "product_id": 1,
      "quantity": 1,
      "unit_price": 25000.00,
      "product": {
        "id": 1,
        "name": "Traditional Yoruba Agbada",
        "gender": "male",
        "style": "traditional",
        "tribe": "yoruba",
        "description": "Beautiful handcrafted Agbada",
        "price": 25000.00,
        "image": "https://example.com/agbada.jpg",
        "seller_profile": {
          "id": 1,
          "business_name": "Yoruba Traditions Ltd"
        }
      }
    }
  ],
  "reviews": [
    {
      "id": 1,
      "user_id": 1,
      "reviewable_type": "App\\Models\\Order",
      "reviewable_id": 1,
      "rating": 5,
      "comment": "Excellent service and fast delivery!",
      "created_at": "2025-01-23T10:00:00.000000Z"
    }
  ]
}
```

**Test Coverage:** ✅ Tested

---

### 4. Update Order Status (Admin)
**PUT** `/api/orders/{id}/status`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "status": "shipped",
  "tracking_number": "TRK987654321",
  "cancellation_reason": null
}
```

**Validation Rules:**
- `status`: required|in:pending,processing,shipped,delivered,cancelled
- `tracking_number`: nullable|string
- `cancellation_reason`: nullable|string

**Response (200):**
```json
{
  "message": "Order status updated successfully",
  "order": {
    "id": 1,
    "user_id": 1,
    "total_price": 45000.00,
    "status": "shipped",
    "tracking_number": "TRK987654321",
    "delivered_at": null,
    "updated_at": "2025-01-20T16:00:00.000000Z",
    "user": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe",
      "email": "john.doe@example.com"
    },
    "order_items": [...]
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 5. Track Order
**GET** `/api/orders/{id}/tracking`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200) - Active Order:**
```json
{
  "status": "success",
  "data": {
    "order_id": 1,
    "current_status": "shipped",
    "tracking_number": "TRK123456789",
    "stages": [
      {
        "title": "Order Placed",
        "description": "Your order has been placed and is awaiting processing",
        "completed": true,
        "date": "Jan 19, 2025 10:00"
      },
      {
        "title": "Processing",
        "description": "Your order is being prepared",
        "completed": true,
        "date": null
      },
      {
        "title": "Shipped",
        "description": "Tracking: TRK123456789",
        "completed": true,
        "date": "Current stage"
      },
      {
        "title": "Delivered",
        "description": "Your order has been delivered",
        "completed": false,
        "date": null
      }
    ],
    "estimated_delivery": "Jan 24, 2025"
  }
}
```

**Response (200) - Cancelled Order:**
```json
{
  "status": "success",
  "data": {
    "order_id": 2,
    "current_status": "cancelled",
    "tracking_number": null,
    "stages": [
      {
        "title": "Order Placed",
        "description": "Your order has been placed and is awaiting processing",
        "completed": true,
        "date": "Jan 20, 2025 10:00"
      },
      {
        "title": "Cancelled",
        "description": "Customer requested cancellation",
        "completed": true,
        "date": "Jan 20, 2025 15:30"
      }
    ],
    "estimated_delivery": null
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Payment Management

### 6. Generate Payment Link
**POST** `/api/payment/initialize`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "order_id": 1
}
```

**Validation Rules:**
- `order_id`: required|integer|exists:orders,id

**Response (200):**
```json
{
  "status": "success",
  "message": "Payment link generated successfully",
  "data": {
    "payment_url": "https://checkout.paystack.com/v3/hosted/pay/abc123def456",
    "access_code": "abc123def456",
    "reference": "ORDER_202501201234_REF",
    "amount": 45000.00,
    "currency": "NGN"
  }
}
```

**Error Response (400) - Already Paid:**
```json
{
  "status": "error",
  "message": "Order is already paid"
}
```

**Test Coverage:** 🔴 Not Tested

---

### 7. Verify Payment
**POST** `/api/payment/verify`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "reference": "ORDER_202501201234_REF"
}
```

**Validation Rules:**
- `reference`: required|string

**Response (200) - Successful Payment:**
```json
{
  "status": "success",
  "message": "Payment verified successfully",
  "data": {
    "order_id": 1,
    "payment_status": "success",
    "amount": 45000.00,
    "currency": "NGN",
    "paid_at": "2025-01-20T14:30:00.000000Z"
  }
}
```

**Error Response (404) - Order Not Found:**
```json
{
  "status": "error",
  "message": "Order not found for this payment reference"
}
```

**Error Response (400) - Payment Failed:**
```json
{
  "status": "error",
  "message": "Payment verification failed"
}
```

**Test Coverage:** 🔴 Not Tested

---

### 8. Payment Webhook (Paystack)
**POST** `/api/payment/webhook`
**Middleware:** None (Webhook endpoint)

**Request Headers:**
```
x-paystack-signature: computed_signature_from_paystack
Content-Type: application/json
```

**Request (Successful Payment):**
```json
{
  "event": "charge.success",
  "data": {
    "reference": "ORDER_202501201234_REF",
    "amount": 4500000,
    "currency": "NGN",
    "status": "success",
    "paid_at": "2025-01-20T14:30:00.000000Z",
    "metadata": {
      "payment_type": "order",
      "order_id": 1,
      "user_id": 1
    },
    "gateway_response": "Approved"
  }
}
```

**Request (Failed Payment):**
```json
{
  "event": "charge.failed",
  "data": {
    "reference": "ORDER_202501201234_REF",
    "amount": 4500000,
    "currency": "NGN",
    "status": "failed",
    "metadata": {
      "payment_type": "order",
      "order_id": 1,
      "user_id": 1
    },
    "gateway_response": "Declined by financial institution"
  }
}
```

**Response (200):**
```json
{
  "message": "Webhook received"
}
```

**Error Response (400) - Invalid Signature:**
```json
{
  "message": "Invalid signature"
}
```

**Test Coverage:** 🔴 Not Tested

---

## Review Management

### 9. Create Review
**POST** `/api/reviews`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "reviewable_type": "App\\Models\\Product",
  "reviewable_id": 1,
  "rating": 5,
  "comment": "Excellent quality product! Fast delivery and great customer service."
}
```

**Validation Rules:**
- `reviewable_type`: required|string|in:App\\Models\\Product,App\\Models\\Order
- `reviewable_id`: required|integer
- `rating`: required|integer|between:1,5
- `comment`: required|string|max:1000

**Response (201):**
```json
{
  "message": "Review created successfully",
  "review": {
    "id": 1,
    "user_id": 1,
    "reviewable_type": "App\\Models\\Product",
    "reviewable_id": 1,
    "rating": 5,
    "comment": "Excellent quality product! Fast delivery and great customer service.",
    "created_at": "2025-01-20T16:30:00.000000Z",
    "updated_at": "2025-01-20T16:30:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 10. Get Reviews by Entity
**GET** `/api/reviews/{type}/{id}`
**Middleware:** `auth:sanctum` (for order reviews), None for product reviews

**Request:** No body required

**Examples:**
- GET `/api/reviews/product/1` - Get all reviews for product ID 1
- GET `/api/reviews/order/1` - Get all reviews for order ID 1 (own orders only)

**Response (200) - Product Reviews:**
```json
{
  "entity": {
    "id": 1,
    "type": "product",
    "avg_rating": 4.7
  },
  "reviews": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 2,
        "reviewable_type": "App\\Models\\Product",
        "reviewable_id": 1,
        "rating": 5,
        "comment": "Excellent quality product!",
        "created_at": "2025-01-20T16:30:00.000000Z",
        "user": {
          "id": 2,
          "firstname": "Jane",
          "lastname": "Smith"
        }
      },
      {
        "id": 2,
        "user_id": 3,
        "reviewable_type": "App\\Models\\Product",
        "reviewable_id": 1,
        "rating": 4,
        "comment": "Good product, delivery could be faster.",
        "created_at": "2025-01-19T14:20:00.000000Z",
        "user": {
          "id": 3,
          "firstname": "Bob",
          "lastname": "Johnson"
        }
      }
    ],
    "first_page_url": "http://localhost/api/reviews/product/1?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 2
  }
}
```

**Error Response (400) - Invalid Entity Type:**
```json
{
  "message": "Invalid entity type"
}
```

**Error Response (403) - Unauthorized Order Access:**
```json
{
  "message": "Unauthorized to view these reviews"
}
```

**Test Coverage:** ✅ Tested

---

## Order & Payment Status Flow

### Order Status Progression:
1. **pending** - Order created, awaiting payment/processing
2. **processing** - Order confirmed and being prepared
3. **shipped** - Order shipped with tracking number
4. **delivered** - Order delivered to customer
5. **cancelled** - Order cancelled (with reason)

### Payment Integration:
- **Paystack** for payment processing
- **NGN** currency support
- **Webhook** integration for real-time payment updates
- **Payment references** for order tracking
- **Automatic status updates** when payment confirmed

### Review System:
- **Polymorphic reviews** - Can review products or entire orders
- **1-5 star rating** system
- **Public product reviews** - Visible to all users
- **Private order reviews** - Only visible to order owner
- **Average rating calculation** using model accessors

## Security & Business Rules

1. **Order Ownership:** Users can only view/modify their own orders
2. **Payment Security:** Webhook signature verification for Paystack
3. **Review Authorization:** Users can only review their purchased products/orders
4. **Status Updates:** Only admins can update order status
5. **Transaction Safety:** Database transactions for order creation
6. **Notification Integration:** Email and push notifications for order events

## Test Coverage Summary

- **Order Management:** ✅ 3/5 endpoints tested (60%)
- **Payment Management:** 🔴 0/3 endpoints tested (0%)
- **Review Management:** ✅ 2/2 endpoints tested (100%)

**Total Order & Payment Management:** ✅ 5/10 endpoints tested (50%)

---
*Last Updated: January 2025*