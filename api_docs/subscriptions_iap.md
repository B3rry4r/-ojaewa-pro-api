# Ojaewa IAP Subscriptions API (Laravel)

Base URL: `https://api.ojaewa.com/api`

Authentication: Bearer token (Sanctum)

---

## 1) Store Subscription (No Verification Yet)

**Endpoint:** `POST /subscriptions/verify`

This endpoint only stores the purchase data. Receipt verification will be added later.

**Request Body**
```json
{
  "platform": "ios",
  "product_id": "ojaewa_pro",
  "transaction_id": "1000000123456789",
  "receipt_data": "MIIbngYJKoZIhvc...",
  "environment": "sandbox"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Subscription verified and activated",
  "data": {
    "subscription": {
      "id": 4,
      "product_id": "ojaewa_pro",
      "tier": "ojaewa_pro",
      "status": "active",
      "starts_at": "2026-01-20T17:57:54.000000Z",
      "expires_at": "2027-01-20T17:57:54.000000Z",
      "is_auto_renewing": true,
      "platform": "ios"
    }
  }
}
```

---

## 2) Get Subscription Status

**Endpoint:** `GET /subscriptions/status`

**Response (No Subscription)**
```json
{
  "success": true,
  "data": {
    "has_subscription": false,
    "subscription": null
  }
}
```

**Response (Active Subscription)**
```json
{
  "success": true,
  "data": {
    "has_subscription": true,
    "subscription": {
      "id": 4,
      "product_id": "ojaewa_pro",
      "tier": "ojaewa_pro",
      "status": "active",
      "platform": "ios",
      "starts_at": "2026-01-20T17:57:54.000000Z",
      "expires_at": "2027-01-20T17:57:54.000000Z",
      "days_remaining": 364,
      "is_auto_renewing": true,
      "will_renew": true
    }
  }
}
```

---

## Notes

- Only `ojaewa_pro` is currently supported
- Verification with Apple/Google will be added later
- If transaction_id + platform already exists, subscription is updated
