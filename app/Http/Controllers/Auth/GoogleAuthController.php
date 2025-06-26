<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        $client = app(Google_Client::class);
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $payload = $client->verifyIdToken($request->token);

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
            'token'      => $token,
            'user'       => $user,
            'need_phone' => $needPhone,
        ]);
    }
}
