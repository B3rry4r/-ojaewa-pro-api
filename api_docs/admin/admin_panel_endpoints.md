# Admin Panel Endpoints

## Admin Dashboard & Overview

### 1. Get Dashboard Statistics
**GET** `/api/admin/dashboard`
**Middleware:** `admin.auth`

**Request:** No body required (admin token in Authorization header)

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "total_users": 1250,
    "total_revenue": 2500000.00,
    "total_businesses": 75,
    "total_sellers": 42,
    "market_revenue": 1800000.00
  }
}
```

**Test Coverage:** ✅ Tested

---

## Admin User Management

### 2. Get All Users (Admin View)
**GET** `/api/admin/users`
**Middleware:** `admin.auth`

**Query Parameters:**
- `search`: Search by firstname, lastname, email, or phone
- `page`: Page number for pagination

**Response (200):**
```json
{
  "status": "success",
  "message": "Users retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@example.com",
        "phone": "+2348012345678",
        "email_verified_at": "2025-01-20T10:00:00.000000Z",
        "created_at": "2025-01-15T08:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "name": "John Doe",
        "seller_profile_count": 1,
        "business_profiles_count": 2,
        "orders_count": 5
      },
      {
        "id": 2,
        "firstname": "Jane",
        "lastname": "Smith",
        "email": "jane.smith@example.com",
        "phone": "+2348087654321",
        "email_verified_at": "2025-01-18T14:30:00.000000Z",
        "created_at": "2025-01-18T14:00:00.000000Z",
        "updated_at": "2025-01-18T14:30:00.000000Z",
        "name": "Jane Smith",
        "seller_profile_count": 0,
        "business_profiles_count": 1,
        "orders_count": 2
      }
    ],
    "links": {
      "first": "http://localhost/api/admin/users?page=1",
      "last": "http://localhost/api/admin/users?page=5",
      "prev": null,
      "next": "http://localhost/api/admin/users?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 5,
      "path": "http://localhost/api/admin/users",
      "per_page": 15,
      "to": 15,
      "total": 75
    }
  }
}
```

**Test Coverage:** ✅ Tested

---

### 3. Get Single User (Admin View)
**GET** `/api/admin/users/{id}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "User retrieved successfully",
  "data": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "john.doe@example.com",
    "phone": "+2348012345678",
    "email_verified_at": "2025-01-20T10:00:00.000000Z",
    "created_at": "2025-01-15T08:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z",
    "seller_profile": {
      "id": 1,
      "business_name": "Traditional Wear Nigeria",
      "active": true,
      "created_at": "2025-01-15T10:00:00.000000Z"
    },
    "business_profiles": [
      {
        "id": 1,
        "business_name": "Beauty Store Lagos",
        "category": "beauty",
        "store_status": "approved",
        "created_at": "2025-01-16T08:00:00.000000Z"
      }
    ],
    "orders": [
      {
        "id": 1,
        "total_price": 45000.00,
        "status": "delivered",
        "created_at": "2025-01-19T10:00:00.000000Z"
      }
    ]
  }
}
```

**Test Coverage:** ✅ Tested

---

### 4. Update User (Admin)
**PUT** `/api/admin/users/{id}`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "firstname": "John",
  "lastname": "Doe",
  "email": "john.doe.updated@example.com",
  "phone": "+2348012345678",
  "email_verified_at": "2025-01-20T10:00:00.000000Z"
}
```

**Validation Rules:**
- `firstname`: sometimes|required|string|max:255
- `lastname`: sometimes|required|string|max:255
- `email`: sometimes|required|email|unique:users,email,{user_id}
- `phone`: sometimes|nullable|string|max:30
- `email_verified_at`: sometimes|nullable|date

**Response (200):**
```json
{
  "status": "success",
  "message": "User updated successfully",
  "data": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "john.doe.updated@example.com",
    "phone": "+2348012345678",
    "email_verified_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T16:00:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 5. Suspend/Unsuspend User
**PUT** `/api/admin/users/{id}/suspend`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "suspend": true,
  "reason": "Violation of terms of service"
}
```

**Validation Rules:**
- `suspend`: required|boolean
- `reason`: required_if:suspend,true|string|max:500

