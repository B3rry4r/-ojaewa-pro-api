# Oja Ewa Pro API Documentation

## 📋 Complete API Documentation Structure

This directory contains comprehensive documentation for all 113 endpoints in the Oja Ewa Pro API, organized by functional categories with detailed request/response examples, validation rules, and test coverage status.

## 🗂️ Documentation Organization

### **Authentication** (9 endpoints)
📄 [`auth/authentication_endpoints.md`](auth/authentication_endpoints.md)
- User registration, login, logout
- Password reset functionality
- Google OAuth integration
- Admin authentication
- **Test Coverage:** ✅ 100% (9/9)

### **User Management** (10 endpoints)
📄 [`user_management/user_endpoints.md`](user_management/user_endpoints.md)
- User profile management
- Address management (CRUD)
- Notification preferences
- **Test Coverage:** 🔴 0% (0/10)

### **Product Management** (10 endpoints)
📄 [`product_management/product_endpoints.md`](product_management/product_endpoints.md)
- Product CRUD operations
- Product search and suggestions
- Category management
- **Test Coverage:** 🟡 60% (6/10)

### **Business & Seller Management** (12 endpoints)
📄 [`business_management/business_seller_endpoints.md`](business_management/business_seller_endpoints.md)
- Business profile management
- Seller profile management
- File upload endpoints
- **Test Coverage:** 🟡 75% (9/12)

### **Order & Payment Management** (10 endpoints)
📄 [`order_management/order_payment_endpoints.md`](order_management/order_payment_endpoints.md)
- Order lifecycle management
- Payment integration (Paystack)
- Order tracking and reviews
- **Test Coverage:** 🟡 50% (5/10)

### **Content & Features** (15 endpoints)
📄 [`content_management/content_features_endpoints.md`](content_management/content_features_endpoints.md)
- Blog management and favorites
- FAQ system
- Wishlist functionality
- Subscription management
- **Test Coverage:** 🟡 67% (10/15)

### **Notification Management** (8 endpoints)
📄 [`notifications/notification_endpoints.md`](notifications/notification_endpoints.md)
- User notification system
- Notification preferences
- Read/unread management
- **Test Coverage:** 🟡 50% (4/8)

### **Connect & School Services** (7 endpoints)
📄 [`features/connect_school_endpoints.md`](features/connect_school_endpoints.md)
- Connect information (social links, contact)
- School registration and payment
- **Test Coverage:** 🔴 14% (1/7)

### **Admin Panel - Core** (22 endpoints)
📄 [`admin/admin_panel_endpoints.md`](admin/admin_panel_endpoints.md)
- Dashboard and overview
- User, order, product management
- Seller and business approval
- **Test Coverage:** ✅ 100% (22/22)

### **Admin Panel - Content** (6 endpoints)
📄 [`admin/admin_content_management.md`](admin/admin_content_management.md)
- Blog post management
- Content publication controls
- **Test Coverage:** 🔴 0% (6/6)

### **Admin Panel - Advanced Features** (11 endpoints)
📄 [`admin/admin_advanced_features.md`](admin/admin_advanced_features.md)
- Advertisement management
- Admin notification broadcasting
- Application settings
- School registration management
- **Test Coverage:** 🔴 0% (11/11)

### **Admin Panel - Sustainability** (4 endpoints)
📄 [`admin/admin_sustainability.md`](admin/admin_sustainability.md)
- Sustainability initiative management
- Environmental and social programs
- **Test Coverage:** 🔴 0% (0/4)

## 🔧 **Backend Services & Infrastructure**

### **Real-Time Services & Push Notifications**
📄 [`services/real_time_services.md`](services/real_time_services.md)
- **Pusher Beams Integration** - Multi-platform push notifications (Web, iOS, Android)
- **Email Service (Resend API)** - Transactional email delivery
- **Payment Processing (Paystack)** - Nigerian payment gateway integration
- **Subscription Management** - Automated subscription lifecycle
- **Background Jobs & Scheduling** - Console commands and queue processing

### **Email Template System**
📄 [`services/email_templates.md`](services/email_templates.md)
- **6 Email Templates** - Order confirmations, business approvals, subscription alerts
- **Responsive Design** - Mobile-optimized email layouts
- **Variable Substitution** - Dynamic content rendering
- **Brand Consistency** - Unified Oja Ewa branding

## 📊 Overall Statistics

### **API Endpoints:**
- **Total Endpoints:** 117 across all categories
- **Tested Endpoints:** 67 (57% coverage)
- **Untested Endpoints:** 50 (43% gaps)
- **Documentation Status:** ✅ Complete with payloads

### **Backend Services:**
- **Real-Time Services:** Pusher Beams, Resend Email, Paystack Payment
- **Background Processing:** Subscription reminders, payment webhooks, notifications
- **Email Templates:** 6 responsive templates with brand consistency
- **Console Commands:** Automated subscription management
- **Queue System:** Database/Redis queue processing

## 🚨 Critical Testing Gaps

### **High Priority (Must Test)**
1. **Payment Processing** - All payment endpoints untested
2. **User Profile Management** - Core user functionality
3. **School Services** - Revenue-generating feature
4. **Product Search** - Core marketplace functionality

### **Medium Priority**
1. **Admin Content Management** - Blog and content features
2. **Admin Settings** - Application configuration
3. **File Upload Endpoints** - Document management

## 📋 Test Status Report

For detailed test coverage analysis and recommendations, see:
📄 [`../API_TEST_STATUS_REPORT.md`](../API_TEST_STATUS_REPORT.md)

## 🔧 Quick Reference

### **Base URL Pattern**
- User endpoints: `/api/{endpoint}`
- Admin endpoints: `/api/admin/{endpoint}`
- Public endpoints: No authentication required
- Protected endpoints: Require `auth:sanctum` or `admin.auth`

### **Authentication Headers**
```http
# User Authentication
Authorization: Bearer {user_token}
Content-Type: application/json
Accept: application/json

# Admin Authentication  
Authorization: Bearer {admin_token}
Content-Type: application/json
Accept: application/json
```

### **Common Response Format**
```json
{
  "status": "success|error",
  "message": "Human readable message",
  "data": { /* Response payload */ }
}
```

## 🚀 Next Steps

1. **Review Critical Gaps** - Focus on payment and user management testing
2. **Run Test Suite** - Execute existing tests to verify current functionality
3. **Implement Missing Tests** - Create tests for untested endpoints
4. **API Documentation** - Consider generating OpenAPI/Swagger documentation
5. **Performance Testing** - Load test critical endpoints

---

*Documentation Last Updated: January 2025*  
*API Version: 2.x*  
*Total Documentation Coverage: 100% ✅*