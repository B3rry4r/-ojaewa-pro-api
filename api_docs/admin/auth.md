# Admin Authentication & Profile

Base: `/api/admin`

## Public Endpoints

### POST /admin/login
Login with admin credentials to receive a Sanctum authentication token.

**Request:**
```json
{
  "email": "admin@example.com",
  "password": "secure_password123"
}
```

**Success Response (200):**
```json
{
  "token": "1|abcdefghijklmnopqrstuvwxyz123456...",
  "admin": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "admin@example.com",
    "is_super_admin": true,
    "created_at": "2025-06-26T10:30:00Z",
    "updated_at": "2025-06-26T10:30:00Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### POST /admin/create
Create a new admin user. **Should be protected in production.**

**Request:**
```json
{
  "firstname": "Jane",
  "lastname": "Smith",
  "email": "jane.smith@example.com",
  "password": "secure_password123",
  "password_confirmation": "secure_password123",
  "is_super_admin": false
}
```

**Success Response (201):**
```json
{
  "message": "Admin created successfully",
  "token": "2|defghijklmnopqrstuvwxyz123456789...",
  "admin": {
    "id": 2,
    "firstname": "Jane",
    "lastname": "Smith",
    "email": "jane.smith@example.com",
    "is_super_admin": false,
    "created_at": "2025-06-26T11:45:00Z",
    "updated_at": "2025-06-26T11:45:00Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password confirmation does not match."]
  }
}
```

---

## Protected Endpoints
**Requires:** `Authorization: Bearer {token}` (with admin abilities)

### GET /admin/profile
Get the current authenticated admin's profile information.

**Response (200):**
```json
{
  "admin": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "admin@example.com",
    "is_super_admin": true,
    "created_at": "2025-06-26T10:30:00Z",
    "updated_at": "2025-06-26T10:30:00Z"
  }
}
```

---

### POST /admin/logout
Logout the current admin user by invalidating the authentication token.

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```
