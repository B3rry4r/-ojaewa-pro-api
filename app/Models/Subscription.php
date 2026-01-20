<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_name',
        'price',
        'status',
        'starts_at',
        'expires_at',
        'next_billing_date',
        'payment_method',
        'features',
        'product_id',
        'tier',
        'platform',
        'store_transaction_id',
        'store_product_id',
        'purchase_token',
        'receipt_data',
        'cancelled_at',
        'is_auto_renewing',
        'will_renew',
        'renewal_price',
        'renewal_currency',
        'environment',
        'raw_data',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'features' => 'array',
        'price' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'is_auto_renewing' => 'boolean',
        'will_renew' => 'boolean',
        'renewal_price' => 'decimal:2',
        'raw_data' => 'array',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    /**
     * Check if subscription is expiring soon (within 7 days).
     */
    public function isExpiringSoon(): bool
    {
        return $this->isActive() && $this->expires_at <= now()->addDays(7);
    }

    /**
     * Get days until expiration.
     */
    public function daysUntilExpiration(): int
    {
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope for expiring subscriptions.
     */
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->active()
                    ->where('expires_at', '<=', now()->addDays($days));
    }
}
