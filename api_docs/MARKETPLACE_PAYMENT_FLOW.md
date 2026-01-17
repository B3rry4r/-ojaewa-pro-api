# OjaEwa Marketplace Payment Flow - Analysis & Implementation Plan

## Executive Summary

This document analyzes the current buyer/seller payment flow in OjaEwa and outlines what's needed to implement a proper **Marketplace Model** where OjaEwa holds funds until order fulfillment.

---

## 1. CURRENT STATE ANALYSIS

### What We Have ✅

| Feature | Status | Details |
|---------|--------|---------|
| Buyer creates order | ✅ Working | POST /api/orders creates order with items |
| Buyer pays via Paystack | ✅ Working | POST /api/payment/order initializes payment |
| Payment verification | ✅ Working | Webhook & callback verify payment |
| Order status tracking | ✅ Working | pending → processing → shipped → delivered |
| Seller dashboard | ✅ Working | GET /api/seller/orders shows seller's orders |
| Seller status updates | ✅ Working | PATCH /api/seller/orders/{id}/status |
| Buyer notifications | ✅ Working | Buyer notified on status changes |
| Seller bank details collected | ✅ Working | bank_name, account_number stored |

### What's Missing ❌

| Feature | Status | Impact |
|---------|--------|--------|
| Seller notification on new order | ❌ Missing | Seller doesn't know when buyer pays |
| Money split to sellers | ❌ Missing | All money goes to OjaEwa's Paystack |
| Seller earnings tracking | ❌ Missing | No record of what sellers are owed |
| Refunds on cancellation | ❌ Missing | Status changes but no money returned |
| Seller payout system | ❌ Missing | No way to pay sellers |
| Commission calculation | ❌ Missing | Platform fee not tracked |

---

## 2. CURRENT PAYMENT FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              CURRENT FLOW (INCOMPLETE)                                   │
└─────────────────────────────────────────────────────────────────────────────────────────┘

    BUYER                        PLATFORM                      PAYSTACK                    SELLER
      │                             │                             │                          │
      │ 1. Create Order             │                             │                          │
      │ POST /api/orders ──────────►│                             │                          │
      │                             │                             │                          │
      │ 2. Get Payment Link         │                             │                          │
      │ POST /api/payment/order ───►│                             │                          │
      │                             │────► Initialize ───────────►│                          │
      │◄─────── payment_url ────────│◄─────────────────────────── │                          │
      │                             │                             │                          │
      │ 3. Pay on Paystack          │                             │                          │
      │ ────────────────────────────┼────────────────────────────►│                          │
      │                             │                             │                          │
      │ 4. Webhook                  │                             │                          │
      │                             │◄──── charge.success ────────│                          │
      │                             │     (order → "paid")        │                          │
      │                             │                             │                          │
      │                             │ 💰 MONEY GOES TO:           │                          │
      │                             │    OjaEwa's Paystack Account│                          │
      │                             │                             │                          │
      │                             │ ❌ NO NOTIFICATION ─────────┼─────────────────────────►│
      │                             │                             │                          │
      │ 5. Order Delivered          │                             │                          │
      │                             │◄─────────────────────────────────── status: delivered ─│
      │◄──── notification ──────────│                             │                          │
      │                             │                             │                          │
      │                             │ ❌ NO PAYOUT TO SELLER      │                          │
      │                             │ ❌ NO EARNINGS TRACKED      │                          │
      │                             │                             │                          │
