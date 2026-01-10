# Admin Content Management Endpoints

## Admin Blog Management

### 1. Get All Blogs (Admin View)
**GET** `/api/admin/blogs`
**Middleware:** `admin.auth`

**Query Parameters:**
- `published`: Filter by published status (true/false)
- `page`: Page number for pagination

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "The Future of African Fashion",
        "slug": "future-of-african-fashion",
        "body": "<p>Full blog content here...</p>",
        "excerpt": "Auto-generated excerpt from content",
        "category": "fashion",
        "featured_image": "https://example.com/images/african-fashion.jpg",
        "is_published": true,
        "published_at": "2025-01-20T10:00:00.000000Z",
        "reading_time": 5,
        "admin_id": 1,
        "created_at": "2025-01-19T15:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      }
    ],
    "per_page": 15,
    "total": 25
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 2. Create New Blog Post
**POST** `/api/admin/blogs`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "title": "Sustainable Fashion in Nigeria",
  "body": "<p>Nigeria's fashion industry is embracing sustainability...</p><p>Local designers are creating eco-friendly collections...</p>",
  "featured_image": "https://example.com/images/sustainable-fashion.jpg",
  "published": true
}
```

**Validation Rules:**
- `title`: required|string|max:255
- `body`: required|string
- `featured_image`: nullable|url
- `published`: boolean

**Response (201):**
```json
{
  "status": "success",
  "message": "Blog created successfully",
  "data": {
    "id": 2,
    "title": "Sustainable Fashion in Nigeria",
    "slug": "sustainable-fashion-in-nigeria",
    "body": "<p>Nigeria's fashion industry is embracing sustainability...</p>",
    "excerpt": "Nigeria's fashion industry is embracing sustainability...",
    "category": null,
    "featured_image": "https://example.com/images/sustainable-fashion.jpg",
    "is_published": true,
    "published_at": "2025-01-20T16:00:00.000000Z",
    "reading_time": 3,
    "admin_id": 1,
    "created_at": "2025-01-20T16:00:00.000000Z",
    "updated_at": "2025-01-20T16:00:00.000000Z",
    "admin": {
      "id": 1,
      "firstname": "Admin",
      "lastname": "User"
    }
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 3. Get Single Blog (Admin View)
**GET** `/api/admin/blogs/{id}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "title": "The Future of African Fashion",
    "slug": "future-of-african-fashion",
    "body": "<p>Full blog content with HTML formatting...</p>",
    "excerpt": "Auto-generated excerpt from content",
    "category": "fashion",
    "featured_image": "https://example.com/images/african-fashion.jpg",
    "is_published": true,
    "published_at": "2025-01-20T10:00:00.000000Z",
    "reading_time": 5,
    "admin_id": 1,
    "created_at": "2025-01-19T15:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z",
    "admin": {
      "id": 1,
      "firstname": "Admin",
      "lastname": "User"
    }
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 4. Update Blog Post
**PUT** `/api/admin/blogs/{id}`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "title": "Updated: The Future of African Fashion",
  "body": "<p>Updated content with new information...</p>",
  "featured_image": "https://example.com/images/african-fashion-updated.jpg",
  "published": true
}
```

**Validation Rules:**
- `title`: required|string|max:255
- `body`: required|string
- `featured_image`: nullable|url
- `published`: boolean

**Response (200):**
```json
{
  "status": "success",
  "message": "Blog updated successfully",
  "data": {
    "id": 1,
    "title": "Updated: The Future of African Fashion",
    "slug": "updated-the-future-of-african-fashion",
    "body": "<p>Updated content with new information...</p>",
    "featured_image": "https://example.com/images/african-fashion-updated.jpg",
    "is_published": true,
    "published_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T17:00:00.000000Z",
    "admin": {
      "id": 1,
      "firstname": "Admin",
      "lastname": "User"
    }
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 5. Delete Blog Post
**DELETE** `/api/admin/blogs/{id}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Blog deleted successfully"
}
```

**Test Coverage:** 🔴 Not Tested

---

### 6. Toggle Blog Publication Status
**PATCH** `/api/admin/blogs/{id}/toggle-publish`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200) - Publishing:**
```json
{
  "status": "success",
  "message": "Blog published successfully",
  "data": {
    "id": 1,
    "title": "The Future of African Fashion",
    "slug": "future-of-african-fashion",
    "is_published": true,
    "published_at": "2025-01-20T17:30:00.000000Z",
    "updated_at": "2025-01-20T17:30:00.000000Z",
    "admin": {
      "id": 1,
      "firstname": "Admin",
      "lastname": "User"
    }
  }
}
```

**Response (200) - Unpublishing:**
```json
{
  "status": "success",
  "message": "Blog unpublished successfully",
  "data": {
    "id": 1,
    "title": "The Future of African Fashion",
    "is_published": false,
    "published_at": null,
    "updated_at": "2025-01-20T17:45:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Test Coverage Summary

- **Admin Blog Management:** 🔴 0/6 endpoints tested (0%)

**Total Admin Content Management:** 🔴 0/6 endpoints tested (0%)

---
*Last Updated: January 2025*