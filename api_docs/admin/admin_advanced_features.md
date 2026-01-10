# Admin Advanced Features Endpoints

## Advertisement Management

### 1. Get All Adverts
**GET** `/api/admin/adverts`
**Middleware:** `admin.auth`

**Query Parameters:**
- `status`: Filter by status (active/inactive/scheduled)
- `position`: Filter by position (banner/sidebar/footer/popup)
- `per_page`: Items per page (default: 10)

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
        "description": "Get up to 50% off on all summer collection items",
        "image_url": "https://example.com/images/summer-sale.jpg",
        "action_url": "https://ojaewa.com/summer-sale",
        "position": "banner",
        "status": "active",
        "priority": 90,
        "start_date": "2025-01-20",
        "end_date": "2025-02-28",
        "created_by": 1,
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      }
    ],
    "per_page": 10,
    "total": 5
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 2. Create New Advert
**POST** `/api/admin/adverts`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "title": "New Collection Launch",
  "description": "Discover our latest African print collection",
  "image_url": "https://example.com/images/new-collection.jpg",
  "action_url": "https://ojaewa.com/new-collection",
  "position": "banner",
  "status": "active",
  "priority": 85,
  "start_date": "2025-01-25",
  "end_date": "2025-03-15"
}
```

**Validation Rules:**
- `title`: required|string|max:255
- `description`: required|string
- `image_url`: required|url
- `action_url`: nullable|url
- `position`: required|in:banner,sidebar,footer,popup
- `status`: required|in:active,inactive,scheduled
- `priority`: nullable|integer|min:0|max:100
- `start_date`: nullable|date|after_or_equal:today
- `end_date`: nullable|date|after:start_date

**Response (201):**
```json
{
  "status": "success",
  "message": "Advert created successfully",
  "data": {
    "id": 2,
    "title": "New Collection Launch",
    "description": "Discover our latest African print collection",
    "image_url": "https://example.com/images/new-collection.jpg",
    "action_url": "https://ojaewa.com/new-collection",
    "position": "banner",
    "status": "active",
    "priority": 85,
    "start_date": "2025-01-25",
    "end_date": "2025-03-15",
    "created_by": 1,
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

### 3. Update Advert
**PUT** `/api/admin/adverts/{advert}`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "title": "Updated: New Collection Launch",
  "description": "Discover our latest African print collection - Now with 20% off!",
  "status": "active",
  "priority": 95
}
```

**Validation Rules:**
- `title`: sometimes|string|max:255
- `description`: sometimes|string
- `image_url`: sometimes|url
- `action_url`: nullable|url
- `position`: sometimes|in:banner,sidebar,footer,popup
- `status`: sometimes|in:active,inactive,scheduled
- `priority`: nullable|integer|min:0|max:100
- `start_date`: nullable|date
- `end_date`: nullable|date|after:start_date

**Response (200):**
```json
{
  "status": "success",
  "message": "Advert updated successfully",
  "data": {
    "id": 2,
    "title": "Updated: New Collection Launch",
    "description": "Discover our latest African print collection - Now with 20% off!",
    "status": "active",
    "priority": 95,
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

### 4. Delete Advert
**DELETE** `/api/admin/adverts/{advert}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Advert deleted successfully"
}
```

**Test Coverage:** 🔴 Not Tested

---

## Admin Notification Broadcasting

### 5. Send Notification to Users
**POST** `/api/admin/notifications/send`
**Middleware:** `admin.auth`

**Request - To All Users:**
```json
{
  "title": "Platform Maintenance Notice",
  "message": "We will be performing system maintenance on January 25th from 2AM to 6AM WAT",
  "type": "system",
  "recipient_type": "all",
  "action_url": "https://ojaewa.com/maintenance-notice",
  "send_immediately": true
}
```

**Request - To Specific Users:**
```json
{
  "title": "Your Order Has Been Shipped",
  "message": "Your recent order has been shipped and is on its way to you",
  "type": "general",
  "recipient_type": "specific",
  "user_ids": [1, 5, 12, 28],
  "action_url": "https://ojaewa.com/orders/tracking",
  "send_immediately": true
}
```

**Request - To Sellers Only:**
```json
{
  "title": "New Seller Guidelines",
  "message": "We've updated our seller guidelines. Please review the new policies",
  "type": "promotion",
  "recipient_type": "sellers",
  "action_url": "https://ojaewa.com/seller-guidelines",
  "send_immediately": false
}
```

**Validation Rules:**
- `title`: required|string|max:255
- `message`: required|string
- `type`: required|in:general,promotion,system,security
- `recipient_type`: required|in:all,specific,sellers
- `user_ids`: required_if:recipient_type,specific|array
- `user_ids.*`: exists:users,id
- `action_url`: nullable|url
- `send_immediately`: boolean

**Response (201):**
```json
{
  "status": "success",
  "message": "Notification sent successfully to 1250 users",
  "data": {
    "recipients_count": 1250,
    "notification": {
      "title": "Platform Maintenance Notice",
      "message": "We will be performing system maintenance on January 25th from 2AM to 6AM WAT",
      "type": "system",
      "recipient_type": "all"
    }
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Application Settings Management

### 6. Get Application Settings
**GET** `/api/admin/settings`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Application settings retrieved successfully",
  "data": {
    "maintenance_mode": false,
    "maintenance_message": "System is under maintenance",
    "allow_registrations": true,
    "allow_seller_registrations": true,
    "max_products_per_seller": 100,
    "commission_rate": 5.0,
    "default_currency": "NGN",
    "email_notifications_enabled": true,
    "sms_notifications_enabled": false,
    "auto_approve_products": false,
    "auto_approve_sellers": false,
    "min_order_amount": 1000,
    "max_order_amount": 1000000,
    "featured_products_limit": 20,
    "support_email": "support@ojaewa.com",
    "support_phone": "+234-800-OJA-EWA"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 7. Update Application Settings
**PUT** `/api/admin/settings`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "maintenance_mode": false,
  "allow_registrations": true,
  "allow_seller_registrations": true,
  "max_products_per_seller": 150,
  "commission_rate": 4.5,
  "auto_approve_products": false,
  "min_order_amount": 500,
  "max_order_amount": 2000000,
  "support_email": "help@ojaewa.com"
}
```

**Validation Rules:**
- `maintenance_mode`: sometimes|boolean
- `maintenance_message`: sometimes|string|max:500
- `allow_registrations`: sometimes|boolean
- `allow_seller_registrations`: sometimes|boolean
- `max_products_per_seller`: sometimes|integer|min:1|max:1000
- `commission_rate`: sometimes|numeric|min:0|max:100
- `default_currency`: sometimes|in:NGN,USD,EUR,GBP
- `email_notifications_enabled`: sometimes|boolean
- `sms_notifications_enabled`: sometimes|boolean
- `auto_approve_products`: sometimes|boolean
- `auto_approve_sellers`: sometimes|boolean
- `min_order_amount`: sometimes|numeric|min:0
- `max_order_amount`: sometimes|numeric|min:1000
- `featured_products_limit`: sometimes|integer|min:1|max:100
- `support_email`: sometimes|email
- `support_phone`: sometimes|string|max:20

**Response (200):**
```json
{
  "status": "success",
  "message": "Application settings updated successfully",
  "data": {
    "maintenance_mode": false,
    "allow_registrations": true,
    "allow_seller_registrations": true,
    "max_products_per_seller": 150,
    "commission_rate": 4.5,
    "auto_approve_products": false,
    "min_order_amount": 500,
    "max_order_amount": 2000000,
    "support_email": "help@ojaewa.com"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## School Registration Management

### 8. Get All School Registrations
**GET** `/api/admin/school-registrations`
**Middleware:** `admin.auth`

**Query Parameters:**
- `status`: Filter by registration status
- `page`: Page number for pagination

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
        "full_name": "John Doe",
        "phone_number": "+2348012345678",
        "state": "Lagos",
        "city": "Lagos",
        "address": "123 Education Street, Ikeja",
        "status": "processing",
        "payment_reference": "SCHOOL_202501201234_REF",
        "payment_data": "{\"status\":\"success\",\"amount\":5000000}",
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T14:30:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 42
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 9. Get Single School Registration
**GET** `/api/admin/school-registrations/{schoolRegistration}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "School registration retrieved successfully",
  "data": {
    "id": 1,
    "country": "Nigeria",
    "full_name": "John Doe",
    "phone_number": "+2348012345678",
    "state": "Lagos",
    "city": "Lagos",
    "address": "123 Education Street, Ikeja, Lagos",
    "status": "processing",
    "payment_reference": "SCHOOL_202501201234_REF",
    "payment_data": "{\"status\":\"success\",\"amount\":5000000,\"paid_at\":\"2025-01-20T14:30:00Z\"}",
    "admin_notes": null,
    "created_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T14:30:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 10. Update School Registration
**PUT** `/api/admin/school-registrations/{schoolRegistration}`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "status": "approved",
  "admin_notes": "Application approved. Welcome to our program!"
}
```

**Validation Rules:**
- `status`: sometimes|in:pending,processing,approved,rejected
- `admin_notes`: nullable|string|max:1000

**Response (200):**
```json
{
  "status": "success",
  "message": "School registration updated successfully",
  "data": {
    "id": 1,
    "full_name": "John Doe",
    "status": "approved",
    "admin_notes": "Application approved. Welcome to our program!",
    "updated_at": "2025-01-20T18:00:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 11. Delete School Registration
**DELETE** `/api/admin/school-registrations/{schoolRegistration}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "School registration deleted successfully"
}
```

**Test Coverage:** 🔴 Not Tested

---

## Test Coverage Summary

- **Advertisement Management:** 🔴 0/4 endpoints tested (0%)
- **Admin Notifications:** 🔴 0/1 endpoints tested (0%)
- **Application Settings:** 🔴 0/2 endpoints tested (0%)
- **School Registration Management:** 🔴 0/4 endpoints tested (0%)

**Total Admin Advanced Features:** 🔴 0/11 endpoints tested (0%)

---
*Last Updated: January 2025*