```

---

## 3. SELLER DATA CURRENTLY COLLECTED

### SellerProfile Model Fields

```json
{
  "id": 1,
  "user_id": 1,
  "business_name": "OjaEwa Fashion House",
  "business_email": "seller@ojaewa.com",
  "business_phone_number": "+2348012345678",
  "business_registration_number": "RC-675566",
  
  // ✅ BANK DETAILS COLLECTED
  "bank_name": "First Bank Nigeria",
  "account_number": "3063823009",
  
  // Missing for payouts:
  // ❌ account_name (needed for verification)
  // ❌ bank_code (needed for Paystack transfers)
  // ❌ recipient_code (Paystack transfer recipient ID)
  
  "country": "Nigeria",
  "state": "Lagos State",
  "city": "Lagos",
  "address": "45 Victoria Island, Lagos",
  "registration_status": "approved",
  "active": true
}
```

---

## 4. PROPOSED MARKETPLACE MODEL

### How It Should Work

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                           PROPOSED MARKETPLACE MODEL                                     │
└─────────────────────────────────────────────────────────────────────────────────────────┘

    BUYER                        OJAEWA                       PAYSTACK                    SELLER
      │                             │                             │                          │
      │ 1. Create & Pay Order       │                             │                          │
      │ ───────────────────────────►│────────────────────────────►│                          │
      │                             │                             │                          │
      │                             │ 💰 Money goes to OjaEwa     │                          │
      │                             │    (HELD IN ESCROW)         │                          │
      │                             │                             │                          │
      │                             │ 📧 NEW ORDER NOTIFICATION ─►│─────────────────────────►│
      │                             │                             │                          │
      │                             │ 📊 Create Seller Earning:   │                          │
      │                             │    gross: ₦10,000           │                          │
      │                             │    commission: ₦1,500 (15%) │                          │
      │                             │    net: ₦8,500              │                          │
      │                             │    status: pending          │                          │
      │                             │                             │                          │
      │ 2. Seller Ships             │                             │                          │
      │                             │◄─────────────────────────────────── status: shipped ──│
      │◄──── notification ──────────│                             │                          │
      │                             │                             │                          │
      │ 3. Order Delivered          │                             │                          │
      │                             │◄─────────────────────────────────── status: delivered │
      │◄──── notification ──────────│                             │                          │
      │                             │                             │                          │
      │                             │ 💰 UPDATE EARNING:          │                          │
      │                             │    status: ready_for_payout │                          │
      │                             │                             │                          │
      │ 4. Payout (Admin/Scheduled) │                             │                          │
      │                             │────► Transfer ─────────────►│                          │
      │                             │      ₦8,500 to seller       │──────────────────────────►│
      │                             │                             │                   💰 Money│
      │                             │ 📊 UPDATE EARNING:          │                          │
      │                             │    status: paid             │                          │
      │                             │    paid_at: 2026-01-20      │                          │
      │                             │                             │                          │
```

---

## 5. CANCELLATION & REFUND FLOW

### Current Cancellation Logic

```php
// OrderController::cancel()
// Can cancel if status is: pending OR processing

if (!in_array($order->status, ["pending", "processing"])) {
    return error("Cannot cancel");
}

$order->status = "cancelled";
$order->cancellation_reason = $request->cancellation_reason;
$order->save();

// ❌ NO REFUND INITIATED
// ❌ SELLER NOT NOTIFIED
```

### Refund Options with Paystack

#### Option A: Full Refund via Paystack API

Paystack supports refunds via their API:

```
POST https://api.paystack.co/refund
{
  "transaction": "transaction_reference",
  "amount": 1000000  // Amount in kobo (optional, for partial)
}
```

**Requirements:**
- Must have original transaction reference (we store this as `payment_reference`)
- Refund goes back to original payment method (card, bank, etc.)
- Takes 5-10 business days to reflect

#### Option B: Manual Refund via Bank Transfer

For cases where Paystack refund fails:
- Admin manually transfers to buyer's bank account
- Requires collecting buyer bank details

### Proposed Cancellation Flow

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              CANCELLATION SCENARIOS                                      │
└─────────────────────────────────────────────────────────────────────────────────────────┘

SCENARIO 1: Cancel BEFORE Payment (status: pending)
─────────────────────────────────────────────────────
  • Order status → cancelled
  • No refund needed (no payment made)
  • Notify buyer: "Order cancelled"

SCENARIO 2: Cancel AFTER Payment, BEFORE Shipping (status: processing)
─────────────────────────────────────────────────────────────────────────
  • Order status → cancelled
  • Initiate FULL REFUND via Paystack API
  • Update seller_earnings → status: cancelled
  • Notify buyer: "Order cancelled, refund initiated (5-10 days)"
  • Notify seller: "Order #{id} was cancelled"

SCENARIO 3: Cancel AFTER Shipping (status: shipped)
────────────────────────────────────────────────────
  • ❌ CANNOT CANCEL (current logic)
  • Alternative: Buyer can request return after delivery
  • Admin handles returns manually

SCENARIO 4: Seller Cancels (out of stock, etc.)
───────────────────────────────────────────────
  • Seller initiates cancellation with reason
  • Full refund to buyer
  • Seller earnings → cancelled
  • Notify buyer: "Seller cancelled - item unavailable"
