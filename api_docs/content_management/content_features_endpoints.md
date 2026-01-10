# Content Management & Features Endpoints

## Blog Management

### 1. Get All Published Blogs
**GET** `/api/blogs`
**Middleware:** None (Public endpoint)

**Query Parameters:**
- `page`: Page number for pagination

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "The Future of African Fashion",
        "slug": "future-of-african-fashion",
        "body": "<p>Exploring the evolution of traditional African wear...</p>",
        "excerpt": "A look into how traditional African fashion is evolving",
        "category": "fashion",
        "featured_image": "https://example.com/images/african-fashion.jpg",
        "is_published": true,
        "published_at": "2025-01-20T10:00:00.000000Z",
        "reading_time": 5,
        "admin_id": 1,
        "created_at": "2025-01-19T15:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      },
      {
        "id": 2,
        "title": "Sustainable Fashion Practices",
        "slug": "sustainable-fashion-practices",
        "body": "<p>How Nigerian designers are embracing sustainability...</p>",
        "excerpt": "Eco-friendly approaches in Nigerian fashion",
        "category": "sustainability",
        "featured_image": "https://example.com/images/sustainable.jpg",
        "is_published": true,
        "published_at": "2025-01-19T14:00:00.000000Z",
        "reading_time": 3,
        "admin_id": 1,
        "admin": {
          "id": 1,
          "firstname": "Admin", 
          "lastname": "User"
        }
      }
    ],
    "first_page_url": "http://localhost/api/blogs?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 2
  }
}
```

**Test Coverage:** ✅ Tested

---

### 2. Get Single Blog by Slug
**GET** `/api/blogs/{slug}`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "blog": {
      "id": 1,
      "title": "The Future of African Fashion",
      "slug": "future-of-african-fashion",
      "body": "<p>Exploring the evolution of traditional African wear in modern times. From ancient tribal patterns to contemporary runway shows, African fashion continues to influence global trends.</p><p>Nigerian designers are at the forefront of this movement, blending traditional craftsmanship with modern aesthetics...</p>",
      "excerpt": "A look into how traditional African fashion is evolving",
      "category": "fashion",
      "featured_image": "https://example.com/images/african-fashion.jpg",
      "is_published": true,
      "published_at": "2025-01-20T10:00:00.000000Z",
      "reading_time": 5,
      "admin_id": 1,
      "created_at": "2025-01-19T15:00:00.000000Z",
      "updated_at": "2025-01-20T10:00:00.000000Z",
      "admin": {
        "id": 1,
        "firstname": "Admin",
        "lastname": "User"
      }
    },
    "related_posts": [
      {
        "id": 3,
        "title": "Traditional Nigerian Textiles",
        "slug": "traditional-nigerian-textiles",
        "excerpt": "Exploring the rich heritage of Nigerian textile arts",
        "category": "fashion",
        "featured_image": "https://example.com/images/textiles.jpg",
        "published_at": "2025-01-18T12:00:00.000000Z",
        "reading_time": 4,
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      }
    ]
  }
}
```

**Error Response (404):**
```json
{
  "status": "error",
  "message": "Blog post not found"
}
```

**Test Coverage:** ✅ Tested

---

### 3. Get Latest Blog Posts
**GET** `/api/blogs/latest`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "The Future of African Fashion",
      "slug": "future-of-african-fashion",
      "excerpt": "A look into how traditional African fashion is evolving",
      "category": "fashion",
      "featured_image": "https://example.com/images/african-fashion.jpg",
      "published_at": "2025-01-20T10:00:00.000000Z",
      "reading_time": 5,
      "admin": {
        "id": 1,
        "firstname": "Admin",
        "lastname": "User"
      }
    }
  ]
}
```

**Test Coverage:** 🔴 Not Tested

---

### 4. Search Blog Posts
**GET** `/api/blogs/search`
**Middleware:** None (Public endpoint)

**Query Parameters:**
```
GET /api/blogs/search?query=fashion&page=1
```

**Validation Rules:**
- `query`: required|string|min:3

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "The Future of African Fashion",
        "slug": "future-of-african-fashion",
        "body": "<p>Exploring the evolution of traditional African wear...</p>",
        "excerpt": "A look into how traditional African fashion is evolving",
        "category": "fashion",
        "featured_image": "https://example.com/images/african-fashion.jpg",
        "published_at": "2025-01-20T10:00:00.000000Z",
        "reading_time": 5,
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      }
    ],
    "per_page": 10,
    "total": 1
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Blog Favorites

### 5. Get User's Favorite Blogs
**GET** `/api/blogs/favorites`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Favorite blogs retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "The Future of African Fashion",
        "slug": "future-of-african-fashion",
        "body": "<p>Exploring the evolution of traditional African wear...</p>",
        "excerpt": "A look into how traditional African fashion is evolving",
        "category": "fashion",
        "featured_image": "https://example.com/images/african-fashion.jpg",
        "published_at": "2025-01-20T10:00:00.000000Z",
        "reading_time": 5,
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      }
    ],
    "per_page": 10,
    "total": 1
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 6. Add Blog to Favorites
**POST** `/api/blogs/favorites`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "blog_id": 1
}
```

