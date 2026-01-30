# Admin Dashboard

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

---

### GET /admin/dashboard/overview
Retrieve comprehensive dashboard overview statistics including users, revenue, businesses, and sellers.

**Query Parameters:**
- None

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "total_users": 1250,
    "total_revenue": 5750000.00,
    "total_businesses": 45,
    "total_sellers": 120,
    "market_revenue": 3250000.00
  }
}
```

**Field Descriptions:**
- `total_users` — Total number of registered users
- `total_revenue` — Sum of all paid orders (NGN)
- `total_businesses` — Total number of business profiles across all categories
- `total_sellers` — Total number of active sellers
- `market_revenue` — Revenue from marketplace orders only (products sold by sellers)
