<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\SellerProfile;
use App\Models\BusinessProfile;
use App\Models\Order;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'password',
        'email_notifications',
        'push_notifications',
        'sms_notifications',
        'marketing_emails',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    /**
     * Get the user's seller profile.
     */
    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }
    
    /**
     * Get the user's business profiles.
     */
    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }
    
    /**
     * Get the user's orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the user's wishlist items.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
    
    /**
     * Get the user's notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
    
    /**
     * Get the user's addresses.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
