# Business & Seller Management Endpoints

## Business Profile Management

### 1. Get User's Business Profiles
**GET** `/api/business`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profiles retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "category": "beauty",
      "country": "Nigeria",
      "state": "Lagos",
      "city": "Lagos",
      "address": "123 Beauty Street, Victoria Island",
      "business_email": "info@beautystore.com",
      "business_phone_number": "+2348012345678",
      "website_url": "https://beautystore.com",
      "instagram": "@beautystore",
      "facebook": "beautystore",
      "business_name": "Beauty Store Lagos",
      "business_description": "Premium beauty products and services",
      "business_logo": "storage/logos/beauty_logo.jpg",
      "offering_type": "selling_product",
      "product_list": "[\"Skincare\", \"Makeup\", \"Haircare\"]",
      "service_list": null,
      "business_certificates": "[\"storage/certs/beauty_cert.pdf\"]",
      "professional_title": null,
      "store_status": "approved",
      "subscription_status": "active",
      "subscription_ends_at": "2025-12-31T23:59:59.000000Z",
      "rejection_reason": null,
      "created_at": "2025-01-15T10:00:00.000000Z",
      "updated_at": "2025-01-20T10:00:00.000000Z"
    }
  ]
}
```

**Test Coverage:** ✅ Tested

---

### 2. Create Business Profile
**POST** `/api/business`
**Middleware:** `auth:sanctum`

**Request - Beauty Business:**
```json
{
  "category": "beauty",
  "country": "Nigeria",
  "state": "Lagos", 
  "city": "Lagos",
  "address": "456 Beauty Avenue, Ikeja",
  "business_email": "contact@newbeauty.com",
  "business_phone_number": "+2348087654321",
  "website_url": "https://newbeauty.com",
  "instagram": "@newbeauty",
  "facebook": "newbeauty",
  "business_name": "New Beauty Concepts",
  "business_description": "Modern beauty solutions for the African woman",
  "offering_type": "providing_service",
  "service_list": "[\"Makeup Services\", \"Beauty Consulting\", \"Skincare Treatment\"]",
  "professional_title": "Certified Beauty Specialist"
}
```

**Request - School Business:**
```json
{
  "category": "school",
  "country": "Nigeria",
  "state": "Abuja",
  "city": "Abuja", 
  "address": "789 Education Street, Wuse 2",
  "business_email": "admin@fashionschool.edu.ng",
  "business_phone_number": "+2348098765432",
  "business_name": "Nigerian Fashion Institute",
  "business_description": "Premier fashion design education",
  "school_type": "fashion",
  "school_biography": "Leading fashion school in Nigeria with over 10 years of experience training designers",
  "classes_offered": "[\"Fashion Design\", \"Pattern Making\", \"Textile Arts\", \"Fashion Business\"]"
}
```

**Request - Music Business:**
```json
{
  "category": "music",
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Lagos",
  "address": "321 Music Street, Surulere", 
  "business_email": "bookings@djpro.com",
  "business_phone_number": "+2348076543210",
  "business_name": "DJ Pro Entertainment",
  "business_description": "Professional DJ services for all events",
  "music_category": "dj",
  "identity_document": "storage/docs/dj_license.pdf",
  "youtube": "https://youtube.com/@djpro",
  "spotify": "https://open.spotify.com/artist/djpro"
}
```

**Validation Rules:**
- `category`: required|string|in:beauty,brand,school,music
- `country`: required|string|max:100
- `state`: required|string|max:100
- `city`: required|string|max:100
- `address`: required|string|max:255
- `business_email`: required|email|max:100
- `business_phone_number`: required|string|max:20
- `website_url`: nullable|url|max:255
- `instagram`: nullable|string|max:100
- `facebook`: nullable|string|max:100
- `business_name`: required|string|max:100
- `business_description`: required|string|max:1000
- `business_logo`: nullable|string|max:255
- `offering_type`: nullable|string|in:selling_product,providing_service

**Conditional Rules:**
- **For Service Providers:** `service_list`: required|json, `professional_title`: required
- **For Product Sellers:** `product_list`: required|json, `business_certificates`: required|json
- **For Schools:** `school_type`: required|in:fashion,music,catering,beauty, `school_biography`: required, `classes_offered`: required|json
- **For Music:** `music_category`: required|in:dj,artist,producer, `identity_document`: required, At least one of `youtube` or `spotify` required

**Response (201):**
```json
{
  "status": "success",
  "message": "Business profile created successfully",
  "data": {
    "id": 2,
    "user_id": 1,
    "category": "beauty",
    "country": "Nigeria",
    "state": "Lagos",
    "city": "Lagos",
    "address": "456 Beauty Avenue, Ikeja",
    "business_email": "contact@newbeauty.com",
    "business_phone_number": "+2348087654321",
    "website_url": "https://newbeauty.com",
    "instagram": "@newbeauty",
    "facebook": "newbeauty",
    "business_name": "New Beauty Concepts",
    "business_description": "Modern beauty solutions for the African woman",
    "business_logo": null,
    "offering_type": "providing_service",
    "service_list": "[\"Makeup Services\", \"Beauty Consulting\", \"Skincare Treatment\"]",
    "professional_title": "Certified Beauty Specialist",
    "store_status": "pending",
    "subscription_status": null,
    "subscription_ends_at": null,
    "rejection_reason": null,
    "created_at": "2025-01-20T12:00:00.000000Z",
    "updated_at": "2025-01-20T12:00:00.000000Z"
  }
}
```

**Error Response (422) - Duplicate Category:**
```json
{
  "message": "You already have a business in this category."
}
```

**Test Coverage:** ✅ Tested

---

### 3. Get Specific Business Profile
**GET** `/api/business/{id}`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
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
    "city": "Lagos",
    "address": "123 Beauty Street, Victoria Island",
    "business_email": "info@beautystore.com",
    "business_phone_number": "+2348012345678",
    "website_url": "https://beautystore.com",
    "instagram": "@beautystore",
    "facebook": "beautystore",
    "business_name": "Beauty Store Lagos",
    "business_description": "Premium beauty products and services",
    "business_logo": "storage/logos/beauty_logo.jpg",
    "offering_type": "selling_product",
    "product_list": "[\"Skincare\", \"Makeup\", \"Haircare\"]",
    "business_certificates": "[\"storage/certs/beauty_cert.pdf\"]",
    "store_status": "approved",
    "subscription_status": "active",
    "subscription_ends_at": "2025-12-31T23:59:59.000000Z",
    "created_at": "2025-01-15T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Error Response (403) - Unauthorized:**
```json
{
  "message": "Unauthorized access to this business profile"
}
```

**Test Coverage:** ✅ Tested

---

### 4. Update Business Profile
**PUT** `/api/business/{id}`
**Middleware:** `auth:sanctum`

**Request (Partial Update):**
```json
{
  "business_name": "Updated Beauty Store Lagos",
  "business_description": "Premium beauty products and professional services",
  "website_url": "https://updated-beautystore.com",
  "service_list": "[\"Makeup Services\", \"Beauty Consulting\", \"Skincare Treatment\", \"Bridal Makeup\"]"
}
```

**Validation Rules:** Same as creation but all fields are `sometimes` (optional)

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profile updated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "category": "beauty",
    "business_name": "Updated Beauty Store Lagos",
    "business_description": "Premium beauty products and professional services",
    "website_url": "https://updated-beautystore.com",
    "service_list": "[\"Makeup Services\", \"Beauty Consulting\", \"Skincare Treatment\", \"Bridal Makeup\"]",
    "updated_at": "2025-01-20T13:00:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 5. Delete Business Profile
**DELETE** `/api/business/{id}`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profile deleted successfully"
}
```

