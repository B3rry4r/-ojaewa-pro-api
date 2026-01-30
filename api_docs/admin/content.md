# Admin Content Management

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

---

## Blogs Management

### GET /admin/blogs
List all blog posts with optional filtering by publication status.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `per_page` (integer, optional) — Items per page (default: 15)
- `published` (string, optional) — Filter by published status: `true` or `false`

**Request Example:**
```
GET /admin/blogs?page=1&published=true
```

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "5 Tips for Traditional Fashion",
        "slug": "5-tips-for-traditional-fashion",
        "body": "Traditional fashion has been gaining momentum...",
        "featured_image": "https://cdn.example.com/blogs/fashion.jpg",
        "admin_id": 1,
        "published_at": "2025-06-28T10:30:00Z",
        "created_at": "2025-06-28T10:30:00Z",
        "updated_at": "2025-06-28T10:30:00Z",
        "admin": {
          "id": 1,
          "firstname": "John",
          "lastname": "Doe"
        }
      }
    ],
    "first_page_url": "http://localhost:8000/api/admin/blogs?page=1",
    "from": 1,
    "last_page": 3,
    "links": [...],
    "path": "http://localhost:8000/api/admin/blogs",
    "per_page": 15,
    "to": 15,
    "total": 45
  }
}
```

---

### POST /admin/blogs
Create a new blog post.

**Request:**
```json
{
  "title": "5 Tips for Traditional Fashion",
  "body": "Traditional fashion has been gaining momentum in recent years...",
  "featured_image": "https://cdn.example.com/blogs/fashion.jpg",
  "published": true
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Blog created successfully",
  "data": {
    "id": 1,
    "title": "5 Tips for Traditional Fashion",
    "slug": "5-tips-for-traditional-fashion",
    "body": "Traditional fashion has been gaining momentum...",
    "featured_image": "https://cdn.example.com/blogs/fashion.jpg",
    "admin_id": 1,
    "published_at": "2025-06-28T10:30:00Z",
    "created_at": "2025-06-28T10:30:00Z",
    "updated_at": "2025-06-28T10:30:00Z",
    "admin": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe"
    }
  }
}
```

**Note:** When `published` is `true`, a push notification is sent to all users announcing the new blog post.

---

### GET /admin/blogs/{id}
Get detailed information for a specific blog post.

**Path Parameters:**
- `id` (integer, required) — Blog post ID

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "title": "5 Tips for Traditional Fashion",
    "slug": "5-tips-for-traditional-fashion",
    "body": "Traditional fashion has been gaining momentum...",
    "featured_image": "https://cdn.example.com/blogs/fashion.jpg",
    "admin_id": 1,
    "published_at": "2025-06-28T10:30:00Z",
    "created_at": "2025-06-28T10:30:00Z",
    "updated_at": "2025-06-28T10:30:00Z",
    "admin": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe"
    }
  }
}
```

---

### PUT /admin/blogs/{id}
Update an existing blog post.

**Path Parameters:**
- `id` (integer, required) — Blog post ID

**Request:**
```json
{
  "title": "5 Tips for Traditional Fashion (Updated)",
  "body": "Traditional fashion continues to evolve...",
  "featured_image": "https://cdn.example.com/blogs/fashion-updated.jpg",
  "published": true
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Blog updated successfully",
  "data": {
    "id": 1,
    "title": "5 Tips for Traditional Fashion (Updated)",
    "slug": "5-tips-for-traditional-fashion-updated",
    "body": "Traditional fashion continues to evolve...",
    "featured_image": "https://cdn.example.com/blogs/fashion-updated.jpg",
    "published_at": "2025-06-28T10:30:00Z",
    "updated_at": "2025-06-29T11:45:00Z"
  }
}
```

---

### DELETE /admin/blogs/{id}
Delete a blog post.

**Path Parameters:**
- `id` (integer, required) — Blog post ID

**Response (200):**
```json
{
  "status": "success",
  "message": "Blog deleted successfully"
}
```

---

