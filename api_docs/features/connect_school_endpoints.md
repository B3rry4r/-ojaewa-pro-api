# Connect & School Services Endpoints

## Connect Information (Public)

### 1. Get All Connect Links
**GET** `/api/connect`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "social_links": {
      "facebook": "https://facebook.com/ojaewa",
      "instagram": "https://instagram.com/ojaewa",
      "twitter": "https://twitter.com/ojaewa",
      "youtube": "https://youtube.com/ojaewa",
      "tiktok": "https://tiktok.com/@ojaewa"
    },
    "contact": {
      "email": "info@ojaewa.com",
      "phone": "+234-800-OJA-EWA",
      "whatsapp": "+2348012345678",
      "address": "123 Fashion Street, Victoria Island, Lagos, Nigeria"
    },
    "app_links": {
      "android": "https://play.google.com/store/apps/details?id=com.ojaewa",
      "ios": "https://apps.apple.com/app/oja-ewa/id123456789",
      "website": "https://ojaewa.com"
    },
    "support": {
      "help_center": "https://help.ojaewa.com",
      "faq": "https://ojaewa.com/faq",
      "terms": "https://ojaewa.com/terms",
      "privacy": "https://ojaewa.com/privacy"
    }
  }
}
```

**Test Coverage:** ✅ Tested

---

### 2. Get Social Media Links Only
**GET** `/api/connect/social`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "facebook": "https://facebook.com/ojaewa",
    "instagram": "https://instagram.com/ojaewa",
    "twitter": "https://twitter.com/ojaewa",
    "youtube": "https://youtube.com/ojaewa",
    "tiktok": "https://tiktok.com/@ojaewa"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 3. Get Contact Information Only
**GET** `/api/connect/contact`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "email": "info@ojaewa.com",
    "phone": "+234-800-OJA-EWA",
    "whatsapp": "+2348012345678",
    "address": "123 Fashion Street, Victoria Island, Lagos, Nigeria",
    "business_hours": "Monday - Friday: 9AM - 6PM WAT",
    "support_hours": "24/7 Online Support"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 4. Get App Download Links
**GET** `/api/connect/app-links`
**Middleware:** None (Public endpoint)

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "android": "https://play.google.com/store/apps/details?id=com.ojaewa",
    "ios": "https://apps.apple.com/app/oja-ewa/id123456789",
    "website": "https://ojaewa.com",
    "version": {
      "android": "2.1.0",
      "ios": "2.1.0",
      "minimum_supported": "2.0.0"
    }
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

## School Registration Services

### 5. Register for School
**POST** `/api/school-registrations`
**Middleware:** None (Public endpoint)

**Request:**
```json
{
  "country": "Nigeria",
  "full_name": "John Doe",
  "phone_number": "+2348012345678",
  "state": "Lagos",
  "city": "Lagos",
  "address": "123 Education Street, Ikeja, Lagos"
}
```

**Validation Rules:**
- `country`: required|string|max:100
- `full_name`: required|string|max:255
- `phone_number`: required|string|max:20
- `state`: required|string|max:100
- `city`: required|string|max:100
- `address`: required|string|max:500

**Response (201):**
```json
{
  "status": "success",
  "message": "School registration submitted successfully",
  "data": {
    "id": 1,
    "country": "Nigeria",
    "full_name": "John Doe",
    "phone_number": "+2348012345678",
    "state": "Lagos",
    "city": "Lagos",
    "address": "123 Education Street, Ikeja, Lagos",
    "status": "pending",
    "payment_reference": null,
    "payment_data": null,
    "created_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:00.000000Z"
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 6. Generate School Payment Link
**POST** `/api/payment/link/school`
**Middleware:** `auth:sanctum`

**Request:**
```json
{
  "registration_id": 1,
  "email": "john.doe@example.com"
}
```

**Validation Rules:**
- `registration_id`: required|integer|exists:school_registrations,id
- `email`: required|email

**Response (200):**
```json
{
  "status": "success",
  "message": "Payment link generated successfully",
  "data": {
    "payment_url": "https://checkout.paystack.com/v3/hosted/pay/xyz789abc123",
    "access_code": "xyz789abc123",
    "reference": "SCHOOL_202501201234_REF",
    "amount": 50000,
    "currency": "NGN",
    "registration_details": {
      "id": 1,
      "full_name": "John Doe",
      "registration_fee": "₦50,000"
    }
  }
}
```

**Error Response (400) - Already Paid:**
```json
{
  "status": "error",
  "message": "Registration fee already paid"
}
```

**Test Coverage:** 🔴 Not Tested

---

### 7. School Payment Webhook
**POST** `/api/webhook/paystack/school`
**Middleware:** None (Webhook endpoint)

**Request Headers:**
```
x-paystack-signature: computed_signature_from_paystack
Content-Type: application/json
```

**Request (Successful Payment):**
```json
{
  "event": "charge.success",
  "data": {
    "reference": "SCHOOL_202501201234_REF",
    "amount": 5000000,
    "currency": "NGN",
    "status": "success",
    "paid_at": "2025-01-20T14:30:00.000000Z",
    "metadata": {
      "registration_id": 1,
      "payment_type": "school_registration",
      "custom_fields": [
        {
          "display_name": "Registration ID",
          "variable_name": "registration_id",
          "value": "1"
        },
        {
          "display_name": "Full Name",
          "variable_name": "full_name",
          "value": "John Doe"
        }
      ]
    },
    "gateway_response": "Approved"
  }
}
```

**Response (200):**
```json
{
  "message": "Webhook received"
}
```

**Error Response (400) - Invalid Signature:**
```json
{
  "message": "Invalid signature"
}
```

**Test Coverage:** 🔴 Not Tested

---

## School Registration Status Flow

### Registration Status:
1. **pending** - Initial registration submitted
2. **processing** - Payment confirmed, processing application
3. **approved** - Registration approved by admin
4. **rejected** - Registration rejected with reason

### School Registration Fee:
- **Amount:** ₦50,000 (NGN 50,000)
- **Payment Method:** Paystack integration
- **Currency:** Nigerian Naira (NGN)

## Configuration Dependencies

### Connect Links Configuration:
The connect endpoints rely on configuration files:
- `config/connect.php` - Main connect configuration
- `config/connect_links.php` - Specific link configurations

### Example Configuration Structure:
```php
// config/connect.php
return [
    'social_links' => [
        'facebook' => env('FACEBOOK_URL', 'https://facebook.com/ojaewa'),
        'instagram' => env('INSTAGRAM_URL', 'https://instagram.com/ojaewa'),
        // ... other social links
    ],
    'contact' => [
        'email' => env('CONTACT_EMAIL', 'info@ojaewa.com'),
        'phone' => env('CONTACT_PHONE', '+234-800-OJA-EWA'),
        // ... other contact info
    ],
    'app_links' => [
        'android' => env('ANDROID_APP_URL'),
        'ios' => env('IOS_APP_URL'),
        // ... other app links
    ]
];
```

## Test Coverage Summary

- **Connect Information:** ✅ 1/4 endpoints tested (25%)
- **School Registration:** 🔴 0/3 endpoints tested (0%)

**Total Connect & School Services:** ✅ 1/7 endpoints tested (14%)

---
*Last Updated: January 2025*