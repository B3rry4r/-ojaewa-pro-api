<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class SellerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'country',
        'state',
        'city',
        'address',
        'business_email',
        'business_phone_number',
        'instagram',
        'facebook',
        'identity_document',
        'business_name',
        'business_registration_number',
        'business_certificate',
        'business_logo',
        'bank_name',
        'account_number',
        'registration_status',
        'active',
        'rejection_reason',
    ];

    protected $casts = [
        'registration_status' => 'string',
        'active' => 'boolean',
    ];

    /**
     * Get the user that owns the seller profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the products for the seller profile.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
