# Admin Business Directory Management

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

**Supported Categories:** `school`, `afro_beauty_services`

---

### GET /admin/business/{category}
List business profiles by category with optional status filtering.

**Path Parameters:**
- `category` (string, required) — One of: `school`, `afro_beauty_services`

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `per_page` (integer, optional) — Items per page (default: 15)
- `store_status` (string, optional) — Filter by status: `pending`, `approved`, `deactivated`

**Request Example:**
```
GET /admin/business/school?page=1&store_status=pending
```

**Response (200):**
```json
{
  "status": "success",
  "message": "school businesses retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 10,
        "category": "school",
        "business_name": "Lagos High School",
        "description": "Premier secondary school in Lagos",
        "email": "admin@lagoshs.edu.ng",
        "phone": "+2348012345678",
        "website": "https://www.lagoshs.edu.ng",
        "address": "123 Education Road, Lagos",
        "city": "Lagos",
        "state": "Lagos",
        "country": "Nigeria",
        "logo_url": "https://cdn.example.com/logos/lagoshs.jpg",
        "banner_url": "https://cdn.example.com/banners/lagoshs.jpg",
        "store_status": "approved",
        "rejection_reason": null,
        "rating": 4.8,
        "created_at": "2025-06-26T10:30:00Z",
        "updated_at": "2025-06-29T14:25:00Z",
        "user": {
          "id": 10,
          "firstname": "John",
          "lastname": "Doe",
          "email": "john@example.com"
        }
      }
    ],
    "first_page_url": "http://localhost:8000/api/admin/business/school?page=1",
    "from": 1,
    "last_page": 2,
    "last_page_url": "http://localhost:8000/api/admin/business/school?page=2",
    "links": [...],
    "next_page_url": "http://localhost:8000/api/admin/business/school?page=2",
    "path": "http://localhost:8000/api/admin/business/school",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 28
  }
}
```

**Error Response (422) — Invalid Category:**
```json
{
  "status": "error",
  "message": "Invalid business category"
}
```

---

### GET /admin/business/{category}/{id}
Get detailed information for a specific business profile.

**Path Parameters:**
- `category` (string, required) — One of: `school`, `afro_beauty_services`
- `id` (integer, required) — Business profile ID

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profile retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 10,
    "category": "school",
    "business_name": "Lagos High School",
    "description": "Premier secondary school in Lagos with excellent academics",
    "email": "admin@lagoshs.edu.ng",
    "phone": "+2348012345678",
    "website": "https://www.lagoshs.edu.ng",
    "address": "123 Education Road, Lagos",
    "city": "Lagos",
    "state": "Lagos",
    "country": "Nigeria",
    "logo_url": "https://cdn.example.com/logos/lagoshs.jpg",
    "banner_url": "https://cdn.example.com/banners/lagoshs.jpg",
    "store_status": "approved",
    "rejection_reason": null,
    "rating": 4.8,
    "verification_status": "verified",
    "created_at": "2025-06-26T10:30:00Z",
    "updated_at": "2025-06-29T14:25:00Z",
    "user": {
      "id": 10,
      "firstname": "John",
      "lastname": "Doe",
      "email": "john@example.com",
      "phone": "+2348012345678"
    }
  }
}
```

---

### PATCH /admin/business/{category}/{id}/status
Update the status of a business profile.

**Path Parameters:**
- `category` (string, required) — One of: `school`, `afro_beauty_services`
- `id` (integer, required) — Business profile ID

**Request:**
```json
{
  "store_status": "approved",
  "rejection_reason": null
}
```

**OR (for deactivation with reason):**
```json
{
  "store_status": "deactivated",
  "rejection_reason": "Policy violation - inappropriate content"
}
```

**Valid statuses:**
- `pending` — Awaiting approval
- `approved` — Business is approved and visible
- `deactivated` — Business is deactivated/suspended

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profile status updated to approved successfully",
  "data": {
    "id": 1,
    "business_name": "Lagos High School",
    "category": "school",
    "store_status": "approved",
    "rejection_reason": null,
    "updated_at": "2025-06-29T14:25:00Z"
  }
}
```

**Error Response (422) — Invalid Category:**
```json
{
  "status": "error",
  "message": "Invalid business category"
}
```

**Error Response (404) — Not Found:**
```json
{
  "message": "No query results found for model [App\\Models\\BusinessProfile] 999"
}
```

**Note:** When business status changes to `approved` or `deactivated`, email and push notifications are sent to the business owner with update details and rejection reason (if applicable).
