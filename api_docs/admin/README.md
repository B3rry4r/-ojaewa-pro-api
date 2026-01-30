# Admin API Documentation

Complete API reference for the Oja Ewa Pro Admin Dashboard. All admin endpoints require Sanctum authentication with admin token abilities.

## Quick Start

### Authentication
1. Login with admin credentials: `POST /api/admin/login`
2. Receive authentication token (valid for 30 days)
3. Include token in all subsequent requests: `Authorization: Bearer {token}`
4. Logout: `POST /api/admin/logout`

See [auth.md](./auth.md) for complete authentication details.

---

## Endpoint Categories

### 📊 Dashboard & Analytics
- **File:** [dashboard.md](./dashboard.md)
- **Endpoints:**
  - `GET /admin/dashboard/overview` — Overview statistics (users, revenue, sellers, businesses)

### 👥 User Management
- **File:** [users.md](./users.md)
- **Endpoints:**
  - `GET /admin/users` — List all users with search and pagination

### 🏪 Market Management (Sellers & Products)
- **File:** [market.md](./market.md)
- **Sellers:**
  - `GET /admin/pending/sellers` — List sellers awaiting approval
  - `GET /admin/market/sellers` — List all sellers with filtering
  - `GET /admin/sellers/{id}` — Get seller details
  - `PATCH /admin/seller/{id}/approve` — Approve/reject seller
  - `PATCH /admin/market/seller/{id}/status` — Activate/deactivate seller

- **Products:**
  - `GET /admin/pending/products` — List products awaiting approval
  - `GET /admin/market/products` — List all products with filtering
  - `GET /admin/products/{id}` — Get product details
  - `PATCH /admin/product/{id}/approve` — Approve/reject product
  - `PATCH /admin/market/product/{id}/status` — Update product status

### 📦 Order Management
- **File:** [orders.md](./orders.md)
- **Endpoints:**
  - `GET /admin/orders` — List all orders with status filtering
  - `GET /admin/order/{id}` — Get order details with items
  - `PATCH /admin/order/{id}/status` — Update order status and tracking

### 🏢 Business Directory Management
- **File:** [business.md](./business.md)
- **Supported Categories:** `school`, `afro_beauty_services`
- **Endpoints:**
  - `GET /admin/business/{category}` — List businesses by category
  - `GET /admin/business/{category}/{id}` — Get business profile
  - `PATCH /admin/business/{category}/{id}/status` — Update business status

### 📝 Content Management (Blogs & Adverts)
- **File:** [content.md](./content.md)
- **Blogs:**
  - `GET /admin/blogs` — List all blogs
  - `POST /admin/blogs` — Create new blog
  - `GET /admin/blogs/{id}` — Get blog details
  - `PUT /admin/blogs/{id}` — Update blog
  - `DELETE /admin/blogs/{id}` — Delete blog
  - `PATCH /admin/blogs/{id}/toggle-publish` — Toggle publish status

- **Adverts:**
  - `GET /admin/adverts` — List adverts with filtering
  - `POST /admin/adverts` — Create advert
  - `PUT /admin/adverts/{advert}` — Update advert
  - `DELETE /admin/adverts/{advert}` — Delete advert

### 🎓 School Registration Management
- **File:** [school_registrations.md](./school_registrations.md)
- **Endpoints:**
  - `GET /admin/school-registrations` — List school registrations
  - `GET /admin/school-registrations/{schoolRegistration}` — Get details
  - `PUT /admin/school-registrations/{schoolRegistration}` — Update status
  - `DELETE /admin/school-registrations/{schoolRegistration}` — Delete registration

### 🌱 Sustainability Initiatives
- **File:** [sustainability.md](./sustainability.md)
- **Endpoints:**
  - `GET /admin/sustainability` — List initiatives
  - `POST /admin/sustainability` — Create initiative
  - `PUT /admin/sustainability/{sustainabilityInitiative}` — Update initiative
  - `DELETE /admin/sustainability/{sustainabilityInitiative}` — Delete initiative

### 🔔 Notifications & Settings
- **File:** [notifications_settings.md](./notifications_settings.md)
- **Endpoints:**
  - `POST /admin/notifications/send` — Send notifications to users
  - `GET /admin/settings` — Get all app settings
  - `PUT /admin/settings` — Update app settings

---

## Authentication

All endpoints (except login/create) require:
```
Authorization: Bearer {token}
```

Where `{token}` is obtained from the login endpoint.

**Important:** Admin tokens include `admin` ability and are different from regular user tokens.

---

## Response Format

All responses follow this standard format:

**Success Response:**
```json
{
  "status": "success",
  "message": "Optional message",
  "data": { /* response data */ }
}
```

**Error Response:**
```json
{
  "status": "error",
  "message": "Error message",
  "errors": { /* validation errors */ }
}
```

---

## Pagination

List endpoints use cursor-based pagination with the following structure:

**Query Parameters:**
- `page` — Page number (default: 1)
- `per_page` — Items per page (default varies by endpoint)

**Response includes:**
```json
{
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": "...",
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "path": "...",
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

---

## Notifications

When certain actions are performed, notifications are automatically sent to affected users:

- **Seller Approval/Rejection** — Email + push notification
- **Product Approval/Rejection** — Email + push notification
- **Business Status Update** — Email + push notification
- **Order Status Update** — Email + push notification with tracking info
- **Blog Published** — Push notification to all users
- **Admin Message Sent** — Email + push notification

---

## Common Query Parameters

### Filtering
- `status` — Filter by status (varies by resource)
- `search` — Search by name, email, phone (where supported)
- `category` — Filter by category (business, sustainability)

### Pagination
- `page` — Page number (default: 1)
- `per_page` — Items per page (varies by endpoint)

### Sorting
- Most endpoints sort by `created_at` in descending order by default

---

## Rate Limiting

Admin endpoints are rate limited to prevent abuse:
- 100 requests per minute per admin account
- Excessive requests will return 429 Too Many Requests

---

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Server Error |

---

## Integration Checklist

- [ ] Implement admin login flow
- [ ] Store and use authentication token
- [ ] Implement dashboard overview
- [ ] Build user management interface
- [ ] Create seller approval workflow
- [ ] Create product approval workflow
- [ ] Build order management interface
- [ ] Implement business directory management
- [ ] Build content management (blogs/adverts)
- [ ] Implement settings management
- [ ] Add notifications system

---

## API Base URL

```
https://api.example.com/api
```

All endpoint examples use `/api/admin/...` prefix.
