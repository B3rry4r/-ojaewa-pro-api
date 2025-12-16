<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolRegistration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country',
        'full_name',
        'phone_number',
        'state',
        'city',
        'address',
        'status',
        'payment_reference',
        'payment_data',
        'submitted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payment_data' => 'array',
        'submitted_at' => 'datetime',
    ];
}
