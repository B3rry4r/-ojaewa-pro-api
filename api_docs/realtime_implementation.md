# Real-Time Implementation Documentation

## Overview
The Oja Ewa API implements real-time push notifications using **Pusher Beams** (not WebSockets).

---

## ❌ What's NOT Implemented

### No Cart/Bag Management System
**Confirmed:** There is NO cart or shopping bag functionality in the codebase.

**Current Order Flow:**
```
1. User browses products
2. User selects products (frontend handles temporary cart state)
3. User creates order directly → POST /api/orders
4. Order creation accepts array of items with product_id and quantity
```

**Frontend Responsibility:**
- Frontend must manage cart state locally (localStorage, React state, etc.)
- When ready to checkout, send all items in one API call
- No server-side cart persistence

**Order Creation Example:**
```json
POST /api/orders
{
  "items": [
    { "product_id": 3, "quantity": 2 },
    { "product_id": 5, "quantity": 1 }
  ]
}
```

### No WebSocket Implementation
- No Socket.io
- No Laravel WebSockets
- No real-time chat
- No live order tracking updates

---

## ✅ What IS Implemented: Pusher Beams (Push Notifications)

### Technology Stack
- **Service:** Pusher Beams (Push Notifications API)
- **Purpose:** Send mobile/web push notifications
- **Integration:** HTTP API calls from Laravel backend

### Configuration

**Location:** `config/services.php`
```php
'pusher_beams' => [
    'instance_id' => env('PUSHER_BEAMS_INSTANCE_ID'),
    'secret_key' => env('PUSHER_BEAMS_SECRET_KEY'),
]
```

**Environment Variables Required:**
```env
PUSHER_BEAMS_INSTANCE_ID=your-instance-id
PUSHER_BEAMS_SECRET_KEY=your-secret-key
```

---

## Push Notification Implementation

### NotificationService Class
**Location:** `app/Services/NotificationService.php`

### Method 1: Send to Specific User
```php
public function sendEmailAndPush($user, $subject, $view, $title, $message, $emailData = [], $pushData = [])
```

**What it does:**
1. Sends email notification using Laravel Mail
2. Sends push notification via Pusher Beams
3. Creates database notification record

**Implementation Details:**
```php
// Push notification to specific user
$instanceId = config('services.pusher_beams.instance_id');
$secretKey = config('services.pusher_beams.secret_key');

Http::withHeaders([
    'Authorization' => 'Bearer ' . $secretKey,
    'Content-Type' => 'application/json'
])->post("https://{$instanceId}.pushnotifications.pusher.com/publish_api/v1/instances/{$instanceId}/publishes", [
    'interests' => ["user-{$user->id}"], // User-specific channel
    'web' => [
        'notification' => [
            'title' => $title,
            'body' => $message,
            'deep_link' => $pushData['deep_link'] ?? null,
        ]
    ],
    'fcm' => [
        'notification' => [
            'title' => $title,
            'body' => $message,
        ],
        'data' => $pushData
    ]
]);
```

### Method 2: Broadcast to All Users
```php
public function sendPushToAllUsers($title, $message, $data = [])
```

**What it does:**
- Sends push notification to ALL registered users
- Uses general interest: `all-users`

**Implementation:**
```php
Http::withHeaders([
    'Authorization' => 'Bearer ' . $secretKey,
    'Content-Type' => 'application/json'
])->post("https://{$instanceId}.pushnotifications.pusher.com/publish_api/v1/instances/{$instanceId}/publishes", [
    'interests' => ['all-users'], // Broadcast channel
    'web' => [
        'notification' => [
            'title' => $title,
            'body' => $message,
            'deep_link' => $data['deep_link'] ?? null,
        ]
    ],
    'fcm' => [
        'notification' => [
            'title' => $title,
            'body' => $message,
        ],
        'data' => $data
    ]
]);
```

---

## Push Notification Channels (Interests)

### User-Specific Channels
```
user-{user_id}
```
- Example: `user-1`, `user-25`, `user-100`
- Each user subscribes to their own channel
- Used for personal notifications

### Broadcast Channels
```
all-users
```
- All users subscribe to this channel
- Used for announcements, new blog posts, system updates

---

## Notification Triggers

### Automatic Push Notifications Sent For:

