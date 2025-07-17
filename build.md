You are building a Laravel 11 API-only project called **Oja Ewa**. This is Phase 1 of the build.

1. Install Laravel Sanctum for token-based user authentication.
2. Create a `users` table with the following fields:
   - id, firstname, lastname, email (unique), phone (nullable), password, email_verified_at, remember_token, timestamps
   - Make all fields nullable where possible, except email and password.
3. Create a `User` model and a seeder that creates 5 fake users.
4. Add a `login` and `register` route for users:
   - POST /api/login → email + password
   - POST /api/register → firstname, lastname, email, phone, password
5. Create `forgot password` and `reset password` endpoints:
   - POST /api/password/forgot
   - POST /api/password/reset
6. Set up Google OAuth login:
   - POST /api/oauth/google → accepts an access token from frontend
   - Verify token via Google API and register/login user
   - If phone is missing, require it to be submitted by frontend
   - Still require setting a password after Google sign-in
7. Create an `admins` table with: id, firstname, lastname, email (unique), password, timestamps
8. Create an `Admin` model and seeder that creates 1 superadmin:
   - email: admin@ojaewa.com
   - password: Admin@1234 (hashed)
9. Set up a separate `admin` guard in `auth.php` using `admins` table
10. Add admin login route:
   - POST /api/admin/login → email + password
11. Add feature tests for:
   - User login/register
   - Google login
   - Admin login
12. Generate Swagger/OpenAPI docs for:
   - Auth endpoints (grouped by `User Auth` and `Admin Auth`)
   - Output a Postman-importable JSON file

13. **At the end, generate a clear and comprehensive summary/report of what was created**, including:
   - Files added or modified
   - Routes generated
   - Models and seeders
   - Tests added
   - Swagger groupings

Use Laravel best practices. Avoid unnecessary constraints. Keep migrations and logic minimal. All models must include seeders.


--------


Continue building the Laravel 12 API-only project "Oja Ewa" (Phase 2).

This phase is about enabling any user to become a seller by creating a seller profile.

1. Create a `seller_profiles` table with these fields:
   - id, user_id (nullable), country, state, city, address
   - business_email, business_phone_number
   - instagram (nullable), facebook (nullable)
   - identity_document (nullable string)
   - business_name, business_registration_number
   - business_certificate (nullable string)
   - business_logo (nullable string)
   - bank_name, account_number
   - registration_status (enum: pending, approved, rejected), timestamps

2. Set up a one-to-one relationship between `User` and `SellerProfile` (a user becomes a seller if they have a profile).

3. Create the `SellerProfile` model with a factory and seeder.

4. Add the following authenticated endpoints:
   - GET /api/seller/profile → return current user’s seller profile
   - POST /api/seller/profile → create a new seller profile
   - PUT /api/seller/profile → update the current seller profile
   - DELETE /api/seller/profile → soft delete seller profile (accepts reason field)

5. Add file upload support (mock/stub for now) for:
   - `identity_document`, `business_certificate`, `business_logo`
   - Assume files will be uploaded to DigitalOcean Spaces

6. Add basic authorization to ensure only the user who owns the profile can modify it.

7. Add feature tests for:
   - Creating a seller profile
   - Getting, updating, and deleting a seller profile
   - Uploading files

8. Generate Swagger/OpenAPI documentation grouped under “Seller Profile”

9. Ensure the model includes a factory and seeder with at least 3 fake seller profiles linked to existing users.

10. At the end, generate a comprehensive report summarizing:
   - Migrations
   - Models, controllers, routes
   - Tests written
   - Swagger changes
   - Seeders created
   - Anything else Windsurf did

Follow Laravel best practices. Avoid foreign key constraints unless absolutely required. Keep migrations and logic minimal.


--------


Continue the Laravel 12 API-only project "Oja Ewa" (Phase 3).

This phase covers: seller products, multi-item orders, and polymorphic reviews.

---

1. Create `products` table:
- id, seller_profile_id (nullable), name, gender (enum), style, tribe, description, image, size
- processing_time_type (enum: normal, quick_quick), processing_days, price
- status (enum: pending, approved, rejected), timestamps
- soft deletes enabled

2. Create `orders` table:
- id, user_id, total_price, status (enum: pending, paid, cancelled), timestamps

3. Create `order_items` table:
- id, order_id, product_id, quantity, unit_price

4. Create `reviews` table (polymorphic):
- id, user_id, reviewable_id, reviewable_type
- rating (1–5), headline, body, timestamps

---

5. Define relationships:
- SellerProfile hasMany Products
- Product belongsTo SellerProfile
- Order belongsTo User
- Order hasMany OrderItems
- OrderItem belongsTo Product
- Review belongsTo User, morphTo Reviewable

6. Endpoints:

**Product endpoints (auth required):**
- POST /api/seller/products → create product
- GET /api/seller/products → list my products
- GET /api/seller/products/{id} → view my product
- PUT /api/seller/products/{id} → update product
- DELETE /api/seller/products/{id} → delete product

**Order endpoints (auth required):**
- POST /api/orders → create order with cart (product_ids, quantities)
- GET /api/orders → list user’s orders
- GET /api/orders/{id} → view specific order

