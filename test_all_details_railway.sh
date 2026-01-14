#!/bin/bash

RAILWAY_URL="https://ojaewa-pro-api-production.up.railway.app"

echo "🧪 Testing ALL Detail Endpoints on Railway"
echo "=========================================="
echo ""

# Test 1: Product Details
echo "1️⃣ Testing Product Detail Endpoint"
echo "GET /api/products/public/1"
RESPONSE=$(curl -s "$RAILWAY_URL/api/products/public/1")
echo "$RESPONSE" | jq '{
  status,
  product_name: .data.product.name,
  price: .data.product.price,
  seller: .data.product.seller_profile.business_name,
  avg_rating: .data.product.avg_rating,
  suggestions_count: (.data.suggestions | length)
}' 2>/dev/null || echo "Error: $RESPONSE" | head -20
echo ""

# Test 2: Business Profile Details
echo "2️⃣ Testing Business Profile Detail Endpoint"
echo "First, get a business ID..."
BUSINESS_ID=$(curl -s "$RAILWAY_URL/api/business/public" | jq -r '.data.data[0].id' 2>/dev/null)
echo "Business ID: $BUSINESS_ID"
echo ""
echo "GET /api/business/public/$BUSINESS_ID"
curl -s "$RAILWAY_URL/api/business/public/$BUSINESS_ID" | jq '{
  status,
  business_name: .data.business_name,
  category: .data.category,
  store_status: .data.store_status,
  owner: .data.user.firstname
}' 2>/dev/null || echo "Failed"
echo ""

# Test 3: Sustainability Initiative Details
echo "3️⃣ Testing Sustainability Initiative Detail Endpoint"
echo "First, get initiatives list..."
INITIATIVE_COUNT=$(curl -s "$RAILWAY_URL/api/sustainability" | jq -r '.data.total' 2>/dev/null)
echo "Total initiatives: $INITIATIVE_COUNT"

if [ "$INITIATIVE_COUNT" != "0" ] && [ "$INITIATIVE_COUNT" != "null" ]; then
    INITIATIVE_ID=$(curl -s "$RAILWAY_URL/api/sustainability" | jq -r '.data.data[0].id' 2>/dev/null)
    echo "Initiative ID: $INITIATIVE_ID"
    echo ""
    echo "GET /api/sustainability/$INITIATIVE_ID"
    curl -s "$RAILWAY_URL/api/sustainability/$INITIATIVE_ID" | jq '{
      status,
      title: .data.title,
      category: .data.category,
      status: .data.status,
      target: .data.target_amount,
      current: .data.current_amount,
      participants: .data.participant_count
    }' 2>/dev/null || echo "Failed"
else
    echo "❌ No sustainability initiatives seeded yet"
fi
echo ""

# Test 4: Check if we have data in all category types
echo "4️⃣ Checking Data Availability Across Categories"
echo "================================================"

for TYPE in market beauty brand school music sustainability; do
    echo -n "📂 $TYPE: "
    
    # Get first category slug for this type
    SLUG=$(curl -s "$RAILWAY_URL/api/categories?type=$TYPE" | jq -r '.data[0].slug' 2>/dev/null)
    
    if [ "$SLUG" != "null" ] && [ -n "$SLUG" ]; then
        TOTAL=$(curl -s "$RAILWAY_URL/api/categories/$TYPE/$SLUG/items" | jq -r '.data.items.total' 2>/dev/null)
        echo "$TOTAL items in '$SLUG' category"
        
        # If items exist, get first item details
        if [ "$TOTAL" != "0" ] && [ "$TOTAL" != "null" ]; then
            ITEM_ID=$(curl -s "$RAILWAY_URL/api/categories/$TYPE/$SLUG/items" | jq -r '.data.items.data[0].id' 2>/dev/null)
            
            # Try to fetch detail endpoint
            if [ "$TYPE" = "market" ]; then
                echo "   Testing detail: /api/products/public/$ITEM_ID"
                DETAIL_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$RAILWAY_URL/api/products/public/$ITEM_ID")
                echo "   Detail endpoint status: $DETAIL_STATUS"
            elif [ "$TYPE" = "sustainability" ]; then
                echo "   Testing detail: /api/sustainability/$ITEM_ID"
                DETAIL_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$RAILWAY_URL/api/sustainability/$ITEM_ID")
                echo "   Detail endpoint status: $DETAIL_STATUS"
            else
                echo "   Testing detail: /api/business/public/$ITEM_ID"
                DETAIL_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$RAILWAY_URL/api/business/public/$ITEM_ID")
                echo "   Detail endpoint status: $DETAIL_STATUS"
            fi
        fi
    else
        echo "No categories found"
    fi
    echo ""
done

echo "=========================================="
echo "✅ Testing Complete!"
