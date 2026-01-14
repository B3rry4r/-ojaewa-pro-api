# 🚨 Railway 500 Error - Emergency Troubleshooting

## The Problem
All API endpoints are returning 500 errors, which means there's a fundamental issue with your Railway deployment.

## Most Likely Causes

### 1. Database Connection Issue (Most Common)
Railway might not have the correct database credentials set.

**Fix:**
1. Go to Railway Dashboard
2. Click on your service
3. Go to **Variables** tab
4. Check if you have these variables:
   - `DB_CONNECTION=mysql` (or `pgsql` if using PostgreSQL)
   - `DB_HOST` (should be auto-populated by Railway)
   - `DB_PORT` (should be auto-populated by Railway)
   - `DB_DATABASE` (should be auto-populated by Railway)
   - `DB_USERNAME` (should be auto-populated by Railway)
   - `DB_PASSWORD` (should be auto-populated by Railway)

**If using Railway's PostgreSQL:**
- Railway should automatically inject `DATABASE_URL`
- Make sure your `config/database.php` can parse it

### 2. APP_KEY Not Set
Laravel requires an APP_KEY to encrypt sessions.

**Fix:**
```bash
# Generate a new key locally
php artisan key:generate --show

# Copy the output (starts with "base64:")
# Add it to Railway variables:
# Variable: APP_KEY
# Value: base64:YOUR_GENERATED_KEY_HERE
```

### 3. Missing Dependencies
Railway might not be installing all PHP extensions.

**Check your Railway Build Logs** for any installation errors.

### 4. File Permissions
Laravel needs to write to `storage/` and `bootstrap/cache/`.

## Immediate Actions to Take

### Step 1: Check Railway Logs Right Now
```bash
# Install Railway CLI if not already
npm i -g @railway/cli

# Login
railway login

# Link to project
railway link

# View logs
railway logs
```

**Look for:**
- Database connection errors
- "SQLSTATE" errors
- "Class not found" errors
- "Permission denied" errors

### Step 2: Verify Environment Variables
```bash
railway variables
```

**You MUST have:**
- `APP_KEY` (Laravel encryption key)
- Database variables (DB_* or DATABASE_URL)
- `APP_ENV=production`
- `APP_DEBUG=false` (set to `true` temporarily to see errors)

### Step 3: Enable Debug Mode Temporarily
```bash
railway variables set APP_DEBUG=true
```

Then check the endpoint again - you should see detailed error messages.

### Step 4: Test Database Connection
```bash
railway run php artisan tinker --execute="
try {
    \$pdo = DB::connection()->getPdo();
    echo 'Database connected successfully!' . PHP_EOL;
    echo 'Database: ' . DB::connection()->getDatabaseName() . PHP_EOL;
} catch (\Exception \$e) {
    echo 'Database connection failed!' . PHP_EOL;
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

## Quick Fixes

### Fix 1: Set APP_DEBUG=true
This will show you the actual error:
```bash
railway variables set APP_DEBUG=true
```

Then visit: `https://ojaewa-pro-api-production.up.railway.app/api/products/browse`

You should see a detailed Laravel error page.

### Fix 2: Clear All Caches
```bash
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan route:clear
railway run php artisan view:clear
```

### Fix 3: Regenerate APP_KEY
```bash
# Generate locally
php artisan key:generate --show

# Copy the output, then:
railway variables set APP_KEY="base64:YOUR_KEY_HERE"
```

### Fix 4: Check Database Connection
If Railway is using PostgreSQL, update your `config/database.php`:

```php
// In config/database.php, add this to the connections array:
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
],
```

## Your Action Plan (Do This Now)

1. **Enable debug mode:**
   ```bash
   railway variables set APP_DEBUG=true
   ```

2. **Check the logs:**
   ```bash
   railway logs
   ```

3. **Visit the endpoint and see the actual error:**
   ```
   https://ojaewa-pro-api-production.up.railway.app/api/products/browse
   ```

4. **Send me the error message** - I'll help you fix it!

## Common Error Messages & Solutions

### "SQLSTATE[HY000] [2002] Connection refused"
**Problem:** Database not connected
**Solution:** Check `DB_HOST` and `DB_PORT` in Railway variables

### "No application encryption key has been specified"
**Problem:** Missing `APP_KEY`
**Solution:** Generate and set `APP_KEY`

### "Class 'PDO' not found"
**Problem:** PHP PDO extension not installed
**Solution:** Add to your Dockerfile or Railway config

### "SQLSTATE[08006] [7] could not translate host name"
**Problem:** PostgreSQL connection issue
**Solution:** Check `DATABASE_URL` format

## After You Get the Error Message

Once you enable debug mode and see the actual error, we can:
1. Identify the exact problem
2. Apply the specific fix
3. Disable debug mode
4. Test everything

---

**DO THIS RIGHT NOW:**
```bash
railway variables set APP_DEBUG=true
```

Then refresh: https://ojaewa-pro-api-production.up.railway.app/api/products/browse

**What error do you see?** Share it with me and we'll fix it! 🚀
