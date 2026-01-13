# New Public Endpoints Documentation

## Overview
These endpoints were added to enable public browsing and fix critical mobile app issues.

**Date Added:** January 2024  
**Total New Endpoints:** 11

---

## 🌐 Public Business Profiles

### 1. List All Approved Businesses (Public)
**Endpoint:** `GET /api/business/public`  
**Middleware:** None (Public)  
**Description:** Browse all approved business profiles

#### Query Parameters
```
category: string (optional) - Filter by beauty|brand|school|music
offering_type: string (optional) - Filter by selling_product|providing_service
per_page: integer (optional, default: 15, max: 50)
page: integer (optional, default: 1)
```

#### Request Example
```bash
GET /api/business/public?category=beauty&per_page=20
```

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Public business profiles retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "business_name": "Glam Beauty Studio",
        "category": "beauty",
        "offering_type": "providing_service",
        "business_description": "Professional beauty services",
        "business_logo": "https://...",
        "business_email": "beauty@example.com",
        "business_phone_number": "+2348012345678",
        "store_status": "approved",
        "user": {
          "id": 1,
          "firstname": "Jane",
          "lastname": "Doe"
        }
      }
    ],
    "links": {...},
    "meta": {
      "current_page": 1,
      "per_page": 15,
      "total": 45
    }
  }
}
```

---

### 2. View Single Business Profile (Public)
**Endpoint:** `GET /api/business/public/{id}`  
**Middleware:** None (Public)  
**Description:** View detailed information about an approved business

#### URL Parameters
- `id`: Business Profile ID (integer)

#### Request Example
```bash
GET /api/business/public/1
```

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Business profile retrieved successfully",
  "data": {
    "id": 1,
    "business_name": "Glam Beauty Studio",
    "category": "beauty",
    "business_description": "...",
    "business_logo": "...",
    "store_status": "approved",
    "user": {
      "id": 1,
      "firstname": "Jane",
      "lastname": "Doe"
    }
  }
}
```

#### Error Response (404)
```json
{
  "message": "No query results for model [App\\Models\\BusinessProfile] 1"
}
```

**Note:** Only approved businesses can be viewed publicly.

---

## 🛍️ Public Product Browsing

### 3. Browse Products (Public)
**Endpoint:** `GET /api/products/browse`  
**Middleware:** None (Public)  
**Description:** Search, filter, and sort approved products

#### Query Parameters
```
q: string (optional) - Search in name and description
gender: string (optional) - Filter by male|female|unisex
style: string (optional) - Filter by style
tribe: string (optional) - Filter by tribe
price_min: decimal (optional) - Minimum price
price_max: decimal (optional) - Maximum price
sort: string (optional) - Sort by price_asc|price_desc|newest|popular
per_page: integer (optional, default: 10, max: 50)
page: integer (optional, default: 1)
```

#### Request Example
```bash
GET /api/products/browse?q=ankara&gender=female&sort=price_asc&per_page=20
```

#### Success Response (200)
```json
{
  "status": "success",
  "data": {
    "data": [
      {
        "id": 3,
        "name": "Ankara Print Dress",
        "description": "Beautiful Ankara dress",
        "gender": "female",
        "style": "Modern",
        "tribe": "Yoruba",
        "price": 18000.00,
        "image": "https://...",
        "status": "approved",
        "seller_profile": {
          "id": 2,
          "business_name": "Fashion House"
        }
      }
    ],
    "links": {...},
    "meta": {
      "current_page": 1,
      "per_page": 10,
      "total": 156
    }
  }
}
```

#### Business Logic
- Only shows approved products
- Search is case-insensitive
- Supports multiple filters simultaneously
- Sort options:
  - `newest`: Most recent first (default)
  - `price_asc`: Lowest price first
  - `price_desc`: Highest price first
  - `popular`: Most reviewed first

---

### 4. Get Product Filters Metadata (Public)
**Endpoint:** `GET /api/products/filters`  
**Middleware:** None (Public)  
**Description:** Get all available filter values for UI dropdowns

#### Request Example
```bash
GET /api/products/filters
```

