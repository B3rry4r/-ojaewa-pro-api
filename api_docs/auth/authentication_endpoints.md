# Authentication Endpoints

## User Authentication

### 1. User Registration
**POST** `/api/auth/register`

**Request:**
```json
{
  "firstname": "John",
  "lastname": "Doe", 
  "email": "john.doe@example.com",
  "phone": "+1234567890",
  "password": "password123"
}
```

**Validation Rules:**
- `firstname`: required|string|max:255
- `lastname`: required|string|max:255  
- `email`: required|email|unique:users,email
- `phone`: nullable|string|max:30
- `password`: required|string|min:8

**Response (201):**
```json
{
  "token": "1|laravel_sanctum_token_here...",
  "user": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "email_verified_at": null,
    "created_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Test Coverage:** ✅ Tested

---

### 2. User Login
**POST** `/api/auth/login`

**Request:**
```json
{
  "email": "john.doe@example.com",
  "password": "password123"
}
```

**Validation Rules:**
- `email`: required|email
- `password`: required|string

**Response (200):**
```json
{
  "token": "2|laravel_sanctum_token_here...",
  "user": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "email_verified_at": null,
    "created_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

**Test Coverage:** ✅ Tested

---

### 3. Forgot Password
**POST** `/api/auth/forgot-password`

**Request:**
```json
{
  "email": "john.doe@example.com"
}
```

**Validation Rules:**
- `email`: required|email

**Response (200):**
```json
{
  "message": "We have emailed your password reset link!"
}
```

**Error Response (400):**
```json
{
  "message": "We can't find a user with that email address."
}
```

**Test Coverage:** ✅ Tested

---

### 4. Reset Password
**POST** `/api/auth/reset-password`

**Request:**
```json
{
  "token": "password_reset_token_here",
  "email": "john.doe@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Validation Rules:**
- `token`: required
- `email`: required|email
- `password`: required|string|min:8|confirmed

**Response (200):**
```json
{
  "message": "Your password has been reset!"
}
```

**Error Response (400):**
```json
{
  "message": "This password reset token is invalid."
}
```

**Test Coverage:** ✅ Tested

---

## Google OAuth Authentication

### 5. Google OAuth Sign-in
**POST** `/api/auth/google`

**Request:**
```json
{
  "token": "google_id_token_from_frontend"
}
```

**Validation Rules:**
- `token`: required|string

**Response (200) - Existing User:**
```json
{
  "token": "3|laravel_sanctum_token_here...",
  "user": {
    "id": 2,
    "firstname": "Jane",
    "lastname": "Smith",
    "email": "jane.smith@gmail.com",
    "phone": "+1987654321",
    "email_verified_at": "2025-01-20T09:00:00.000000Z",
    "created_at": "2025-01-19T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  },
  "need_phone": false
}
```

**Response (200) - New User:**
```json
{
  "token": "4|laravel_sanctum_token_here...",
  "user": {
    "id": 3,
    "firstname": "Bob",
    "lastname": "Johnson",
    "email": "bob.johnson@gmail.com",
    "phone": null,
    "email_verified_at": null,
    "created_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  },
  "need_phone": true
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "token": ["Invalid Google token."]
  }
}
```

**Test Coverage:** ✅ Tested

---

## Admin Authentication

### 6. Admin Login
**POST** `/api/admin/login`

**Request:**
```json
{
  "email": "admin@ojaewa.com",
  "password": "adminpassword"
}
```

**Validation Rules:**
- `email`: required|email
- `password`: required|string

**Response (200):**
```json
{
  "token": "5|laravel_sanctum_admin_token_here...",
  "admin": {
    "id": 1,
    "firstname": "Admin",
    "lastname": "User",
    "email": "admin@ojaewa.com",
    "is_super_admin": true,
    "created_at": "2025-01-15T08:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

**Test Coverage:** ✅ Tested

---

### 7. Create Admin User
**POST** `/api/admin/create`

**Request:**
```json
{
  "firstname": "New",
  "lastname": "Admin",
  "email": "newadmin@ojaewa.com",
  "password": "securepassword123",
  "password_confirmation": "securepassword123",
  "is_super_admin": false
}
```

**Validation Rules:**
- `firstname`: required|string|max:255
- `lastname`: required|string|max:255
- `email`: required|email|unique:admins,email
- `password`: required|string|min:8|confirmed
- `is_super_admin`: boolean

**Response (201):**
```json
{
  "message": "Admin created successfully",
  "token": "6|laravel_sanctum_admin_token_here...",
  "admin": {
    "id": 2,
    "firstname": "New",
    "lastname": "Admin",
    "email": "newadmin@ojaewa.com",
    "is_super_admin": false,
    "created_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 8. Admin Profile
**GET** `/api/admin/profile`
**Middleware:** `admin.auth`

**Request:** No body required (token in Authorization header)

**Response (200):**
```json
{
  "admin": {
    "id": 1,
    "firstname": "Admin",
    "lastname": "User",
    "email": "admin@ojaewa.com",
    "is_super_admin": true,
    "created_at": "2025-01-15T08:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 9. Admin Logout
**POST** `/api/admin/logout`
**Middleware:** `admin.auth`

**Request:** No body required (token in Authorization header)

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

**Test Coverage:** ✅ Tested

---

## Authentication Headers

### For User Endpoints:
```
Authorization: Bearer {user_token}
Content-Type: application/json
Accept: application/json
```

### For Admin Endpoints:
```
Authorization: Bearer {admin_token}
Content-Type: application/json
Accept: application/json
```

## Notes

1. **Token Types:** Uses Laravel Sanctum for token-based authentication
2. **User Tokens:** Standard user authentication tokens
3. **Admin Tokens:** Admin tokens with `admin` ability scope
4. **Google OAuth:** Requires Google Client ID configuration
5. **Password Requirements:** Minimum 8 characters for all passwords
6. **Phone Handling:** Google OAuth users may need to provide phone number separately

---
*Last Updated: January 2025*