# Product Management Endpoints

## Product CRUD Operations

### 1. Get User's Products (Seller View)
**GET** `/api/products`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200) - With Seller Profile:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Traditional Yoruba Agbada",
      "gender": "male",
      "style": "traditional",
      "tribe": "yoruba",
      "description": "Beautiful handcrafted Agbada with intricate embroidery",
      "image": "https://example.com/images/agbada1.jpg",
      "size": "XL",
      "processing_time_type": "normal",
      "processing_days": 7,
      "price": 25000.00,
      "status": "approved",
      "seller_profile_id": 1,
      "rejection_reason": null,
      "created_at": "2025-01-19T10:00:00.000000Z",
      "updated_at": "2025-01-20T10:00:00.000000Z"
    }
  ],
  "first_page_url": "http://localhost/api/products?page=1",
  "from": 1,
  "last_page": 1,
  "last_page_url": "http://localhost/api/products?page=1",
  "links": [...],
  "next_page_url": null,
  "path": "http://localhost/api/products",
  "per_page": 10,
  "prev_page_url": null,
  "to": 1,
  "total": 1
}
```

**Error Response (403) - No Seller Profile:**
```json
{
  "message": "You must have a seller profile to view products"
}
```

**Test Coverage:** ✅ Tested

---

### 2. Create New Product
**POST** `/api/products`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "name": "Traditional Igbo Chieftancy Outfit",
  "gender": "male",
  "style": "traditional",
  "tribe": "igbo",
  "description": "Handwoven chieftancy outfit with traditional patterns",
  "image": "https://example.com/images/chieftancy.jpg",
  "size": "L",
  "processing_time_type": "normal",
  "processing_days": 10,
  "price": 35000.00
}
```

**Validation Rules:**
- `name`: required|string|max:255
- `gender`: required|in:male,female,unisex
- `style`: required|string|max:100
- `tribe`: required|string|max:100
- `description`: required|string|max:1000
- `image`: nullable|string|max:2000 (URL)
- `size`: required|string|max:50
- `processing_time_type`: required|in:normal,quick_quick
- `processing_days`: required|integer|min:1|max:30
- `price`: required|numeric|min:0.01

**Response (201):**
```json
{
  "message": "Product created successfully",
  "product": {
    "id": 2,
    "name": "Traditional Igbo Chieftancy Outfit",
    "gender": "male",
    "style": "traditional",
    "tribe": "igbo",
    "description": "Handwoven chieftancy outfit with traditional patterns",
    "image": "https://example.com/images/chieftancy.jpg",
    "size": "L",
    "processing_time_type": "normal",
    "processing_days": 10,
    "price": 35000.00,
    "status": "pending",
    "seller_profile_id": 1,
    "rejection_reason": null,
    "created_at": "2025-01-20T12:00:00.000000Z",
    "updated_at": "2025-01-20T12:00:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 3. Get Single Product (Public View)
**GET** `/api/products/{id}`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "id": 1,
  "name": "Traditional Yoruba Agbada",
  "gender": "male",
  "style": "traditional",
  "tribe": "yoruba",
  "description": "Beautiful handcrafted Agbada with intricate embroidery",
  "image": "https://example.com/images/agbada1.jpg",
  "size": "XL",
  "processing_time_type": "normal",
  "processing_days": 7,
  "price": 25000.00,
  "status": "approved",
  "seller_profile_id": 1,
  "rejection_reason": null,
  "created_at": "2025-01-19T10:00:00.000000Z",
  "updated_at": "2025-01-20T10:00:00.000000Z",
  "seller_profile": {
    "id": 1,
    "user_id": 1,
    "business_name": "Yoruba Traditions Ltd",
    "bio": "Authentic traditional wear specialists",
    "active": true
  },
  "reviews": [
    {
      "id": 1,
      "user_id": 2,
      "product_id": 1,
      "rating": 5,
      "comment": "Excellent quality and fast delivery!",
      "created_at": "2025-01-20T09:00:00.000000Z"
    }
  ],
  "suggestions": [
    {
      "id": 3,
      "name": "Modern Yoruba Dashiki",
      "gender": "male",
      "style": "modern",
      "tribe": "yoruba",
      "price": 15000.00,
      "image": "https://example.com/images/dashiki.jpg",
      "seller_profile": {
        "id": 2,
        "business_name": "Modern African Wear"
      }
    }
  ]
}
```

**Test Coverage:** ✅ Tested

---

### 4. Update Product
**PUT** `/api/products/{id}`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "name": "Updated Traditional Yoruba Agbada",
  "price": 27000.00,
  "description": "Updated description with new features",
  "processing_days": 5
}
```

**Validation Rules:**
- `name`: sometimes|string|max:255
- `gender`: sometimes|in:male,female,unisex
- `style`: sometimes|string|max:100
- `tribe`: sometimes|string|max:100
- `description`: sometimes|string|max:1000
- `image`: nullable|string|max:2000
- `size`: sometimes|string|max:50
- `processing_time_type`: sometimes|in:normal,quick_quick
- `processing_days`: sometimes|integer|min:1|max:30
- `price`: sometimes|numeric|min:0.01
- `status`: sometimes|in:pending,approved,rejected

**Response (200):**
```json
{
  "message": "Product updated successfully",
  "product": {
    "id": 1,
    "name": "Updated Traditional Yoruba Agbada",
    "gender": "male",
    "style": "traditional",
    "tribe": "yoruba",
    "description": "Updated description with new features",
    "image": "https://example.com/images/agbada1.jpg",
    "size": "XL",
    "processing_time_type": "normal",
    "processing_days": 5,
    "price": 27000.00,
    "status": "approved",
    "seller_profile_id": 1,
    "rejection_reason": null,
    "created_at": "2025-01-19T10:00:00.000000Z",
    "updated_at": "2025-01-20T12:30:00.000000Z"
  }
}
```

**Error Response (403) - Unauthorized:**
```json
{
  "message": "This action is unauthorized."
}
```

**Test Coverage:** ✅ Tested

---

### 5. Delete Product
**DELETE** `/api/products/{id}`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "message": "Product deleted successfully"
}
```

