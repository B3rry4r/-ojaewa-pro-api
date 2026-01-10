# User Management Endpoints

## User Profile Management

### 1. Get User Profile
**GET** `/api/profile`
**Middleware:** `auth:sanctum`

**Request:** No body required (token in Authorization header)

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "email_verified_at": "2025-01-20T09:00:00.000000Z",
    "created_at": "2025-01-19T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 2. Update User Profile
**PUT** `/api/profile`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "firstname": "John",
  "lastname": "Doe",
  "email": "john.doe@example.com",
  "phone": "+1234567890"
}
```

**Validation Rules:**
- `firstname`: required|string|max:255
- `lastname`: required|string|max:255
- `email`: required|email|unique:users,email,{user_id}
- `phone`: nullable|string|max:30

**Response (200):**
```json
{
  "status": "success",
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe", 
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "updated_at": "2025-01-20T10:30:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 3. Update Password
**PUT** `/api/password`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "current_password": "oldpassword123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Validation Rules:**
- `current_password`: required|string
- `password`: required|string|min:8|confirmed

**Response (200):**
```json
{
  "status": "success",
  "message": "Password updated successfully"
}
```

**Error Response (422) - Wrong Current Password:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "current_password": ["The current password is incorrect."]
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## User Address Management

### 4. Get All User Addresses
**GET** `/api/addresses`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "country": "Nigeria",
      "full_name": "John Doe",
      "phone_number": "+2348012345678",
      "state": "Lagos",
      "city": "Lagos",
      "zip_code": "100001",
      "address": "123 Main Street, Victoria Island",
      "is_default": true,
      "created_at": "2025-01-19T10:00:00.000000Z",
      "updated_at": "2025-01-20T10:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "country": "Nigeria",
      "full_name": "John Doe",
      "phone_number": "+2348012345678",
      "state": "Abuja",
      "city": "Abuja",
      "zip_code": "900001",
      "address": "456 Federal Capital Territory",
      "is_default": false,
      "created_at": "2025-01-20T08:00:00.000000Z",
      "updated_at": "2025-01-20T08:00:00.000000Z"
    }
  ]
}
```

**Test Coverage:** 🔴 Not Tested

---

### 5. Create New Address
**POST** `/api/addresses`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "country": "Nigeria",
  "full_name": "John Doe",
  "phone_number": "+2348012345678",
  "state": "Lagos",
  "city": "Lagos",
  "zip_code": "100001",
  "address": "123 Main Street, Victoria Island",
  "is_default": true
}
```

**Validation Rules:**
- `country`: required|string|max:255
- `full_name`: required|string|max:255
- `phone_number`: required|string|max:30
- `state`: required|string|max:255
- `city`: required|string|max:255
- `zip_code`: required|string|max:20
- `address`: required|string
- `is_default`: boolean

**Response (201):**
```json
{
  "status": "success",
  "message": "Address created successfully",
  "data": {
    "id": 3,
    "user_id": 1,
    "country": "Nigeria",
    "full_name": "John Doe",
    "phone_number": "+2348012345678",
    "state": "Lagos",
    "city": "Lagos",
    "zip_code": "100001",
    "address": "123 Main Street, Victoria Island",
    "is_default": true,
    "created_at": "2025-01-20T11:00:00.000000Z",
    "updated_at": "2025-01-20T11:00:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 6. Get Specific Address
**GET** `/api/addresses/{id}`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "user_id": 1,
    "country": "Nigeria",
    "full_name": "John Doe",
    "phone_number": "+2348012345678",
    "state": "Lagos",
    "city": "Lagos",
    "zip_code": "100001",
    "address": "123 Main Street, Victoria Island",
    "is_default": true,
    "created_at": "2025-01-19T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Error Response (404):**
```json
{
  "message": "No query results for model [App\\Models\\Address] {id}"
}
```

**Test Coverage:** 🔴 Not Tested

---

### 7. Update Address
**PUT** `/api/addresses/{id}`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "country": "Nigeria",
  "full_name": "John Doe Updated",
  "phone_number": "+2348012345678",
  "state": "Lagos",
  "city": "Ikeja", 
  "zip_code": "100001",
  "address": "789 Updated Street, Ikeja",
  "is_default": false
}
```

**Validation Rules:**
- `country`: required|string|max:255
- `full_name`: required|string|max:255
- `phone_number`: required|string|max:30
- `state`: required|string|max:255
- `city`: required|string|max:255
- `zip_code`: required|string|max:20
- `address`: required|string
- `is_default`: boolean

**Response (200):**
```json
{
  "status": "success",
  "message": "Address updated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "country": "Nigeria",
    "full_name": "John Doe Updated",
    "phone_number": "+2348012345678",
    "state": "Lagos",
    "city": "Ikeja",
    "zip_code": "100001",
    "address": "789 Updated Street, Ikeja",
    "is_default": false,
    "created_at": "2025-01-19T10:00:00.000000Z",
    "updated_at": "2025-01-20T11:30:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 8. Delete Address
**DELETE** `/api/addresses/{id}`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Address deleted successfully"
}
```

**Error Response (404):**
```json
{
  "message": "No query results for model [App\\Models\\Address] {id}"
}
```

**Test Coverage:** 🔴 Not Tested

---

## Notification Preferences

### 9. Get Notification Preferences
**GET** `/api/notification-preferences`
**Middleware:** `auth:sanctum`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "user_id": 1,
    "email_notifications": true,
    "push_notifications": true,
    "sms_notifications": false,
    "marketing_notifications": true,
    "order_notifications": true,
    "product_notifications": false
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 10. Update Notification Preferences
**PUT** `/api/notification-preferences`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "email_notifications": true,
  "push_notifications": false,
  "sms_notifications": false,
  "marketing_notifications": false,
  "order_notifications": true,
  "product_notifications": true
}
```

**Validation Rules:**
- `email_notifications`: boolean
- `push_notifications`: boolean
- `sms_notifications`: boolean
- `marketing_notifications`: boolean
- `order_notifications`: boolean
- `product_notifications`: boolean

**Response (200):**
```json
{
  "status": "success",
  "message": "Notification preferences updated successfully",
  "data": {
    "user_id": 1,
    "email_notifications": true,
    "push_notifications": false,
    "sms_notifications": false,
    "marketing_notifications": false,
    "order_notifications": true,
    "product_notifications": true,
    "updated_at": "2025-01-20T12:00:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## Authentication Headers

### Required Headers:
```
Authorization: Bearer {user_token}
Content-Type: application/json
Accept: application/json
```

## Notes

1. **User Ownership:** All address operations are scoped to the authenticated user
2. **Default Address Logic:** Setting an address as default automatically unsets other default addresses
3. **Profile Updates:** Email uniqueness is enforced but excludes current user's email
4. **Password Security:** Current password verification required before changing password
5. **Notification Preferences:** Stored in user table as JSON or separate preference fields

## Test Coverage Summary

- **Profile Management:** 🔴 0/3 endpoints tested
- **Address Management:** 🔴 0/5 endpoints tested  
- **Notification Preferences:** 🔴 0/2 endpoints tested

**Total User Management:** 🔴 0/10 endpoints tested (0%)

---
*Last Updated: January 2025*