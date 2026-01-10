# Email Template System

## Template Architecture

### Template Location
All email templates are located in `resources/views/emails/`

### Base Layout
**File:** `layout.blade.php`
Provides consistent branding and structure for all emails.

## Available Email Templates

### 1. Business Approval Email
**File:** `business_approved.blade.php`
**Trigger:** When admin approves a business profile
**Variables:**
```php
[
    'business' => BusinessProfile,
    'user' => User,
    'approval_date' => Carbon,
]
```

**Subject:** "Business Profile Approved - Oja Ewa"

**Content Preview:**
```
Congratulations! Your business profile "{business_name}" has been approved 
and is now live on the Oja Ewa platform. You can start showcasing your 
products and services to our community.
```

---

### 2. Order Confirmation Email
**File:** `order_created.blade.php`
**Trigger:** When a new order is placed
**Variables:**
```php
[
    'order' => Order,
    'user' => User,
    'orderItems' => Collection,
    'total' => float,
]
```

**Subject:** "Order Confirmation #{{order_id}} - Oja Ewa"

**Content Includes:**
- Order summary
- Item details with images
- Total amount
- Delivery information
- Payment status
- Tracking information (if available)

---

### 3. Order Status Update Email
**File:** `order_status_updated.blade.php`
**Trigger:** When order status changes
**Variables:**
```php
[
    'order' => Order,
    'user' => User,
    'oldStatus' => string,
    'newStatus' => string,
    'statusClass' => string, // CSS class for styling
    'trackingNumber' => string|null,
]
```

**Subject:** "Order Status Update - Oja Ewa"

**Status Messages:**
- **Processing:** "Your order is being prepared"
- **Shipped:** "Your order has been shipped" + tracking number
- **Delivered:** "Your order has been delivered"
- **Cancelled:** "Your order has been cancelled" + reason

---

### 4. Subscription Reminder Email
**File:** `subscription_reminder.blade.php`
**Trigger:** Before subscription expiration (scheduled command)
**Variables:**
```php
[
    'subscription' => Subscription,
    'user' => User,
    'daysLeft' => int,
    'renewalUrl' => string,
]
```

**Subject:** "Subscription Renewal Reminder - Oja Ewa"

**Content Preview:**
```
Your {{plan_name}} subscription expires in {{days_left}} days. 
Don't lose access to your premium features - renew now to continue 
enjoying uninterrupted service.
```

**Call-to-Action:** "Renew Subscription" button

---

### 5. Subscription Status Email
**File:** `subscription_status.blade.php`
**Trigger:** Subscription lifecycle events
**Variables:**
```php
[
    'subscription' => Subscription,
    'user' => User,
    'status' => string, // 'renewed', 'cancelled', 'expired', 'payment_failed'
    'reason' => string|null,
]
```

**Subject Variations:**
- **Renewed:** "Subscription Renewed - Oja Ewa"
- **Cancelled:** "Subscription Cancelled - Oja Ewa" 
- **Expired:** "Subscription Expired - Oja Ewa"
- **Payment Failed:** "Subscription Payment Failed - Oja Ewa"

**Status-Specific Content:**
```php
@if($status === 'renewed')
    Your subscription has been successfully renewed until {{expires_at}}.
@elseif($status === 'payment_failed')
    We couldn't process your payment. Please update your payment method.
@elseif($status === 'expired')
    Your subscription has expired. Renew now to restore access.
@endif
```

---

## Email Template Features

### 1. Responsive Design
All templates are mobile-responsive with:
- Fluid layouts
- Optimized font sizes
- Touch-friendly buttons
- Dark mode support

### 2. Brand Consistency
Templates include:
- Oja Ewa logo and branding
- Consistent color scheme
- Brand fonts and typography
- Footer with social links

### 3. Internationalization Ready
Templates support:
- Multiple languages (future)
- Localized date formats
- Currency formatting
- Cultural customization

### 4. Rich Content Support
Templates can include:
- Product images
- Order summaries
- Interactive buttons
- Social media links
- Personalization tokens

## Template Variables Guide

### Common Variables Available in All Templates
```php
[
    'user' => [
        'id' => int,
        'firstname' => string,
        'lastname' => string,
        'email' => string,
        'name' => string, // accessor for firstname + lastname
    ],
    'appName' => 'Oja Ewa',
    'appUrl' => 'https://ojaewa.com',
    'supportEmail' => 'support@ojaewa.com',
    'currentYear' => 2025,
]
```

### Order-Specific Variables
```php
[
    'order' => [
        'id' => int,
        'total_price' => float,
        'status' => string,
        'tracking_number' => string|null,
        'created_at' => Carbon,
        'orderItems' => [
            [
                'product' => Product,
                'quantity' => int,
                'unit_price' => float,
            ]
        ]
    ]
]
```

### Business-Specific Variables
```php
[
    'business' => [
        'id' => int,
        'business_name' => string,
        'category' => string,
        'store_status' => string,
        'business_description' => string,
    ]
]
```

### Subscription-Specific Variables
```php
[
    'subscription' => [
        'id' => int,
        'plan_name' => string,
        'price' => float,
        'status' => string,
        'expires_at' => Carbon,
        'next_billing_date' => Carbon,
    ]
]
```

## Email Delivery Configuration

### Resend API Integration
```php
// Email sending via NotificationService
Http::post('https://api.resend.com/emails', [
    'from' => 'Oja Ewa <noreply@ojaewa.com>',
    'to' => [$user->email],
    'subject' => $subject,
    'html' => $htmlContent,
]);
```

### Email Types Supported
- **Transactional emails** (order confirmations, status updates)
- **Notification emails** (account activities, security alerts)
- **Marketing emails** (promotions, newsletters)
- **System emails** (maintenance, updates)

### Delivery Features
- ✅ HTML email support
- ✅ Template rendering with Blade
- ✅ Variable substitution
- ✅ Error handling and logging
- ✅ Delivery status tracking
- 🔄 Bounce handling
- 🔄 Unsubscribe management

## Testing Email Templates

### Preview in Browser
```php
// Create a test route to preview templates
Route::get('/email-preview/{template}', function($template) {
    return view('emails.' . $template, [
        // Mock data for preview
    ]);
});
```

### Sample Test Data
```php
$mockOrder = [
    'id' => 123,
    'total_price' => 45000,
    'status' => 'shipped',
    'tracking_number' => 'TRK123456789',
    'created_at' => now(),
];

$mockUser = [
    'firstname' => 'John',
    'lastname' => 'Doe',
    'email' => 'john.doe@example.com',
];
```

## Email Analytics & Tracking

### Available Metrics
- Email delivery rates
- Open rates (via tracking pixels)
- Click-through rates (via tracked links)
- Bounce rates
- Unsubscribe rates

### Logging
All email sending attempts are logged:
```php
Log::info("Email notification sent successfully", [
    'to' => $user->email,
    'subject' => $subject,
    'view' => $view,
    'delivery_id' => $response['id']
]);
```

---
*Last Updated: January 2025*