**Error Response (403) - Unauthorized:**
```json
{
  "message": "Unauthorized to delete this product"
}
```

**Test Coverage:** ✅ Tested

---

## Product Discovery & Search

### 6. Product Search
**GET** `/api/products/search`
**Middleware:** None (Public endpoint)

**Query Parameters:**
```
GET /api/products/search?q=agbada&gender=male&style=traditional&tribe=yoruba&price_min=10000&price_max=50000&per_page=15
```

**Validation Rules:**
- `q`: required|string|min:1|max:255 (search term)
- `gender`: sometimes|in:male,female,unisex
- `style`: sometimes|string|max:100
- `tribe`: sometimes|string|max:100
- `price_min`: sometimes|numeric|min:0
- `price_max`: sometimes|numeric|min:0
- `per_page`: sometimes|integer|min:1|max:50

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Traditional Yoruba Agbada",
        "gender": "male",
        "style": "traditional",
        "tribe": "yoruba",
        "description": "Beautiful handcrafted Agbada with intricate embroidery",
        "price": 25000.00,
        "image": "https://example.com/images/agbada1.jpg",
        "seller_profile": {
          "id": 1,
          "business_name": "Yoruba Traditions Ltd"
        }
      }
    ],
    "first_page_url": "http://localhost/api/products/search?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 7. Product Suggestions
**GET** `/api/products/suggestions`
**Middleware:** None (Public endpoint)

**Query Parameters:**
```
GET /api/products/suggestions?gender=female&tribe=hausa&style=modern&price_min=5000&price_max=30000&limit=10
```

**Validation Rules:**
- `gender`: sometimes|in:male,female,unisex
- `tribe`: sometimes|string|max:100
- `style`: sometimes|string|max:100
- `price_min`: sometimes|numeric|min:0
- `price_max`: sometimes|numeric|min:0
- `limit`: sometimes|integer|min:1|max:20

**Response (200):**
```json
[
  {
    "id": 4,
    "name": "Modern Hausa Kaftan",
    "gender": "female",
    "style": "modern",
    "tribe": "hausa",
    "price": 18000.00,
    "image": "https://example.com/images/kaftan.jpg",
    "reviews_count": 12,
    "avg_rating": 4.5,
    "reviews": [
      {
        "id": 5,
        "rating": 5,
        "comment": "Love this design!"
      }
    ]
  }
]
```

**Test Coverage:** 🔴 Not Tested

---

## Category Management

### 8. Get Categories by Type
**GET** `/api/categories`
**Middleware:** None (Public endpoint)

**Query Parameters:**
```
GET /api/categories?type=market
```

**Validation Rules:**
- `type`: required|in:market,beauty,brand,school,sustainability,music

**Response (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Traditional Wear",
      "slug": "traditional-wear",
      "type": "market",
      "parent_id": null,
      "order": 1,
      "children": [
        {
          "id": 2,
          "name": "Men's Traditional",
          "slug": "mens-traditional",
          "type": "market",
          "parent_id": 1,
          "order": 1
        },
        {
          "id": 3,
          "name": "Women's Traditional",
          "slug": "womens-traditional",
          "type": "market",
          "parent_id": 1,
          "order": 2
        }
      ]
    }
  ]
}
```

**Test Coverage:** ✅ Tested

---

### 9. Get Category Children
**GET** `/api/categories/{id}/children`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "parent": {
      "id": 1,
      "name": "Traditional Wear",
      "slug": "traditional-wear",
      "type": "market",
      "parent_id": null,
      "order": 1
    },
    "children": [
      {
        "id": 2,
        "name": "Men's Traditional",
        "slug": "mens-traditional",
        "type": "market",
        "parent_id": 1,
        "order": 1
      },
      {
        "id": 3,
        "name": "Women's Traditional", 
        "slug": "womens-traditional",
        "type": "market",
        "parent_id": 1,
        "order": 2
      }
    ]
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 10. Get Category Items
**GET** `/api/categories/{type}/{slug}/items`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "category": {
      "id": 2,
      "name": "Men's Traditional",
      "slug": "mens-traditional",
      "type": "market",
      "parent_id": 1,
      "order": 1
    },
    "items": [1, 3, 5, 8, 12]
  }
}
```

**Error Response (400) - Invalid Type:**
```json
{
  "status": "error",
  "message": "Invalid category type"
}
```

**Test Coverage:** 🔴 Not Tested

---

## Product Status & Authorization

### Product Status Flow:
1. **pending** - Default status when product is created
2. **approved** - Admin approved the product (visible in public listings)
3. **rejected** - Admin rejected the product (includes rejection_reason)

### Authorization Rules:
1. **Create Product**: Requires seller profile
2. **View Own Products**: Must be product owner (seller)
3. **Update Product**: Must be product owner (seller)
4. **Delete Product**: Must be product owner (seller)
5. **Public Views**: No authentication required for approved products

## Test Coverage Summary

- **Product CRUD:** ✅ 5/5 endpoints tested (100%)
- **Product Search & Discovery:** 🔴 0/2 endpoints tested (0%)
- **Category Management:** 🔴 1/3 endpoints tested (33%)

**Total Product Management:** ✅ 6/10 endpoints tested (60%)

---
*Last Updated: January 2025*