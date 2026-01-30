# Admin Notifications & Settings

Base: `/api/admin`

**Requires:** `Authorization: Bearer {token}` (with admin abilities)

---

## Notifications

### POST /admin/notifications/send
Send notifications to users based on recipient type.

**Request:**
```json
{
  "title": "Important System Update",
  "message": "We have released a new feature to improve your experience. Please check it out!",
  "event": "system_update",
  "recipient_type": "all",
  "action_url": "https://app.example.com/features/new",
  "send_immediately": true
}
```

**For specific recipients:**
```json
{
  "title": "Special Seller Promotion",
  "message": "You have been selected for an exclusive seller promotion program",
  "event": "seller_promotion",
  "recipient_type": "specific",
  "user_ids": [10, 20, 35],
  "action_url": "https://app.example.com/seller/promotion",
  "send_immediately": true
}
```

**For sellers only:**
```json
{
  "title": "Seller Dashboard Update",
  "message": "Check out the new analytics features in your seller dashboard",
  "event": "dashboard_update",
  "recipient_type": "sellers",
  "action_url": "https://app.example.com/seller/dashboard",
  "send_immediately": true
}
```

**Valid event examples:** `system_update`, `seller_promotion`, `dashboard_update`, `new_feature`, `maintenance_alert`, etc.

**Valid recipient_types:** `all`, `specific`, `sellers`

**Response (201):**
```json
{
  "status": "success",
  "message": "Notification sent successfully to 1250 users",
  "data": {
    "recipients_count": 1250,
    "notification": {
      "title": "Important System Update",
      "message": "We have released a new feature to improve your experience...",
      "event": "system_update",
      "recipient_type": "all"
    }
  }
}
```

**Error Response (422) — Missing required fields:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "user_ids": ["The user_ids field is required when recipient_type is specific."]
  }
}
```

---

## Settings

### GET /admin/settings
Retrieve all application settings.

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
    "support_email": "support@example.com",
    "support_phone": "+2348012345678"
  }
}
```

**Settings Description:**
- `maintenance_mode` — Enable/disable maintenance mode for the entire application
- `maintenance_message` — Message displayed during maintenance
- `allow_registrations` — Allow new user registrations
- `allow_seller_registrations` — Allow new seller registrations
- `max_products_per_seller` — Maximum products a seller can list
- `commission_rate` — Percentage commission on marketplace sales
- `default_currency` — Default currency for transactions
- `email_notifications_enabled` — Enable email notifications
- `sms_notifications_enabled` — Enable SMS notifications
- `auto_approve_products` — Automatically approve new products
- `auto_approve_sellers` — Automatically approve new sellers
- `min_order_amount` — Minimum order amount in NGN
- `max_order_amount` — Maximum order amount in NGN
- `featured_products_limit` — Maximum featured products visible
- `support_email` — Support contact email
- `support_phone` — Support contact phone number

---

### PUT /admin/settings
Update application settings.

**Request (partial update):**
```json
{
  "maintenance_mode": true,
  "maintenance_message": "Server maintenance scheduled for tonight 10 PM - 2 AM",
  "commission_rate": 6.5,
  "featured_products_limit": 25,
  "support_email": "support-new@example.com"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Application settings updated successfully",
  "data": {
    "maintenance_mode": true,
    "maintenance_message": "Server maintenance scheduled for tonight 10 PM - 2 AM",
    "commission_rate": 6.5,
    "featured_products_limit": 25,
    "support_email": "support-new@example.com"
  }
}
```

**Validation Rules:**
- `commission_rate` — Must be between 0 and 100
- `max_products_per_seller` — Must be between 1 and 1000
- `min_order_amount` — Must be >= 0
- `max_order_amount` — Must be >= 1000
- `featured_products_limit` — Must be between 1 and 100
- `default_currency` — Must be one of: `NGN`, `USD`, `EUR`, `GBP`
- `support_email` — Must be a valid email format
- `maintenance_message` — Maximum 500 characters

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "commission_rate": ["The commission_rate must be between 0 and 100."],
    "max_order_amount": ["The max_order_amount must be at least 1000."]
  }
}
```

**Note:** Settings are cached permanently. Changes take effect immediately across the entire application.
