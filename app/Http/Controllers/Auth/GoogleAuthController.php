<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GoogleAuthController extends Controller
{
    /**
     * Handle Google OAuth sign-in.
     * Request expects: { token: <Google ID token from frontend> }
     * 
     * IMPORTANT: For mobile apps, the token is signed with the mobile Client ID,
     * not the web Client ID. You need to configure ALL Client IDs that should be accepted.
     * 
     * ENV Variables:
     * - GOOGLE_CLIENT_ID: Web Client ID (also used as primary)
     * - GOOGLE_CLIENT_ID_IOS: iOS Client ID (optional)
     * - GOOGLE_CLIENT_ID_ANDROID: Android Client ID (optional)
     */
    public function handle(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        // Collect all configured Client IDs
        $clientIds = array_filter([
            config('services.google.client_id'),
            config('services.google.client_id_ios'),
            config('services.google.client_id_android'),
        ]);
        
        if (empty($clientIds)) {
            Log::error('Google OAuth: No GOOGLE_CLIENT_ID configured');
            return response()->json([
                'status' => 'error',
                'message' => 'Google authentication is not configured on the server.',
            ], 503);
        }

        $payload = null;
        $lastError = null;

        // Try to verify the token against each Client ID
        // This is necessary because mobile tokens are signed with mobile Client IDs
        foreach ($clientIds as $clientId) {
            try {
                $client = new Google_Client();
                $client->setClientId($clientId);
                $result = $client->verifyIdToken($request->token);
                
                if ($result) {
                    $payload = $result;
                    Log::info('Google OAuth: Token verified successfully', [
                        'client_id_used' => substr($clientId, 0, 20) . '...',
                    ]);
                    break;
                }
            } catch (\Exception $e) {
                $lastError = $e;
                Log::debug('Google OAuth: Token verification failed for client ID', [
                    'client_id' => substr($clientId, 0, 20) . '...',
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        if (! $payload) {
            // Log the failure with details
            Log::error('Google OAuth: All verification attempts failed', [
                'client_ids_tried' => count($clientIds),
                'last_error' => $lastError?->getMessage(),
                'token_preview' => substr($request->token, 0, 50) . '...',
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Google token. Please ensure you are using the correct Google account.',
                'error_detail' => 'Token could not be verified against any configured Client ID.',
                'hint' => 'If using a mobile app, ensure GOOGLE_CLIENT_ID_IOS and/or GOOGLE_CLIENT_ID_ANDROID are set on the server.',
            ], 401);
        }

        $email     = $payload['email'] ?? null;
        $firstname = $payload['given_name'] ?? null;
        $lastname  = $payload['family_name'] ?? null;

        if (! $email) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not present in Google token.',
            ], 422);
        }

        $user = User::firstOrNew(['email' => $email]);

        // If this is a brand-new user, set names and a random password
        if (! $user->exists) {
            $user->firstname = $firstname;
            $user->lastname  = $lastname;
            $user->password  = Hash::make(Str::random(16));
            $user->email_verified_at = now(); // Google verified the email
        }

        // If phone is still missing, frontend should collect it
        $needPhone = empty($user->phone);

        $user->save();

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token'      => $token,
            'user'       => $user,
            'need_phone' => $needPhone,
        ]);
    }
}
