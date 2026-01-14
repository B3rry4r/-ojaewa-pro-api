# Search & Filter Endpoints Documentation

This document covers all search and filter endpoints for Products, Business Profiles, and Sustainability Initiatives.

---

## Table of Contents
- [Products](#products)
- [Business Profiles](#business-profiles)
- [Sustainability Initiatives](#sustainability-initiatives)

---

## Products

### Search Products

**URL:** `GET /api/products/search`  
**Auth:** Required (Bearer token)

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `q` | string | **Yes** | Search term (1-255 chars). Searches in `name` and `description` |
| `gender` | string | No | Filter by gender: `male`, `female`, `unisex` |
| `style` | string | No | Filter by style (partial match) |
| `tribe` | string | No | Filter by tribe (partial match) |
| `price_min` | number | No | Minimum price filter |
| `price_max` | number | No | Maximum price filter |
| `per_page` | integer | No | Results per page (1-50, default: 10) |

#### Example Request
```bash
curl -X GET "https://api.example.com/api/products/search?q=Agbada&gender=female&price_min=100&price_max=5000&per_page=10" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

#### Response Payload
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 3,
        "seller_profile_id": 1,
        "name": "Traditional Agbada",
        "gender": "female",
        "style": "Agbada",
        "tribe": "Yoruba",
        "description": "Beautiful handcrafted Agbada...",
        "image": "https://images.unsplash.com/...",
        "size": "M",
        "processing_time_type": "days",
        "processing_days": 5,
        "price": "250.00",
        "status": "approved",
        "created_at": "2026-01-14T15:39:05.000000Z",
        "updated_at": "2026-01-14T15:39:05.000000Z",
        "deleted_at": null,
        "rejection_reason": null,
        "avg_rating": 4.5,
        "seller_profile": {
          "id": 1,
          "business_name": "Fashion House",
          "business_email": "seller@example.com",
          "city": "Lagos",
          "state": "Lagos"
        }
      }
    ],
    "first_page_url": "https://api.example.com/api/products/search?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "https://api.example.com/api/products/search?page=1",
    "links": [
      { "url": null, "label": "&laquo; Previous", "active": false },
      { "url": "...?page=1", "label": "1", "active": true },
      { "url": null, "label": "Next &raquo;", "active": false }
    ],
    "next_page_url": null,
    "path": "https://api.example.com/api/products/search",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

---

### Get Product Filters

**URL:** `GET /api/products/filters`  
**Auth:** Not required

#### Example Request
```bash
curl -X GET "https://api.example.com/api/products/filters" \
  -H "Accept: application/json"
```

#### Response Payload
```json
{
  "status": "success",
  "data": {
    "genders": ["female", "male", "unisex"],
    "styles": ["Kaftan", "Aso Oke", "Agbada", "Dashiki", "Boubou", "Gele", "Ankara"],
    "tribes": ["Fulani", "Hausa", "Ashanti", "Igbo", "Zulu", "Yoruba", "Tuareg"],
    "price_range": {
      "min": 89.81,
      "max": 467.04
    },
    "sort_options": [
      { "value": "newest", "label": "Newest First" },
      { "value": "price_asc", "label": "Price: Low to High" },
      { "value": "price_desc", "label": "Price: High to Low" },
      { "value": "popular", "label": "Most Popular" }
    ]
  }
}
```

---

## Business Profiles

### Get Single Business Profile (Public)

**URL:** `GET /api/business/{id}` or `GET /api/business/public/{id}`  
**Auth:** Not required

#### Example Request
```bash
curl -X GET "https://api.example.com/api/business/1" \
  -H "Accept: application/json"
```

#### Response Payload
```json
{
  "status": "success",
  "message": "Business profile retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "category": "beauty",
    "country": "Nigeria",
    "state": "Lagos",
    "city": "Ikeja",
    "address": "123 Business Street",
    "business_email": "business@example.com",
    "business_phone_number": "08012345678",
    "website_url": "https://example.com",
    "instagram": "@businessname",
    "facebook": "businessname",
    "identity_document": null,
    "business_name": "Example Beauty Store",
    "offering_type": "selling_product",
    "business_description": "Description of the business...",
    "store_status": "approved",
    "rejection_reason": null,
    "created_at": "2026-01-14T15:39:05.000000Z",
    "updated_at": "2026-01-14T15:39:05.000000Z",
    "user": {
      "id": 1,
      "firstname": "John",
      "lastname": "Doe"
    }
  }
}
```

---

### List All Business Profiles (Public)

**URL:** `GET /api/business/public`  
**Auth:** Not required

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `category` | string | No | Filter by category |
| `offering_type` | string | No | Filter by type: `providing_service`, `selling_product` |
| `per_page` | integer | No | Results per page (default: 15) |

#### Example Request
```bash
curl -X GET "https://api.example.com/api/business/public?category=beauty&per_page=10" \
  -H "Accept: application/json"
```

---

### Search Business Profiles

**URL:** `GET /api/business/public/search`  
**Auth:** Not required

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `q` | string | **Yes** | Search term (1-255 chars). Searches in `business_name` and `business_description` |
| `category` | string | No | Filter by category: `fashion`, `beauty`, `brand`, `school`, `music` |
| `offering_type` | string | No | Filter by type: `providing_service`, `selling_product` |
| `state` | string | No | Filter by state (partial match) |
| `city` | string | No | Filter by city (partial match) |
| `sort` | string | No | Sort by: `newest`, `oldest`, `name_asc`, `name_desc` (default: `newest`) |
| `per_page` | integer | No | Results per page (1-50, default: 10) |

#### Example Request
```bash
curl -X GET "https://api.example.com/api/business/public/search?q=beauty&category=beauty&offering_type=selling_product&sort=name_asc&per_page=10" \
  -H "Accept: application/json"
```

#### Response Payload
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "category": "beauty",
        "country": "Nigeria",
        "state": "Lagos",
        "city": "Ikeja",
        "address": "123 Business Street",
        "business_email": "business@example.com",
        "business_phone_number": "08012345678",
        "website_url": "https://example.com",
        "instagram": "@businessname",
        "facebook": "businessname",
        "identity_document": null,
        "business_name": "Example Beauty Store",
        "business_description": "Premium beauty products and services...",
        "offering_type": "selling_product",
        "store_status": "approved",
        "rejection_reason": null,
        "created_at": "2026-01-14T15:39:05.000000Z",
        "updated_at": "2026-01-14T15:39:05.000000Z",
        "user": {
          "id": 1,
          "firstname": "John",
          "lastname": "Doe"
        }
      }
    ],
    "first_page_url": "https://api.example.com/api/business/public/search?page=1",
    "from": 1,
    "last_page": 1,
    "links": [...],
    "next_page_url": null,
    "path": "https://api.example.com/api/business/public/search",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

---

### Get Business Profile Filters

**URL:** `GET /api/business/public/filters`  
**Auth:** Not required

#### Example Request
```bash
curl -X GET "https://api.example.com/api/business/public/filters" \
  -H "Accept: application/json"
```

#### Response Payload
```json
{
  "status": "success",
  "data": {
    "categories": ["beauty", "brand", "school", "music", "fashion"],
    "offering_types": ["providing_service", "selling_product"],
    "states": ["Lagos", "Abuja", "Rivers", "Kano"],
    "cities": ["Ikeja", "Victoria Island", "Lekki", "Wuse"],
    "sort_options": [
      { "value": "newest", "label": "Newest First" },
      { "value": "oldest", "label": "Oldest First" },
      { "value": "name_asc", "label": "Name: A to Z" },
      { "value": "name_desc", "label": "Name: Z to A" }
    ]
  }
}
```

---

## Sustainability Initiatives

### List Sustainability Initiatives

**URL:** `GET /api/sustainability`  
**Auth:** Not required

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `category` | string | No | Filter by category: `environmental`, `social`, `economic`, `governance` |
| `per_page` | integer | No | Results per page (1-50, default: 10) |

#### Example Request
```bash
curl -X GET "https://api.example.com/api/sustainability?category=environmental&per_page=5" \
  -H "Accept: application/json"
```

---

### Get Single Sustainability Initiative

**URL:** `GET /api/sustainability/{id}`  
**Auth:** Not required

#### Example Request
```bash
curl -X GET "https://api.example.com/api/sustainability/1" \
  -H "Accept: application/json"
```

#### Response Payload
```json
{
  "status": "success",
  "message": "Sustainability initiative retrieved successfully",
  "data": {
    "id": 1,
    "title": "Zero Waste Fashion Initiative",
    "description": "Promoting sustainable fashion practices and reducing textile waste in our communities...",
    "image_url": "https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=600&h=400&fit=crop",
    "category": "environmental",
    "status": "active",
    "target_amount": "100000.00",
    "current_amount": "45000.00",
    "start_date": "2026-01-01",
    "end_date": "2026-12-31",
    "created_at": "2026-01-14T15:39:05.000000Z",
    "updated_at": "2026-01-14T15:39:05.000000Z",
    "admin": {
      "id": 1,
      "firstname": "Super",
      "lastname": "Admin"
    }
  }
}
```

---

### Search Sustainability Initiatives

**URL:** `GET /api/sustainability/search`  
**Auth:** Not required

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `q` | string | **Yes** | Search term (1-255 chars). Searches in `title` and `description` |
| `category` | string | No | Filter by category: `environmental`, `social`, `economic`, `governance` |
| `sort` | string | No | Sort by: `newest`, `oldest`, `target_asc`, `target_desc`, `progress` (default: `newest`) |
| `per_page` | integer | No | Results per page (1-50, default: 10) |

#### Example Request
```bash
curl -X GET "https://api.example.com/api/sustainability/search?q=waste&category=environmental&sort=progress&per_page=5" \
  -H "Accept: application/json"
```

#### Response Payload
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Zero Waste Fashion Initiative",
        "description": "Promoting sustainable fashion practices and reducing textile waste...",
        "image_url": "https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=600&h=400&fit=crop",
        "category": "environmental",
        "status": "active",
        "target_amount": "100000.00",
        "current_amount": "45000.00",
        "start_date": "2026-01-01",
        "end_date": "2026-12-31",
        "created_at": "2026-01-14T15:39:05.000000Z",
        "updated_at": "2026-01-14T15:39:05.000000Z",
        "admin": {
          "id": 1,
          "firstname": "Super",
          "lastname": "Admin"
        }
      }
    ],
    "first_page_url": "https://api.example.com/api/sustainability/search?page=1",
    "from": 1,
    "last_page": 1,
    "links": [...],
    "next_page_url": null,
    "path": "https://api.example.com/api/sustainability/search",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

---

### Get Sustainability Filters

**URL:** `GET /api/sustainability/filters`  
**Auth:** Not required

#### Example Request
```bash
curl -X GET "https://api.example.com/api/sustainability/filters" \
  -H "Accept: application/json"
```

#### Response Payload
```json
{
  "status": "success",
  "data": {
    "categories": ["environmental", "social", "economic", "governance"],
    "target_range": {
      "min": "50000.00",
      "max": "1000000.00"
    },
    "sort_options": [
      { "value": "newest", "label": "Newest First" },
      { "value": "oldest", "label": "Oldest First" },
      { "value": "target_asc", "label": "Target: Low to High" },
      { "value": "target_desc", "label": "Target: High to Low" },
      { "value": "progress", "label": "Most Progress" }
    ]
  }
}
```

---

## Quick Reference Table

| Resource | List | Get Single | Search | Filters |
|----------|------|------------|--------|---------|
| **Products** | `GET /api/products` | `GET /api/products/{id}` | `GET /api/products/search?q=` | `GET /api/products/filters` |
| **Business** | `GET /api/business/public` | `GET /api/business/{id}` | `GET /api/business/public/search?q=` | `GET /api/business/public/filters` |
| **Sustainability** | `GET /api/sustainability` | `GET /api/sustainability/{id}` | `GET /api/sustainability/search?q=` | `GET /api/sustainability/filters` |

---

## Notes

1. **Products belong to SellerProfile**, not BusinessProfile
2. **All search endpoints** use `q` as the search parameter
3. **All endpoints support pagination** via `per_page` parameter
4. **Only approved/active items** are returned in public endpoints
5. **Product search requires authentication**, while Business and Sustainability search are public
