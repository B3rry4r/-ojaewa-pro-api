<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
    protected $fillable = [
        'question',
        'answer',
        'category',
    ];

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all available categories.
     */
    public static function getCategories()
    {
        return self::whereNotNull('category')
                  ->distinct()
                  ->pluck('category')
                  ->toArray();
    }
}
