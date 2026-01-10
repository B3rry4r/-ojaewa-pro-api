# Oja Ewa Pro API Documentation

This document provides comprehensive API documentation for the Oja Ewa Pro platform, covering User, Seller, and Admin endpoints with expected request/response data.

## 📋 Table of Contents
- [User Endpoints](#user-endpoints)
- [Seller Endpoints](#seller-endpoints) 
- [Admin Endpoints](#admin-endpoints)
- [Data Models](#data-models)

---

# User Endpoints

## 🔐 Authentication

### Sign In
**POST** `/api/login`

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

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Password reset token sent to email"
}
```

### Password Reset Submit
**POST** `/api/password/reset`

**Request Body:**
```json
{
  "email": "user@example.com",
  "token": "reset_token_here",
  "password": "new_password123"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Password reset successful"
}
```

### Sign In with Google
**POST** `/api/oauth/google`

**Request Body:**
```json
{
  "email": "user@example.com",
  "fullname": "John Doe",
  "phone": "+234567890",
  "password": "password123"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Google authentication successful",
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

## 🏠 Home Screen

### Get Product Categories
**GET** `/api/categories?type=market`

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

**Response:**
```json
{
  "status": "success",
  "data": {
    "products": [
      {
        "id": 1,
        "name": "Traditional Shirt",
        "price": 5000,
        "image": "image_url",
        "avg_rating": 4.5,
        "seller": {
          "id": 1,
          "business_name": "Fashion Store"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 50
    }
  }
}
```

### Get Single Product
**GET** `/api/products/{id}`

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
    },
    "reviews": [
      {
        "id": 1,
        "rating": 5,
        "headline": "Great quality",
        "description": "Love this product",
        "user_name": "John D.",
        "created_at": "2024-01-15"
      }
    ],
    "suggested_products": [
      {
        "id": 2,
        "name": "Similar Shirt",
        "price": 4500,
        "image": "image_url"
      }
    ]
  }
}
```

## 🛍️ Market

### Get Main Categories
**GET** `/api/categories?type=market`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Men",
      "slug": "men",
      "type": "market"
    },
    {
      "id": 2,
      "name": "Women", 
      "slug": "women",
      "type": "market"
    }
  ]
}
```

### Get Sub Categories
**GET** `/api/categories/{id}/children`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 3,
      "name": "Shirts",
      "slug": "shirts",
      "parent_id": 1
    },
    {
      "id": 4,
      "name": "Trousers",
      "slug": "trousers", 
      "parent_id": 1
    }
  ]
}
```

### Get Products by Category
**GET** `/api/categories/{type}/{slug}/items`

**Response:**
```json
{
  "status": "success",
  "data": {
    "products": [
      {
        "id": 1,
        "name": "Traditional Shirt",
        "price": 5000,
        "image": "image_url",
        "avg_rating": 4.5,
        "seller": {
          "business_name": "Fashion Store"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 10
    }
  }
}
```

### Store Product Review
**POST** `/api/reviews`

**Request Body:**
```json
{
  "product_id": 1,
  "rating": 5,
  "headline": "Great product",
  "description": "I love this product, great quality"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Review submitted successfully",
  "data": {
    "review": {
      "id": 1,
      "rating": 5,
      "headline": "Great product",
      "description": "I love this product, great quality",
      "user_name": "John D."
    }
  }
}
```

### Get Product Reviews
**GET** `/api/reviews/product/{id}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "reviews": [
      {
        "id": 1,
        "rating": 5,
        "headline": "Great product",
        "description": "I love this product",
        "user_name": "John D.",
        "created_at": "2024-01-15"
      }
    ],
    "stats": {
      "avg_rating": 4.5,
      "total_reviews": 25,
      "rating_breakdown": {
        "5": 15,
        "4": 8,
        "3": 2,
        "2": 0,
        "1": 0
      }
    }
  }
}
```

### Store Order
**POST** `/api/orders`

**Request Body:**
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "size": "M",
      "processing_type": "normal"
    }
  ],
  "shipping_address": {
    "country": "Nigeria",
    "full_name": "John Doe",
    "phone_number": "+234567890",
    "state": "Lagos",
    "city": "Ikeja",
    "zip_code": "100001",
    "address": "123 Main Street",
    "is_default": true
  }
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Order placed successfully",
  "data": {
    "order": {
      "id": 1,
      "order_ref": "OJA-2024-001",
      "total_amount": 10000,
      "status": "pending",
      "items": [
        {
          "product": {
            "name": "Traditional Shirt",
            "price": 5000
          },
          "quantity": 2,
          "subtotal": 10000
        }
      ]
    }
  }
}
```

### Store Address
**POST** `/api/addresses`

**Request Body:**
```json
{
  "country": "Nigeria",
  "full_name": "John Doe",
  "phone_number": "+234567890",
  "state": "Lagos",
  "city": "Ikeja",
  "zip_code": "100001",
  "address": "123 Main Street",
  "is_default": true
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Address saved successfully",
  "data": {
    "address": {
      "id": 1,
      "country": "Nigeria",
      "full_name": "John Doe",
      "phone_number": "+234567890",
      "state": "Lagos",
      "city": "Ikeja",
      "zip_code": "100001",
      "address": "123 Main Street",
      "is_default": true
    }
  }
}
```