**Validation Rules:**
- `blog_id`: required|integer|exists:blogs,id

**Response (201):**
```json
{
  "status": "success",
  "message": "Blog added to favorites successfully"
}
```

**Error Response (400) - Already Favorited:**
```json
{
  "status": "error",
  "message": "Blog is already in your favorites"
}
```

**Test Coverage:** 🔴 Not Tested

---

### 7. Remove Blog from Favorites
**DELETE** `/api/blogs/favorites`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "blog_id": 1
}
```

**Validation Rules:**
- `blog_id`: required|integer|exists:blogs,id

**Response (200):**
```json
{
  "status": "success",
  "message": "Blog removed from favorites successfully"
}
```

**Error Response (404) - Not in Favorites:**
```json
{
  "status": "error",
  "message": "Blog is not in your favorites"
}
```

**Test Coverage:** 🔴 Not Tested

---

## FAQ Management

### 8. Get All FAQs
**GET** `/api/faqs`
**Middleware:** None (Public endpoint)

**Query Parameters:**
- `category`: Filter by FAQ category (optional)
- `page`: Page number for pagination

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "question": "How do I create a seller account?",
        "answer": "To create a seller account, navigate to the seller registration page and fill out the required information including business details, contact information, and upload necessary documents for verification.",
        "category": "selling",
        "order": 1,
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z"
      },
      {
        "id": 2,
        "question": "What payment methods do you accept?",
        "answer": "We accept various payment methods including bank transfers, card payments through Paystack, and mobile money options. All payments are processed securely.",
        "category": "payments",
        "order": 2,
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 2
  }
}
```

**Test Coverage:** ✅ Tested

---

### 9. Get FAQ Categories
**GET** `/api/faqs/categories`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": [
    "general",
    "selling",
    "buying",
    "payments",
    "shipping",
    "returns",
    "account",
    "technical"
  ]
}
```

**Test Coverage:** 🔴 Not Tested

---

### 10. Search FAQs
**GET** `/api/faqs/search`
**Middleware:** None (Public endpoint)

**Query Parameters:**
```
GET /api/faqs/search?query=payment&page=1
```

**Validation Rules:**
- `query`: required|string|min:3

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 2,
        "question": "What payment methods do you accept?",
        "answer": "We accept various payment methods including bank transfers, card payments through Paystack, and mobile money options. All payments are processed securely.",
        "category": "payments",
        "order": 2,
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-15T10:00:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 1
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 11. Get Single FAQ
**GET** `/api/faqs/{id}`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "question": "How do I create a seller account?",
    "answer": "To create a seller account, navigate to the seller registration page and fill out the required information including business details, contact information, and upload necessary documents for verification.",
    "category": "selling",
    "order": 1,
    "created_at": "2025-01-15T10:00:00.000000Z",
    "updated_at": "2025-01-15T10:00:00.000000Z"
  }
}
```

**Error Response (404):**
```json
{
  "status": "error",
  "message": "FAQ not found"
}
```

**Test Coverage:** ✅ Tested

---

## Wishlist Management