**Response (200):**
```json
{
  "status": "success",
  "message": "User suspended successfully",
  "data": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "john.doe@example.com",
    "suspended": true,
    "suspension_reason": "Violation of terms of service",
    "suspended_at": "2025-01-20T16:30:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 6. Delete User (Admin)
**DELETE** `/api/admin/users/{id}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "User deleted successfully"
}
```

**Test Coverage:** ✅ Tested

---

## Admin Order Management

### 7. Get All Orders (Admin View)
**GET** `/api/admin/orders`
**Middleware:** `admin.auth`

**Query Parameters:**
- `status`: Filter by order status (optional)
- `page`: Page number for pagination

**Response (200):**
```json
{
  "status": "success",
  "message": "Orders retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "total_price": 45000.00,
        "status": "shipped",
        "tracking_number": "TRK123456789",
        "payment_reference": "ORDER_202501201234_REF",
        "created_at": "2025-01-19T10:00:00.000000Z",
        "updated_at": "2025-01-20T14:00:00.000000Z",
        "user": {
          "id": 1,
          "firstname": "John",
          "lastname": "Doe",
          "email": "john.doe@example.com"
        },
        "order_items": [
          {
            "id": 1,
            "product_id": 1,
            "quantity": 1,
            "unit_price": 25000.00,
            "product": {
              "id": 1,
              "name": "Traditional Yoruba Agbada",
              "price": 25000.00
            }
          }
        ]
      }
    ],
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 3,
      "per_page": 15,
      "total": 42
    }
  }
}
```

**Test Coverage:** ✅ Tested

---

### 8. Get Single Order (Admin View)
**GET** `/api/admin/orders/{id}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Order retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "total_price": 45000.00,
    "status": "delivered",
    "tracking_number": "TRK123456789",
    "payment_reference": "ORDER_202501201234_REF",
    "payment_data": "{\"status\":\"success\",\"amount\":4500000}",
    "cancellation_reason": null,
    "delivered_at": "2025-01-22T16:30:00.000000Z",
    "created_at": "2025-01-19T10:00:00.000000Z",
    "updated_at": "2025-01-22T16:30:00.000000Z",
    "user": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe",
      "email": "john.doe@example.com",
      "phone": "+2348012345678"
    },
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
          "price": 25000.00,
          "seller_profile": {
            "id": 1,
            "business_name": "Traditional Wear Nigeria",
            "user": {
              "id": 2,
              "firstname": "Seller",
              "lastname": "User",
              "email": "seller@traditional.com"
            }
          }
        }
      }
    ],
    "reviews": [
      {
        "id": 1,
        "user_id": 1,
        "rating": 5,
        "comment": "Excellent service!",
        "created_at": "2025-01-23T10:00:00.000000Z"
      }
    ]
  }
}
```

**Test Coverage:** ✅ Tested

---

### 9. Update Order Status (Admin)
**PUT** `/api/admin/orders/{id}/status`
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
- `status`: required|in:pending,processing,shipped,delivered,canceled,paid
- `tracking_number`: nullable|string
- `cancellation_reason`: nullable|string

**Response (200):**
```json
{
  "status": "success",
  "message": "Order status updated to shipped successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "total_price": 45000.00,
    "status": "shipped",
    "tracking_number": "TRK987654321",
    "delivered_at": null,
    "updated_at": "2025-01-20T17:00:00.000000Z",
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

**Test Coverage:** ✅ Tested

---

## Admin Product Management

### 10. Get All Products (Admin View)
**GET** `/api/admin/products`
**Middleware:** `admin.auth`

**Query Parameters:**
- `status`: Filter by product status (optional)
- `page`: Page number for pagination

**Response (200):**
```json
{
  "status": "success",
  "message": "Products retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Traditional Yoruba Agbada",
        "gender": "male",
        "style": "traditional",
        "tribe": "yoruba",
        "description": "Beautiful handcrafted Agbada with intricate embroidery",
        "price": 25000.00,
        "status": "pending",
        "rejection_reason": null,
        "seller_profile_id": 1,
        "created_at": "2025-01-19T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "seller_profile": {
          "id": 1,
          "business_name": "Traditional Wear Nigeria",
          "user": {
            "id": 2,
            "firstname": "Seller",
            "lastname": "User",
            "email": "seller@traditional.com"
          }
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 15,
      "total": 125
    }
  }
}
```

**Test Coverage:** ✅ Tested

---

### 11. Get Single Product (Admin View)
**GET** `/api/admin/products/{id}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "name": "Traditional Yoruba Agbada",
    "gender": "male",
    "style": "traditional",
    "tribe": "yoruba",
    "description": "Beautiful handcrafted Agbada with intricate embroidery",
    "image": "https://example.com/agbada.jpg",
    "size": "XL",
    "processing_time_type": "normal",
    "processing_days": 7,
    "price": 25000.00,
    "status": "pending",
    "rejection_reason": null,
    "seller_profile_id": 1,
    "created_at": "2025-01-19T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z",
    "seller_profile": {
      "id": 1,
      "business_name": "Traditional Wear Nigeria",
      "active": true,
      "user": {
        "id": 2,
        "firstname": "Seller",
        "lastname": "User",
        "email": "seller@traditional.com",
        "phone": "+2348012345678"
      }
    },
    "reviews": [
      {
        "id": 1,
        "user_id": 1,
        "rating": 5,
        "comment": "Excellent quality!",
        "created_at": "2025-01-20T16:00:00.000000Z"
      }
    ]
  }
}
```

**Test Coverage:** ✅ Tested

---

### 12. Approve Product
**PUT** `/api/admin/products/{id}/approve`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Product approved successfully",
  "data": {
    "id": 1,
    "name": "Traditional Yoruba Agbada",
    "status": "approved",
    "rejection_reason": null,
    "updated_at": "2025-01-20T17:30:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 13. Reject Product
**PUT** `/api/admin/products/{id}/reject`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "reason": "Images are not clear enough. Please upload higher quality photos."
}
```

**Validation Rules:**
- `reason`: required|string|max:500

**Response (200):**
```json
{
  "status": "success",
  "message": "Product rejected successfully",
  "data": {
    "id": 1,
    "name": "Traditional Yoruba Agbada",
    "status": "rejected",
    "rejection_reason": "Images are not clear enough. Please upload higher quality photos.",
    "updated_at": "2025-01-20T17:45:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

## Admin Seller Management

### 14. Get All Sellers (Admin View)
**GET** `/api/admin/sellers`
**Middleware:** `admin.auth`

**Query Parameters:**
- `active`: Filter by active status (optional)
- `page`: Page number for pagination

**Response (200):**
```json
{
  "status": "success",
  "message": "Sellers retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 2,
        "business_name": "Traditional Wear Nigeria",
        "business_email": "seller@traditional.com",
        "business_phone_number": "+2348012345678",
        "country": "Nigeria",
        "state": "Lagos",
        "city": "Lagos",
        "active": false,
        "rejection_reason": null,
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "user": {
          "id": 2,
          "firstname": "Seller",
          "lastname": "User",
          "email": "seller@traditional.com"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 15,
      "total": 42
    }
  }
}
```

**Test Coverage:** ✅ Tested

---

### 15. Get Single Seller (Admin View)
**GET** `/api/admin/sellers/{id}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Seller retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 2,
    "country": "Nigeria",
    "state": "Lagos",
    "city": "Lagos",
    "address": "789 Seller Street, Ikeja",
    "business_email": "seller@traditional.com",
    "business_phone_number": "+2348012345678",
    "instagram": "@traditionalwear",
    "facebook": "traditionalwear",
    "identity_document": "storage/docs/identity.pdf",
    "business_name": "Traditional Wear Nigeria",
    "business_registration_number": "RC123456",
    "business_certificate": "storage/certs/business_cert.pdf",
    "business_logo": "storage/logos/traditional_logo.jpg",
    "bank_name": "First Bank Nigeria",
    "account_number": "1234567890",
    "active": false,
    "rejection_reason": null,
    "created_at": "2025-01-15T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z",
    "user": {
      "id": 2,
      "firstname": "Seller",
      "lastname": "User",
      "email": "seller@traditional.com",
      "phone": "+2348012345678"
    },
    "products": [
      {
        "id": 1,
        "name": "Traditional Yoruba Agbada",
        "price": 25000.00,
        "status": "approved"
      }
    ]
  }
}
```

**Test Coverage:** ✅ Tested

---

### 16. Approve Seller
**PUT** `/api/admin/sellers/{id}/approve`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Seller approved successfully",
  "data": {
    "id": 1,
    "business_name": "Traditional Wear Nigeria",
    "active": true,
    "rejection_reason": null,
    "updated_at": "2025-01-20T18:00:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 17. Reject Seller
**PUT** `/api/admin/sellers/{id}/reject`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "reason": "Business registration certificate is not valid. Please upload a valid certificate."
}
```

**Validation Rules:**
- `reason`: required|string|max:500

**Response (200):**
```json
{
  "status": "success",
  "message": "Seller rejected successfully",
  "data": {
    "id": 1,
    "business_name": "Traditional Wear Nigeria",
    "active": false,
    "rejection_reason": "Business registration certificate is not valid. Please upload a valid certificate.",
    "updated_at": "2025-01-20T18:15:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

## Admin Business Management

### 18. Get All Business Profiles (Admin View)
**GET** `/api/admin/businesses`
**Middleware:** `admin.auth`

**Query Parameters:**
- `category`: Filter by business category (optional)
- `store_status`: Filter by store status (optional)
- `page`: Page number for pagination

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profiles retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "category": "beauty",
        "business_name": "Beauty Store Lagos",
        "business_email": "info@beautystore.com",
        "business_description": "Premium beauty products and services",
        "store_status": "pending",
        "subscription_status": null,
        "rejection_reason": null,
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "user": {
          "id": 1,
          "firstname": "John",
          "lastname": "Doe",
          "email": "john.doe@example.com"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 15,
      "total": 75
    }
  }
}
```

**Test Coverage:** ✅ Tested

---

### 19. Get Single Business Profile (Admin View)
**GET** `/api/admin/businesses/{id}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profile retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "category": "beauty",
    "country": "Nigeria",
    "state": "Lagos",
    "city": "Lagos",
    "address": "123 Beauty Street, Victoria Island",
    "business_email": "info@beautystore.com",
    "business_phone_number": "+2348012345678",
    "website_url": "https://beautystore.com",
    "instagram": "@beautystore",
    "facebook": "beautystore",
    "business_name": "Beauty Store Lagos",
    "business_description": "Premium beauty products and services",
    "business_logo": "storage/logos/beauty_logo.jpg",
    "offering_type": "selling_product",
    "product_list": "[\"Skincare\", \"Makeup\", \"Haircare\"]",
    "business_certificates": "[\"storage/certs/beauty_cert.pdf\"]",
    "store_status": "pending",
    "subscription_status": null,
    "subscription_ends_at": null,
    "rejection_reason": null,
    "created_at": "2025-01-15T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z",
    "user": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe",
      "email": "john.doe@example.com",
      "phone": "+2348012345678"
    }
  }
}
```

**Test Coverage:** ✅ Tested

---

### 20. Approve Business Profile
**PUT** `/api/admin/businesses/{id}/approve`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profile approved successfully",
  "data": {
    "id": 1,
    "business_name": "Beauty Store Lagos",
    "store_status": "approved",
    "rejection_reason": null,
    "updated_at": "2025-01-20T18:30:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 21. Reject Business Profile
**PUT** `/api/admin/businesses/{id}/reject`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "reason": "Business description does not clearly explain services offered. Please provide more details."
}
```

