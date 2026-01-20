# OjaEwa AI Endpoints - Complete API Guide

**Base URL:** `https://ojaewa-ai-production.up.railway.app`

**Authentication:** Most endpoints require a Bearer token from Laravel login.
```
Authorization: Bearer {token_from_laravel_login}
```

---

## BUYER ENDPOINTS

### 1. Style DNA Quiz

Submit style preferences to create a personalized style profile.

**Endpoint:** `POST /ai/buyer/style-quiz`  
**Auth:** Required

**Request:**
```json
{
  "answers": {
    "preferredStyle": "modern",
    "favoriteColors": ["blue", "green", "earth tones"],
    "occasions": ["casual", "work", "traditional events"],
    "budgetRange": "mid-range",
    "culturalPreference": ["Yoruba", "Modern African"],
    "fitPreference": "fitted",
    "patternPreference": "bold"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Style profile saved",
  "styleProfile": {
    "stylePersonality": "modern",
    "colorPreferences": ["blue", "green", "earth tones"],
    "occasionFocus": ["casual", "work", "traditional events"],
    "budgetRange": "mid-range",
    "culturalAffinity": ["Yoruba", "Modern African"],
    "fitPreference": "fitted",
    "patternPreference": "bold",
    "summary": "You gravitate towards a modern aesthetic, appreciating clean lines and contemporary designs..."
  }
}
```

---

### 2. Get Personalized Recommendations

Get AI-powered product recommendations based on user's style profile.

**Endpoint:** `GET /ai/buyer/recommendations/{userId}`  
**Auth:** Required

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| limit | number | No | Max results (default: 10) |
| category | string | No | Filter by category |

**Request:**
```
GET /ai/buyer/recommendations/1?limit=5
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "source": "fresh",
  "recommendations": [
    {
      "productId": 45,
      "matchScore": 95,
      "reason": "Perfect match for your modern African style with bold patterns",
      "product": {
        "id": 45,
        "name": "Modern Ankara Blazer",
        "price": 35000,
        "image": "https://..."
      }
    }
  ],
  "insight": "Based on your style profile, we recommend bold Ankara pieces in blue and green tones..."
}
```

---

### 3. AI Chat Assistant

Chat with AI for fashion advice and recommendations.

**Endpoint:** `POST /ai/buyer/chat`  
**Auth:** Required

**Request:**
```json
{
  "message": "I need an outfit for a traditional wedding in Lagos",
  "context": {
    "budget": 50000,
    "occasion": "wedding"
  }
}
```

**Response:**
```json
{
  "success": true,
  "response": "For a traditional Lagos wedding, I recommend an elegant Aso Oke ensemble...",
  "suggestedProducts": [
    {
      "id": 12,
      "name": "Aso Oke Agbada Set",
      "price": 45000,
      "matchReason": "Perfect for traditional weddings"
    }
  ],
  "sessionId": "chat_abc123"
}
```

---

### 4. Get Chat History

Retrieve previous chat conversations.

**Endpoint:** `GET /ai/buyer/chat/history/{userId}`  
**Auth:** Required

**Response:**
```json
{
  "success": true,
  "history": [
    {
      "id": 1,
      "role": "user",
      "content": "I need an outfit for a wedding",
      "timestamp": "2026-01-16T10:00:00Z"
    },
    {
      "id": 2,
      "role": "assistant",
      "content": "For a traditional Lagos wedding...",
      "timestamp": "2026-01-16T10:00:05Z"
    }
  ]
}
```

---

### 5. Clear Chat History

**Endpoint:** `DELETE /ai/buyer/chat/history`  
**Auth:** Required

**Response:**
```json
{
  "success": true,
  "message": "Chat history cleared"
}
```

---

### 6. Build Wedding Guest Look

Generate a complete wedding guest outfit.

**Endpoint:** `POST /ai/buyer/looks/wedding-guest`  
**Auth:** Required

**Request:**
```json
{
  "budget": 50000,
  "style": "traditional",
  "weddingType": "Nigerian traditional",
  "colorPreference": "earth tones"
}
```

**Response:**
```json
{
  "success": true,
  "weddingLook": {
    "event": "Nigerian traditional wedding guest",
    "mainOutfit": {
      "products": [4],
      "description": "A vibrant Ankara Skirt & Top set with matching Gele..."
    },
    "shoes": "Heeled sandals or embellished flat mules...",
    "accessories": {
      "jewelry": ["Statement gold earrings", "Simple bracelet"],
      "bag": "Small elegant clutch",
      "other": ["Traditional hand fan"]
    },
    "totalBudget": 50000,
    "estimatedCost": 35000,
    "stylingTips": ["Arrive early for photos", "Bring cash for spraying"]
  }
}
```

---

### 7. Build Business Casual Look

**Endpoint:** `POST /ai/buyer/looks/business-casual`  
**Auth:** Required

**Request:**
```json
{
  "budget": 40000,
  "industry": "tech",
  "climate": "warm"
}
```

---

### 8. Build Traditional Event Look

