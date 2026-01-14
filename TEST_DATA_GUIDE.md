# 🎉 Test Data Setup Complete!

Your database is now fully populated with comprehensive mock data for client-side testing.

## 📊 Database Contents

| Entity | Count | Status Breakdown |
|--------|-------|------------------|
| **Users** | 13 | All active, verified |
| **Admins** | 1 | 1 super admin |
| **Seller Profiles** | 6 | Mix of approved/pending |
| **Products** | 13 | Mix of approved/pending/rejected |
| **Business Profiles** | 8 | Various categories (beauty, brand, school) |
| **Orders** | 20 | All statuses (pending, confirmed, shipped, delivered, cancelled) |
| **Reviews** | 17 | Ratings 3-5 stars |
| **Blogs** | 5 | Published, with favorites |
| **FAQs** | 26 | Various categories |
| **Categories** | 133 | Market, beauty, brand, school, sustainability, music |
| **Carts** | 5 | With cart items |
| **Cart Items** | 15 | Various products |
| **Wishlists** | 25 | Multiple users |
| **Addresses** | 5 | Shipping addresses |
| **Notifications** | 30 | Read/unread mix |
| **Subscriptions** | 3 | Active subscriptions |
| **Sustainability Initiatives** | 1 | Environmental project |
| **Adverts** | 1 | Active banner ad |

---

## 🔑 Test Credentials

### Admin Access
```
Email: superadmin@ojaewa.com
Password: password123
Role: Super Admin
```

### Regular Users (All use password: `password123`)
All test users can be found by running:
```bash
php artisan tinker --execute="App\Models\User::all(['firstname', 'lastname', 'email'])"
```

### Quick Test User
```
Email: Check database for first user
Password: password123
```

---

## 🚀 Quick API Testing Commands

### 1. Login as User
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"<user_email>","password":"password123"}'
```

### 2. Get All Products (Public)
```bash
curl http://localhost:8000/api/products/browse
```

### 3. Get Product Details
```bash
curl http://localhost:8000/api/products/public/1
```

### 4. Get Categories with Items
```bash
# Market products
curl http://localhost:8000/api/categories/market/men/items

# Beauty businesses
curl http://localhost:8000/api/categories/beauty/beauty-services/items
```

### 5. Get Blogs
```bash
curl http://localhost:8000/api/blogs
```

### 6. Get FAQs
```bash
curl http://localhost:8000/api/faqs
```

### 7. Search Products
```bash
curl "http://localhost:8000/api/products/search?query=ankara"
```

### 8. Get Authenticated User's Cart (requires token)
```bash
curl http://localhost:8000/api/cart \
  -H "Authorization: Bearer <your_token>"
```

### 9. Get User's Orders (requires token)
```bash
curl http://localhost:8000/api/orders \
  -H "Authorization: Bearer <your_token>"
```

### 10. Get Notifications (requires token)
```bash
curl http://localhost:8000/api/notifications \
  -H "Authorization: Bearer <your_token>"
```

---

## 📱 Client Testing Scenarios

### Scenario 1: Browse Products as Guest
1. GET `/api/products/browse` - See all products
2. GET `/api/products/public/1` - View product details
3. GET `/api/categories` - List all categories
4. GET `/api/categories/market/women/items` - Products in category

### Scenario 2: User Registration & Shopping
1. POST `/api/register` - Create account
2. POST `/api/login` - Get auth token
3. GET `/api/cart` - View cart (empty)
4. POST `/api/cart/items` - Add product to cart
5. GET `/api/cart` - View cart with items
6. POST `/api/orders` - Create order
7. GET `/api/orders` - View order history

### Scenario 3: Seller Journey
1. POST `/api/register` - Create account
2. POST `/api/login` - Get token
3. POST `/api/seller/register` - Register as seller
4. GET `/api/seller/profile` - Check status (pending)
5. (Admin approves seller)
6. GET `/api/seller/profile` - Status now approved
7. POST `/api/products` - Create product
8. GET `/api/products` - View own products

### Scenario 4: Business Profile
1. POST `/api/business/profiles` - Create business
2. GET `/api/business/profiles` - Check status
3. GET `/api/business/public` - View public businesses

### Scenario 5: Reviews & Ratings
1. GET `/api/reviews/product/1` - View product reviews
2. POST `/api/reviews` - Submit review
3. GET `/api/products/public/1` - See updated avg_rating

---

## 🔍 Data Characteristics

### Products
- **Genders**: male, female, unisex
- **Styles**: Traditional, Modern
- **Tribes**: Yoruba, Igbo, Hausa, Pan-African
- **Price Range**: ₦5,000 - ₦45,000
- **Statuses**: approved, pending, rejected

### Orders
- **Statuses**: pending, confirmed, shipped, delivered, cancelled
- **Payment Methods**: paystack
- **Payment Status**: pending, paid, failed
- Some orders have tracking numbers
- Some orders have reviews

### Business Profiles
- **Categories**: beauty, brand, school, music
- **Offering Types**: providing_service, selling_product
- **Statuses**: approved, pending, deactivated

### Reviews
- All reviews have ratings 3-5 stars
- Include headline and detailed body
- Linked to products and users

---

## 🛠️ Reseed Database

To refresh all test data:
```bash
php artisan migrate:fresh --seed
```

**Warning**: This will delete ALL data and recreate fresh test data.

---

## 📝 Notes for Frontend Developers

1. **Authentication**: All endpoints requiring auth need `Authorization: Bearer <token>` header
2. **Pagination**: Most list endpoints return paginated data with `data`, `links`, and `meta`
3. **Status Fields**: Check `status`, `store_status`, or `registration_status` depending on entity
4. **Images**: All images use placeholder URLs - replace with real uploads in production
5. **Relationships**: Many entities include related data (e.g., products include seller_profile)

---

## 🎯 Testing Checklist for Mobile App

- [ ] User registration and login
- [ ] Browse products (public)
- [ ] View product details with seller info
- [ ] Add to cart
- [ ] View cart
- [ ] Create order
- [ ] View order history with different statuses
- [ ] Submit product review
- [ ] View product reviews and ratings
- [ ] Browse categories
- [ ] Search products
- [ ] Wishlist functionality
- [ ] View notifications
- [ ] Seller registration
- [ ] Create products as seller
- [ ] Business profile creation
- [ ] View blogs and FAQs
- [ ] Check subscription status

---

## 💡 Pro Tips

1. **Use Postman/Insomnia**: Import the API endpoints for easier testing
2. **Check Logs**: `tail -f storage/logs/laravel.log` for debugging
3. **Database Inspection**: Use `php artisan tinker` to query data
4. **API Documentation**: Check `api_docs/` folder for detailed endpoint docs
5. **Test Different Users**: Switch between users to test different scenarios

---

Happy Testing! 🚀
