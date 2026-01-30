# Admin Market Management

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

---

## Sellers Management

### GET /admin/pending/sellers
Get all seller profiles awaiting approval.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `per_page` (integer, optional) — Items per page (default: 15)

**Response (200):**
```json
{
  "status": "success",
  "message": "Pending seller profiles retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 10,
        "business_name": "Fashion Store",
        "business_email": "fashion@example.com",
        "business_phone_number": "+2348012345678",
        "country": "Nigeria",
        "state": "Lagos",
        "city": "Lekki",
        "address": "123 Victoria Island, Lagos",
        "instagram": "@fashionstore",
        "facebook": "fashionstore",
        "business_registration_number": "RC123456",
        "bank_name": "GTBank",
        "account_number": "0123456789",
        "registration_status": "pending",
        "active": true,
        "rejection_reason": null,
        "identity_document": "https://cdn.example.com/docs/id.jpg",
        "business_certificate": "https://cdn.example.com/docs/cert.jpg",
        "business_logo": "https://cdn.example.com/logos/fashion.jpg",
        "created_at": "2025-06-26T10:30:00Z",
        "user": {
          "id": 10,
          "firstname": "John",
          "lastname": "Doe",
          "email": "john@example.com"
        }
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/admin/pending/sellers?page=1",
      "last": "http://localhost:8000/api/admin/pending/sellers?page=3",
      "prev": null,
      "next": "http://localhost:8000/api/admin/pending/sellers?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 3,
      "path": "http://localhost:8000/api/admin/pending/sellers",
      "per_page": 15,
      "to": 15,
      "total": 45
    }
  }
}
```

---

### GET /admin/market/sellers
Get all seller profiles with optional status filtering.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `status` (string, optional) — Filter by status: `pending`, `approved`, `rejected`

**Response (200):** — Same structure as pending sellers endpoint

---

### GET /admin/sellers/{id}
Get detailed information for a specific seller including their products.

**Path Parameters:**
- `id` (integer, required) — Seller profile ID

**Response (200):**
```json
{
  "status": "success",
  "message": "Seller details retrieved successfully",
  "data": {
    "seller": {
      "id": 1,
      "user": {
        "firstname": "John",
        "lastname": "Doe",
        "email": "john@example.com",
        "phone": "+2348012345678"
      },
      "business_name": "Fashion Store",
      "business_email": "fashion@example.com",
      "business_phone_number": "+2348012345678",
      "country": "Nigeria",
      "state": "Lagos",
      "city": "Lekki",
      "address": "123 Victoria Island, Lagos",
      "instagram": "@fashionstore",
      "facebook": "fashionstore",
      "business_registration_number": "RC123456",
      "bank_name": "GTBank",
      "account_number": "0123456789",
      "registration_status": "approved",
      "documents": {
        "identity_document": "https://cdn.example.com/docs/id.jpg",
        "business_certificate": "https://cdn.example.com/docs/cert.jpg",
        "business_logo": "https://cdn.example.com/logos/fashion.jpg"
      },
      "created_at": "2025-06-26T10:30:00Z",
      "products_count": 25,
      "products": [
        {
          "id": 1,
          "seller_profile_id": 1,
          "name": "Traditional Ankara Dress",
          "price": 15000,
          "status": "approved",
          "created_at": "2025-06-27T08:15:00Z"
        }
      ]
    }
  }
}
```

---

### PATCH /admin/seller/{id}/approve
Approve or reject a seller profile.

**Path Parameters:**
- `id` (integer, required) — Seller profile ID

**Request:**
```json
{
  "status": "approved",
  "rejection_reason": null
}
```

**OR (for rejection):**
```json
{
  "status": "rejected",
  "rejection_reason": "Business certificate is invalid or expired"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Seller profile approved successfully",
  "data": {
    "id": 1,
    "business_name": "Fashion Store",
    "registration_status": "approved",
    "rejection_reason": null,
    "created_at": "2025-06-26T10:30:00Z",
    "updated_at": "2025-06-29T14:25:00Z"
  }
}
```

