<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'seller_profile_id',
        'category_id',
        'name',
        'gender',
        'style',
        'tribe',
        'description',
        'image',
        'size',
        'processing_time_type',
        'processing_days',
        'price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'processing_days' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avg_rating'];

    /**
     * Get the category for this product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the seller profile that owns the product.
     */
    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    /**
     * Get all reviews for the product.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get the average rating for the product.
     */
    public function getAvgRatingAttribute(): float
    {
        // If reviews are already loaded, use them to avoid extra query
        if ($this->relationLoaded('reviews')) {
            $count = $this->reviews->count();
            if ($count === 0) {
                return 0;
            }
            return round($this->reviews->avg('rating'), 1);
        }
        
        // Otherwise, query the database
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }
}
