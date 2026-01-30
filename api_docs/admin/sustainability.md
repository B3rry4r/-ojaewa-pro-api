# Admin Sustainability Initiatives

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

---

### GET /admin/sustainability
List all sustainability initiatives with optional filtering by category and status.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `per_page` (integer, optional) — Items per page (default: 10)
- `category` (string, optional) — Filter by category: `environmental`, `social`, `economic`, `governance`
- `status` (string, optional) — Filter by status: `active`, `completed`, `planned`, `cancelled`

**Request Example:**
```
GET /admin/sustainability?page=1&category=environmental&status=active
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Sustainability initiatives retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Tree Planting Initiative 2025",
        "description": "Community-driven tree planting program to combat deforestation",
        "image_url": "https://cdn.example.com/initiatives/trees.jpg",
        "category": "environmental",
        "category_id": 5,
        "status": "active",
        "target_amount": 500000.00,
        "current_amount": 250000.00,
        "impact_metrics": "10,000 trees planted, 50 tons CO2 offset",
        "start_date": "2025-01-15",
        "end_date": "2025-12-31",
        "partners": ["NGO A", "Community Group B"],
        "participant_count": 250,
        "progress_notes": "On track for Q2 targets",
        "created_by": 1,
        "created_at": "2025-01-15T10:30:00Z",
        "updated_at": "2025-06-29T14:00:00Z",
        "admin": {
          "id": 1,
          "firstname": "John",
          "lastname": "Doe"
        }
      }
    ],
    "first_page_url": "http://localhost:8000/api/admin/sustainability?page=1",
    "from": 1,
    "last_page": 2,
    "links": [...],
    "path": "http://localhost:8000/api/admin/sustainability",
    "per_page": 10,
    "to": 10,
    "total": 15
  }
}
```

---

### POST /admin/sustainability
Create a new sustainability initiative.

**Request:**
```json
{
  "title": "Tree Planting Initiative 2025",
  "description": "Community-driven tree planting program to combat deforestation",
  "image_url": "https://cdn.example.com/initiatives/trees.jpg",
  "category": "environmental",
  "category_id": 5,
  "status": "active",
  "target_amount": 500000.00,
  "current_amount": 0,
  "impact_metrics": "10,000 trees planted, 50 tons CO2 offset",
  "start_date": "2025-01-15",
  "end_date": "2025-12-31",
  "partners": ["NGO A", "Community Group B"],
  "participant_count": 0,
  "progress_notes": "Initiative just started"
}
```

**Valid categories:** `environmental`, `social`, `economic`, `governance`

**Valid statuses:** `active`, `completed`, `planned`, `cancelled`

**Response (201):**
```json
{
  "status": "success",
  "message": "Sustainability initiative created successfully",
  "data": {
    "id": 1,
    "title": "Tree Planting Initiative 2025",
    "description": "Community-driven tree planting program...",
    "category": "environmental",
    "status": "active",
    "target_amount": 500000.00,
    "current_amount": 0,
    "created_by": 1,
    "created_at": "2025-06-29T14:00:00Z",
    "admin": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe"
    }
  }
}
```

---

### PUT /admin/sustainability/{sustainabilityInitiative}
Update an existing sustainability initiative.

**Path Parameters:**
- `sustainabilityInitiative` (integer, required) — Initiative ID

**Request:**
```json
{
  "title": "Tree Planting Initiative 2025 - Extended",
  "status": "active",
  "current_amount": 350000.00,
  "participant_count": 350,
  "progress_notes": "Excellent progress - halfway to target"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Sustainability initiative updated successfully",
  "data": {
    "id": 1,
    "title": "Tree Planting Initiative 2025 - Extended",
    "status": "active",
    "current_amount": 350000.00,
    "participant_count": 350,
    "updated_at": "2025-06-29T15:00:00Z"
  }
}
```

---

### DELETE /admin/sustainability/{sustainabilityInitiative}
Delete a sustainability initiative.

**Path Parameters:**
- `sustainabilityInitiative` (integer, required) — Initiative ID

**Response (200):**
```json
{
  "status": "success",
  "message": "Sustainability initiative deleted successfully"
}
```