**Endpoint:** `POST /ai/buyer/looks/traditional-event`  
**Auth:** Required

**Request:**
```json
{
  "budget": 60000,
  "eventType": "naming ceremony",
  "tribe": "Yoruba"
}
```

---

### 9. Build Party Look

**Endpoint:** `POST /ai/buyer/looks/party`  
**Auth:** Required

**Request:**
```json
{
  "budget": 35000,
  "partyType": "birthday",
  "style": "glamorous"
}
```

---

### 10. Generate Outfits

Generate outfit combinations from available products.

**Endpoint:** `POST /ai/buyer/outfits/generate`  
**Auth:** Required

**Request:**
```json
{
  "occasion": "casual",
  "budget": 30000,
  "style": "modern african"
}
```

**Response:**
```json
{
  "success": true,
  "outfits": [
    {
      "name": "Modern Casual Chic",
      "products": [9, 23],
      "totalPrice": 28000,
      "description": "A relaxed yet stylish combination..."
    }
  ]
}
```

---

### 11. Complete a Look

Get suggestions to complete a partial outfit.

**Endpoint:** `POST /ai/buyer/outfits/complete-look`  
**Auth:** Required

**Request:**
```json
{
  "existingItems": [12, 45],
  "occasion": "dinner date",
  "budget": 20000
}
```

---

### 12. Size Prediction

Predict the best size for a product based on measurements.

**Endpoint:** `POST /ai/buyer/sizing/predict`  
**Auth:** Required

**Request:**
```json
{
  "productId": 1,
  "measurements": {
    "bust": 90,
    "waist": 70,
    "hips": 95,
    "height": 165
  }
}
```

**Response:**
```json
{
  "success": true,
  "recommendedSize": "M",
  "confidence": 0.85,
  "fitNotes": "May be slightly fitted around the hips",
  "alternativeSize": "L"
}
```

---

### 13. Save Measurements

Save user's body measurements for future predictions.

**Endpoint:** `POST /ai/buyer/sizing/save-measurements`  
**Auth:** Required

**Request:**
```json
{
  "measurements": {
    "bust": 90,
    "waist": 70,
    "hips": 95,
    "height": 165,
    "weight": 65
  }
}
```

---

### 14. Get Fit Feedback

Get fit feedback from other buyers for a product.

**Endpoint:** `GET /ai/buyer/sizing/fit-feedback/{productId}`  
**Auth:** Optional

**Response:**
```json
{
  "success": true,
  "fitData": {
    "runsSmall": 20,
    "trueToSize": 65,
    "runsLarge": 15,
    "recommendation": "Order your usual size"
  }
}
```

---

### 15. Beauty - Match Foundation

Find foundation shades for skin tone.

**Endpoint:** `POST /ai/buyer/beauty/match-foundation`  
**Auth:** Required

**Request:**
```json
{
  "skinTone": "medium brown",
  "undertone": "warm"
}
```

---

### 16. Beauty - Color Season Analysis

Determine your color season for fashion choices.

**Endpoint:** `POST /ai/buyer/beauty/color-season`  
**Auth:** Required

**Request:**
```json
{
  "skinTone": "deep brown",
  "eyeColor": "dark brown",
  "hairColor": "black"
}
```

---

### 17. Get Beauty Recommendations

**Endpoint:** `GET /ai/buyer/beauty/recommendations/{userId}`  
**Auth:** Required

---

## PUBLIC ENDPOINTS (No Auth Required)

### 18. Get Upcoming Trends

**Endpoint:** `GET /ai/buyer/trends/upcoming/{category}`  
**Auth:** Optional

**Categories:** `textiles`, `shoes_bags`, `afro_beauty_products`, `art`

**Response:**
```json
{
  "success": true,
  "category": "textiles",
  "trends": [
    {
      "name": "Bold Ankara Prints",
      "description": "Vibrant geometric patterns...",
      "popularity": "rising",
      "peakSeason": "December-January"
    }
  ],
  "seasonalFactors": ["Festive season driving demand"],
  "investmentAdvice": "Stock up on bold prints for the holiday rush"
}
```

---

### 19. Get Review Summary

AI-powered summary of product reviews.

**Endpoint:** `GET /ai/buyer/reviews/summary/{productId}`  
**Auth:** Optional

**Response:**
```json
{
  "success": true,
  "product": {
    "id": "1",
    "name": "Kente Evening Dress"
  },
  "reviewCount": 15,
  "summary": {
    "overallSentiment": "positive",
    "averageRating": 4.2,
    "highlights": [
      "Beautiful fabric quality",
      "True to size",
      "Fast delivery"
    ],
    "concerns": [
      "Limited color options"
    ],
    "fitFeedback": "Runs true to size",
    "qualityFeedback": "High quality fabric and stitching",
    "summary": "Customers love the authentic Kente fabric...",
    "buyerAdvice": "Great choice for special occasions. Order your usual size."
  }
}
```

---

### 20. Get Sentiment Analysis

Detailed sentiment analysis of reviews.

**Endpoint:** `GET /ai/buyer/reviews/sentiment/{productId}`  
**Auth:** Optional