**Error Response (403) - Unauthorized:**
```json
{
  "message": "Unauthorized access to this business profile"
}
```

**Test Coverage:** ✅ Tested

---

### 6. Deactivate Business Profile
**PUT** `/api/business/{id}/deactivate`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Business profile deactivated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "business_name": "Beauty Store Lagos",
    "store_status": "deactivated",
    "updated_at": "2025-01-20T13:30:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 7. Upload Business Files
**POST** `/api/business/{id}/upload`
**Middleware:** `auth:sanctum`

**Request (Form Data):**
```
Content-Type: multipart/form-data

file: [binary file data]
file_type: "business_logo" | "business_certificates" | "identity_document"
```

**Validation Rules:**
- `file`: required|file|max:10240 (10MB)
- `file_type`: required|string|in:business_logo,business_certificates,identity_document

**Response (200):**
```json
{
  "status": "success",
  "message": "File uploaded successfully",
  "data": {
    "file_path": "storage/business_logos/unique_filename.jpg",
    "file_type": "business_logo"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Seller Profile Management

### 8. Get Seller Profile
**GET** `/api/seller/profile`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Lagos",
  "address": "789 Seller Street, Ikeja",
  "business_email": "seller@traditional.com",
  "business_phone_number": "+2348012345678",
  "instagram": "@traditionalwear",
  "facebook": "traditionalwear",
  "business_name": "Traditional Wear Nigeria",
  "business_registration_number": "RC123456",
  "business_certificate": "storage/certs/business_cert.pdf",
  "business_logo": "storage/logos/traditional_logo.jpg",
  "bank_name": "First Bank Nigeria",
  "account_number": "1234567890",
  "active": true,
  "rejection_reason": null,
  "selling_since": "2025-01-15T10:00:00.000000Z",
  "total_sales": 150000.00,
  "avg_rating": 4.7,
  "created_at": "2025-01-15T10:00:00.000000Z",
  "updated_at": "2025-01-20T10:00:00.000000Z"
}
```