**Review endpoints (auth required):**
- POST /api/reviews → body, rating, reviewable_type, reviewable_id
- GET /api/reviews/{reviewable_type}/{id} → list reviews for entity

7. Return average rating in:
- Product responses
- Reviewable models should have a computed avg_rating attribute

8. Add "You may also like" logic:
- GET /api/products/{id}/suggestions → return up to 5 products with similar gender/style/tribe

---

9. Tests:
- Create/edit/delete product
- Create order with multiple items
- Write reviews for different models
- Get reviews + check average rating logic
- Auth and validation tests

10. Swagger/OpenAPI docs:
- Grouped under "Products", "Orders", "Reviews"
- Full request/response schemas
- Example payloads for cart and review types

11. Seeders:
- Add 10 random products for existing sellers
- Add 2 orders per user with fake items
- Add reviews for some products

12. At the end, generate a comprehensive summary of:
- Migrations, models, routes
- Seeders and factories
- Tests written
- Swagger changes
- Key logic implemented

Follow Laravel best practices. Keep relationships clean and avoid unnecessary constraints.


--------


Continue the Laravel 12 API-only project "Oja Ewa" (Phase 4).

This phase covers the "Show Your Business" section where users can register and manage their business profiles.

---

1. Create `business_profiles` table with the following columns:
- id, user_id (nullable), category (enum: beauty, brand, school, music)
- country, state, city, address
- business_email, business_phone_number
- website_url (nullable), instagram (nullable), facebook (nullable)
- identity_document (nullable)
- business_name, business_description
- business_logo (nullable)
- offering_type (enum: selling_product, providing_service, nullable)
- product_list (JSON, nullable), service_list (JSON, nullable)
- business_certificates (JSON array, nullable)
- professional_title (nullable)
- school_type (enum: fashion, music, catering, beauty, nullable)
- school_biography (nullable)
- classes_offered (JSON, nullable)
- music_category (enum: dj, artist, producer, nullable)
- youtube (nullable), spotify (nullable)
- store_status (enum: pending, approved, deactivated), default: pending
- subscription_status (enum: active, expired)
- subscription_ends_at (nullable datetime)
- soft deletes, timestamps

---

2. Create `BusinessProfile` model:
- With casts, fillables, and relationships
- Add logic to enforce field relevance by category and offering_type

---

3. Endpoints (auth required):

- POST /api/business → create new business
- GET /api/business → get all businesses by current user
- PUT /api/business/{id} → update business
- DELETE /api/business/{id} → delete business
- PATCH /api/business/{id}/deactivate → update store_status to deactivated

4. Validation rules:
- If offering_type = providing_service → require service_list + professional_title
- If offering_type = selling_product → require product_list + business_certificates
- If category = school → require school_type, school_biography, classes_offered
- If category = music → require music_category, identity_document, youtube/spotify
- Document fields must be nullable but validated if provided

---

5. File upload:
- Stub file upload logic for `business_logo`, `identity_document`
- Simulate upload to DigitalOcean Spaces

---

6. Seeders:
- Create one sample business per category for existing users
- Use random mix of offering types and data

---

7. Tests:
- Create, update, delete business
- Validation tests for each offering/category type
- Deactivation and access control tests

---

8. Swagger/OpenAPI:
- Tag group: “Show Your Business”
- Include request/response schemas
- Enum, JSON array, and file upload examples

---

9. At the end, generate a full report summarizing:
- Migrations, models, routes, controllers
- Seeders and tests
- Swagger additions
- Any key logic or decisions

Use Laravel best practices. Avoid foreign keys unless required. Keep logic simple, clean, and well-validated.


--------


Continue building the Laravel 12 API-only project "Oja Ewa" (Phase 5).

This phase focuses on the Admin Dashboard.

---

1. Use the existing `admin` guard and protect all routes with it.

2. Create controllers for admin actions:
- AdminOverviewController
- AdminSellerController
- AdminProductController
- AdminOrderController
- AdminBusinessController (handles beauty, brand, school, music)
- AdminUserController

---

3. Endpoints (all under /api/admin):

**Authentication**
- POST /api/admin/login → already exists

**Overview**
- GET /dashboard/overview → returns:
   - total_users, total_revenue (sum of paid orders)
   - total_businesses, total_sellers
   - market_revenue (sum of paid product-based orders)

**Pending Sellers**
- GET /pending/sellers
- PATCH /seller/{id}/approve → approve/reject seller

**Pending Product Listings**
- GET /pending/products
- PATCH /product/{id}/approve → approve/reject product

**New Orders**
- GET /orders?status=new
- GET /order/{id}
- PATCH /order/{id}/status

**User Management**
- GET /users (paginated list: name, reg date, phone, email)

**Market Management**
- GET /market/sellers (paginated)
- GET /market/products (paginated)
- PATCH /market/seller/{id}/status
- PATCH /market/product/{id}/status

**Business Approval Pages**
For each of: beauty, brand, school, music
- GET /business/{category}
- GET /business/{category}/{id}
- PATCH /business/{category}/{id}/status

---