### 12. Get User's Wishlist
**GET** `/api/wishlist`
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
        "id": 1,
        "user_id": 1,
        "wishlistable_type": "App\\Models\\Product",
        "wishlistable_id": 1,
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "wishlistable": {
          "id": 1,
          "name": "Traditional Yoruba Agbada",
          "price": 25000.00,
          "image": "https://example.com/agbada.jpg",
          "gender": "male",
          "style": "traditional",
          "tribe": "yoruba"
        }
      },
      {
        "id": 2,
        "user_id": 1,
        "wishlistable_type": "App\\Models\\BusinessProfile",
        "wishlistable_id": 1,
        "created_at": "2025-01-20T11:00:00.000000Z",
        "updated_at": "2025-01-20T11:00:00.000000Z",
        "wishlistable": {
          "id": 1,
          "business_name": "Beauty Store Lagos",
          "category": "beauty",
          "business_description": "Premium beauty products and services",
          "business_logo": "https://example.com/beauty_logo.jpg"
        }
      }
    ],
    "per_page": 20,
    "total": 2
  }
}
```

**Test Coverage:** ✅ Tested

---

### 13. Add Item to Wishlist
**POST** `/api/wishlist`
**Middleware:** `auth:sanctum`

**Request - Add Product:**
```json
{
  "wishlistable_type": "product",
  "wishlistable_id": 1
}
```

**Request - Add Business:**
```json
{
  "wishlistable_type": "business_profile",
  "wishlistable_id": 2
}
```

**Validation Rules:**
- `wishlistable_type`: required|string|in:product,business_profile
- `wishlistable_id`: required|integer

**Response (201):**
```json
{
  "status": "success",
  "message": "Item added to wishlist",
  "data": {
    "id": 3,
    "user_id": 1,
    "wishlistable_type": "App\\Models\\Product",
    "wishlistable_id": 1,
    "created_at": "2025-01-20T12:00:00.000000Z",
    "updated_at": "2025-01-20T12:00:00.000000Z",
    "wishlistable": {
      "id": 1,
      "name": "Traditional Yoruba Agbada",
      "price": 25000.00
    }
  }
}
```

**Error Response (404) - Item Not Found:**
```json
{
  "status": "error",
  "message": "Item not found"
}
```

**Error Response (409) - Already in Wishlist:**
```json
{
  "status": "error",
  "message": "Item already in wishlist"
}
```

**Test Coverage:** ✅ Tested

---

### 14. Remove Item from Wishlist
**DELETE** `/api/wishlist`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "wishlistable_type": "product",
  "wishlistable_id": 1
}
```

**Validation Rules:**
- `wishlistable_type`: required|string|in:product,business_profile
- `wishlistable_id`: required|integer

**Response (200):**
```json
{
  "status": "success",
  "message": "Item removed from wishlist"
}
```

**Error Response (404) - Not in Wishlist:**
```json
{
  "status": "error",
  "message": "Item not found in wishlist"
}
```

**Test Coverage:** ✅ Tested

---


## Subscription Management

### 21. Update Business Subscription
**PUT** `/api/subscriptions`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "business_id": 1,
  "subscription_type": "premium",
  "billing_cycle": "yearly"
}
```

**Validation Rules:**
- `business_id`: required|integer|exists:business_profiles,id
- `subscription_type`: required|in:basic,premium,enterprise
- `billing_cycle`: required|in:monthly,quarterly,yearly

**Response (200):**
```json
{
  "status": "success",
  "message": "Business subscription updated successfully",
  "data": {
    "business_id": 1,
    "subscription_type": "premium",
    "subscription_status": "active",
    "billing_cycle": "yearly",
    "amount": 96000,
    "currency": "NGN",
    "next_billing_date": "2026-01-20",
    "features": {
      "max_products": 200,
      "max_photos_per_product": 10,
      "analytics_access": true,
      "priority_support": true,
      "advanced_promotion": true
    }
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Subscription Pricing (NGN)

### Basic Plan:
- **Monthly:** ₦5,000
- **Quarterly:** ₦13,500 (10% discount)
- **Yearly:** ₦48,000 (20% discount)

### Premium Plan:
- **Monthly:** ₦10,000
- **Quarterly:** ₦27,000 (10% discount)
- **Yearly:** ₦96,000 (20% discount)

### Enterprise Plan:
- **Monthly:** ₦20,000
- **Quarterly:** ₦54,000 (10% discount)
- **Yearly:** ₦192,000 (20% discount)

## Content Categories

### Blog Categories:
- fashion
- beauty
- sustainability
- culture
- business
- technology

### FAQ Categories:
- general
- selling
- buying
- payments
- shipping
- returns
- account
- technical

## Test Coverage Summary

- **Blog Management:** ✅ 2/4 endpoints tested (50%)
- **Blog Favorites:** 🔴 0/3 endpoints tested (0%)
- **FAQ Management:** ✅ 2/4 endpoints tested (50%)
- **Wishlist Management:** ✅ 3/3 endpoints tested (100%)
- **Notification Management:** ✅ 4/6 endpoints tested (67%)
- **Subscription Management:** 🔴 0/1 endpoints tested (0%)

**Total Content & Features:** ✅ 11/21 endpoints tested (52%)

---
*Last Updated: January 2025*