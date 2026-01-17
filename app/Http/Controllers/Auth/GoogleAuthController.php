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
     */
    public function handle(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        // Check if Google Client ID is configured
        $googleClientId = config('services.google.client_id');
        
        if (empty($googleClientId)) {
            Log::error('Google OAuth: GOOGLE_CLIENT_ID not configured');
            return response()->json([
                'status' => 'error',
                'message' => 'Google authentication is not configured on the server.',
            ], 503);
        }

        try {
            $client = new Google_Client();
            $client->setClientId($googleClientId);
            $payload = $client->verifyIdToken($request->token);
        } catch (\Exception $e) {
            Log::error('Google OAuth verification failed: ' . $e->getMessage(), [
                'exception' => $e,
                'client_id_set' => !empty($googleClientId),
                'client_id_length' => strlen($googleClientId ?? ''),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify Google token.',
                'error_type' => get_class($e),
                'error_hint' => str_contains($e->getMessage(), 'cURL') ? 'Network/SSL issue' : 
                               (str_contains($e->getMessage(), 'client') ? 'Client ID issue' : 'Token verification issue'),
            ], 500);
        }

        if (! $payload) {
            throw ValidationException::withMessages([
                'token' => ['Invalid Google token.'],
            ]);
        }

        $email     = $payload['email'] ?? null;
        $firstname = $payload['given_name'] ?? null;
        $lastname  = $payload['family_name'] ?? null;

        if (! $email) {
            throw ValidationException::withMessages([
                'token' => ['Email not present in Google token.'],
            ]);
        }

        $user = User::firstOrNew(['email' => $email]);

        // If this is a brand-new user, set names and a random password they must change later
        if (! $user->exists) {
            $user->firstname = $firstname;
            $user->lastname  = $lastname;
            $user->password  = Hash::make(Str::random(16));
        }

        // If phone is still missing, request frontend to supply it in next step
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