### Get Addresses
**GET** `/api/addresses`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "country": "Nigeria",
      "full_name": "John Doe",
      "phone_number": "+234567890",
      "state": "Lagos",
      "city": "Ikeja",
      "zip_code": "100001",
      "address": "123 Main Street",
      "is_default": true
    }
  ]
}
```

### Edit Address
**PUT** `/api/addresses/{id}`

**Request Body:**
```json
{
  "country": "Nigeria",
  "full_name": "John Doe Updated",
  "phone_number": "+234567890",
  "state": "Lagos",
  "city": "Victoria Island",
  "zip_code": "100001",
  "address": "456 Updated Street",
  "is_default": false
}
```

### Get All Orders
**GET** `/api/orders`

**Response:**
```json
{
  "status": "success",
  "data": {
    "orders": [
      {
        "id": 1,
        "order_ref": "OJA-2024-001",
        "total_amount": 10000,
        "status": "processing",
        "created_at": "2024-01-15",
        "items_count": 2
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5
    }
  }
}
```

### Generate Payment Link
**POST** `/api/payment/link`

**Request Body:**
```json
{
  "order_id": 1,
  "callback_url": "https://app.ojaewa.com/payment/callback"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "payment_url": "https://checkout.paystack.com/xxxxxxxxx",
    "reference": "OJA-PAY-2024-001"
  }
}
```

### Payment Webhook
**POST** `/api/webhook/paystack`

**Request Body (from Paystack):**
```json
{
  "event": "charge.success",
  "data": {
    "reference": "OJA-PAY-2024-001",
    "amount": 1000000,
    "status": "success"
  }
}
```

## 💄 Beauty

### Get Beauty Categories
**GET** `/api/categories?type=beauty`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 10,
      "name": "Hair Salon",
      "slug": "hair-salon",
      "type": "beauty"
    },
    {
      "id": 11,
      "name": "Makeup Artist",
      "slug": "makeup-artist",
      "type": "beauty"
    }
  ]
}
```

### Get Beauty Services
**GET** `/api/business?category=beauty`

**Response:**
```json
{
  "status": "success",
  "data": {
    "businesses": [
      {
        "id": 1,
        "business_name": "Glam Beauty Studio",
        "description": "Professional beauty services",
        "avg_rating": 4.8,
        "location": "Lagos, Nigeria",
        "services": ["Hair styling", "Makeup"],
        "image": "business_logo_url"
      }
    ]
  }
}
```

### Get Single Beauty Service
**GET** `/api/business/{id}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "business": {
      "id": 1,
      "business_name": "Glam Beauty Studio",
      "description": "Professional beauty services",
      "avg_rating": 4.8,
      "total_reviews": 45,
      "location": "Lagos, Nigeria",
      "contact": {
        "phone": "+234567890",
        "email": "info@glambeauty.com"
      },
      "services": ["Hair styling", "Makeup", "Nails"],
      "images": ["image1.jpg", "image2.jpg"],
      "social_media": {
        "instagram": "@glambeauty",
        "facebook": "GlamBeautyStudio"
      }
    },
    "reviews": [
      {
        "id": 1,
        "rating": 5,
        "headline": "Amazing service",
        "description": "Best beauty salon in Lagos",
        "user_name": "Jane D.",
        "created_at": "2024-01-15"
      }
    ]
  }
}
```

### Get Beauty Service Reviews
**GET** `/api/reviews/business/{id}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "reviews": [
      {
        "id": 1,
        "rating": 5,
        "headline": "Amazing service",
        "description": "Best beauty salon in Lagos",
        "user_name": "Jane D.",
        "created_at": "2024-01-15"
      }
    ],
    "stats": {
      "avg_rating": 4.8,
      "total_reviews": 45
    }
  }
}
```

### Store Beauty Service Review
**POST** `/api/reviews`

**Request Body:**
```json
{
  "business_id": 1,
  "rating": 5,
  "headline": "Amazing service",
  "description": "Best beauty salon in Lagos"
}
```

## 🏷️ Brands

### Get Brand Categories
**GET** `/api/categories?type=brand`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 20,
      "name": "Fashion Brands",
      "slug": "fashion-brands",
      "type": "brand"
    },
    {
      "id": 21,
      "name": "Beauty Brands",
      "slug": "beauty-brands",
      "type": "brand"
    }
  ]
}
```

### Get Brand Products
**GET** `/api/categories/{type}/{slug}/items`

*Uses same response format as Market products*

## 🎓 Schools

### Get School Categories
**GET** `/api/categories?type=school`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 30,
      "name": "Fashion Schools",
      "slug": "fashion-schools",
      "type": "school"
    },
    {
      "id": 31,
      "name": "Beauty Schools",
      "slug": "beauty-schools",
      "type": "school"
    }
  ]
}
```

### Get School Sub Categories
**GET** `/api/categories/{id}/children`

*Uses same response format as Market subcategories*

### Get Schools in Category
**GET** `/api/business?category=school&subcategory=fashion-schools`

**Response:**
```json
{
  "status": "success",
  "data": {
    "schools": [
      {
        "id": 1,
        "school_name": "Fashion Design Institute",
        "description": "Learn fashion design",
        "avg_rating": 4.6,
        "location": "Lagos, Nigeria",
        "school_type": "fashion",
        "classes_offered": ["Fashion Design", "Pattern Making"],
        "image": "school_logo_url"
      }
    ]
  }
}
```

### Store School Registration
**POST** `/api/school-registrations`

**Request Body:**
```json
{
  "school_id": 1,
  "country": "Nigeria",
  "full_name": "John Doe",
  "phone_number": "+234567890",
  "state": "Lagos",
  "city": "Ikeja",
  "address": "123 Main Street"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "School registration submitted",
  "data": {
    "registration": {
      "id": 1,
      "school_name": "Fashion Design Institute",
      "student_name": "John Doe",
      "status": "pending",
      "created_at": "2024-01-15"
    }
  }
}
```

### School Payment Link
**POST** `/api/payment/link/school`

**Request Body:**
```json
{
  "registration_id": 1,
  "callback_url": "https://app.ojaewa.com/school/payment/callback"
}
```

### Update School Registration Status
**PATCH** `/api/school-registrations/{id}/status`

**Request Body:**
```json
{
  "status": "successful"
}
```

## 🌱 Sustainability

### Get Sustainability Categories
**GET** `/api/categories?type=sustainability`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 40,
      "name": "Eco Fashion",
      "slug": "eco-fashion",
      "type": "sustainability"
    }
  ]
}
```

### Get Sustainability Services
**GET** `/api/business?category=sustainability`

*Uses same response format as Beauty services*

## 🎵 Music

### Get Music Categories  
**GET** `/api/categories?type=music`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 50,
      "name": "DJs",
      "slug": "djs", 
      "type": "music"
    },
    {
      "id": 51,
      "name": "Artists",
      "slug": "artists",
      "type": "music"
    }
  ]
}
```

