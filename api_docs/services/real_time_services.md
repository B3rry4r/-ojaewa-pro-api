# Real-Time & Background Services

## Push Notification Service (Pusher Beams)

### Service Overview
The Oja Ewa Pro platform integrates **Pusher Beams** for real-time push notifications across web, iOS, and Android platforms.

### Configuration
```php
// config/services.php
'pusher_beams' => [
    'instance_id' => env('PUSHER_BEAMS_INSTANCE_ID'),
    'secret_key' => env('PUSHER_BEAMS_SECRET_KEY'),
],
```

### Environment Variables Required
```env
PUSHER_BEAMS_INSTANCE_ID=your_instance_id
PUSHER_BEAMS_SECRET_KEY=your_secret_key
```

### Push Notification Features

#### 1. Individual User Notifications
**Service Method:** `NotificationService::sendPush()`

```php
public function sendPush(User $user, string $title, string $body, array $payload = []): bool
```

**Platform Support:**
- **Web Notifications** - Browser push notifications
- **FCM (Android)** - Firebase Cloud Messaging
- **APNS (iOS)** - Apple Push Notification Service

**Payload Structure:**
```json
{
  "interests": ["user-{user_id}"],
  "web": {
    "notification": {
      "title": "Order Status Updated",
      "body": "Your order #123 has been shipped",
      "deep_link": "/orders/123",
      "icon": "https://example.com/logo.png"
    },
    "data": {
      "order_id": 123,
      "status": "shipped"
    }
  },
  "fcm": {
    "notification": {
      "title": "Order Status Updated",
      "body": "Your order #123 has been shipped"
    },
    "data": {
      "order_id": "123",
      "status": "shipped"
    }
  },
  "apns": {
    "aps": {
      "alert": {
        "title": "Order Status Updated",
        "body": "Your order #123 has been shipped"
      }
    },
    "data": {
      "order_id": "123",
      "status": "shipped"
    }
  }
}
```

#### 2. Broadcast Notifications (All Users)
**Service Method:** `NotificationService::sendPushToAllUsers()`

```php
public function sendPushToAllUsers(string $title, string $body, array $payload = []): bool
```

**Use Cases:**
- New blog post announcements
- System maintenance alerts
- Platform-wide promotions
- Feature announcements

**Example Usage:**
```php
$notificationService->sendPushToAllUsers(
    'New Blog Post',
    'Check out our latest blog post: The Future of African Fashion',
    [
        'blog_id' => 1,
        'blog_slug' => 'future-of-african-fashion',
        'deep_link' => '/blogs/future-of-african-fashion'
    ]
);
```

#### 3. Interest-Based Targeting
**Available Interest Groups:**
- `user-{id}` - Individual user targeting
- `all-users` - Broadcast to all users
- Future: `sellers`, `buyers`, `region-{code}`, `category-{name}`

### Real-Time Notification Triggers

#### Automatic Notifications Sent:
1. **Order Events:**
   - Order created → Customer notification
   - Order status updated → Customer notification
   - Order delivered → Customer notification

2. **Product Events:**
   - Product approved → Seller notification
   - Product rejected → Seller notification

3. **Business Events:**
   - Business profile approved → Business owner notification
   - Seller profile approved → Seller notification

4. **Content Events:**
   - New blog published → All users notification
   - System announcements → All users notification

5. **Subscription Events:**
   - Subscription activated → Customer notification
   - Subscription expiring → Customer notification
   - Payment failed → Customer notification

### Deep Linking Support
All push notifications support deep linking to specific app screens:

```json
{
  "deep_link": "/orders/123",           // Order details
  "deep_link": "/products/456",         // Product page
  "deep_link": "/blogs/slug",           // Blog post
  "deep_link": "/subscription/manage",  // Subscription page
  "deep_link": "/notifications",        // Notifications center
}
```

### Error Handling & Logging
All notification sending is logged for debugging:

```php
// Success logging
Log::info("Push notification sent successfully", [
    'user_id' => $user->id,
    'title' => $title,
    'body' => $body
]);

// Error logging
Log::error("Failed to send push notification", [
    'user_id' => $user->id,
    'response' => $response->body(),
    'status' => $response->status()
]);
```

---

## Email Notification Service (Resend)

### Service Overview
The platform uses **Resend API** for transactional email delivery with HTML templating.

### Configuration
```php
// config/services.php
'resend' => [
    'api_key' => env('RESEND_API_KEY'),
    'from_email' => env('RESEND_FROM_EMAIL', 'noreply@ojaewa.com'),
],
```

### Environment Variables Required
```env
RESEND_API_KEY=your_resend_api_key
RESEND_FROM_EMAIL=noreply@ojaewa.com
```

### Email Templates Available
Located in `resources/views/emails/`:

1. **business_approved.blade.php** - Business profile approval
2. **layout.blade.php** - Base email template
3. **order_created.blade.php** - Order confirmation
4. **order_status_updated.blade.php** - Order status changes
5. **subscription_reminder.blade.php** - Subscription renewal reminders
6. **subscription_status.blade.php** - Subscription status changes

### Email Features

#### 1. Transactional Emails
**Service Method:** `NotificationService::sendEmail()`

```php
public function sendEmail(User $user, string $subject, string $view, array $data = []): bool
```

**Template Rendering:**
```php
$htmlContent = View::make("emails.{$view}", $data)->render();
```

#### 2. Dual Channel Notifications
**Service Method:** `NotificationService::sendEmailAndPush()`

```php
public function sendEmailAndPush(
    User $user, 
    string $subject, 
    string $emailView, 
    string $pushTitle, 
    string $pushBody, 
    array $emailData = [], 
    array $pushPayload = []
): array
```

**Returns:**
```php
[
    'email_sent' => true,
    'push_sent' => true,
]
```

---

## Payment Processing Service (Paystack)

### Service Overview
Comprehensive payment processing using **Paystack API** for Nigerian market.

### Configuration
```php
// config/services.php
'paystack' => [
    'public_key' => env('PAYSTACK_PUBLIC_KEY'),
    'secret_key' => env('PAYSTACK_SECRET_KEY'),
],
```

### Payment Features

#### 1. Payment Initialization
```php
PaystackService::initializePayment(array $paymentData): array
```

**Supported Payment Channels:**
- Card payments
- Bank transfers
- USSD payments
- QR code payments
- Mobile money
- Bank transfers

#### 2. Payment Verification
```php
PaystackService::verifyPayment(string $reference): array
```

#### 3. Webhook Security
```php
PaystackService::verifyWebhookSignature(string $input, string $signature): bool
```

#### 4. Currency Conversion
```php
PaystackService::convertToKobo(float $amount): int
PaystackService::convertFromKobo(int $amount): float
```

### Payment Types Supported
1. **Order Payments** - Product purchases
2. **School Registration Payments** - Educational services
3. **Subscription Payments** - Business subscriptions
4. **Service Payments** - Platform fees

---

## Subscription Management Service

### Service Overview
Automated subscription lifecycle management with notifications.

### Subscription Features

#### 1. Subscription Creation
```php
SubscriptionService::createSubscription(User $user, array $data): Subscription
```

#### 2. Automatic Renewals
```php
SubscriptionService::renewSubscription(Subscription $subscription): bool
```

#### 3. Payment Failure Handling
```php
SubscriptionService::handlePaymentFailure(Subscription $subscription, string $reason = null): void
```

#### 4. Subscription Cancellation
```php
SubscriptionService::cancelSubscription(Subscription $subscription): bool
```

#### 5. Automatic Expiration
```php
SubscriptionService::expireSubscriptions(): int
```

### Subscription Status Flow
1. **active** - Subscription is current and valid
2. **payment_failed** - Payment processing failed
3. **expired** - Subscription has expired
4. **cancelled** - User or admin cancelled subscription

---

## Background Jobs & Scheduling

### Console Commands Available

#### 1. Subscription Reminder Command
**Command:** `php artisan subscriptions:send-reminders`

**Purpose:** Send renewal reminders to users with expiring subscriptions

**Options:**
```bash
php artisan subscriptions:send-reminders --days=7  # 7 days before expiration
php artisan subscriptions:send-reminders --days=3  # 3 days before expiration  
php artisan subscriptions:send-reminders --days=1  # 1 day before expiration
```

**Scheduled Execution:**
```php
// In app/Console/Kernel.php (if scheduled)
$schedule->command('subscriptions:send-reminders --days=7')->daily();
$schedule->command('subscriptions:send-reminders --days=3')->daily();
$schedule->command('subscriptions:send-reminders --days=1')->daily();
```

### Queue Configuration
Located in `config/queue.php`:

**Supported Queue Drivers:**
- Database queues
- Redis queues
- Beanstalkd queues
- Amazon SQS queues

**Job Storage:**
- Jobs table: `jobs`
- Failed jobs table: `failed_jobs`
- Job batches table: `job_batches`

---

## Integration Summary

### Third-Party Services Integrated
1. **Pusher Beams** - Real-time push notifications
2. **Resend API** - Transactional email delivery
3. **Paystack API** - Payment processing
4. **Google OAuth** - Social authentication

### Real-Time Features
- ✅ Push notifications (Web, iOS, Android)
- ✅ Email notifications
- ✅ Payment webhooks
- ✅ Subscription reminders
- 🔄 Real-time order tracking
- 🔄 Live inventory updates

### Background Processing
- ✅ Subscription expiration monitoring
- ✅ Payment webhook processing
- ✅ Email queue processing
- ✅ Notification delivery
- 🔄 Automated billing cycles

---
*Last Updated: January 2025*