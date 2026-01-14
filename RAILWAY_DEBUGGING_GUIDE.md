# 🔍 Railway Debugging Guide

## View Logs in Real-Time

### Using Railway Dashboard
1. Go to your Railway project
2. Click on your service
3. Go to **Deployments** tab
4. Click on the latest deployment
5. View the logs in real-time

### Using Railway CLI
```bash
# Install Railway CLI
npm i -g @railway/cli

# Login and link to your project
railway login
railway link

# View logs
railway logs
```

## Common Errors & Solutions

### 1. 500 Internal Server Error
**Check logs for:**
- Database connection issues
- Missing relationships
- Query errors
- Missing environment variables

**Quick fix:**
```bash
railway run php artisan tinker --execute="
try {
    \$product = App\Models\Product::with(['sellerProfile', 'reviews'])->first();
    echo 'Product loaded successfully' . PHP_EOL;
    echo 'Seller Profile: ' . (\$product->sellerProfile ? 'exists' : 'missing') . PHP_EOL;
} catch (\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

### 2. Database Migration Errors
**Solution:**
```bash
# Check current migration status
railway run php artisan migrate:status

# Run migrations
railway run php artisan migrate --force

# Fresh start (⚠️ deletes all data)
railway run php artisan migrate:fresh --seed --force
```

### 3. Missing Environment Variables
**Check:**
```bash
railway variables
```

**Set a variable:**
```bash
railway variables set KEY=VALUE
```

### 4. API Returns Empty Data
**Debug query:**
```bash
railway run php artisan tinker --execute="
echo 'Total Products: ' . App\Models\Product::count() . PHP_EOL;
echo 'Approved Products: ' . App\Models\Product::where('status', 'approved')->count() . PHP_EOL;
echo 'Products with Seller: ' . App\Models\Product::whereHas('sellerProfile')->count() . PHP_EOL;
"
```

### 5. Relationship Issues
**Test relationships:**
```bash
railway run php artisan tinker --execute="
\$product = App\Models\Product::first();
if (\$product) {
    echo 'Product ID: ' . \$product->id . PHP_EOL;
    echo 'Has Seller Profile: ' . (\$product->sellerProfile ? 'Yes' : 'No') . PHP_EOL;
    echo 'Reviews Count: ' . \$product->reviews->count() . PHP_EOL;
    echo 'Avg Rating: ' . \$product->avg_rating . PHP_EOL;
} else {
    echo 'No products found' . PHP_EOL;
}
"
```

## Useful Railway Commands

### Run Artisan Commands
```bash
# Clear cache
railway run php artisan cache:clear

# Clear config cache
railway run php artisan config:clear

# View routes
railway run php artisan route:list

# Database query
railway run php artisan tinker --execute="echo json_encode(App\Models\Product::take(3)->get(), JSON_PRETTY_PRINT);"
```

### Check Application Status
```bash
# View environment
railway run php artisan env

# Check database connection
railway run php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connected successfully' . PHP_EOL;
} catch (\Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage() . PHP_EOL;
}
"
```

## Testing Endpoints After Deploy

### Test Product Endpoint
```bash
# Get your Railway URL
RAILWAY_URL="https://ojaewa-pro-api-production.up.railway.app"

# Test browse products
curl "$RAILWAY_URL/api/products/browse"

# Test single product (should now work)
curl "$RAILWAY_URL/api/products/1"

# Test public product
curl "$RAILWAY_URL/api/products/public/1"
```

### Test with Authentication
```bash
# Login first
TOKEN=$(curl -s -X POST "$RAILWAY_URL/api/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}' \
  | jq -r '.token')

# Use token
curl "$RAILWAY_URL/api/user" \
  -H "Authorization: Bearer $TOKEN"
```

## Error Response Format

With our updated error handling, you should now see:
```json
{
  "status": "error",
  "message": "Failed to retrieve product",
  "error": "Detailed error message (only in debug mode)"
}
```

## Enable Debug Mode Temporarily

**⚠️ Only for testing, never in production with real users!**

```bash
# Set APP_DEBUG to true
railway variables set APP_DEBUG=true

# Test your endpoint
curl https://your-app.railway.app/api/products/1

# Turn it back off
railway variables set APP_DEBUG=false
```

## Monitor Deployment

Watch deployment in real-time:
```bash
railway logs --follow
```

## Quick Health Check Script

Create a file `railway_health_check.sh`:
```bash
#!/bin/bash

RAILWAY_URL="https://ojaewa-pro-api-production.up.railway.app"

echo "🏥 Health Check for Railway Deployment"
echo "======================================"

echo "1. Testing root endpoint..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$RAILWAY_URL/"

echo "2. Testing products browse..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$RAILWAY_URL/api/products/browse"

echo "3. Testing product detail..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$RAILWAY_URL/api/products/1"

echo "4. Testing categories..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$RAILWAY_URL/api/categories"

echo "5. Testing blogs..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$RAILWAY_URL/api/blogs"

echo "Done! ✅"
```

Run it:
```bash
chmod +x railway_health_check.sh
./railway_health_check.sh
```

## If All Else Fails

1. **Check Railway Service Logs** - Look for PHP errors
2. **Verify Database Connection** - Check `DATABASE_URL` in variables
3. **Check Memory Usage** - Railway free tier has limits
4. **Restart Service** - Sometimes helps with cache issues
5. **Contact Railway Support** - If it's a platform issue

---

**Current Issue Status:**
- ✅ Added error handling to product endpoint
- ✅ Optimized avg_rating accessor
- ⏳ Waiting for Railway to redeploy
- 🧪 Test after deploy: `curl https://ojaewa-pro-api-production.up.railway.app/api/products/1`
