# Admin Order Management

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

---

### GET /admin/orders
List all orders with optional status filtering and pagination.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `status` (string, optional) — Filter by status: `pending`, `processing`, `shipped`, `delivered`, `canceled`, `paid`

**Request Example:**
```
GET /admin/orders?page=1&status=pending
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Orders retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 10,
        "status": "pending",
        "total_price": 45000.00,
        "subtotal": 43000.00,
        "delivery_fee": 2000.00,
        "shipping_name": "John Doe",
        "shipping_phone": "+2348012345678",
        "shipping_address": "123 Main Street",
        "shipping_city": "Lagos",
        "shipping_state": "Lagos",
        "shipping_country": "Nigeria",
        "payment_reference": null,
        "payment_data": null,
        "tracking_number": null,
        "delivered_at": null,
        "cancellation_reason": null,
        "created_at": "2025-06-28T09:30:00Z",
        "updated_at": "2025-06-28T09:30:00Z",
        "user": {
          "id": 10,
          "firstname": "John",
          "lastname": "Doe",
          "email": "john@example.com"
        },
        "orderItems": [
          {
            "id": 1,
            "order_id": 1,
            "product_id": 5,
            "quantity": 2,
            "price": 15000.00,
            "product": {
              "id": 5,
              "name": "Traditional Ankara Dress",
              "price": 15000.00
            }
          }
        ]
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/admin/orders?page=1",
      "last": "http://localhost:8000/api/admin/orders?page=5",
      "prev": null,
      "next": "http://localhost:8000/api/admin/orders?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 5,
      "path": "http://localhost:8000/api/admin/orders",
      "per_page": 15,
      "to": 15,
      "total": 75
    }
  }
}
```

---

### GET /admin/order/{id}
Get detailed information for a specific order including items, user, and reviews.

**Path Parameters:**
- `id` (integer, required) — Order ID

**Response (200):**
```json
{
  "status": "success",
  "message": "Order retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 10,
    "status": "processing",
    "total_price": 45000.00,
    "subtotal": 43000.00,
    "delivery_fee": 2000.00,
    "shipping_name": "John Doe",
    "shipping_phone": "+2348012345678",
    "shipping_address": "123 Main Street",
    "shipping_city": "Lagos",
    "shipping_state": "Lagos",
    "shipping_country": "Nigeria",
    "payment_reference": "PAY_abc123xyz",
    "payment_data": {"gateway": "paystack", "status": "success"},
    "tracking_number": "NG123456789",
    "delivered_at": null,
    "cancellation_reason": null,
    "created_at": "2025-06-28T09:30:00Z",
    "updated_at": "2025-06-29T10:15:00Z",
    "user": {
      "id": 10,
      "firstname": "John",
      "lastname": "Doe",
      "email": "john@example.com",
      "phone": "+2348012345678"
    },
    "orderItems": [
      {
        "id": 1,
        "order_id": 1,
        "product_id": 5,
        "quantity": 2,
        "price": 15000.00,
        "created_at": "2025-06-28T09:30:00Z",
        "product": {
          "id": 5,
          "name": "Traditional Ankara Dress",
          "price": 15000.00,
          "seller_profile_id": 1,
          "sellerProfile": {
            "id": 1,
            "business_name": "Fashion Store",
            "user": {
              "id": 20,
              "firstname": "Jane",
              "lastname": "Smith"
            }
          }
        }
      }
    ],
    "reviews": []
  }
}
```

---

### PATCH /admin/order/{id}/status
Update order status with optional tracking information and cancellation reasons.

**Path Parameters:**
- `id` (integer, required) — Order ID

**Request:**
```json
{
  "status": "shipped",
  "tracking_number": "NG123456789",
  "cancellation_reason": null
}
```

**Valid statuses:**
- `pending` — Order created, awaiting payment processing
- `processing` — Payment received, order being prepared
- `shipped` — Order shipped with tracking number
- `delivered` — Order delivered to customer
- `canceled` — Order cancelled
- `paid` — Payment confirmed

**Response (200):**
```json
{
  "status": "success",
  "message": "Order status updated to shipped successfully",
  "data": {
    "id": 1,
    "status": "shipped",
    "tracking_number": "NG123456789",
    "updated_at": "2025-06-29T11:20:00Z"
  }
}
```

**Error Response (404):**
```json
{
  "message": "No query results found for model [App\\Models\\Order] 1"
}
```

**Note:** When order status changes, email and push notifications are sent to the customer with status update details and tracking information (if applicable).
