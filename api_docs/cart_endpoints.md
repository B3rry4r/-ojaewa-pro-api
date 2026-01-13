# Shopping Cart API Documentation

## Overview
Server-side shopping cart system for managing user shopping bags.

**Authentication Required:** All cart endpoints require `auth:sanctum`

---

## 🛒 Cart Endpoints

### 1. Get User's Cart
**Endpoint:** `GET /api/cart`  
**Middleware:** `auth:sanctum`  
**Description:** Get current user's cart with all items

#### Headers
```
Authorization: Bearer {token}
```

#### Success Response (200)
```json
{
  "status": "success",
  "data": {
    "cart_id": 1,
    "items": [
      {
        "id": 1,
        "cart_id": 1,
        "product_id": 3,
        "quantity": 2,
        "unit_price": 18000.00,
        "subtotal": 36000.00,
        "created_at": "2024-01-20T10:00:00Z",
        "product": {
          "id": 3,
          "name": "Ankara Print Dress",
          "image": "https://...",
          "status": "approved",
          "seller_profile": {
            "id": 2,
            "business_name": "Fashion House"
          }
        }
      },
      {
        "id": 2,
        "cart_id": 1,
        "product_id": 5,
        "quantity": 1,
        "unit_price": 15000.00,
        "subtotal": 15000.00,
        "created_at": "2024-01-20T10:05:00Z",
        "product": {
          "id": 5,
          "name": "Aso Oke Gele",
          "image": "https://...",
          "status": "approved",
          "seller_profile": {
            "id": 1,
            "business_name": "Adire Creations"
          }
        }
      }
    ],
    "total": 51000.00,
    "items_count": 3
  }
}
```

#### Business Logic
- Cart is automatically created for user if it doesn't exist
- Returns empty items array if cart is empty
- `total`: Sum of all item subtotals
- `items_count`: Total quantity of all items
- Items include full product information

---

### 2. Add Item to Cart
**Endpoint:** `POST /api/cart/items`  
**Middleware:** `auth:sanctum`  
**Description:** Add a product to cart or increase quantity if already exists

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Request Body
```json
{
  "product_id": "integer (required, must exist and be approved)",
  "quantity": "integer (required, min: 1)"
}
```

#### Validation Rules
- `product_id`: required|exists:products,id
- `quantity`: required|integer|min:1
- Product must have `status: approved`

#### Success Response (201)
```json
{
  "status": "success",
  "message": "Item added to cart",
  "data": {
    "cart_item": {
      "id": 3,
      "cart_id": 1,
      "product_id": 7,
      "quantity": 1,
      "unit_price": 25000.00,
      "subtotal": 25000.00,
      "created_at": "2024-01-20T11:00:00Z",
      "product": {
        "id": 7,
        "name": "Traditional Agbada",
        "image": "https://...",
        "seller_profile": {
          "id": 3,
          "business_name": "Lagos Fashion"
        }
      }
    },
    "cart_total": 76000.00,
    "items_count": 4
  }
}
```

#### Error Response (404)
```json
{
  "message": "No query results for model [App\\Models\\Product] 999"
}
```

#### Business Logic
- If product already in cart, adds to existing quantity
- If new product, creates new cart item
- Captures product price at time of adding (price snapshot)
- Cart is auto-created if user doesn't have one
- Only approved products can be added

---

### 3. Update Cart Item Quantity
**Endpoint:** `PATCH /api/cart/items/{id}`  
**Middleware:** `auth:sanctum`  
**Description:** Update quantity of specific cart item

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### URL Parameters
- `id`: Cart Item ID (integer)

#### Request Body
```json
{
  "quantity": "integer (required, min: 1)"
}
```

#### Validation Rules
- `quantity`: required|integer|min:1

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Cart item updated",
  "data": {
    "cart_item": {
      "id": 1,
      "cart_id": 1,
      "product_id": 3,
      "quantity": 5,
      "unit_price": 18000.00,
      "subtotal": 90000.00,
      "updated_at": "2024-01-20T12:00:00Z",
      "product": {
        "id": 3,
        "name": "Ankara Print Dress",
        "image": "https://..."
      }
    },
    "cart_total": 130000.00,
    "items_count": 7
  }
}
```

#### Error Response (404)
```json
{
  "message": "No query results for model [App\\Models\\CartItem] 99"
}
```

#### Business Logic
- User can only update items in their own cart
- Quantity replaces current quantity (not incremental)
- Unit price remains unchanged (original price locked)
- Returns updated cart totals

---

### 4. Remove Item from Cart
**Endpoint:** `DELETE /api/cart/items/{id}`  
**Middleware:** `auth:sanctum`  
**Description:** Remove specific item from cart

#### Headers
```
Authorization: Bearer {token}
```

#### URL Parameters
- `id`: Cart Item ID (integer)

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Item removed from cart",
  "data": {
    "cart_total": 51000.00,
    "items_count": 3
  }
}
```

