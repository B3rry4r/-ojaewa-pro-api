# Admin User Management

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

---

### GET /admin/users
List all users with pagination and optional search filtering.

**Query Parameters:**
- `page` (integer, optional) — Page number for pagination (default: 1)
- `per_page` (integer, optional) — Number of items per page (default: 15)
- `search` (string, optional) — Search by firstname, lastname, email, or phone

**Request Example:**
```
GET /admin/users?page=1&search=john
```

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
        "email_verified_at": "2025-06-26T10:30:00Z",
        "created_at": "2025-06-26T10:30:00Z",
        "updated_at": "2025-06-26T10:30:00Z",
        "seller_profile_count": 1,
        "business_profiles_count": 2,
        "orders_count": 15,
        "name": "John Doe"
      },
      {
        "id": 2,
        "firstname": "Jane",
        "lastname": "Smith",
        "email": "jane.smith@example.com",
        "phone": "+2348087654321",
        "email_verified_at": "2025-06-27T14:20:00Z",
        "created_at": "2025-06-27T14:20:00Z",
        "updated_at": "2025-06-27T14:20:00Z",
        "seller_profile_count": 0,
        "business_profiles_count": 1,
        "orders_count": 8,
        "name": "Jane Smith"
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/admin/users?page=1",
      "last": "http://localhost:8000/api/admin/users?page=10",
      "prev": null,
      "next": "http://localhost:8000/api/admin/users?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 10,
      "path": "http://localhost:8000/api/admin/users",
      "per_page": 15,
      "to": 15,
      "total": 150
    }
  }
}
```

**Field Descriptions:**
- `seller_profile_count` — Number of seller profiles created by this user
- `business_profiles_count` — Number of business profiles created by this user
- `orders_count` — Number of orders placed by this user
- `name` — Computed full name (firstname + lastname)
