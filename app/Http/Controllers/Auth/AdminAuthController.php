<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class AdminAuthController extends Controller
{
    /**
     * Admin login endpoint.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Use Sanctum token with admin guard abilities
        $token = $admin->createToken('admin-auth', abilities: ['admin'])->plainTextToken;

        return response()->json(['token' => $token, 'admin' => $admin]);
    }

    /**
     * Create a new admin user.
     * This endpoint should be protected or used only for initial setup.
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'is_super_admin' => 'boolean',
        ]);

        $admin = Admin::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_super_admin' => $request->boolean('is_super_admin', false),
        ]);

        // Generate token with admin abilities
        $token = $admin->createToken('admin-auth', abilities: ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'Admin created successfully',
            'token' => $token,
            'admin' => $admin
        ], 201);
    }

    /**
     * Get current admin profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'admin' => Auth::guard('admin')->user()
        ]);
    }

    /**
     * Admin logout.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('admin')->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