#### Success Response (200)
```json
{
  "status": "success",
  "data": {
    "genders": ["male", "female", "unisex"],
    "styles": ["Traditional", "Modern", "Casual", "Formal"],
    "tribes": ["Yoruba", "Igbo", "Hausa", "Mixed"],
    "price_range": {
      "min": 5000.00,
      "max": 150000.00
    },
    "sort_options": [
      {"value": "newest", "label": "Newest First"},
      {"value": "price_asc", "label": "Price: Low to High"},
      {"value": "price_desc", "label": "Price: High to Low"},
      {"value": "popular", "label": "Most Popular"}
    ]
  }
}
```

#### Use Case
Frontend uses this to populate:
- Gender filter dropdown
- Style filter dropdown
- Tribe filter dropdown
- Price range slider min/max
- Sort dropdown options

---

### 5. View Single Product (Public)
**Endpoint:** `GET /api/products/public/{id}`  
**Middleware:** None (Public)  
**Description:** View detailed product information with suggestions

#### URL Parameters
- `id`: Product ID (integer)

#### Request Example
```bash
GET /api/products/public/3
```

#### Success Response (200)
```json
{
  "status": "success",
  "data": {
    "product": {
      "id": 3,
      "name": "Ankara Print Dress",
      "description": "Beautiful Ankara dress...",
      "gender": "female",
      "style": "Modern",
      "tribe": "Yoruba",
      "price": 18000.00,
      "image": "https://...",
      "size": "S, M, L, XL",
      "processing_time_type": "normal",
      "processing_days": 7,
      "status": "approved",
      "seller_profile": {
        "id": 2,
        "business_name": "Fashion House",
        "business_email": "fashion@example.com",
        "city": "Lagos",
        "state": "Lagos"
      },
      "reviews": [
        {
          "id": 1,
          "rating": 5,
          "headline": "Excellent!",
          "body": "Love it!",
          "user": {
            "id": 5,
            "firstname": "Mary",
            "lastname": "Jane"
          }
        }
      ]
    },
    "suggestions": [
      {
        "id": 5,
        "name": "Similar Ankara Style",
        "price": 20000.00,
        "image": "https://...",
        "seller_profile": {
          "id": 2,
          "business_name": "Fashion House"
        }
      }
    ]
  }
}
```

#### Business Logic
- Only shows approved products
- Includes seller information
- Includes all product reviews
- Provides 5 related product suggestions based on style/tribe/gender
- Suggestions are randomized

---

## 🌱 Public Sustainability Initiatives

### 6. List Sustainability Initiatives (Public)
**Endpoint:** `GET /api/sustainability`  
**Middleware:** None (Public)  
**Description:** List all active sustainability initiatives

#### Query Parameters
```
category: string (optional) - Filter by environmental|social|economic|governance
per_page: integer (optional, default: 10, max: 50)
page: integer (optional, default: 1)
```

#### Request Example
```bash
GET /api/sustainability?category=environmental
```

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Sustainability initiatives retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Zero Waste Fashion Initiative",
        "description": "Promoting sustainable fashion practices...",
        "image_url": "https://...",
        "category": "environmental",
        "status": "active",
        "target_amount": 1000000.00,
        "current_amount": 350000.00,
        "progress_percentage": 35.00,
        "impact_metrics": "200 artisans trained",
        "start_date": "2024-01-01",
        "end_date": "2024-12-31",
        "partners": ["NGO Partner", "Government"],
        "participant_count": 150,
        "progress_notes": "Great progress...",
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      }
    ],
    "links": {...},
    "meta": {...}
  }
}
```

#### Categories
- `environmental`: Eco-friendly, waste reduction
- `social`: Community development, social impact
- `economic`: Fair trade, economic sustainability
- `governance`: Transparency, ethics

---

### 7. View Single Initiative (Public)
**Endpoint:** `GET /api/sustainability/{id}`  
**Middleware:** None (Public)  
**Description:** View detailed initiative information

#### URL Parameters
- `id`: Initiative ID (integer)

#### Request Example
```bash
GET /api/sustainability/1
```

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Sustainability initiative retrieved successfully",
  "data": {
    "id": 1,
    "title": "Zero Waste Fashion Initiative",
    "description": "...",
    "category": "environmental",
    "status": "active",
    "target_amount": 1000000.00,
    "current_amount": 350000.00,
    "progress_percentage": 35.00,
    "partners": ["NGO", "Government"],
    "participant_count": 150
  }
}
```

---

## 📢 Public Advertisements

