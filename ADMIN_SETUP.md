# Admin System Setup Guide

## Overview
The admin system has been implemented with proper authentication and authorization using Laravel Sanctum.

## Components

### 1. AdminAuth Middleware
- **Location**: `app/Http/Middleware/AdminAuth.php`
- **Purpose**: Validates admin authentication and token abilities
- **Checks**: 
  - User is authenticated
  - User is instance of Admin model
  - Token has 'admin' abilities

### 2. Admin Endpoints

#### Authentication Endpoints
- `POST /api/admin/login` - Admin login
- `POST /api/admin/create` - Create admin user (⚠️ **PRODUCTION WARNING**)
- `GET /api/admin/profile` - Get admin profile (protected)
- `POST /api/admin/logout` - Admin logout (protected)

#### Dashboard Endpoints
All dashboard endpoints are protected with `['auth:sanctum', 'admin']` middleware:
- `GET /api/admin/dashboard/overview` - Dashboard statistics
- `GET /api/admin/pending/sellers` - Pending seller approvals
- `GET /api/admin/pending/products` - Pending product approvals
- And more...

## Security Considerations

### ⚠️ IMPORTANT: Production Security

The admin creation endpoint (`POST /api/admin/create`) is currently **UNPROTECTED** for initial setup purposes. 

**Before deploying to production:**

1. **Option 1: Disable the endpoint after creating initial admin**
   ```php
   // In routes/api.php, comment out or remove:
   // Route::post('/admin/create', [AdminAuthController::class, 'create']);
   ```

2. **Option 2: Protect with super admin middleware**
   ```php
   Route::middleware(['auth:sanctum', 'admin'])->group(function () {
       Route::post('/admin/create', [AdminAuthController::class, 'create']);
   });
   ```

3. **Option 3: Add environment-based protection**
   ```php
   // In AdminAuthController::create method
   if (app()->environment('production')) {
       abort(404); // Hide endpoint in production
   }
   ```

## Initial Setup

### 1. Create First Admin
```bash
curl -X POST http://localhost:8002/api/admin/create \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "Super",
    "lastname": "Admin",
    "email": "admin@yourdomain.com",
    "password": "secure_password_here",
    "password_confirmation": "secure_password_here",
    "is_super_admin": true
  }'
```

### 2. Test Admin Login
```bash
curl -X POST http://localhost:8002/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@yourdomain.com",
    "password": "secure_password_here"
  }'
```

### 3. Access Dashboard
```bash
curl -X GET http://localhost:8002/api/admin/dashboard/overview \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

## Testing Results

✅ **Admin Creation**: Working with super admin support  
✅ **Admin Login**: Generates proper tokens with admin abilities  
✅ **Admin Dashboard**: Accessible with valid admin tokens  
✅ **Security**: Regular users blocked from admin endpoints  
✅ **Middleware**: All admin routes properly protected  

## Database Schema

The `admins` table includes:
- `id` - Primary key
- `firstname` - Admin first name
- `lastname` - Admin last name
- `email` - Unique email address
- `password` - Hashed password
- `is_super_admin` - Boolean flag for super admin privileges
- `email_verified_at` - Email verification timestamp
- `remember_token` - Remember token for sessions
- `created_at` / `updated_at` - Timestamps

## Next Steps

1. **Secure the admin creation endpoint** before production deployment
2. **Test remaining admin endpoints** (pending approvals, user management)
3. **Implement role-based permissions** for different admin levels
4. **Add admin activity logging** for security auditing
5. **Set up proper backup and recovery** procedures for admin accounts