#### 1. Order Events
**Order Created:**
```php
// File: app/Http/Controllers/API/OrderController.php
$this->notificationService->sendEmailAndPush(
    $user,
    'Order Confirmation - Oja Ewa',
    'order_created',
    'Order Confirmed!',
    "Your order #{$order->id} has been confirmed and is being processed.",
    ['order' => $order->load('orderItems.product')],
    ['order_id' => $order->id, 'deep_link' => "/orders/{$order->id}"]
);
```

**Order Status Updated:**
```php
// File: app/Http/Controllers/API/Admin/AdminOrderController.php
$this->notificationService->sendEmailAndPush(
    $order->user,
    "Order Status Update - Oja Ewa",
    'order_status_updated',
    'Order Status Updated',
    "Your order #{$order->id} status has been updated to {$newStatus}.",
    ['order' => $order, 'statusClass' => $statusClass],
    ['order_id' => $order->id, 'status' => $newStatus, 'deep_link' => "/orders/{$order->id}"]
);
```

#### 2. Business Approval Events
```php
// File: app/Http/Controllers/API/Admin/AdminBusinessController.php
$this->notificationService->sendEmailAndPush(
    $business->user,
    'Business Profile Update - Oja Ewa',
    'business_approved',
    $newStatus === 'approved' ? 'Business Approved!' : 'Business Profile Needs Update',
    $newStatus === 'approved' 
        ? "Congratulations! Your {$business->business_name} profile has been approved."
        : "Your {$business->business_name} profile needs some updates before approval.",
    [
        'business' => $business,
        'status' => $newStatus,
        'rejectionReason' => $business->rejection_reason
    ],
    [
        'business_id' => $business->id,
        'status' => $newStatus,
        'deep_link' => "/business/{$business->id}"
    ]
);
```

#### 3. Blog Publishing Events
```php
// File: app/Http/Controllers/API/Admin/AdminBlogController.php
$this->notificationService->sendPushToAllUsers(
    'New Blog Post',
    "Check out our latest blog post: {$blog->title}",
    [
        'blog_id' => $blog->id,
        'blog_slug' => $blog->slug,
        'deep_link' => "/blogs/{$blog->slug}"
    ]
);
```

---

## Frontend Implementation Guide

### Step 1: Install Pusher Beams SDK

**For Web (React/Vue/Angular):**
```bash
npm install @pusher/push-notifications-web
```

**For React Native:**
```bash
npm install @pusher/push-notifications-react-native
```

**For iOS Native:**
```swift
pod 'PushNotifications'
```

**For Android Native:**
```gradle
implementation 'com.pusher:push-notifications-android:1.9.0'
```

---

### Step 2: Initialize Pusher Beams (Web Example)

```javascript
import * as PusherPushNotifications from "@pusher/push-notifications-web";

// Initialize on app load
const beamsClient = new PusherPushNotifications.Client({
  instanceId: 'YOUR_INSTANCE_ID', // From your Pusher Beams dashboard
});

// Start the Beams client
beamsClient
  .start()
  .then(() => console.log('Pusher Beams initialized'))
  .catch(console.error);
```

---

### Step 3: Subscribe to User-Specific Notifications

```javascript
// After user logs in
async function subscribeToUserNotifications(userId) {
  try {
    // Subscribe to user-specific channel
    await beamsClient.addDeviceInterest(`user-${userId}`);
    
    // Subscribe to broadcast channel
    await beamsClient.addDeviceInterest('all-users');
    
    console.log(`Subscribed to notifications for user ${userId}`);
  } catch (error) {
    console.error('Failed to subscribe:', error);
  }
}

// Call after successful login
subscribeToUserNotifications(user.id);
```

---

### Step 4: Unsubscribe on Logout

```javascript
async function unsubscribeFromNotifications(userId) {
  try {
    await beamsClient.removeDeviceInterest(`user-${userId}`);
    await beamsClient.stop();
    console.log('Unsubscribed from notifications');
  } catch (error) {
    console.error('Failed to unsubscribe:', error);
  }
}

// Call on logout
unsubscribeFromNotifications(user.id);
```

---

### Step 5: Handle Notification Clicks

```javascript
// Listen for notification clicks
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.addEventListener('message', (event) => {
    if (event.data && event.data.notification) {
      const notification = event.data.notification;
      
      // Navigate to deep link
      if (notification.data && notification.data.deep_link) {
        window.location.href = notification.data.deep_link;
      }
    }
  });
}
```

---

## Testing Push Notifications