**Note:** When status changes, email and push notifications are sent to the seller's user account.

---

### PATCH /admin/market/seller/{id}/status
Activate or deactivate a seller profile (toggle active status).

**Path Parameters:**
- `id` (integer, required) — Seller profile ID

**Request:**
```json
{
  "active": false
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Seller profile deactivated successfully",
  "data": {
    "id": 1,
    "business_name": "Fashion Store",
    "active": false,
    "updated_at": "2025-06-29T14:25:00Z"
  }
}
```

---

## Products Management

### GET /admin/pending/products
Get all products awaiting approval.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)

**Response (200):**
```json
{
  "status": "success",
  "message": "Pending products retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "seller_profile_id": 1,
        "name": "Traditional Ankara Dress",
        "description": "Beautiful handmade traditional dress",
        "price": 15000.00,
        "gender": "female",
        "style": "traditional",
        "tribe": "yoruba",
        "fabric_type": "ankara",
        "status": "pending",
        "image": "https://cdn.example.com/products/dress1.jpg",
        "size": "M",
        "processing_time_type": "normal",
        "processing_days": 3,
        "rejection_reason": null,
        "avg_rating": 4.5,
        "created_at": "2025-06-28T09:30:00Z",
        "sellerProfile": {
          "id": 1,
          "business_name": "Fashion Store",
          "user": {
            "id": 10,
            "firstname": "John",
            "lastname": "Doe"
          }
        }
      }
    ],
    "links": {...},
    "meta": {...}
  }
}
```

---

### GET /admin/market/products
Get all products with optional status filtering.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `status` (string, optional) — Filter by status: `pending`, `approved`, `rejected`

**Response (200):** — Same structure as pending products

---

### GET /admin/products/{id}
Get detailed product information with seller and review data.

**Path Parameters:**
- `id` (integer, required) — Product ID

**Response (200):**
```json
{
  "status": "success",
  "message": "Product details retrieved successfully",
  "data": {
    "product": {
      "id": 1,
      "name": "Traditional Ankara Dress",
      "description": "Beautiful handmade traditional dress",
      "gender": "female",
      "style": "traditional",
      "tribe": "yoruba",
      "fabric_type": "ankara",
      "price": 15000.00,
      "image": "https://cdn.example.com/products/dress1.jpg",
      "size": "M",
      "processing_time_type": "normal",
      "processing_days": 3,
      "status": "approved",
      "rejection_reason": null,
      "avg_rating": 4.5,
      "total_reviews": 12,
      "created_at": "2025-06-28T09:30:00Z"
    },
    "seller": {
      "business_name": "Fashion Store",
      "email": "fashion@example.com",
      "phone": "+2348012345678"
    }
  }
}
```

---

### PATCH /admin/product/{id}/approve
Approve or reject a product.

**Path Parameters:**
- `id` (integer, required) — Product ID

**Request:**
```json
{
  "status": "approved",
  "rejection_reason": null
}
```

**OR (for rejection):**
```json
{
  "status": "rejected",
  "rejection_reason": "Images do not meet quality standards"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Product approved successfully",
  "data": {
    "id": 1,
    "name": "Traditional Ankara Dress",
    "status": "approved",
    "rejection_reason": null,
    "updated_at": "2025-06-29T14:25:00Z"
  }
}
```

---

### PATCH /admin/market/product/{id}/status
Update product status (pending, approved, or rejected).

**Path Parameters:**
- `id` (integer, required) — Product ID

**Request:**
```json
{
  "status": "rejected"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Product status updated to rejected successfully",
  "data": {
    "id": 1,
    "name": "Traditional Ankara Dress",
    "status": "rejected",
    "updated_at": "2025-06-29T14:25:00Z"
  }
}
```