### Get Music Services
**GET** `/api/business?category=music`

*Uses same response format as Beauty services*

## 👤 Account Management

### Edit Profile
**PUT** `/api/profile`

**Request Body:**
```json
{
  "firstname": "John Updated",
  "lastname": "Doe Updated", 
  "email": "newemail@example.com",
  "phone": "+234567890"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "id": 1,
      "firstname": "John Updated",
      "lastname": "Doe Updated",
      "email": "newemail@example.com",
      "phone": "+234567890"
    }
  }
}
```

### Update Password
**PUT** `/api/password`

**Request Body:**
```json
{
  "current_password": "old_password",
  "new_password": "new_password123"
}
```

### Get Order Tracking Status
**GET** `/api/orders/{id}/tracking`

**Response:**
```json
{
  "status": "success",
  "data": {
    "tracking": {
      "status": "shipped",
      "tracking_number": "TRK123456",
      "estimated_delivery": "2024-01-20",
      "tracking_history": [
        {
          "status": "processing",
          "date": "2024-01-15",
          "description": "Order is being prepared"
        },
        {
          "status": "shipped", 
          "date": "2024-01-18",
          "description": "Order has been shipped"
        }
      ]
    }
  }
}
```

### Get Notification Preferences
**GET** `/api/notification-preferences`

**Response:**
```json
{
  "status": "success",
  "data": {
    "preferences": {
      "push_notifications": true,
      "new_products": true,
      "new_blog_posts": false,
      "discounts_and_sales": true,
      "new_orders": true
    }
  }
}
```

### Update Notification Preferences
**PUT** `/api/notification-preferences`

**Request Body:**
```json
{
  "push_notifications": false,
  "new_products": true,
  "new_blog_posts": true,
  "discounts_and_sales": false,
  "new_orders": true
}
```

### Get Social Links
**GET** `/api/connect`

**Response:**
```json
{
  "status": "success",
  "data": {
    "social_links": {
      "instagram": "https://instagram.com/ojaewa",
      "facebook": "https://facebook.com/ojaewa",
      "twitter": "https://twitter.com/ojaewa"
    }
  }
}
```

### Get FAQs
**GET** `/api/faqs`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "question": "How do I track my order?",
      "answer": "You can track your order by going to Your Orders section..."
    }
  ]
}
```

## ❤️ Wishlist

### Get Wishlist
**GET** `/api/wishlist`

**Response:**
```json
{
  "status": "success",
  "data": {
    "items": [
      {
        "id": 1,
        "type": "product",
        "item": {
          "id": 1,
          "name": "Traditional Shirt",
          "price": 5000,
          "image": "image_url"
        }
      },
      {
        "id": 2,
        "type": "service",
        "item": {
          "id": 1,
          "business_name": "Glam Beauty Studio",
          "description": "Professional beauty services",
          "image": "logo_url"
        }
      }
    ]
  }
}
```

### Add to Wishlist
**POST** `/api/wishlist`

**Request Body:**
```json
{
  "type": "product",
  "item_id": 1
}
```

### Remove from Wishlist
**DELETE** `/api/wishlist`

**Request Body:**
```json
{
  "type": "product", 
  "item_id": 1
}
```

## 🔔 Notifications

### Get Notifications
**GET** `/api/notifications`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "text": "Your order has been shipped",
      "date": "2024-01-15T10:30:00Z",
      "is_read": false
    }
  ]
}
```

## 📝 Blog

### Get All Posts
**GET** `/api/blogs?page=1`

**Response:**
```json
{
  "status": "success",
  "data": {
    "posts": [
      {
        "id": 1,
        "title": "Latest Fashion Trends",
        "slug": "latest-fashion-trends",
        "featured_image": "image_url",
        "excerpt": "Discover the latest trends...",
        "published_at": "2024-01-15"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "per_page": 10
    }
  }
}
```

### Get Single Post
**GET** `/api/blogs/{slug}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "post": {
      "id": 1,
      "title": "Latest Fashion Trends",
      "slug": "latest-fashion-trends",
      "content": "Full blog content here...",
      "featured_image": "image_url",
      "published_at": "2024-01-15"
    },
    "related_posts": [
      {
        "id": 2,
        "title": "Fashion Week Highlights",
        "slug": "fashion-week-highlights",
        "featured_image": "image_url"
      }
    ]
  }
}
```