### 8. List Active Adverts (Public)
**Endpoint:** `GET /api/adverts`  
**Middleware:** None (Public)  
**Description:** Get all active advertisements

#### Query Parameters
```
position: string (optional) - Filter by banner|sidebar|footer|popup
per_page: integer (optional, default: 10, max: 50)
page: integer (optional, default: 1)
```

#### Request Example
```bash
GET /api/adverts?position=banner
```

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Adverts retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Summer Sale",
        "description": "50% off all items",
        "image_url": "https://...",
        "action_url": "https://...",
        "position": "banner",
        "status": "active",
        "priority": 10,
        "start_date": "2024-01-01",
        "end_date": "2024-12-31"
      }
    ],
    "links": {...},
    "meta": {...}
  }
}
```

#### Business Logic
- Only shows active adverts
- Respects start_date and end_date
- Ordered by priority (highest first)
- Positions: banner, sidebar, footer, popup

---

### 9. View Single Advert (Public)
**Endpoint:** `GET /api/adverts/{id}`  
**Middleware:** None (Public)  
**Description:** View single advertisement details

#### URL Parameters
- `id`: Advert ID (integer)

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Advert retrieved successfully",
  "data": {
    "id": 1,
    "title": "Summer Sale",
    "description": "50% off all items",
    "image_url": "https://...",
    "action_url": "https://...",
    "position": "banner",
    "priority": 10
  }
}
```

---

## 🔐 User Logout

### 10. User Logout
**Endpoint:** `POST /api/logout`  
**Middleware:** `auth:sanctum` (Authenticated)  
**Description:** Logout user and revoke current access token

#### Headers
```
Authorization: Bearer {token}
```

#### Request Example
```bash
POST /api/logout
Authorization: Bearer 1|aBcDeFgHiJkLmNoPqRsTuVwXyZ
```

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Logged out successfully"
}
```

#### Business Logic
- Deletes only the current access token
- Other active sessions remain valid
- User must login again to get new token

---

## 📦 Order Cancellation

### 11. Cancel Order
**Endpoint:** `POST /api/orders/{id}/cancel`  
**Middleware:** `auth:sanctum` (Authenticated)  
**Description:** Cancel a pending or processing order

#### Headers
```
Authorization: Bearer {token}
```

#### URL Parameters
- `id`: Order ID (integer)

#### Request Body
```json
{
  "cancellation_reason": "string (required, max: 500)"
}
```

#### Request Example
```bash
POST /api/orders/5/cancel
Authorization: Bearer {token}
Content-Type: application/json

{
  "cancellation_reason": "Changed my mind about the purchase"
}
```

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Order cancelled successfully",
  "data": {
    "id": 5,
    "status": "cancelled",
    "cancellation_reason": "Changed my mind about the purchase",
    "total_price": 45000.00,
    "updated_at": "2024-01-20T10:30:00Z"
  }
}
```

#### Error Response (400)
```json
{
  "status": "error",
  "message": "Cannot cancel order with status 'shipped'. Only pending or processing orders can be cancelled."
}
```

#### Business Rules
- Only order owner can cancel
- Can only cancel orders with status: `pending` or `processing`
- Cannot cancel: `shipped`, `delivered`, or already `cancelled` orders
- Sends email and push notification to user
- Cancellation reason is required and stored

---

## 📊 Summary

### By Access Level

**Public (No Auth Required):** 9 endpoints
- 2 Business profile endpoints
- 3 Product endpoints  
- 2 Sustainability endpoints
- 2 Advert endpoints

**Authenticated:** 2 endpoints
- 1 Logout endpoint
- 1 Order cancellation endpoint

### By Category

- **Business Profiles:** 2 endpoints
- **Products:** 3 endpoints
- **Sustainability:** 2 endpoints
- **Adverts:** 2 endpoints
- **User Account:** 1 endpoint
- **Orders:** 1 endpoint

---

## 🔧 Common Patterns

### Pagination
All list endpoints support pagination:
```json
{
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 50
  }
}
```

### Error Responses
```json
{
  "message": "Error message here"
}
```

Common status codes:
- `200`: Success
- `400`: Bad request / Business rule violation
- `401`: Unauthorized (missing or invalid token)
- `404`: Resource not found
- `422`: Validation error

---

**Created:** January 2024  
**Status:** Production Ready  
**Total New Endpoints:** 11