```

---

## 6. MULTI-SELLER ORDER HANDLING

### Current Problem

One order can contain items from multiple sellers:

```
Order #123:
  - Item 1: Ankara Dress (Seller A) - ₦15,000
  - Item 2: Leather Bag (Seller B) - ₦8,000
  - Item 3: Hair Oil (Seller C) - ₦3,000
  - Delivery Fee: ₦2,000
  ─────────────────────────────────
  Total: ₦28,000
```

**Problems:**
- Which seller ships which item?
- How to track partial fulfillment?
- How to split the delivery fee?
- What if only one seller cancels?

### Recommended Solutions

#### Option A: Split Orders at Checkout (Recommended)

When buyer checks out, create separate orders per seller:

```
Original Cart → Split into:

Order #123-A (Seller A):
  - Ankara Dress: ₦15,000
  - Delivery: ₦2,000
  - Total: ₦17,000

Order #123-B (Seller B):
  - Leather Bag: ₦8,000
  - Delivery: ₦2,000
  - Total: ₦10,000

Order #123-C (Seller C):
  - Hair Oil: ₦3,000
  - Delivery: ₦2,000
  - Total: ₦5,000
```

**Pros:** Clean separation, easy tracking, simple payouts
**Cons:** Multiple delivery fees (or need consolidated shipping)

#### Option B: Keep Single Order, Track Per-Seller (Current + Enhancement)

Keep single order but track fulfillment per seller:

```
Order #123:
  seller_fulfillments:
    - seller_a: { items: [1], status: shipped, shipped_at: ... }
    - seller_b: { items: [2], status: processing }
    - seller_c: { items: [3], status: delivered }