4. Add policies/logic to:
- Allow only admins
- Prevent invalid updates

5. Tests:
- Admin can fetch and paginate dashboard resources
- Admin can approve/reject sellers, products, businesses
- Admin can update order status

6. Swagger/OpenAPI:
- Group all admin routes under “Admin”
- Describe all request/response formats, including enums

7. Seeder:
- Create sample pending sellers, products, and businesses for testing

8. At the end, generate a summary report including:
- Migrations/Models (if any)
- Routes
- Tests
- Swagger docs
- Special logic or access control notes

Use Laravel best practices. Avoid unnecessary complexity. Keep routes grouped and consistent.


--------


Continue building the Laravel 12 API-only project "Oja Ewa" (Phase 6).

This phase covers final user-side features: wishlist, blog, notifications, FAQs, and social connect.

---

1. Create `wishlists` table:
- id, user_id, wishlistable_id, wishlistable_type, timestamps
- Polymorphic: allow saving both products and services
- User can save/remove items to/from wishlist

2. Create `blogs` table:
- id, title, slug, body (long text), featured_image (string), published_at (nullable datetime), timestamps
- Blog belongsTo Admin (optional)

3. Create `faqs` table:
- id, question, answer, category (nullable), timestamps

4. Create `notifications` table (already present or confirm):
- id, user_id, type (push, email), event (e.g. new_order), title, message, payload (JSON), read_at, timestamps

5. Add static config file `connect_links.php`:
- Contains Instagram, Facebook, Twitter URLs

---

6. Endpoints (all auth required except FAQs/blog viewing):

**Wishlist**
- GET /api/wishlist → get all items
- POST /api/wishlist → add to wishlist (wishlistable_type, wishlistable_id)
- DELETE /api/wishlist → remove from wishlist (wishlistable_type, wishlistable_id)

**Blog**
- GET /api/blogs → paginated list (10 per page)
- GET /api/blogs/{slug} → single post with 5 related posts
- GET /api/blogs/favorites → list of favorited posts
- POST /api/blogs/{id}/favorite → toggle favorite

**FAQs**
- GET /api/faqs → all FAQs (optionally grouped by category)

**Notifications**
- GET /api/notifications → list user notifications
- PATCH /api/notifications/{id}/read → mark as read
- Add default push/email prefs in user settings

**Connect**
- GET /api/connect → return Instagram, Twitter, Facebook links from config

---

7. Tests:
- Wishlist add/remove
- Blog fetch, favorite
- FAQs returned properly
- Notifications list + mark as read
- Social links return expected config

---

8. Seeders:
- 5 blogs with tags + featured images
- 10 FAQs with categories
- Notifications and wishlist items per user

---

9. Swagger/OpenAPI:
- Grouped under “Wishlist”, “Blog”, “FAQs”, “Notifications”, “Connect”
- All request/response formats documented

---

10. Final report must include:
- Migrations + models
- All routes
- Test results
- Seeder info
- Swagger updates

Use Laravel best practices. Use polymorphic relationships where needed. Minimize logic duplication.


--------


Continue the Laravel 12 API-only project "Oja Ewa" (Phase 7).

This phase covers transactional emails (via Resend) and real-time push notifications (via Pusher Beams).

---

1. Email Setup (Resend):
- Create NotificationService class
- Add method: sendEmail(to_email, subject, view, data = [])
- Configure Resend using API key from `.env` (RESEND_API_KEY)
- Use Laravel's HTTP client or Guzzle to call Resend
- Email templates should use rich HTML with:
   - Oja Ewa logo at the top (https://example.com/logo.png placeholder)
   - Styled content using inlined CSS

2. Push Notifications (Pusher Beams):
- Add method in NotificationService: sendPush(to_user, title, body, payload = [])
- Use `.env` config keys for:
   - PUSHER_BEAMS_INSTANCE_ID
   - PUSHER_BEAMS_SECRET_KEY
- Pushes should be sent to users by their user ID or external user ID

3. Add Notification Triggers for:

📦 Orders
- New order → email to buyer, push to seller
- Status change → email + push to buyer

🏢 Business
- Approval/rejection → email + push to user

💳 Subscriptions
- Renewal reminder (scheduled) → email
- Success/failure → email + push

📰 Blog
- New post published → optional push to all users

4. Create rich HTML views for these emails:
- order_created.blade.php
- order_status_updated.blade.php
- business_approved.blade.php
- subscription_reminder.blade.php
- subscription_status.blade.php

Include logo at top (logo URL placeholder) and styled body content.

5. Add `.env` placeholders:
- RESEND_API_KEY
- PUSHER_BEAMS_INSTANCE_ID
- PUSHER_BEAMS_SECRET_KEY

6. Tests:
- Mock email and push calls
- Test NotificationService methods
- Confirm triggers call NotificationService (e.g. in OrderController)

7. Swagger:
- Note that emails and pushes are triggered internally, no public endpoints
- Add developer note on which events generate user notifications

8. Final report should summarize:
- NotificationService methods
- HTML templates created
- Trigger coverage
- Test results

Follow Laravel best practices. Centralize logic. Keep templates clean and brand-consistent.
