# Admin School Registrations

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

---

### GET /admin/school-registrations
List all school registrations with optional status filtering and pagination.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `per_page` (integer, optional) — Items per page (default: 10)
- `status` (string, optional) — Filter by status: `pending`, `processing`, `approved`, `rejected`

**Request Example:**
```
GET /admin/school-registrations?page=1&status=pending&per_page=20
```

**Response (200):**
```json
{
  "status": "success",
  "message": "School registrations retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "country": "Nigeria",
        "full_name": "Mr. Okafor",
        "phone_number": "+2348012345678",
        "state": "Lagos",
        "city": "Lagos",
        "address": "123 Education Avenue, Lagos",
        "status": "pending",
        "payment_reference": null,
        "payment_data": null,
        "submitted_at": "2025-06-28T10:30:00Z",
        "created_at": "2025-06-28T10:30:00Z",
        "updated_at": "2025-06-28T10:30:00Z"
      }
    ],
    "first_page_url": "http://localhost:8000/api/admin/school-registrations?page=1",
    "from": 1,
    "last_page": 3,
    "links": [...],
    "path": "http://localhost:8000/api/admin/school-registrations",
    "per_page": 10,
    "to": 10,
    "total": 28
  }
}
```

---

### GET /admin/school-registrations/{schoolRegistration}
Get detailed information for a specific school registration.

**Path Parameters:**
- `schoolRegistration` (integer, required) — School registration ID

**Response (200):**
```json
{
  "status": "success",
  "message": "School registration retrieved successfully",
  "data": {
    "id": 1,
    "country": "Nigeria",
    "full_name": "Mr. Okafor",
    "phone_number": "+2348012345678",
    "state": "Lagos",
    "city": "Lagos",
    "address": "123 Education Avenue, Lagos",
    "status": "pending",
    "payment_reference": null,
    "payment_data": null,
    "submitted_at": "2025-06-28T10:30:00Z",
    "created_at": "2025-06-28T10:30:00Z",
    "updated_at": "2025-06-28T10:30:00Z"
  }
}
```

---

### PUT /admin/school-registrations/{schoolRegistration}
Update the status of a school registration.

**Path Parameters:**
- `schoolRegistration` (integer, required) — School registration ID

**Request:**
```json
{
  "status": "approved"
}
```

**Valid statuses:**
- `pending` — Initial submission status
- `processing` — Under review
- `approved` — Registration approved
- `rejected` — Registration rejected

**Response (200):**
```json
{
  "status": "success",
  "message": "School registration status updated successfully",
  "data": {
    "id": 1,
    "full_name": "Mr. Okafor",
    "status": "approved",
    "updated_at": "2025-06-29T14:25:00Z"
  }
}
```

---

### DELETE /admin/school-registrations/{schoolRegistration}
Delete a school registration record.

**Path Parameters:**
- `schoolRegistration` (integer, required) — School registration ID

**Response (200):**
```json
{
  "status": "success",
  "message": "School registration deleted successfully"
}
```
