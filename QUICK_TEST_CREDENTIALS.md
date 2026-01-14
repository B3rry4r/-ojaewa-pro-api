# 🔑 Quick Test Credentials

## Admin Login
```
Email: admin@ojaewa.com
Password: password123
```

## Regular Users (All use: password123)
```
1. janick26@example.net
2. candice94@example.org
3. ioberbrunner@example.com
4. name.kilback@example.com
5. lesch.schuyler@example.net
```

## Quick API Test
```bash
# 1. Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"janick26@example.net","password":"password123"}'

# Copy the token from response, then:

# 2. Get user profile
curl http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# 3. View products
curl http://localhost:8000/api/products/browse

# 4. View cart
curl http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Database Summary
- 13 Users
- 13 Products (various statuses)
- 20 Orders (all statuses)
- 17 Reviews
- 6 Seller Profiles
- 8 Business Profiles
- 5 Blogs
- 133 Categories
- 30 Notifications

## Reseed Database
```bash
php artisan migrate:fresh --seed
```

This will regenerate all test data from scratch.