### Get Favorited Posts
**GET** `/api/blogs/favorites`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Latest Fashion Trends",
      "slug": "latest-fashion-trends", 
      "featured_image": "image_url",
      "published_at": "2024-01-15"
    }
  ]
}
```

### Blog Search
**GET** `/api/blogs/search?q=fashion`

**Response:**
```json
{
  "status": "success",
  "data": {
    "posts": [
      {
        "id": 1,
        "title": "Latest Fashion Trends",
        "slug": "latest-fashion-trends",
        "featured_image": "image_url",
        "excerpt": "Discover the latest trends..."
      }
    ]
  }
}
```

---

# Seller Endpoints

## 🔐 Seller Authentication

*Sellers use the same authentication endpoints as users since seller profile is created after user registration*

## 👨‍💼 Seller Profile

### Create Seller Profile
**POST** `/api/seller/profile`

**Request Body:**
```json
{
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Ikeja",
  "address": "123 Business Street",
  "business_email": "business@example.com",
  "business_phone_number": "+234567890",
  "instagram": "@mybusiness",
  "facebook": "MyBusiness",
  "identity_document": "identity_doc.pdf",
  "business_name": "Fashion Store",
  "business_registration_number": "REG123456",
  "business_certificate": "certificate.pdf",
  "business_logo": "logo.jpg",
  "bank_name": "First Bank",
  "account_number": "1234567890"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Seller profile created successfully",
  "data": {
    "seller": {
      "id": 1,
      "business_name": "Fashion Store",
      "registration_status": "pending",
      "created_at": "2024-01-15"
    }
  }
}
```

### Get Seller Profile
**GET** `/api/seller/profile`

**Response:**
```json
{
  "status": "success",
  "data": {
    "seller": {
      "id": 1,
      "country": "Nigeria",
      "state": "Lagos", 
      "city": "Ikeja",
      "address": "123 Business Street",
      "business_email": "business@example.com",
      "business_phone_number": "+234567890",
      "instagram": "@mybusiness",
      "facebook": "MyBusiness",
      "business_name": "Fashion Store",
      "business_registration_number": "REG123456",
      "business_logo": "logo.jpg",
      "bank_name": "First Bank",
      "account_number": "1234567890",
      "registration_status": "approved",
      "selling_since": "2024-01-15",
      "total_sales": 150,
      "avg_rating": 4.2
    }
  }
}
```

### Edit Seller Profile
**PUT** `/api/seller/profile`

**Request Body:** *Same as create seller profile*

### Delete Seller Profile
**DELETE** `/api/seller/profile`

**Request Body:**
```json
{
  "reason": "No longer selling"
}
```

### Upload Seller Documents
**POST** `/api/seller/profile/upload`

**Request Body (multipart/form-data):**
```
identity_document: file
business_certificate: file  
business_logo: file
```

## 🛍️ Product Management

### Get All Products
**GET** `/api/products`

**Response:**
```json
{
  "status": "success",
  "data": {
    "products": [
      {
        "id": 1,
        "name": "Traditional Shirt",
        "price": 5000,
        "status": "approved",
        "created_at": "2024-01-15",
        "total_orders": 25
      }
    ]
  }
}
```

### Get Single Product
**GET** `/api/products/{id}`

*Same response as user product endpoint*

### Create Product
**POST** `/api/products`

**Request Body:**
```json
{
  "name": "Traditional Shirt",
  "gender": "men",
  "style": "traditional",
  "tribe": "yoruba",
  "description": "Beautiful traditional shirt",
  "images": ["image1.jpg", "image2.jpg"],
  "sizes": ["S", "M", "L", "XL"],
  "processing_time": {
    "normal": {"days": 7, "price": 5000},
    "express": {"days": 3, "price": 7000}
  }
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Product created successfully",
  "data": {
    "product": {
      "id": 1,
      "name": "Traditional Shirt",
      "status": "pending",
      "created_at": "2024-01-15"
    }
  }
}
```

### Edit Product
**PUT** `/api/products/{id}`

**Request Body:** *Same as create product*

### Delete Product
**DELETE** `/api/products/{id}`

**Response:**
```json
{
  "status": "success",
  "message": "Product deleted successfully"
}
```

## 📦 Order Management

### Get Seller Orders
**GET** `/api/orders`

**Response:**
```json
{
  "status": "success",
  "data": {
    "orders": [
      {
        "id": 1,
        "order_ref": "OJA-2024-001",
        "customer_name": "John Doe",
        "total_amount": 10000,
        "status": "processing",
        "created_at": "2024-01-15"
      }
    ]
  }
}
```

### Get Single Order
**GET** `/api/orders/{id}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "order": {
      "id": 1,
      "order_ref": "OJA-2024-001",
      "customer": {
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+234567890"
      },
      "items": [
        {
          "product": {
            "name": "Traditional Shirt",
            "price": 5000
          },
          "quantity": 2,
          "size": "M",
          "subtotal": 10000
        }
      ],
      "shipping_address": {
        "full_name": "John Doe",
        "address": "123 Main Street",
        "city": "Ikeja",
        "state": "Lagos"
      },
      "total_amount": 10000,
      "status": "processing",
      "created_at": "2024-01-15"
    }
  }
}
```

## 🏢 Business Management

### Get Business Categories
**GET** `/api/categories?type=beauty|brand|school|music`

*Uses existing category system responses*

### Create Beauty Business
**POST** `/api/business`

**Request Body:**
```json
{
  "category": "beauty",
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Ikeja",
  "address": "123 Business Street",
  "business_email": "beauty@example.com",
  "business_phone_number": "+234567890",
  "website_url": "https://mybeauty.com",
  "instagram": "@mybeauty",
  "facebook": "MyBeauty",
  "identity_document": "id_doc.pdf",
  "business_name": "Glam Beauty Studio",
  "type_of_offering": "providing_service",
  "product_category": "hair_salon",
  "business_description": "Professional beauty services",
  "product_list": ["Hair styling", "Makeup", "Nails"],
  "professional_title": "Senior Beautician",
  "service_list": ["Bridal makeup", "Hair treatment"],
  "business_logo": "logo.jpg"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Beauty business created successfully",
  "data": {
    "business": {
      "id": 1,
      "business_name": "Glam Beauty Studio",
      "category": "beauty",
      "status": "pending",
      "created_at": "2024-01-15"
    }
  }
}
```

### Create Brand Business
**POST** `/api/business`

**Request Body:**
```json
{
  "category": "brand",
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Ikeja",
  "address": "123 Business Street",
  "business_email": "brand@example.com",
  "business_phone_number": "+234567890",
  "website_url": "https://mybrand.com",
  "instagram": "@mybrand",
  "facebook": "MyBrand",
  "business_name": "Premium Fashion Brand",
  "business_description": "High-end fashion brand",
  "brand_category": "fashion",
  "product_list": ["Dresses", "Suits", "Accessories"],
  "cac_document": "cac_doc.pdf",
  "business_logo": "logo.jpg"
}
```

### Create School Business
**POST** `/api/business`

**Request Body:**
```json
{
  "category": "school",
  "country": "Nigeria",
  "state": "Lagos", 
  "city": "Ikeja",
  "address": "123 School Street",
  "business_email": "school@example.com",
  "business_phone_number": "+234567890",
  "website_url": "https://myschool.com",
  "instagram": "@myschool",
  "facebook": "MySchool",
  "school_name": "Fashion Design Institute",
  "school_type": "fashion",
  "school_biography": "Leading fashion design school",
  "classes_offered": ["Fashion Design", "Pattern Making"],
  "business_certificate": "cert.pdf",
  "recognition_and_certifications": ["cert1.pdf", "cert2.pdf"],
  "business_logo": "logo.jpg"
}
```

### Create Music Business
**POST** `/api/business`

**Request Body:**
```json
{
  "category": "music",
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Ikeja", 
  "address": "123 Music Street",
  "business_email": "music@example.com",
  "business_phone_number": "+234567890",
  "website_url": "https://mymusic.com",
  "instagram": "@mymusic",
  "facebook": "MyMusic",
  "youtube": "MyMusicChannel",
  "spotify": "MyMusicSpotify",
  "music_category": "dj",
  "identity_document": "id_doc.pdf",
  "biography": "Professional DJ with 10 years experience",
  "business_logo": "logo.jpg"
}
```

### Get User Businesses
**GET** `/api/business`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "business_name": "Glam Beauty Studio",
      "category": "beauty",
      "status": "approved",
      "created_at": "2024-01-15"
    }
  ]
}
```