```

**Pros:** Single delivery fee, unified tracking for buyer
**Cons:** Complex logic, partial refunds tricky

---

## 7. IMPLEMENTATION REQUIREMENTS

### Database Changes Needed

#### 1. New Table: `seller_earnings`

```sql
CREATE TABLE seller_earnings (
    id BIGINT PRIMARY KEY,
    order_id BIGINT REFERENCES orders(id),
    order_item_id BIGINT REFERENCES order_items(id),
    seller_profile_id BIGINT REFERENCES seller_profiles(id),
    
    -- Amounts
    gross_amount DECIMAL(10,2),      -- Full item price
    commission_rate DECIMAL(5,2),     -- e.g., 15.00
    commission_amount DECIMAL(10,2),  -- Platform fee
    net_amount DECIMAL(10,2),         -- Seller receives
    
    -- Status tracking
    status ENUM('pending', 'ready_for_payout', 'paid', 'cancelled'),
    
    -- Payout details
    payout_reference VARCHAR(255),
    paid_at TIMESTAMP NULL,
    payout_method VARCHAR(50),        -- 'paystack_transfer', 'manual'
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 2. Update `seller_profiles` Table

```sql
ALTER TABLE seller_profiles ADD COLUMN account_name VARCHAR(255);
ALTER TABLE seller_profiles ADD COLUMN bank_code VARCHAR(10);
ALTER TABLE seller_profiles ADD COLUMN paystack_recipient_code VARCHAR(255);
ALTER TABLE seller_profiles ADD COLUMN total_earnings DECIMAL(12,2) DEFAULT 0;
ALTER TABLE seller_profiles ADD COLUMN pending_payout DECIMAL(12,2) DEFAULT 0;
```

### PaystackService Additions Needed

```php
// 1. Create Transfer Recipient (one-time per seller)
public function createTransferRecipient(array $data): array
{
    // POST https://api.paystack.co/transferrecipient
    // Required: type, name, account_number, bank_code, currency
}

// 2. Initiate Transfer (payout to seller)
public function initiateTransfer(array $data): array
{
    // POST https://api.paystack.co/transfer
    // Required: source, amount, recipient, reason
}

// 3. Initiate Refund (return money to buyer)
public function initiateRefund(string $transactionRef, ?int $amount = null): array
{
    // POST https://api.paystack.co/refund
    // Required: transaction (reference)
    // Optional: amount (for partial refund)
}

// 4. Get Banks List (for bank_code lookup)
public function getBanks(): array
{
    // GET https://api.paystack.co/bank
}
```

---

## 8. COMMISSION MODEL OPTIONS

### Option A: Flat Percentage (Simple)

```
Commission Rate: 15%

Order Item: ₦10,000
Commission: ₦1,500
Seller Gets: ₦8,500
```

### Option B: Tiered by Volume (Growth Incentive)

```
Monthly Sales     Commission
₦0 - ₦100,000        15%
₦100,001 - ₦500,000  12%
₦500,001+            10%
```

### Option C: Category-Based

```
Category              Commission
Textiles              15%
Shoes & Bags          12%
Art                   20%
Beauty Products       10%
```

---

## 9. PAYOUT SCHEDULE OPTIONS

### Option A: After Delivery Confirmed (Per Order)
- **Trigger:** Order status → delivered
- **Delay:** 3-7 days (return window)
- **Pros:** Automatic, seller gets paid quickly
- **Cons:** Complex with many small transfers

### Option B: Weekly Batch Payouts
- **Trigger:** Every Monday, pay all "ready" earnings
- **Minimum:** ₦5,000 threshold
- **Pros:** Fewer transfers, lower fees
- **Cons:** Sellers wait longer

### Option C: On-Demand with Threshold
- **Trigger:** Seller requests payout
- **Minimum:** ₦10,000
- **Pros:** Seller control
- **Cons:** Unpredictable cash flow

---

## 10. IMMEDIATE ACTION ITEMS

### Phase 1: Quick Wins (1-2 days)

- [ ] Add seller notification when order is paid
- [ ] Add seller notification when order is cancelled
- [ ] Add buyer notification when seller updates status

### Phase 2: Earnings Tracking (3-5 days)

- [ ] Create `seller_earnings` migration
- [ ] Create SellerEarning model
- [ ] Create earnings when order is paid
- [ ] Update earnings status on delivery
- [ ] Add seller earnings dashboard endpoint

### Phase 3: Paystack Integration (1-2 weeks)

- [ ] Add bank_code and account_name to seller registration
- [ ] Implement createTransferRecipient
- [ ] Implement initiateTransfer
- [ ] Implement initiateRefund
- [ ] Build admin payout management

### Phase 4: Full Marketplace (2-4 weeks)

- [ ] Decide on multi-seller order handling
- [ ] Implement commission model
- [ ] Build automated payout scheduling
- [ ] Add seller wallet/balance view
- [ ] Add payout history

---

## 11. QUESTIONS FOR TEAM DECISION

1. **Commission Rate:** What percentage should OjaEwa take?
   - [ ] Flat 15%
   - [ ] Flat 10%
   - [ ] Tiered by volume
   - [ ] Category-based
   - [ ] Other: ______

2. **Payout Schedule:** When do sellers get paid?
   - [ ] Immediately after delivery (+ 3 day buffer)
   - [ ] Weekly batch
   - [ ] Bi-weekly batch
   - [ ] On seller request (min threshold)

3. **Payout Minimum:** Threshold before payout?
   - [ ] No minimum
   - [ ] ₦5,000
   - [ ] ₦10,000
   - [ ] Other: ______

4. **Multi-Seller Orders:** How to handle?
   - [ ] Split into separate orders per seller
   - [ ] Keep single order, track per-seller fulfillment
   - [ ] Hybrid approach

5. **Refund Policy:** When can orders be refunded?
   - [ ] Anytime before shipping (auto-refund)
   - [ ] Admin approval required
   - [ ] Only within 24 hours of payment

6. **Delivery Fee Split:** Who gets the ₦2,000?
   - [ ] OjaEwa keeps 100%
   - [ ] Split among sellers proportionally
   - [ ] First seller to ship gets it

---

## 12. TECHNICAL NOTES

### Current PaystackService Methods

```php
✅ initializePayment()    - Create payment link
✅ verifyPayment()        - Verify transaction
✅ verifyWebhookSignature() - Validate webhook
✅ generateReference()    - Create unique refs
✅ convertToKobo()        - Amount conversion
✅ convertFromKobo()      - Amount conversion

❌ createTransferRecipient() - NEEDED
❌ initiateTransfer()        - NEEDED
❌ initiateRefund()          - NEEDED
❌ getBanks()                 - NEEDED
```

### Order Status Flow

```
pending → paid → processing → shipped → delivered
   │        │         │          │
   └────────┴─────────┴──────────┴─────► cancelled
```

### Key Files to Modify

```
app/Services/PaystackService.php          - Add transfer/refund methods
app/Http/Controllers/API/PaymentController.php  - Add seller notifications
app/Http/Controllers/API/OrderController.php    - Add refund on cancel
app/Models/SellerEarning.php              - NEW MODEL
database/migrations/xxx_seller_earnings   - NEW MIGRATION
```

---

**Document Version:** 1.0  
**Date:** January 2026  
**Author:** Development Team  
**Status:** For Team Review
