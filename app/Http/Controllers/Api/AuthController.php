<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SettingsController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate user and issue a Sanctum Bearer Token for mobile devices.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $email = strtolower(trim($data['email']));
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'email' => ['Incorrect email or password.'],
                ],
            ], 422);
        }

        $deviceName = $request->input('device_name', 'Mobile App');
        $user->tokens()->where('name', $deviceName)->delete();

        $expiresInMinutes = (int) config('sanctum.expiration', config('session.lifetime', 120));
        $expiresAt = $expiresInMinutes > 0
            ? now()->addMinutes($expiresInMinutes)
            : null;

        // Generate Sanctum Bearer token for mobile device
        $token = $user->createToken($deviceName, ['*'], $expiresAt)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Authentication successful.',
            'token' => $token,
            'expires_at' => $expiresAt?->toIso8601String(),
            'expires_in_minutes' => $expiresInMinutes,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'theme' => $user->preference('theme', SettingsController::DEFAULT_PREFERENCES['theme']),
            ],
        ]);
    }

    /**
     * Revoke current access token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Token revoked successfully.',
        ]);
    }
}