### Edit Business
**PUT** `/api/business/{id}`

**Request Body:** *Same as respective create business endpoint*

### Deactivate Business
**PATCH** `/api/business/{id}/deactivate`

**Response:**
```json
{
  "status": "success",
  "message": "Business deactivated successfully"
}
```

### Delete Business
**DELETE** `/api/business/{id}`

**Response:**
```json
{
  "status": "success",
  "message": "Business deleted successfully"
}
```

### Upload Business Files
**POST** `/api/business/{id}/upload`

**Request Body (multipart/form-data):**
```
business_logo: file
business_certificate: file
identity_document: file
```

### Manage Subscription
**POST** `/api/business/{id}/subscription`

**Request Body:**
```json
{
  "plan": "premium",
  "billing_cycle": "monthly"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Subscription updated",
  "data": {
    "subscription": {
      "plan": "premium",
      "status": "active",
      "expires_at": "2024-02-15"
    }
  }
}
```

---

# Admin Endpoints

## 🔐 Admin Authentication

### Admin Login
**POST** `/api/admin/login`

**Request Body:**
```json
{
  "email": "admin@ojaewa.com",
  "password": "Admin@1234",
  "remember_me": true
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Admin login successful",
  "data": {
    "admin": {
      "id": 1,
      "firstname": "Admin",
      "lastname": "User",
      "email": "admin@ojaewa.com"
    },
    "token": "admin_jwt_token"
  }
}
```

### Admin Profile
**GET** `/api/admin/profile`

**Response:**
```json
{
  "status": "success",
  "data": {
    "admin": {
      "id": 1,
      "firstname": "Admin",
      "lastname": "User",
      "email": "admin@ojaewa.com"
    }
  }
}
```

### Admin Logout
**POST** `/api/admin/logout`

**Response:**
```json
{
  "status": "success",
  "message": "Logged out successfully"
}
```

### Create Admin
**POST** `/api/admin/create`

**Request Body:**
```json
{
  "firstname": "New",
  "lastname": "Admin",
  "email": "newadmin@ojaewa.com",
  "password": "Admin@1234"
}
```

## 📊 Dashboard Overview

### Overview Stats
**GET** `/api/admin/dashboard/overview`

**Response:**
```json
{
  "status": "success",
  "data": {
    "admin_name": "Admin",
    "statistics": {
      "total_users": 1250,
      "total_revenue": 5000000,
      "total_businesses": 85,
      "market_revenue": 3500000,
      "total_sellers": 45
    },
    "pending_sellers": [
      {
        "business_name": "Fashion Store",
        "reg_date": "2024-01-15",
        "phone": "+234567890",
        "email": "seller@example.com",
        "status": "pending"
      }
    ],
    "pending_products": [
      {
        "product_name": "Traditional Shirt",
        "gender": "men",
        "style": "traditional",
        "tribe": "yoruba",
        "status": "pending"
      }
    ],
    "new_orders": [
      {
        "order_ref": "OJA-2024-001",
        "customer": "John Doe",
        "order_date": "2024-01-15",
        "email": "john@example.com",
        "status": "processing"
      }
    ],
    "pending_businesses": {
      "beauty": 5,
      "schools": 3,
      "brands": 2,
      "music": 1
    }
  }
}
```

### Pending Sellers
**GET** `/api/admin/pending/sellers`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "business_name": "Fashion Store",
      "reg_date": "2024-01-15",
      "phone": "+234567890",
      "email": "seller@example.com",
      "status": "pending"
    }
  ]
}
```

### Pending Products
**GET** `/api/admin/pending/products`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "product_name": "Traditional Shirt",
      "gender": "men",
      "style": "traditional",
      "tribe": "yoruba",
      "status": "pending"
    }
  ]
}
```

### New Orders
**GET** `/api/admin/orders`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "order_ref": "OJA-2024-001",
      "customer": "John Doe",
      "order_date": "2024-01-15",
      "email": "john@example.com",
      "status": "processing"
    }
  ]
}
```

### Pending Businesses
**GET** `/api/admin/business/{category}`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "business_name": "Glam Beauty Studio",
      "reg_date": "2024-01-15",
      "phone": "+234567890",
      "email": "beauty@example.com",
      "status": "pending"
    }
  ]
}
```

## 👥 User Management

### Get All Users
**GET** `/api/admin/users?page=1`

**Response:**
```json
{
  "status": "success",
  "data": {
    "users": [
      {
        "id": 1,
        "name": "John Doe",
        "reg_date": "2024-01-15",
        "phone": "+234567890",
        "email": "user@example.com"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 25,
      "per_page": 50
    }
  }
}
```

## 🛍️ Market Management

### Get All Sellers
**GET** `/api/admin/market/sellers`

**Response:**
```json
{
  "status": "success",
  "data": {
    "sellers": [
      {
        "id": 1,
        "business_name": "Fashion Store",
        "reg_date": "2024-01-15",
        "phone": "+234567890",
        "email": "seller@example.com",
        "status": "pending"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 10
    }
  }
}
```