### Method 1: Via Pusher Dashboard
1. Go to Pusher Beams Dashboard
2. Select your instance
3. Go to "Debug Console"
4. Send test notification to interest: `user-1`

### Method 2: Via API Test
```bash
# Trigger order creation (which sends notification)
curl -X POST https://your-api.com/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {"product_id": 1, "quantity": 1}
    ]
  }'
```

### Method 3: Via Admin Panel
1. Login as admin
2. Create/publish a blog post
3. All users subscribed to `all-users` will receive notification

---

## Notification Data Structure

### Push Notification Payload
```json
{
  "interests": ["user-123"],
  "web": {
    "notification": {
      "title": "Order Confirmed!",
      "body": "Your order #5 has been confirmed",
      "deep_link": "/orders/5"
    }
  },
  "fcm": {
    "notification": {
      "title": "Order Confirmed!",
      "body": "Your order #5 has been confirmed"
    },
    "data": {
      "order_id": 5,
      "status": "pending",
      "deep_link": "/orders/5"
    }
  }
}
```

### Database Notification Record
```json
{
  "id": 1,
  "user_id": 123,
  "title": "Order Confirmed!",
  "message": "Your order #5 has been confirmed",
  "type": "push",
  "event": "order_created",
  "data": {
    "order_id": 5,
    "deep_link": "/orders/5"
  },
  "read_at": null,
  "created_at": "2024-01-17T11:00:00Z"
}
```

---

## Environment Setup

### Required Environment Variables
```env
# Pusher Beams Configuration
PUSHER_BEAMS_INSTANCE_ID=your-instance-id-here
PUSHER_BEAMS_SECRET_KEY=your-secret-key-here
```

### Getting Credentials
1. Sign up at https://pusher.com/beams
2. Create a new Beams instance
3. Copy Instance ID and Secret Key
4. Add to your `.env` file

---

## Cost Considerations

### Pusher Beams Pricing (as of 2024)
- **Free Tier:** 1,000 monthly active devices
- **Startup:** $49/month for 10,000 devices
- **Growth:** $99/month for 50,000 devices

**Alternative:** Consider Firebase Cloud Messaging (FCM) - completely free and unlimited

---

## Migration to FCM (Recommended)

If you want to avoid Pusher costs, you can switch to Firebase Cloud Messaging:

### Why FCM?
- ✅ Completely free
- ✅ Unlimited notifications
- ✅ Direct Google support
- ✅ Better Android integration
- ✅ Same functionality as Pusher Beams

### Migration Steps
1. Create Firebase project
2. Get FCM server key
3. Update NotificationService to use FCM HTTP API
4. Update frontend to use Firebase SDK instead of Pusher

---

## Limitations & Notes

### Current Limitations
1. **No Real-Time Chat:** Only push notifications, no bidirectional communication
2. **No Live Updates:** Order status changes require page refresh or polling
3. **No WebSockets:** Cannot do live tracking without frontend polling
4. **No Cart Sync:** No server-side cart, frontend must manage state

### What Works Well
1. ✅ Order confirmation notifications
2. ✅ Order status update alerts
3. ✅ Business approval notifications
4. ✅ Blog post announcements
5. ✅ Admin-initiated broadcasts

### What Doesn't Work
1. ❌ Real-time chat with sellers
2. ❌ Live order tracking (map view)
3. ❌ Real-time inventory updates
4. ❌ Live cart synchronization across devices

---

## Recommendations

### For Immediate Implementation
1. **Use Pusher Beams as-is** - It's already configured
2. **Implement frontend SDK** - Follow setup guide above
3. **Test all notification flows** - Order creation, status updates, etc.

### For Future Improvements
1. **Migrate to FCM** - Save costs, unlimited usage
2. **Add WebSocket for Live Features** - If needed for real-time chat
3. **Implement Server-Side Cart** - If you want cart persistence
4. **Add Real-Time Tracking** - Use Laravel Echo + Pusher Channels or Socket.io

---

## Summary

**What You Have:**
- ✅ Push notifications via Pusher Beams
- ✅ Email notifications
- ✅ Database notification records
- ✅ User-specific and broadcast channels

**What You Don't Have:**
- ❌ Shopping cart/bag API
- ❌ WebSocket connections
- ❌ Real-time chat
- ❌ Live tracking

**Next Steps:**
1. Set up Pusher Beams credentials
2. Implement frontend SDK
3. Test notification flows
4. Consider FCM migration for cost savings
