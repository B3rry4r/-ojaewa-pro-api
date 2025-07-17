<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConnectController extends Controller
{
    /**
     * Get all social connect links.
     */
    public function index(): JsonResponse
    {
        $connectLinks = config('connect');

        return response()->json([
            'status' => 'success',
            'data' => $connectLinks,
        ]);
    }

    /**
     * Get only social media links.
     */
    public function social(): JsonResponse
    {
        $socialLinks = config('connect.social_links');

        return response()->json([
            'status' => 'success',
            'data' => $socialLinks,
        ]);
    }

    /**
     * Get contact information.
     */
    public function contact(): JsonResponse
    {
        $contactInfo = config('connect.contact');

        return response()->json([
            'status' => 'success',
            'data' => $contactInfo,
        ]);
    }

    /**
     * Get app download links.
     */
    public function appLinks(): JsonResponse
    {
        $appLinks = config('connect.app_links');

        return response()->json([
            'status' => 'success',
            'data' => $appLinks,
        ]);
    }
}