### Get Single Seller
**GET** `/api/admin/sellers/{id}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "seller": {
      "id": 1,
      "user": {
        "firstname": "John",
        "lastname": "Doe",
        "email": "seller@example.com",
        "phone": "+234567890"
      },
      "business_name": "Fashion Store",
      "business_email": "business@example.com",
      "business_phone_number": "+234567891",
      "country": "Nigeria",
      "state": "Lagos",
      "city": "Ikeja",
      "address": "123 Business Street",
      "instagram": "@fashionstore",
      "facebook": "FashionStore",
      "business_registration_number": "REG123456",
      "bank_name": "First Bank",
      "account_number": "1234567890",
      "registration_status": "pending",
      "documents": {
        "identity_document": "id_doc.pdf",
        "business_certificate": "cert.pdf",
        "business_logo": "logo.jpg"
      },
      "created_at": "2024-01-15"
    }
  }
}
```

### Approve/Reject Seller
**PATCH** `/api/seller/{id}/approve`

**Request Body:**
```json
{
  "status": "approved"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Seller status updated successfully"
}
```

### Get All Products
**GET** `/api/admin/market/products`

**Response:**
```json
{
  "status": "success",
  "data": {
    "products": [
      {
        "id": 1,
        "product_name": "Traditional Shirt",
        "gender": "men",
        "style": "traditional",
        "tribe": "yoruba",
        "status": "pending",
        "seller": {
          "business_name": "Fashion Store"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 15
    }
  }
}
```

### Get Single Product
**GET** `/api/admin/products/{id}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "product": {
      "id": 1,
      "name": "Traditional Shirt",
      "description": "Beautiful traditional shirt",
      "gender": "men",
      "style": "traditional", 
      "tribe": "yoruba",
      "price": 5000,
      "images": ["image1.jpg", "image2.jpg"],
      "sizes": ["S", "M", "L"],
      "processing_time": {
        "normal": {"days": 7, "price": 5000},
        "express": {"days": 3, "price": 7000}
      },
      "status": "pending",
      "created_at": "2024-01-15"
    },
    "seller": {
      "business_name": "Fashion Store",
      "email": "seller@example.com"
    }
  }
}
```

### Approve/Reject Product
**PATCH** `/api/product/{id}/approve`

**Request Body:**
```json
{
  "status": "approved"
}
```

### Get Single Order
**GET** `/api/admin/order/{id}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "order": {
      "id": 1,
      "order_ref": "OJA-2024-001",
      "customer": {
        "firstname": "John",
        "lastname": "Doe",
        "email": "john@example.com",
        "phone": "+234567890"
      },
      "items": [
        {
          "product": {
            "id": 1,
            "name": "Traditional Shirt",
            "price": 5000,
            "image": "image.jpg"
          },
          "quantity": 2,
          "size": "M",
          "subtotal": 10000
        }
      ],
      "shipping_address": {
        "full_name": "John Doe",
        "address": "123 Main Street",
        "city": "Ikeja",
        "state": "Lagos",
        "country": "Nigeria"
      },
      "total_amount": 10000,
      "status": "processing",
      "order_date": "2024-01-15",
      "payment_status": "paid"
    }
  }
}
```

### Update Order Status
**PATCH** `/api/admin/order/{id}/status`

**Request Body:**
```json
{
  "status": "shipped"
}
```

## 🏢 Business Category Management

### Beauty Businesses
**GET** `/api/admin/business/beauty`

**Response:**
```json
{
  "status": "success", 
  "data": [
    {
      "id": 1,
      "business_name": "Glam Beauty Studio",
      "reg_date": "2024-01-15",
      "phone": "+234567890",
      "email": "beauty@example.com",
      "offering": "providing_service",
      "status": "pending"
    }
  ]
}
```

### Brands Businesses
**GET** `/api/admin/business/brand`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "business_name": "Premium Fashion Brand", 
      "reg_date": "2024-01-15",
      "phone": "+234567890",
      "email": "brand@example.com",
      "product_category": "fashion",
      "status": "pending"
    }
  ]
}
```

### Schools Businesses
**GET** `/api/admin/business/school`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "business_name": "Fashion Design Institute",
      "reg_date": "2024-01-15",
      "phone": "+234567890", 
      "email": "school@example.com",
      "type": "fashion",
      "status": "pending"
    }
  ]
}
```

### Music Businesses
**GET** `/api/admin/business/music`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "business_name": "DJ Pro Services",
      "reg_date": "2024-01-15",
      "phone": "+234567890",
      "email": "music@example.com", 
      "category": "dj",
      "status": "pending"
    }
  ]
}
```

### Get Single Business
**GET** `/api/admin/business/{category}/{id}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "business": {
      "id": 1,
      "business_name": "Glam Beauty Studio",
      "category": "beauty",
      "owner": {
        "firstname": "Jane",
        "lastname": "Doe",
        "email": "jane@example.com",
        "phone": "+234567890"
      },
      "business_email": "beauty@example.com",
      "business_phone_number": "+234567891",
      "country": "Nigeria",
      "state": "Lagos",
      "city": "Ikeja",
      "address": "123 Beauty Street",
      "website_url": "https://glambeauty.com",
      "instagram": "@glambeauty",
      "facebook": "GlamBeautyStudio",
      "business_description": "Professional beauty services",
      "type_of_offering": "providing_service",
      "product_category": "hair_salon",
      "service_list": ["Bridal makeup", "Hair treatment"],
      "status": "pending",
      "documents": {
        "business_logo": "logo.jpg",
        "identity_document": "id_doc.pdf"
      },
      "created_at": "2024-01-15"
    }
  }
}
```

### Update Business Status
**PATCH** `/api/admin/business/{category}/{id}/status`

**Request Body:**
```json
{
  "status": "approved"
}
```

## 📝 Content Management

### Blog Management

#### Get All Blogs
**GET** `/api/admin/blogs`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "featured_image": "image.jpg",
      "title": "Latest Fashion Trends",
      "date": "2024-01-15",
      "status": "published"
    }
  ]
}
```

#### Create Blog
**POST** `/api/admin/blogs`

**Request Body:**
```json
{
  "title": "Latest Fashion Trends",
  "body": "Full blog content here...",
  "featured_image": "image.jpg"
}
```

