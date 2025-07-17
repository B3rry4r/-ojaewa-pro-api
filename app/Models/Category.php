<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    
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
     * Scope a query to only include market categories.
     */
    public function scopeMarket(Builder $query): Builder
    {
        return $query->where('type', 'market');
    }
    
    /**
     * Scope a query to only include beauty categories.
     */
    public function scopeBeauty(Builder $query): Builder
    {
        return $query->where('type', 'beauty');
    }
    
    /**
     * Scope a query to only include brand categories.
     */
    public function scopeBrand(Builder $query): Builder
    {
        return $query->where('type', 'brand');
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
     * Scope a query to only include music categories.
     */
    public function scopeMusic(Builder $query): Builder
    {
        return $query->where('type', 'music');
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
}