#### Error Response (404)
```json
{
  "message": "No query results for model [App\\Models\\CartItem] 99"
}
```

#### Business Logic
- Permanently deletes cart item
- User can only delete items from their own cart
- Returns updated cart totals after deletion
- Cart remains even if empty

---

### 5. Clear Entire Cart
**Endpoint:** `DELETE /api/cart`  
**Middleware:** `auth:sanctum`  
**Description:** Remove all items from cart

#### Headers
```
Authorization: Bearer {token}
```

#### Success Response (200)
```json
{
  "status": "success",
  "message": "Cart cleared successfully"
}
```

#### Business Logic
- Deletes all cart items
- Cart record remains (only items deleted)
- Idempotent (can call multiple times safely)
- Typically called after successful order creation

---

## 💡 Usage Scenarios

### Typical Shopping Flow

1. **Browse Products:**
   ```bash
   GET /api/products/browse?q=ankara
   ```

2. **Add to Cart:**
   ```bash
   POST /api/cart/items
   {
     "product_id": 3,
     "quantity": 2
   }
   ```

3. **View Cart:**
   ```bash
   GET /api/cart
   ```

4. **Update Quantity:**
   ```bash
   PATCH /api/cart/items/1
   {
     "quantity": 3
   }
   ```

5. **Remove Item:**
   ```bash
   DELETE /api/cart/items/2
   ```

6. **Checkout - Create Order:**
   ```bash
   POST /api/orders
   {
     "items": [
       {"product_id": 3, "quantity": 3},
       {"product_id": 5, "quantity": 1}
     ]
   }
   ```

7. **Clear Cart After Order:**
   ```bash
   DELETE /api/cart
   ```

---

## 🗄️ Database Schema

### carts Table
```sql
id: BIGINT PRIMARY KEY
user_id: BIGINT (FOREIGN KEY, UNIQUE)
created_at: TIMESTAMP
updated_at: TIMESTAMP
```

### cart_items Table
```sql
id: BIGINT PRIMARY KEY
cart_id: BIGINT (FOREIGN KEY)
product_id: BIGINT (FOREIGN KEY)
quantity: INTEGER
unit_price: DECIMAL(10,2)
created_at: TIMESTAMP
updated_at: TIMESTAMP

UNIQUE(cart_id, product_id) -- One product per cart
```

---

## 🔒 Security & Authorization

- All endpoints require authenticated user
- Users can only access their own cart
- Cart items linked to user via cart relationship
- Products must be approved to be added
- Price snapshot taken at add time (protects against price changes)

---

## ⚠️ Important Notes

### Price Locking
When item is added to cart, the current product price is saved as `unit_price`. If the product price changes later, the cart retains the original price.

### Duplicate Prevention
The database prevents adding the same product twice to a cart (unique constraint). If user adds same product again, quantity is increased instead.

### Cart Persistence
Cart remains in database until cleared. User can logout and return to find cart intact.

### Product Availability
If a product is deleted or status changes to non-approved after being added to cart:
- Item remains in cart
- Frontend should handle displaying unavailable items
- Consider adding availability check before checkout

---

## 📊 Response Patterns

### Cart Calculations

**Total:**
```
cart.total = Σ (item.unit_price × item.quantity)
```

**Items Count:**
```
cart.items_count = Σ (item.quantity)
```

**Item Subtotal:**
```
item.subtotal = item.unit_price × item.quantity
```

---

## 🧪 Testing Examples

### Add Multiple Items
```bash
# Add item 1
curl -X POST https://api.example.com/api/cart/items \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_id": 3, "quantity": 2}'

# Add item 2
curl -X POST https://api.example.com/api/cart/items \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_id": 5, "quantity": 1}'

# View cart
curl https://api.example.com/api/cart \
  -H "Authorization: Bearer TOKEN"
```

### Update and Remove
```bash
# Update quantity
curl -X PATCH https://api.example.com/api/cart/items/1 \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"quantity": 5}'

# Remove item
curl -X DELETE https://api.example.com/api/cart/items/2 \
  -H "Authorization: Bearer TOKEN"

# Clear cart
curl -X DELETE https://api.example.com/api/cart \
  -H "Authorization: Bearer TOKEN"
```

---

## 🎯 Frontend Integration Tips

### Cart Badge Count
Use `items_count` from any cart response:
```javascript
<CartIcon badge={cartData.items_count} />
```

### Cart Total Display
```javascript
<CartTotal>₦{cartData.total.toLocaleString()}</CartTotal>
```

### Optimistic Updates
Update UI immediately, then sync with server:
```javascript
// Optimistic: Update local state
setCartItems(prev => [...prev, newItem]);

// Then sync with API
const response = await addToCart(newItem);
```

---

**Created:** January 2024  
**Total Endpoints:** 5  
**Status:** Production Ready
