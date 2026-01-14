#!/bin/bash

RAILWAY_URL="https://ojaewa-pro-api-production.up.railway.app"

echo "1️⃣ Testing login with test@ojaewa.com..."
LOGIN_RESPONSE=$(curl -s -X POST "$RAILWAY_URL/api/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@ojaewa.com","password":"password123"}')

echo "$LOGIN_RESPONSE" | jq '.' 2>/dev/null || echo "$LOGIN_RESPONSE"

TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.token' 2>/dev/null)

if [ "$TOKEN" != "null" ] && [ -n "$TOKEN" ]; then
    echo ""
    echo "✅ Login successful! Token: ${TOKEN:0:50}..."
    echo ""
    echo "2️⃣ Testing blog favorites endpoint..."
    curl -s "$RAILWAY_URL/api/blogs/favorites" \
      -H "Authorization: Bearer $TOKEN" \
      -H "Accept: application/json" | jq '.' 2>/dev/null || curl -s "$RAILWAY_URL/api/blogs/favorites" -H "Authorization: Bearer $TOKEN"
else
    echo "❌ Login failed!"
fi
