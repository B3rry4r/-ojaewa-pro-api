# Oja Ewa API Documentation

Complete API documentation for all 117 endpoints in the Oja Ewa Laravel backend.

## 📚 Documentation Files

### 1. [Authentication](authentication.md) - 9 Endpoints
- User registration and login
- Password reset flow
- Google OAuth integration
- Admin authentication
- Profile management

### 2. [User Management](user_management.md) - 10 Endpoints
- User profile CRUD
- Address management (5 endpoints)
- Notification preferences

### 3. [Products](products.md) - 9 Endpoints
- Product CRUD operations
- Product search and filtering
- Product suggestions/recommendations
- Review system

### 4. [Orders & Payments](orders_payments.md) - 10 Endpoints
- Order creation and management
- Order tracking
- Paystack payment integration
- Payment webhooks
- School registration and payment

### 5. [Business & Seller](business_seller.md) - 13 Endpoints
- Seller profile management
- Business profile CRUD (beauty, brand, school, music)
- File uploads
- Subscription management

### 6. [Content, Blogs & FAQs](content_blogs_faqs.md) - 21 Endpoints
- Blog listing and search (4 endpoints)
- FAQ management (4 endpoints)
- Category management (3 endpoints)
- Wishlist operations (3 endpoints)
- Blog favorites (3 endpoints)
- Connect/Social media info (4 endpoints)

### 7. [Notifications](notifications.md) - 8 Endpoints
- Notification listing and filtering
- Mark as read/unread
- Notification preferences
- Push notification integration

### 8. [Admin](admin.md) - 38 Endpoints
- Dashboard overview
- User management
- Seller approval (5 endpoints)
- Product approval (5 endpoints)
- Order management (3 endpoints)
- Business management (3 endpoints)
- Content management (5 endpoints)
- Advertisement management (4 endpoints)
- School registrations (4 endpoints)
- Sustainability initiatives (4 endpoints)
- System settings (2 endpoints)

### 9. [Real-Time Implementation](realtime_implementation.md)
- Push notification setup (Pusher Beams)
- No cart/bag system explanation
- Frontend integration guide
- Notification triggers

---

## 🚀 Quick Start

### Base URL
```
Production: https://your-production-url.com/api
Development: http://localhost:8000/api
```

### Authentication
Most endpoints require authentication using Bearer token:

```bash
curl -X GET https://api.example.com/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Get Token
```bash
# User Login
curl -X POST https://api.example.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'

# Returns: { "token": "...", "user": {...} }
```

---

## 🔑 Key Concepts

### No Cart System
- **Important:** There is NO server-side cart/bag management
- Frontend must manage cart state locally
- Create order directly: `POST /api/orders` with items array

### Authentication Middleware
- `auth:sanctum` - Requires authenticated user
- `admin` - Requires admin user with admin token ability

### Polymorphic Relationships
- **Wishlist:** Can save products OR business profiles
- **Reviews:** Can review products OR orders
- Use `_type` and `_id` fields (e.g., `wishlistable_type`, `reviewable_id`)

### Pagination
Most list endpoints return paginated results:
```json
{
  "data": [...],
  "links": { "first": "...", "last": "...", "prev": "...", "next": "..." },
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 50
  }
}
```

### Product Status Flow
```
pending → approved (by admin)
pending → rejected (by admin with reason)
```

### Order Status Flow
```
pending → paid → processing → shipped → delivered
   ↓
cancelled (with cancellation_reason)
```

---

## 📱 Real-Time Features

### Push Notifications (Pusher Beams)
- **Technology:** Pusher Beams HTTP API
- **NOT WebSockets:** One-way push notifications only
- **Channels:**
  - `user-{id}` - User-specific notifications
  - `all-users` - Broadcast notifications

### Automatic Notifications Sent For:
- ✅ Order creation
- ✅ Order status updates
- ✅ Business/seller approval
- ✅ Product approval
- ✅ Blog post publication

See [Real-Time Implementation](realtime_implementation.md) for setup guide.

---

## 🗄️ Database Schema

### Key Tables
- `users` - User accounts
- `admins` - Admin accounts
- `seller_profiles` - Seller information
- `business_profiles` - Business listings (beauty, brand, school, music)
- `products` - Marketplace products
- `orders` - Order records
- `order_items` - Order line items
- `reviews` - Polymorphic reviews
- `wishlists` - Polymorphic wishlist
- `notifications` - Push notifications
- `addresses` - User shipping addresses
- `blogs` - Blog posts
- `faqs` - FAQ entries
- `categories` - Hierarchical categories

---

## 🔍 Common Patterns

### Error Responses
```json
// 422 Validation Error
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}

// 401 Unauthorized
{
  "message": "Unauthenticated."
}

// 403 Forbidden
{
  "message": "This action is unauthorized."
}

// 404 Not Found
{
  "message": "Resource not found."
}
```

### Success Responses
```json
// Standard success
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": { ... }
}

// Create (201)
{
  "message": "Resource created successfully",
  "resource": { ... }
}
```

---

## 🛠️ For Developers

### Testing Endpoints
Use provided Postman collection or test with curl:

```bash
# Set token as environment variable
export TOKEN="your_token_here"

# Test authenticated endpoint
curl -H "Authorization: Bearer $TOKEN" \
  https://api.example.com/api/profile
```

### Building Integrations
1. **Read the specific documentation** for your endpoint
2. **Check authentication requirements** (public vs authenticated)
3. **Review validation rules** for request body
4. **Test with actual data** from your frontend
5. **Handle errors gracefully** with proper error messages

### Node.js AI Backend Integration
See `NODE_AI_BACKEND_SIMPLE_PLAN.md` for:
- Database access patterns
- API calling examples
- AI feature mappings
- Deployment guide

---

## 📊 Endpoint Summary

| Category | Public | Authenticated | Admin | Total |
|----------|--------|---------------|-------|-------|
| Authentication | 5 | 0 | 4 | 9 |
| User Management | 0 | 10 | 0 | 10 |
| Products | 0 | 9 | 0 | 9 |
| Orders & Payments | 3 | 7 | 0 | 10 |
| Business & Seller | 0 | 13 | 0 | 13 |
| Content & Blogs | 21 | 0 | 0 | 21 |
| Notifications | 0 | 8 | 0 | 8 |
| Admin | 0 | 0 | 38 | 38 |
| **Total** | **29** | **47** | **42** | **118** |

*Note: 1 endpoint may appear in multiple categories if it has public + authenticated variants*

---

## 📞 Support

For questions or issues with the API:
1. Check the specific endpoint documentation
2. Review `realtime_implementation.md` for push notification setup
3. See `NODE_AI_BACKEND_SIMPLE_PLAN.md` for AI backend integration

---

**Last Updated:** January 2024  
**API Version:** 1.0  
**Total Endpoints:** 117 (verified)