**Validation Rules:**
- `reason`: required|string|max:500

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profile rejected successfully",
  "data": {
    "id": 1,
    "business_name": "Beauty Store Lagos",
    "store_status": "rejected",
    "rejection_reason": "Business description does not clearly explain services offered. Please provide more details.",
    "updated_at": "2025-01-20T18:45:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

## Admin Authorization & Permissions

### Authentication Requirements:
- **Admin Token:** All admin endpoints require admin authentication token
- **Admin Abilities:** Admin tokens include `admin` ability scope
- **Super Admin:** Some operations may require super admin privileges

### Admin Token Headers:
```
Authorization: Bearer {admin_token}
Content-Type: application/json
Accept: application/json
```

## Admin Business Rules

1. **Order Management:** Admins can view all orders and update status with notifications
2. **User Management:** Full CRUD operations on user accounts including suspension
3. **Product Approval:** Admins must approve products before public visibility
4. **Seller Approval:** Seller profiles require admin approval before activation
5. **Business Approval:** Business profiles need admin approval for public listing
6. **Status Updates:** All status changes trigger notifications to affected users
7. **Audit Trail:** All admin actions are logged with timestamps and admin ID

## Status Management

### Order Status Flow:
- **pending** → **processing** → **shipped** → **delivered**
- **canceled** (can be set at any time with reason)
- **paid** (payment confirmed)

### Product Status:
- **pending** → **approved** (visible to public)
- **pending** → **rejected** (with reason, seller notified)

### Seller Status:
- **inactive** → **active** (approved seller)
- **inactive** → **rejected** (with reason, seller notified)

### Business Status:
- **pending** → **approved** (publicly visible)
- **pending** → **rejected** (with reason, user notified)
- **deactivated** (temporarily disabled)

## Test Coverage Summary

- **Dashboard & Overview:** ✅ 1/1 endpoints tested (100%)
- **User Management:** ✅ 6/6 endpoints tested (100%)
- **Order Management:** ✅ 3/3 endpoints tested (100%)
- **Product Management:** ✅ 4/4 endpoints tested (100%)
- **Seller Management:** ✅ 4/4 endpoints tested (100%)
- **Business Management:** ✅ 4/4 endpoints tested (100%)

**Total Admin Panel:** ✅ 22/22 endpoints tested (100%)

---
*Last Updated: January 2025*