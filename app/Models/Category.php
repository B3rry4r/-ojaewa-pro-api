<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Category Model
 * 
 * FINAL LOCKED MODEL - Category Types and Entity Mapping:
 * ========================================================
 * 
 * PRODUCT CATALOGS (return Products):
 * - textiles (3 levels: Group → Leaf)
 * - shoes_bags (3 levels: Group → Leaf)
 * - afro_beauty_products (2 levels: Leaf only)
 * 
 * BUSINESS DIRECTORIES (return BusinessProfiles) - 2 levels:
 * - art (2 levels: Leaf only)
 * - school (2 levels: Leaf only)
 * - afro_beauty_services (2 levels: Leaf only)
 * 
 * INITIATIVES (return SustainabilityInitiatives) - 2 levels:
 * - sustainability (2 levels: Leaf only)
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * Valid category types
     */
    public const TYPES = [
        'textiles',
        'shoes_bags', 
        'afro_beauty_products',
        'afro_beauty_services',
        'art',
        'school',
        'sustainability',
    ];
    
    /**
     * Types that return Products
     */
    public const PRODUCT_TYPES = [
        'textiles',
        'shoes_bags',
        'afro_beauty_products',
    ];
    
    /**
     * Types that return BusinessProfiles
     */
    public const BUSINESS_TYPES = [
        'art',
        'school',
        'afro_beauty_services',
    ];
    
    /**
     * Types that return SustainabilityInitiatives
     */
    public const INITIATIVE_TYPES = [
        'sustainability',
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'type',
        'order'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
    ];
    
    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }
    
    /**
     * Scope a query to only include textiles categories.
     */
    public function scopeTextiles(Builder $query): Builder
    {
        return $query->where('type', 'textiles');
    }
    
    /**
     * Scope a query to only include shoes_bags categories.
     */
    public function scopeShoesBags(Builder $query): Builder
    {
        return $query->where('type', 'shoes_bags');
    }
    
    /**
     * Scope a query to only include afro_beauty_products categories.
     */
    public function scopeAfroBeautyProducts(Builder $query): Builder
    {
        return $query->where('type', 'afro_beauty_products');
    }
    
    /**
     * Scope a query to only include afro_beauty_services categories.
     */
    public function scopeAfroBeautyServices(Builder $query): Builder
    {
        return $query->where('type', 'afro_beauty_services');
    }
    
    /**
     * Scope a query to only include art categories.
     */
    public function scopeArt(Builder $query): Builder
    {
        return $query->where('type', 'art');
    }
    
    /**
     * Scope a query to only include school categories.
     */
    public function scopeSchool(Builder $query): Builder
    {
        return $query->where('type', 'school');
    }
    
    /**
     * Scope a query to only include sustainability categories.
     */
    public function scopeSustainability(Builder $query): Builder
    {
        return $query->where('type', 'sustainability');
    }
    
    /**
     * Scope a query to only include top-level categories.
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
    
    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope for product catalog types (textiles, shoes_bags, afro_beauty_products)
     */
    public function scopeProductTypes(Builder $query): Builder
    {
        return $query->whereIn('type', self::PRODUCT_TYPES);
    }
    
    /**
     * Scope for business directory types (art, school, afro_beauty_services)
     */
    public function scopeBusinessTypes(Builder $query): Builder
    {
        return $query->whereIn('type', self::BUSINESS_TYPES);
    }
    
    /**
     * Scope for initiative types (sustainability)
     */
    public function scopeInitiativeTypes(Builder $query): Builder
    {
        return $query->whereIn('type', self::INITIATIVE_TYPES);
    }

    /**
     * Get all descendant IDs (children, grandchildren, etc.)
     */
    public function getAllDescendantIds(): array
    {
        $this->loadMissing('children');

        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }

        return $ids;
    }

    /**
     * Get self + all descendant IDs
     */
    public function getSelfAndDescendantIds(): array
    {
        return array_merge([$this->id], $this->getAllDescendantIds());
    }
    
    /**
     * Check if this category type returns Products
     */
    public function returnsProducts(): bool
    {
        return in_array($this->type, self::PRODUCT_TYPES);
    }
    
    /**
     * Check if this category type returns BusinessProfiles
     */
    public function returnsBusinesses(): bool
    {
        return in_array($this->type, self::BUSINESS_TYPES);
    }
    
    /**
     * Check if this category type returns SustainabilityInitiatives
     */
    public function returnsInitiatives(): bool
    {
        return in_array($this->type, self::INITIATIVE_TYPES);
    }
    
    /**
     * Get the entity type this category returns
     */
    public function getEntityType(): string
    {
        if ($this->returnsProducts()) {
            return 'products';
        }
        if ($this->returnsBusinesses()) {
            return 'businesses';
        }
        if ($this->returnsInitiatives()) {
            return 'initiatives';
        }
        return 'unknown';
    }
}
