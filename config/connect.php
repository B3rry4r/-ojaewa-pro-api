<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Social Media Links
    |--------------------------------------------------------------------------
    |
    | Configure your social media links here. These will be returned by the
    | connect API endpoints for frontend applications to use.
    |
    */
    'social_links' => [
        'facebook' => env('SOCIAL_FACEBOOK', 'https://facebook.com/ojaewa'),
        'twitter' => env('SOCIAL_TWITTER', 'https://twitter.com/ojaewa'),
        'instagram' => env('SOCIAL_INSTAGRAM', 'https://instagram.com/ojaewa'),
        'linkedin' => env('SOCIAL_LINKEDIN', 'https://linkedin.com/company/ojaewa'),
        'youtube' => env('SOCIAL_YOUTUBE', 'https://youtube.com/c/ojaewa'),
        'tiktok' => env('SOCIAL_TIKTOK', 'https://tiktok.com/@ojaewa'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    |
    | Configure your contact information here. This will be returned by the
    | connect API endpoints for users to get in touch.
    |
    */
    'contact' => [
        'email' => env('CONTACT_EMAIL', 'info@ojaewa.com'),
        'phone' => env('CONTACT_PHONE', '+234-800-OJA-EWA'),
        'address' => env('CONTACT_ADDRESS', 'Lagos, Nigeria'),
        'website' => env('CONTACT_WEBSITE', 'https://ojaewa.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | App Download Links
    |--------------------------------------------------------------------------
    |
    | Configure your mobile app download links here. These will be returned
    | by the connect API endpoints for users to download your apps.
    |
    */
    'app_links' => [
        'ios' => env('APP_LINK_IOS', 'https://apps.apple.com/app/ojaewa'),
        'android' => env('APP_LINK_ANDROID', 'https://play.google.com/store/apps/details?id=com.ojaewa'),
        'web' => env('APP_LINK_WEB', 'https://app.ojaewa.com'),
    ],
];
