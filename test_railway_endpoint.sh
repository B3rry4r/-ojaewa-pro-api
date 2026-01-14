#!/bin/bash

RAILWAY_URL="https://ojaewa-pro-api-production.up.railway.app"

echo "🧪 Testing Railway Endpoints"
echo "============================"
echo ""

echo "1️⃣ Testing /api/products/browse (should always work)..."
RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" "$RAILWAY_URL/api/products/browse")
HTTP_CODE=$(echo "$RESPONSE" | grep "HTTP_CODE" | cut -d: -f2)
BODY=$(echo "$RESPONSE" | sed '/HTTP_CODE/d')

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Browse works! Status: $HTTP_CODE"
    echo "Products found: $(echo "$BODY" | jq -r '.data.data | length' 2>/dev/null || echo 'N/A')"
else
    echo "❌ Browse failed! Status: $HTTP_CODE"
fi
echo ""

echo "2️⃣ Testing /api/products/1 (the problematic endpoint)..."
RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" "$RAILWAY_URL/api/products/1")
HTTP_CODE=$(echo "$RESPONSE" | grep "HTTP_CODE" | cut -d: -f2)
BODY=$(echo "$RESPONSE" | sed '/HTTP_CODE/d')

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Product detail works! Status: $HTTP_CODE"
    echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
else
    echo "❌ Product detail failed! Status: $HTTP_CODE"
    echo "Response:"
    echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
fi
echo ""

echo "3️⃣ Testing /api/products/public/1 (alternative endpoint)..."
RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" "$RAILWAY_URL/api/products/public/1")
HTTP_CODE=$(echo "$RESPONSE" | grep "HTTP_CODE" | cut -d: -f2)

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Public product endpoint works! Status: $HTTP_CODE"
else
    echo "❌ Public product endpoint failed! Status: $HTTP_CODE"
fi
echo ""

echo "Done! 🎉"
