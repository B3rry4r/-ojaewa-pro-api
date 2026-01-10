# Oja Ewa Pro API Development Guide

This document tracks the implementation status of all API endpoints for the Oja Ewa Pro platform.

## 🟢 Legend
- ✅ **Implemented & Tested**
- 🟡 **Partially Implemented** (needs modifications)
- ❌ **Not Implemented**
- 🔄 **Needs Review/Enhancement**

---

## 🔐 USER AUTHENTICATION

### Basic Authentication
| Endpoint | Status | Route | Notes |
|----------|---------|--------|--------|
| Sign in | ✅ | `POST /api/login` | Requires `firstname`/`lastname` not `first_name`/`last_name` |
| Register | ✅ | `POST /api/register` | Working with proper field names |
| Password Reset (Request) | ✅ | `POST /api/password/forgot` | |
| Password Reset (Submit) | ✅ | `POST /api/password/reset` | |
| Google OAuth | ✅ | `POST /api/oauth/google` | |
| Code Verification | ❌ | Not implemented | Optional - only needed for non-Google signups |

---

## 🏠 HOME SCREEN FEATURES

### Search Functionality
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Product Categories | ✅ | `GET /api/categories?type=market` | Hierarchical structure implemented |
| Search Products | ❌ | `GET /api/products/search` | **MISSING** - Need search endpoint |
| Get Single Product | ✅ | `GET /api/products/{id}` | Returns product + seller + reviews |

---

## 🛍️ MARKET SECTION

### Categories & Products
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Main Categories (Men/Women) | ✅ | `GET /api/categories?type=market` | Returns Men/Women with subcategories |
| Get Subcategories | ✅ | `GET /api/categories/{id}/children` | |
| Get Products by Category | ✅ | `GET /api/categories/{type}/{slug}/items` | Includes avg ratings |
| Get Single Product + Reviews | ✅ | `GET /api/products/{id}` | |
| Product Suggestions | ❌ | | **MISSING** - "You may also like" feature |

### Reviews & Orders
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Write Product Review | ✅ | `POST /api/reviews` | |
| Get Product Reviews | ✅ | `GET /api/reviews/product/{id}` | |
| Store Order | ✅ | `POST /api/orders` | |
| Get All Orders | ✅ | `GET /api/orders` | |
| Get Single Order | ✅ | `GET /api/orders/{id}` | |

### Seller & Address Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Seller Profile | ✅ | `GET /api/seller/profile` | Need to add: selling_since, total_sales, avg_rating |
| Store Address | ❌ | `POST /api/addresses` | **MISSING** |
| Get Addresses | ❌ | `GET /api/addresses` | **MISSING** |
| Edit Address | ❌ | `PUT /api/addresses/{id}` | **MISSING** |

### Payment Integration
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Generate Payment Link | ❌ | `POST /api/payment/link` | **MISSING** - Paystack integration |
| Payment Webhook | ❌ | `POST /api/webhook/paystack` | **MISSING** |

---

## 💄 BEAUTY SECTION

### Categories & Services
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Beauty Categories | ✅ | `GET /api/categories?type=beauty` | |
| Get Beauty Services/Businesses | 🟡 | `GET /api/business?category=beauty` | Need avg ratings |
| Get Single Service/Business | ✅ | `GET /api/business/{id}` | |
| Service Reviews | ✅ | `GET /api/reviews/business/{id}` | Using existing review system |
| Write Service Review | ✅ | `POST /api/reviews` | |

---

## 🏷️ BRANDS SECTION

### Categories & Products
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Brand Categories | ✅ | `GET /api/categories?type=brand` | |
| Get Brand Products | ✅ | `GET /api/categories/{type}/{slug}/items` | Reuses market product flow |

---

## 🎓 SCHOOLS SECTION

| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get School Categories | ✅ | `GET /api/categories?type=school` | |
| Get School Subcategories | ✅ | `GET /api/categories/{id}/children` | |
| Get Schools in Category | 🟡 | `GET /api/business?category=school` | Need avg ratings |
| Store School Registration | ❌ | `POST /api/school-registrations` | **MISSING** |
| Payment Link (Schools) | ❌ | `POST /api/payment/link/school` | **MISSING** |
| School Payment Webhook | ❌ | `POST /api/webhook/paystack/school` | **MISSING** |
| School Reviews | ✅ | `GET /api/reviews/business/{id}` | |
| Write School Review | ✅ | `POST /api/reviews` | |
| Update Registration Status | ❌ | `PATCH /api/school-registrations/{id}/status` | **MISSING** |

---