### PATCH /admin/blogs/{id}/toggle-publish
Toggle the publication status of a blog post.

**Path Parameters:**
- `id` (integer, required) — Blog post ID

**Response (200):**
```json
{
  "status": "success",
  "message": "Blog published successfully",
  "data": {
    "id": 1,
    "title": "5 Tips for Traditional Fashion",
    "published_at": "2025-06-29T14:00:00Z",
    "updated_at": "2025-06-29T14:00:00Z"
  }
}
```

**OR (if unpublishing):**
```json
{
  "status": "success",
  "message": "Blog unpublished successfully",
  "data": {
    "id": 1,
    "title": "5 Tips for Traditional Fashion",
    "published_at": null,
    "updated_at": "2025-06-29T14:05:00Z"
  }
}
```

---

## Adverts Management

### GET /admin/adverts
List all adverts with optional filtering by status and position.

**Query Parameters:**
- `page` (integer, optional) — Page number (default: 1)
- `per_page` (integer, optional) — Items per page (default: 10)
- `status` (string, optional) — Filter by status: `active`, `inactive`, `scheduled`
- `position` (string, optional) — Filter by position: `banner`, `sidebar`, `footer`, `popup`

**Request Example:**
```
GET /admin/adverts?page=1&status=active&position=banner
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Adverts retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Summer Fashion Sale",
        "description": "Get 50% off on all traditional items",
        "image_url": "https://cdn.example.com/ads/summer.jpg",
        "action_url": "https://shop.example.com/summer-sale",
        "position": "banner",
        "status": "active",
        "priority": 10,
        "start_date": "2025-06-01",
        "end_date": "2025-08-31",
        "created_by": 1,
        "created_at": "2025-05-30T10:00:00Z",
        "updated_at": "2025-06-29T14:00:00Z",
        "admin": {
          "id": 1,
          "firstname": "John",
          "lastname": "Doe"
        }
      }
    ],
    "links": [...],
    "meta": {...}
  }
}
```

---

### POST /admin/adverts
Create a new advert.

**Request:**
```json
{
  "title": "Summer Fashion Sale",
  "description": "Get 50% off on all traditional items",
  "image_url": "https://cdn.example.com/ads/summer.jpg",
  "action_url": "https://shop.example.com/summer-sale",
  "position": "banner",
  "status": "active",
  "priority": 10,
  "start_date": "2025-06-01",
  "end_date": "2025-08-31"
}
```

**Valid positions:** `banner`, `sidebar`, `footer`, `popup`

**Valid statuses:** `active`, `inactive`, `scheduled`

**Response (201):**
```json
{
  "status": "success",
  "message": "Advert created successfully",
  "data": {
    "id": 1,
    "title": "Summer Fashion Sale",
    "description": "Get 50% off on all traditional items",
    "image_url": "https://cdn.example.com/ads/summer.jpg",
    "action_url": "https://shop.example.com/summer-sale",
    "position": "banner",
    "status": "active",
    "priority": 10,
    "start_date": "2025-06-01",
    "end_date": "2025-08-31",
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

### PUT /admin/adverts/{advert}
Update an existing advert.

**Path Parameters:**
- `advert` (integer, required) — Advert ID

**Request:**
```json
{
  "title": "Summer Fashion Sale - Extended",
  "description": "Get 50% off on all traditional items - extended!",
  "status": "active",
  "priority": 15,
  "end_date": "2025-09-30"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Advert updated successfully",
  "data": {
    "id": 1,
    "title": "Summer Fashion Sale - Extended",
    "description": "Get 50% off on all traditional items - extended!",
    "status": "active",
    "priority": 15,
    "end_date": "2025-09-30",
    "updated_at": "2025-06-29T15:00:00Z"
  }
}
```

---

### DELETE /admin/adverts/{advert}
Delete an advert.

**Path Parameters:**
- `advert` (integer, required) — Advert ID

**Response (200):**
```json
{
  "status": "success",
  "message": "Advert deleted successfully"
}
```