---

### 21. Find Similar Products

**Endpoint:** `GET /ai/buyer/similar/{productId}`  
**Auth:** Optional

**Response:**
```json
{
  "success": true,
  "sourceProduct": {
    "id": 1,
    "name": "Ankara Maxi Dress"
  },
  "similarProducts": [
    {
      "id": 15,
      "name": "Kente Maxi Dress",
      "similarity": 0.89,
      "matchReasons": ["Similar style", "Same category", "Similar price range"]
    }
  ]
}
```

---

### 22. Visual Search

Search products by uploading an image.

**Endpoint:** `POST /ai/buyer/visual-search`  
**Auth:** Optional  
**Content-Type:** `multipart/form-data`

**Request:**
```
image: [file upload]
mode: "similar" | "identify" | "shop"
```

**Response:**
```json
{
  "success": true,
  "analysis": {
    "detectedItems": ["ankara dress", "gele"],
    "colors": ["blue", "gold"],
    "style": "traditional Nigerian"
  },
  "matchingProducts": [
    {
      "id": 23,
      "name": "Blue Ankara Dress",
      "matchScore": 0.92
    }
  ]
}
```

---

### 23. Sustainability Score

Get sustainability score for a product.

**Endpoint:** `GET /ai/buyer/sustainability/score/{productId}`  
**Auth:** Optional

**Response:**
```json
{
  "success": true,
  "product": {
    "id": 1,
    "name": "Handwoven Kente"
  },
  "sustainabilityScore": 85,
  "breakdown": {
    "materials": 90,
    "production": 80,
    "packaging": 75,
    "shipping": 85
  },
  "certifications": ["Handmade", "Fair Trade"],
  "impact": "Supports local artisans in Ghana"
}
```

---

## SELLER ENDPOINTS

### 24. Generate Product Description

**Endpoint:** `POST /ai/seller/products/generate-description`  
**Auth:** Required (Seller)

**Request:**
```json
{
  "name": "Ankara Maxi Dress",
  "category": "Women's Dresses",
  "attributes": {
    "fabric": "Ankara",
    "style": "Modern",
    "occasion": "Party"
  }
}
```

**Response:**
```json
{
  "success": true,
  "description": "Stunning Ankara maxi dress featuring vibrant African prints...",
  "seoTitle": "Elegant Ankara Maxi Dress | African Party Wear",
  "seoKeywords": ["ankara dress", "african fashion", "party dress"],
  "bulletPoints": [
    "100% premium Ankara cotton fabric",
    "Floor-length flowing design",
    "Available in multiple sizes"
  ]
}
```

---

### 25. Pricing Suggestions

**Endpoint:** `POST /ai/seller/pricing/suggest/{productId}`  
**Auth:** Required (Seller)

**Request:**
```json
{
  "currentPrice": 25000,
  "competitorPrices": [22000, 28000, 26000]
}
```

**Response:**
```json
{
  "success": true,
  "suggestedPrice": 26500,
  "priceRange": {
    "min": 22000,
    "max": 32000
  },
  "reasoning": "Based on market analysis and your product quality...",
  "competitorAnalysis": "Your price is competitive..."
}
```

---

### 26. Sales Analytics

**Endpoint:** `GET /ai/seller/analytics/sales/{sellerId}`  
**Auth:** Required (Seller)

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| period | string | 7days, 30days, 90days |

---

### 27. Inventory Insights

**Endpoint:** `GET /ai/seller/analytics/inventory/{sellerId}`  
**Auth:** Required (Seller)

---

### 28. Generate Social Media Post

**Endpoint:** `POST /ai/seller/marketing/social-post/{productId}`  
**Auth:** Required (Seller)

**Request:**
```json
{
  "platform": "instagram",
  "tone": "casual",
  "includeHashtags": true
}
```

---

### 29. Generate Ad Copy

**Endpoint:** `POST /ai/seller/marketing/ad-copy/{productId}`  
**Auth:** Required (Seller)

---

### 30. Seller Chat Assistant

**Endpoint:** `POST /ai/seller/chat`  
**Auth:** Required (Seller)

**Request:**
```json
{
  "message": "How can I improve my product listings?"
}
```

---

## Error Responses

All endpoints return errors in this format:

```json
{
  "error": "Error message description"
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `400` - Bad Request (invalid input)
- `401` - Unauthorized (missing or invalid token)
- `403` - Forbidden (not a seller for seller endpoints)
- `404` - Not Found
- `500` - Server Error

---

## Rate Limits

- Authenticated endpoints: 100 requests/minute
- Public endpoints: 30 requests/minute
- AI-heavy endpoints (chat, recommendations): 20 requests/minute

---

## Notes

1. **User ID**: For endpoints requiring `{userId}`, use the user ID from Laravel login response
2. **Product ID**: Use product IDs from Laravel's product endpoints
3. **Seller ID**: For seller endpoints, use the seller profile ID
4. **Tokens**: Use the Bearer token from Laravel's `/api/login` endpoint
5. **Content-Type**: Always use `application/json` unless uploading files