## 🌱 SUSTAINABILITY SECTION

| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Sustainability Categories | ✅ | `GET /api/categories?type=sustainability` | |
| Get Sustainability Services | 🟡 | `GET /api/business?category=sustainability` | Need avg ratings |
| Get Single Service | ✅ | `GET /api/business/{id}` | |
| Service Reviews | ✅ | `GET /api/reviews/business/{id}` | |
| Write Service Review | ✅ | `POST /api/reviews` | |

---

## 🎵 MUSIC SECTION

| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Music Categories | ✅ | `GET /api/categories?type=music` | |
| Get Music Services | 🟡 | `GET /api/business?category=music` | Need avg ratings |
| Get Single Service | ✅ | `GET /api/business/{id}` | |
| Service Reviews | ✅ | `GET /api/reviews/business/{id}` | |
| Write Service Review | ✅ | `POST /api/reviews` | |

---

## 👤 ACCOUNT MANAGEMENT

### Profile & Settings
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Edit User Profile | ❌ | `PUT /api/profile` | **MISSING** |
| Update Password | ❌ | `PUT /api/password` | **MISSING** |
| Get User Orders | ✅ | `GET /api/orders` | |
| Get Single Order | ✅ | `GET /api/orders/{id}` | |
| Write Order Review | ✅ | `POST /api/reviews` | |
| Order Tracking Status | ❌ | `GET /api/orders/{id}/tracking` | **MISSING** |

### Address Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Addresses | ❌ | `GET /api/addresses` | **MISSING** |
| Get Single Address | ❌ | `GET /api/addresses/{id}` | **MISSING** |
| Edit Address | ❌ | `PUT /api/addresses/{id}` | **MISSING** |

### Notifications & Content
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Notifications | ✅ | `GET /api/notifications` | |
| Update Notification Preferences | ❌ | `PUT /api/notification-preferences` | **MISSING** |
| Get Social Links | ✅ | `GET /api/connect` | |
| Get FAQs | ✅ | `GET /api/faqs` | |

---

## ❤️ WISHLIST

| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Wishlist Items | ✅ | `GET /api/wishlist` | Products & services |
| Add to Wishlist | ✅ | `POST /api/wishlist` | |
| Remove from Wishlist | ✅ | `DELETE /api/wishlist` | |

---

## 📝 BLOG SECTION

| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get All Posts | ✅ | `GET /api/blogs` | Paginated (10 per page) |
| Get Single Post | ✅ | `GET /api/blogs/{slug}` | |
| Get Related Posts | ❌ | | **MISSING** - Max 5 related posts |
| Get Favorited Posts | ❌ | `GET /api/blogs/favorites` | **MISSING** |
| Blog Search | ✅ | `GET /api/blogs/search` | |

---

## 🏪 SELLER MANAGEMENT

### Seller Profile
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Seller Profile | ✅ | `GET /api/seller/profile` | All required fields present |
| Create Seller Profile | ✅ | `POST /api/seller/profile` | |
| Edit Seller Profile | ✅ | `PUT /api/seller/profile` | |
| Delete Seller Profile | ✅ | `DELETE /api/seller/profile` | |
| Upload Documents | ✅ | `POST /api/seller/profile/upload` | |

### Product Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get All Products | ✅ | `GET /api/products` | |
| Get Single Product | ✅ | `GET /api/products/{id}` | |
| Create Product | ✅ | `POST /api/products` | |
| Edit Product | ✅ | `PUT /api/products/{id}` | |
| Delete Product | ✅ | `DELETE /api/products/{id}` | |

### Order Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Seller Orders | ✅ | `GET /api/orders` | |
| Get Single Order | ✅ | `GET /api/orders/{id}` | |

---

## 🏢 BUSINESS PROFILES

### Business Categories
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get Business Categories | ✅ | Via existing category system | |

### Business Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Create Beauty Business | ✅ | `POST /api/business` | |
| Create Brand Business | ✅ | `POST /api/business` | |
| Create School Business | ✅ | `POST /api/business` | |
| Create Music Business | ✅ | `POST /api/business` | |
| Get User Businesses | ✅ | `GET /api/business` | |
| Edit Business | ✅ | `PUT /api/business/{id}` | |
| Deactivate Business | ✅ | `PATCH /api/business/{id}/deactivate` | |
| Delete Business | ✅ | `DELETE /api/business/{id}` | |
| Upload Business Files | ✅ | `POST /api/business/{id}/upload` | |
| Manage Subscription | ❌ | `POST /api/business/{id}/subscription` | **MISSING** |

---

## 👨‍💼 ADMIN DASHBOARD