**Error Response (404) - No Seller Profile:**
```json
{
  "message": "Seller profile not found"
}
```

**Test Coverage:** ✅ Tested

---

### 9. Create Seller Profile
**POST** `/api/seller/profile`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Lagos",
  "address": "789 Seller Street, Ikeja",
  "business_email": "seller@traditional.com",
  "business_phone_number": "+2348012345678",
  "instagram": "@traditionalwear",
  "facebook": "traditionalwear",
  "identity_document": "storage/docs/identity.pdf",
  "business_name": "Traditional Wear Nigeria",
  "business_registration_number": "RC123456",
  "business_certificate": "storage/certs/business_cert.pdf",
  "business_logo": "storage/logos/traditional_logo.jpg",
  "bank_name": "First Bank Nigeria",
  "account_number": "1234567890"
}
```

**Validation Rules:**
- `country`: required|string|max:255
- `state`: required|string|max:255
- `city`: required|string|max:255
- `address`: required|string
- `business_email`: required|email|max:255
- `business_phone_number`: required|string|max:255
- `instagram`: nullable|string|max:255
- `facebook`: nullable|string|max:255
- `identity_document`: nullable|string|max:255
- `business_name`: required|string|max:255
- `business_registration_number`: required|string|max:255
- `business_certificate`: nullable|string|max:255
- `business_logo`: nullable|string|max:255
- `bank_name`: required|string|max:255
- `account_number`: required|string|max:255

**Response (201):**
```json
{
  "id": 1,
  "user_id": 1,
  "country": "Nigeria",
  "state": "Lagos",
  "city": "Lagos",
  "address": "789 Seller Street, Ikeja",
  "business_email": "seller@traditional.com",
  "business_phone_number": "+2348012345678",
  "instagram": "@traditionalwear",
  "facebook": "traditionalwear",
  "identity_document": "storage/docs/identity.pdf",
  "business_name": "Traditional Wear Nigeria",
  "business_registration_number": "RC123456",
  "business_certificate": "storage/certs/business_cert.pdf",
  "business_logo": "storage/logos/traditional_logo.jpg",
  "bank_name": "First Bank Nigeria",
  "account_number": "1234567890",
  "active": false,
  "rejection_reason": null,
  "created_at": "2025-01-20T14:00:00.000000Z",
  "updated_at": "2025-01-20T14:00:00.000000Z"
}
```

**Error Response (409) - Already Exists:**
```json
{
  "message": "User already has a seller profile"
}
```

**Test Coverage:** ✅ Tested

---

### 10. Update Seller Profile
**PUT** `/api/seller/profile`
**Middleware:** `auth:sanctum`

**Request (Partial Update):**
```json
{
  "business_name": "Updated Traditional Wear Nigeria",
  "business_phone_number": "+2348087654321",
  "instagram": "@updatedtraditional",
  "bank_name": "GTBank",
  "account_number": "0987654321"
}
```

**Validation Rules:** Same as creation but all fields are `sometimes` (optional)

**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "business_name": "Updated Traditional Wear Nigeria",
  "business_phone_number": "+2348087654321",
  "instagram": "@updatedtraditional",
  "bank_name": "GTBank",
  "account_number": "0987654321",
  "updated_at": "2025-01-20T14:30:00.000000Z"
}
```

