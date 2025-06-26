<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessProfile extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category',
        'country',
        'state',
        'city',
        'address',
        'business_email',
        'business_phone_number',
        'website_url',
        'instagram',
        'facebook',
        'identity_document',
        'business_name',
        'business_description',
        'business_logo',
        'offering_type',
        'product_list',
        'service_list',
        'business_certificates',
        'professional_title',
        'school_type',
        'school_biography',
        'classes_offered',
        'music_category',
        'youtube',
        'spotify',
        'store_status',
        'subscription_status',
        'subscription_ends_at'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_list' => 'json',
        'service_list' => 'json',
        'business_certificates' => 'json',
        'classes_offered' => 'json',
        'subscription_ends_at' => 'datetime',
    ];
    
    /**
     * Get the user that owns the business profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the required fields based on the business category and offering type.
     *
     * @return array
     */
    public static function getRequiredFieldsByCategory(string $category, ?string $offering_type = null): array
    {
        $required = [
            'user_id', 'category', 'country', 'state', 'city', 'address', 
            'business_email', 'business_phone_number', 'business_name', 'business_description'
        ];
        
        if ($offering_type === 'providing_service') {
            $required = array_merge($required, ['service_list', 'professional_title']);
        }
        
        if ($offering_type === 'selling_product') {
            $required = array_merge($required, ['product_list', 'business_certificates']);
        }
        
        if ($category === 'school') {
            $required = array_merge($required, ['school_type', 'school_biography', 'classes_offered']);
        }
        
        if ($category === 'music') {
            $required = array_merge($required, ['music_category', 'identity_document']);
            // At least one of youtube or spotify is required
        }
        
        return $required;
    }
    
    /**
     * Scope a query to only include businesses belonging to a given user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
