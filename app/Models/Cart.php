<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id'];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    /**
     * Calculate total cart price
     */
    public function getTotalAttribute(): float
    {
        // Check if items are loaded to avoid N+1 query
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        
        return $this->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
    }
    
    /**
     * Get total number of items in cart
     */
    public function getItemsCountAttribute(): int
    {
        // Check if items are loaded to avoid N+1 query
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        
        return $this->items->sum('quantity');
    }
}