**Test Coverage:** ✅ Tested

---

### 11. Delete Seller Profile
**DELETE** `/api/seller/profile`
**Middleware:** `auth:sanctum`

**Request (Optional):**
```json
{
  "reason": "Moving to different platform"
}
```

**Response (200):**
```json
{
  "message": "Seller profile deleted successfully"
}
```

**Error Response (404):**
```json
{
  "message": "Seller profile not found"
}
```

**Test Coverage:** ✅ Tested

---

### 12. Upload Seller Files
**POST** `/api/seller/profile/upload`
**Middleware:** `auth:sanctum`

**Request (Form Data):**
```
Content-Type: multipart/form-data

file: [binary file data]
type: "identity_document" | "business_certificate" | "business_logo"
```

**Validation Rules:**
- `file`: required|file|max:10240 (10MB)
- `type`: required|in:identity_document,business_certificate,business_logo

**Response (200):**
```json
{
  "message": "File uploaded successfully",
  "file_path": "spaces/business_logo/unique_filename.jpg",
  "type": "business_logo"
}
```

**Test Coverage:** 🔴 Not Tested

---

## Business Categories & Types

### Business Categories:
- **beauty** - Beauty products and services
- **brand** - Fashion brands and clothing lines  
- **school** - Educational institutions
- **music** - Music artists, DJs, producers

### School Types:
- **fashion** - Fashion design schools
- **music** - Music training schools
- **catering** - Culinary schools
- **beauty** - Beauty training schools

### Music Categories:
- **dj** - DJ services
- **artist** - Music artists
- **producer** - Music producers

### Offering Types:
- **selling_product** - Businesses that sell products
- **providing_service** - Businesses that provide services

### Store Status:
- **pending** - Awaiting admin approval
- **approved** - Approved by admin
- **deactivated** - Deactivated by user or admin

### Subscription Status:
- **active** - Active subscription
- **expired** - Subscription expired

## Authorization & Business Rules

1. **One Business Per Category:** Users can only have one business profile per category
2. **Seller Profile Uniqueness:** Users can only have one seller profile
3. **File Upload Security:** 10MB max file size, specific file types only
4. **Ownership Validation:** Users can only access/modify their own profiles
5. **Conditional Validation:** Different validation rules based on category and offering type

## Test Coverage Summary

- **Business Profile Management:** ✅ 5/7 endpoints tested (71%)
- **Seller Profile Management:** ✅ 4/5 endpoints tested (80%)

**Total Business & Seller Management:** ✅ 9/12 endpoints tested (75%)

---
*Last Updated: January 2025*