#### Edit Blog
**PUT** `/api/admin/blogs/{id}`

**Request Body:** *Same as create blog*

#### Delete Blog
**DELETE** `/api/admin/blogs/{id}`

#### Toggle Blog Publish
**PATCH** `/api/admin/blogs/{id}/toggle-publish`

**Response:**
```json
{
  "status": "success",
  "message": "Blog status updated",
  "data": {
    "blog": {
      "id": 1,
      "title": "Latest Fashion Trends",
      "status": "published"
    }
  }
}
```

## 📢 Adverts Management

### Get All Adverts
**GET** `/api/admin/adverts`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "ad_name": "Summer Sale",
      "image": "ad_image.jpg",
      "go_live_on": "2024-01-20",
      "end_on": "2024-01-30",
      "status": "active"
    }
  ]
}
```

### Create Advert
**POST** `/api/admin/adverts`

**Request Body:**
```json
{
  "ad_name": "Summer Sale",
  "image": "ad_image.jpg",
  "go_live_on": "2024-01-20",
  "end_on": "2024-01-30",
  "link_url": "https://example.com"
}
```

### Edit Advert
**PUT** `/api/admin/adverts/{id}`

**Request Body:** *Same as create advert*

### Delete Advert
**DELETE** `/api/admin/adverts/{id}`

## 🌱 Sustainability Management

### Get Sustainability Businesses
**GET** `/api/admin/sustainability`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "business_name": "Eco Fashion Studio",
      "reg_date": "2024-01-15",
      "phone": "+234567890",
      "email": "eco@example.com",
      "status": "active"
    }
  ]
}
```

### View Sustainability Business
**GET** `/api/admin/sustainability/{id}`

### Add New Sustainability Business
**POST** `/api/admin/sustainability`

### Edit Sustainability Business
**PUT** `/api/admin/sustainability/{id}`

### Update Sustainability Business Status
**PATCH** `/api/admin/sustainability/{id}/status`

## 🔔 Admin Notifications

