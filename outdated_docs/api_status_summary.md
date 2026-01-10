# Oja Ewa Pro API Status Summary

This document provides a summary of the current status of the Oja Ewa Pro API, including which endpoints are working, which are not, and which have not been tested.

## 📋 Table of Contents
- [User Endpoints](#user-endpoints)
- [Seller Endpoints](#seller-endpoints)
- [Admin Endpoints](#admin-endpoints)
- [Implementation Summary](#implementation-summary)

---

# User Endpoints

## 🔐 Authentication

### Sign In
**POST** `/api/login`
**Status:** ✅ Working

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "remember_me": true
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe",
      "email": "user@example.com",
      "phone": "+234567890"
    },
    "token": "jwt_token_here"
  }
}
```

### Register
**POST** `/api/register`
**Status:** ✅ Working

**Request Body:**
```json
{
  "firstname": "John",
  "lastname": "Doe",
  "email": "user@example.com",
  "phone": "+234567890",
  "password": "password123",
  "tos": true
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe",
      "email": "user@example.com",
      "phone": "+234567890"
    },
    "token": "jwt_token_here"
  }
}
```

### Password Reset Request
**POST** `/api/password/forgot`
**Status:** ✅ Working

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

### Sign In with Google
**POST** `/api/oauth/google`
**Status:** ✅ Working

**Request Body:**
```json
{
  "email": "user@example.com",
  "fullname": "John Doe",
  "phone": "+234567890",
  "password": "password123"
}
```

## 🏠 Home Screen

### Get Product Categories
**GET** `/api/categories?type=market`
**Status:** ✅ Working

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Men",
      "slug": "men",
      "type": "market",
      "children": [
        {
          "id": 2,
          "name": "Shirts",
          "slug": "shirts",
          "parent_id": 1
        }
      ]
    }
  ]
}
```

### Search Products
**GET** `/api/products/search?q=shirt&category=men`
**Status:** ⚠️ Untested (Route exists but is not covered by tests)

### Get Single Product
**GET** `/api/products/{id}`
**Status:** ✅ Working

**Response:**
```json
{
  "status": "success",
  "data": {
    "product": {
      "id": 1,
      "name": "Traditional Shirt",
      "description": "Beautiful traditional shirt",
      "price": 5000,
      "images": ["image1.jpg", "image2.jpg"],
      "sizes": ["S", "M", "L"],
      "processing_time": {
        "normal": {"days": 7, "price": 5000},
        "express": {"days": 3, "price": 7000}
      },
      "avg_rating": 4.5,
      "total_reviews": 25
    },
    "seller": {
      "id": 1,
      "business_name": "Fashion Store",
      "selling_since": "2023-01-15",
      "total_sales": 150,
      "avg_rating": 4.2
    }
  }
}
```

## 🛍️ Market

### Get Products by Category
**GET** `/api/categories/{type}/{slug}/items`
**Status:** ✅ Working

### Store Product Review
**POST** `/api/reviews`
**Status:** ✅ Working

**Request Body:**
```json
{
  "product_id": 1,
  "rating": 5,
  "headline": "Great product",
  "description": "I love this product, great quality"
}
```

### Get Product Reviews
**GET** `/api/reviews/product/{id}`
**Status:** ✅ Working

### Store Order
**POST** `/api/orders`
**Status:** ✅ Working

### Store Address
**POST** `/api/addresses`
**Status:** ⚠️ Untested (Route exists but is not covered by tests)

### Get Addresses
**GET** `/api/addresses`
**Status:** ✅ Working

### Edit Address
**PUT** `/api/addresses/{id}`
**Status:** ⚠️ Untested (Route exists but is not covered by tests)

### Get All Orders
**GET** `/api/orders`
**Status:** ✅ Working

### Generate Payment Link
**POST** `/api/payment/link`
**Status:** ⚠️ Untested (Route exists but is not covered by tests)

## ❤️ Wishlist

### Get Wishlist
**GET** `/api/wishlist`
**Status:** ✅ Working

### Add to Wishlist
**POST** `/api/wishlist`
**Status:** ✅ Working

### Remove from Wishlist
**DELETE** `/api/wishlist`
**Status:** ✅ Working

## 📝 Blog

### Get All Posts
**GET** `/api/blogs?page=1`
**Status:** ✅ Working

### Get Single Post
**GET** `/api/blogs/{slug}`
**Status:** ❌ Not Working (Failing tests. See test results for details)

### Get Favorited Posts
**GET** `/api/blogs/favorites`
**Status:** ✅ Working

### Blog Search
**GET** `/api/blogs/search?q=fashion`
**Status:** ✅ Working

# Seller Endpoints

## 👨‍💼 Seller Profile

### Create Seller Profile
**POST** `/api/seller/profile`
**Status:** ✅ Working

### Get Seller Profile
**GET** `/api/seller/profile`
**Status:** ✅ Working

### Edit Seller Profile
**PUT** `/api/seller/profile`
**Status:** ✅ Working

### Delete Seller Profile
**DELETE** `/api/seller/profile`
**Status:** ✅ Working

# Admin Endpoints

## 🔐 Admin Authentication

### Admin Login
**POST** `/api/admin/login`
**Status:** ✅ Working

## 📊 Dashboard Overview

### Overview Stats
**GET** `/api/admin/dashboard/overview`
**Status:** ✅ Working

## 🔔 Notifications

### Notification Triggers
**Status:** ❌ Not Working
**Details:** Multiple tests are failing for notification triggers due to an admin authentication error within the tests. This affects notifications for order status updates, business approvals/rejections, and new blog posts.

---

# Implementation Summary

## ✅ Completed and Verified Features
- User authentication (login, register, password reset, Google OAuth)
- Product catalog (CRUD operations)
- Order management (creating, listing, and viewing orders)
- Review system (creating and listing reviews for products and businesses)
- Seller profile management (CRUD operations)
- Business profile creation (for Beauty, Brands, Schools, and Music categories)
- Admin dashboard & management (most admin features are in place)
- Wishlist functionality (CRUD operations)
- Product Suggestions ("You may also like" feature)
- Notification Preferences (get and update)

## ⚠️ Implemented but Untested Features
The following features have routes but lack automated tests to verify their correctness.
- Product Search Endpoint (`GET /api/products/search`)
- Address Management (Full CRUD operations)
- Payment Integration (Paystack)
- User Profile Edit (`PUT /api/profile`)
- Password Update (`PUT /api/password`)
- School Registration System (Complete flow)
- Order Tracking (Detailed tracking status)
- Adverts Management (Admin interface)
- Sustainability Business Management (Admin tools)

## ❌ Features with Failing Tests
- **Blog System:** The `GET /api/blogs/{slug}` endpoint is failing.
- **Notification Triggers:** The notification system is not reliably sending notifications for key events.