### Authentication
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Admin Login | ✅ | `POST /api/admin/login` | Password: `Admin@1234` |
| Admin Profile | ✅ | `GET /api/admin/profile` | |
| Admin Logout | ✅ | `POST /api/admin/logout` | |
| Create Admin | ✅ | `POST /api/admin/create` | For initial setup |

### Dashboard Overview
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Overview Stats | ✅ | `GET /api/admin/dashboard/overview` | All required statistics |
| Pending Sellers | ✅ | `GET /api/admin/pending/sellers` | |
| Pending Products | ✅ | `GET /api/admin/pending/products` | |
| New Orders | ✅ | `GET /api/admin/orders` | |
| Pending Businesses | ✅ | `GET /api/admin/business/{category}` | |

### User Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get All Users | ✅ | `GET /api/admin/users` | Paginated |

### Market Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Get All Sellers | ✅ | `GET /api/admin/market/sellers` | |
| Get Single Seller | ❌ | `GET /api/admin/sellers/{id}` | **MISSING** |
| Approve/Reject Seller | ✅ | `PATCH /api/seller/{id}/approve` | |
| Get All Products | ✅ | `GET /api/admin/market/products` | |
| Get Single Product | ❌ | `GET /api/admin/products/{id}` | **MISSING** |
| Approve/Reject Product | ✅ | `PATCH /api/product/{id}/approve` | |
| Get Single Order | ✅ | `GET /api/admin/order/{id}` | |
| Update Order Status | ✅ | `PATCH /api/admin/order/{id}/status` | |

### Business Category Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Beauty Businesses | ✅ | `GET /api/admin/business/beauty` | |
| Brands Businesses | ✅ | `GET /api/admin/business/brand` | |
| Schools Businesses | ✅ | `GET /api/admin/business/school` | |
| Music Businesses | ✅ | `GET /api/admin/business/music` | |
| Get Single Business | ✅ | `GET /api/admin/business/{category}/{id}` | |
| Update Business Status | ✅ | `PATCH /api/admin/business/{category}/{id}/status` | |

### Content Management
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Blog Management | ✅ | `GET/POST/PUT/DELETE /api/admin/blogs` | Full CRUD |
| Toggle Blog Publish | ✅ | `PATCH /api/admin/blogs/{id}/toggle-publish` | |

### Missing Admin Features
| Feature | Status | Route | Notes |
|---------|---------|--------|--------|
| Adverts Management | ❌ | `GET/POST/PUT/DELETE /api/admin/adverts` | **MISSING** |
| Sustainability Management | ❌ | `GET/POST/PUT/PATCH /api/admin/sustainability` | **MISSING** |
| Admin Notifications | ❌ | `GET /api/admin/notifications` | **MISSING** |
| Admin Settings | ❌ | `PUT /api/admin/settings` | **MISSING** |

---

## 🔥 HIGH PRIORITY MISSING ENDPOINTS

### Critical User Features
1. ❌ **Product Search** - `GET /api/products/search`
2. ❌ **Address Management** - Full CRUD for user addresses
3. ❌ **Payment Integration** - Paystack payment links & webhooks
4. ❌ **User Profile Edit** - `PUT /api/profile`
5. ❌ **Password Update** - `PUT /api/password`
6. ❌ **Product Suggestions** - "You may also like" feature

### Business Features
7. ❌ **School Registration System** - Registration & payment flow
8. ❌ **Subscription Management** - Business subscription handling
9. ❌ **Order Tracking** - Tracking status for orders

### Admin Features
10. ❌ **Adverts Management** - Complete CRUD system
11. ❌ **Sustainability Business Management** - Admin interface
12. ❌ **Admin Settings & Profile Update**

### Enhancements Needed
13. 🔄 **Seller Profile Enhancement** - Add selling_since, total_sales, avg_rating
14. 🔄 **Business Listings with Ratings** - Add avg_rating to business services
15. 🔄 **Blog Related Posts** - Max 5 related posts feature
16. 🔄 **Notification Preferences** - User notification settings

---

## 📊 IMPLEMENTATION SUMMARY

- **✅ Implemented: 85+ endpoints** 
- **❌ Missing: 16 critical endpoints**
- **🔄 Needs Enhancement: 4 features**

### Next Development Phase Priority:
1. **Payment System Integration** (Paystack)
2. **Address Management System**  
3. **Product Search & Suggestions**
4. **User Profile Management**
5. **Admin Content Management** (Adverts, Sustainability)
6. **School Registration System**

---

*Last Updated: August 2025*
*Use this guide to track progress and plan development sprints.*