### Get Admin Notifications
**GET** `/api/admin/notifications`

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "status": "unread",
      "description": "New seller registration pending approval",
      "date": "1 hour ago"
    }
  ]
}
```

## ⚙️ Admin Settings

### Get Admin Settings
**GET** `/api/admin/settings`

**Response:**
```json
{
  "status": "success",
  "data": {
    "admin": {
      "full_name": "Admin User",
      "email": "admin@ojaewa.com"
    }
  }
}
```

### Edit Admin Profile
**PUT** `/api/admin/profile`

**Request Body:**
```json
{
  "firstname": "Updated",
  "lastname": "Admin"
}
```

### Update Admin Password
**PUT** `/api/admin/password`

**Request Body:**
```json
{
  "current_password": "current_password",
  "new_password": "new_password123"
}
```

---

# Data Models

## User Model
```json
{
  "id": 1,
  "firstname": "John",
  "lastname": "Doe", 
  "email": "user@example.com",
  "phone": "+234567890",
  "created_at": "2024-01-15T00:00:00Z",
  "updated_at": "2024-01-15T00:00:00Z"
}
```

## Seller Model
```json
{
  "id": 1,
  "user_id": 1,
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Ikeja", 
  "address": "123 Business Street",
  "business_email": "business@example.com",
  "business_phone_number": "+234567890",
  "instagram": "@mybusiness",
  "facebook": "MyBusiness",
  "business_name": "Fashion Store",
  "business_registration_number": "REG123456",
  "business_logo": "logo.jpg",
  "bank_name": "First Bank",
  "account_number": "1234567890",
  "registration_status": "pending|approved|rejected",
  "created_at": "2024-01-15T00:00:00Z"
}
```

## Product Model
```json
{
  "id": 1,
  "seller_id": 1,
  "name": "Traditional Shirt",
  "description": "Beautiful traditional shirt",
  "gender": "men|women",
  "style": "traditional|modern|casual",
  "tribe": "yoruba|igbo|hausa",
  "images": ["image1.jpg", "image2.jpg"],
  "sizes": ["S", "M", "L", "XL"],
  "processing_time": {
    "normal": {"days": 7, "price": 5000},
    "express": {"days": 3, "price": 7000}
  },
  "status": "pending|approved|rejected",
  "avg_rating": 4.5,
  "total_reviews": 25,
  "created_at": "2024-01-15T00:00:00Z"
}
```

## Order Model  
```json
{
  "id": 1,
  "user_id": 1,
  "order_ref": "OJA-2024-001",
  "total_amount": 10000,
  "status": "pending|processing|shipped|delivered|cancelled",
  "payment_status": "pending|paid|failed",
  "shipping_address": {
    "country": "Nigeria",
    "full_name": "John Doe", 
    "phone_number": "+234567890",
    "state": "Lagos",
    "city": "Ikeja",
    "zip_code": "100001",
    "address": "123 Main Street"
  },
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "size": "M",
      "processing_type": "normal|express",
      "price": 5000,
      "subtotal": 10000
    }
  ],
  "created_at": "2024-01-15T00:00:00Z"
}
```

## Business Model
```json
{
  "id": 1,
  "user_id": 1,
  "category": "beauty|brand|school|music|sustainability",
  "business_name": "Business Name",
  "business_description": "Business description",
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Ikeja",
  "address": "123 Business Street",
  "business_email": "business@example.com", 
  "business_phone_number": "+234567890",
  "website_url": "https://business.com",
  "instagram": "@business",
  "facebook": "Business",
  "business_logo": "logo.jpg",
  "status": "pending|approved|rejected|inactive",
  "avg_rating": 4.5,
  "total_reviews": 45,
  "created_at": "2024-01-15T00:00:00Z"
}
```

## Review Model
```json
{
  "id": 1,
  "user_id": 1,
  "product_id": 1,
  "business_id": null,
  "rating": 5,
  "headline": "Great product",
  "description": "I love this product, great quality",
  "created_at": "2024-01-15T00:00:00Z"
}
```

## Category Model
```json
{
  "id": 1,
  "name": "Men",
  "slug": "men",
  "type": "market|beauty|brand|school|music|sustainability",
  "parent_id": null,
  "children": [
    {
      "id": 2,
      "name": "Shirts",
      "slug": "shirts",
      "parent_id": 1
    }
  ]
}
```

## Address Model
```json
{
  "id": 1,
  "user_id": 1,
  "country": "Nigeria",
  "full_name": "John Doe",
  "phone_number": "+234567890",
  "state": "Lagos",
  "city": "Ikeja",
  "zip_code": "100001",
  "address": "123 Main Street",
  "is_default": true,
  "created_at": "2024-01-15T00:00:00Z"
}
```

## Notification Model
```json
{
  "id": 1,
  "user_id": 1,
  "text": "Your order has been shipped",
  "is_read": false,
  "created_at": "2024-01-15T00:00:00Z"
}
```

## Blog Model
```json
{
  "id": 1,
  "title": "Latest Fashion Trends",
  "slug": "latest-fashion-trends",
  "content": "Full blog content here...",
  "featured_image": "image_url",
  "status": "draft|published",
  "published_at": "2024-01-15T00:00:00Z",
  "created_at": "2024-01-15T00:00:00Z"
}
```

## Wishlist Model
```json
{
  "id": 1,
  "user_id": 1,
  "item_type": "product|service",
  "item_id": 1,
  "created_at": "2024-01-15T00:00:00Z"
}
```

---

# API Response Status Codes

## Success Responses
- `200` - OK (successful GET, PUT, PATCH requests)
- `201` - Created (successful POST requests)
- `204` - No Content (successful DELETE requests)

## Error Responses
- `400` - Bad Request (validation errors, malformed request)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found (resource doesn't exist)
- `422` - Unprocessable Entity (validation failed)
- `500` - Internal Server Error (server error)

## Standard Error Response Format
```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    "field_name": ["Specific error message"]
  }
}
```

---

# Authentication

## JWT Token Structure
All authenticated requests must include the JWT token in the Authorization header:

```
Authorization: Bearer {jwt_token}
```

## Token Payload
```json
{
  "user_id": 1,
  "email": "user@example.com",
  "role": "user|seller|admin",
  "exp": 1640995200
}
```

---

# File Upload

## Supported File Types
- **Images**: jpg, jpeg, png, gif (max 5MB)
- **Documents**: pdf, doc, docx (max 10MB)

## Upload Response Format
```json
{
  "status": "success",
  "data": {
    "file_url": "https://storage.ojaewa.com/uploads/filename.jpg",
    "file_name": "filename.jpg",
    "file_size": 1024000
  }
}
```

---

# Pagination

## Request Parameters
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 10, max: 100)

## Response Format
```json
{
  "status": "success",
  "data": {
    "items": [],
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "per_page": 10,
      "total_items": 100,
      "has_next_page": true,
      "has_prev_page": false
    }
  }
}
```

---

# Search & Filtering

## Product Search Parameters
- `q` - Search query
- `category` - Category slug
- `gender` - men|women
- `style` - traditional|modern|casual
- `tribe` - yoruba|igbo|hausa
- `min_price` - Minimum price
- `max_price` - Maximum price
- `sort` - price_asc|price_desc|rating|newest

## Business Search Parameters
- `q` - Search query
- `category` - Business category
- `location` - State or city
- `rating` - Minimum rating (1-5)

---

# Payment Integration (Paystack)

## Environment Variables Required
```
PAYSTACK_PUBLIC_KEY=pk_test_xxxxx
PAYSTACK_SECRET_KEY=sk_test_xxxxx
PAYSTACK_WEBHOOK_SECRET=whsec_xxxxx
```

## Payment Flow
1. Create order via `POST /api/orders`
2. Generate payment link via `POST /api/payment/link`
3. Redirect user to Paystack checkout
4. Handle webhook notification at `POST /api/webhook/paystack`
5. Update order status based on payment result

---

# Enum Values

## Order Status
- `pending` - Order placed, awaiting payment
- `processing` - Payment confirmed, preparing order
- `shipped` - Order shipped to customer
- `delivered` - Order delivered successfully
- `cancelled` - Order cancelled

## Registration Status
- `pending` - Awaiting admin approval
- `approved` - Approved and active
- `rejected` - Application rejected

## Business Status
- `pending` - Awaiting admin approval
- `approved` - Approved and active
- `rejected` - Application rejected
- `inactive` - Temporarily inactive

## Gender Options
- `men`
- `women`

## Style Options
- `traditional`
- `modern`
- `casual`

## Tribe Options
- `yoruba`
- `igbo`
- `hausa`

## School Types
- `fashion`
- `music`
- `catering`
- `beauty`

## Music Categories
- `dj`
- `artist`
- `producer`

## Processing Types
- `normal` - Standard processing time
- `express` - Rush processing (additional cost)

---

# Implementation Summary

## ✅ Completed Features
- User authentication (login, register, password reset, Google OAuth)
- Product catalog and search
- Order management
- Review system
- Seller profile management
- Business profile creation (Beauty, Brands, Schools, Music)
- Admin dashboard with comprehensive management tools
- Blog system
- Wishlist functionality
- Notification system

## ❌ Missing Critical Features
1. **Product Search Endpoint** - `GET /api/products/search`
2. **Address Management** - Full CRUD operations
3. **Payment Integration** - Paystack implementation
4. **User Profile Edit** - `PUT /api/profile`
5. **Password Update** - `PUT /api/password`
6. **Product Suggestions** - "You may also like" feature
7. **School Registration System** - Complete flow
8. **Order Tracking** - Detailed tracking status
9. **Adverts Management** - Admin interface
10. **Sustainability Business Management** - Admin tools

## 🔄 Features Needing Enhancement
- Seller profile statistics (selling_since, total_sales, avg_rating)
- Business listings with average ratings
- Blog related posts functionality
- Notification preferences management

---

*This documentation serves as the complete API specification for the Oja Ewa Pro platform. Use it as a reference for frontend development and API testing.*

**Last Updated: January 2024**
**Version: 1.0**