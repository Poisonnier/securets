<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google OAuth consent screen.
     */
    public function redirect(Request $request): SymfonyRedirectResponse
    {
        $client = $request->query('client') === 'mobile' ? 'mobile' : 'web';

        $state = Crypt::encryptString((string) json_encode([
            'client' => $client,
            'exp' => now()->addMinutes(10)->timestamp,
            'nonce' => Str::random(16),
        ]));

        /** @var GoogleProvider $driver */
        $driver = Socialite::driver('google');

        return $driver
            ->stateless()
            ->with(['state' => $state])
            ->redirect();
    }

    /**
     * Handle the OAuth callback from Google for both web and mobile clients.
     */
    public function callback(Request $request): RedirectResponse
    {
        $client = 'web';
        $stateParam = $request->query('state');

        if (! is_string($stateParam) || $stateParam === '') {
            return $this->failRedirect($client);
        }

        try {
            $decoded = json_decode(Crypt::decryptString($stateParam), true);
            if (! is_array($decoded)) {
                return $this->failRedirect($client);
            }

            $client = ($decoded['client'] ?? 'web') === 'mobile' ? 'mobile' : 'web';
            $exp = (int) ($decoded['exp'] ?? 0);

            if ($exp < now()->timestamp) {
                return $this->failRedirect($client);
            }
        } catch (\Throwable) {
            return $this->failRedirect($client);
        }

        if ($request->filled('error')) {
            return $this->failRedirect($client);
        }

        try {
            /** @var GoogleProvider $driver */
            $driver = Socialite::driver('google');
            $googleUser = $driver->stateless()->user();
        } catch (\Throwable) {
            return $this->failRedirect($client);
        }

        $googleId = (string) $googleUser->getId();
        $email = strtolower(trim((string) $googleUser->getEmail()));
        $name = trim((string) ($googleUser->getName() ?: $googleUser->getNickname() ?: explode('@', $email)[0] ?: 'SecureTS User'));

        if ($googleId === '' || $email === '') {
            return $this->failRedirect($client);
        }

        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleId,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(40)),
                ]);
            }
        }

        if ($user->isArchived()) {
            $user->update(['archived_at' => null]);
        }

        if ($client === 'mobile') {
            $plainCode = Str::random(64);
            $user->update([
                'oauth_exchange_code' => hash('sha256', $plainCode),
                'oauth_exchange_expires_at' => now()->addSeconds(60),
            ]);

            return redirect()->away('securets://auth/google?code='.urlencode($plainCode));
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('integrations');
    }

    /**
     * Exchange a single-use mobile Google OAuth code for a Sanctum token.
     */
    public function exchangeMobileCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $hashedCode = hash('sha256', $data['code']);

        $user = User::where('oauth_exchange_code', $hashedCode)
            ->where('oauth_exchange_expires_at', '>', now())
            ->first();

        if (! $user) {
            return response()->json([
                'message' => 'Invalid or expired Google sign-in code.',
                'errors' => [
                    'code' => ['Invalid or expired Google sign-in code.'],
                ],
            ], 422);
        }

        // Strict single-use: clear exchange code and expiry immediately
        $user->update([
            'oauth_exchange_code' => null,
            'oauth_exchange_expires_at' => null,
        ]);

        if ($user->isArchived()) {
            $user->update(['archived_at' => null]);
        }

        $deviceName = $data['device_name'] ?? 'Mobile App';
        $user->tokens()->where('name', $deviceName)->delete();

        $expiresInMinutes = (int) config('sanctum.expiration', config('session.lifetime', 120));
        $expiresAt = $expiresInMinutes > 0
            ? now()->addMinutes($expiresInMinutes)
            : null;

        $token = $user->createToken($deviceName, ['*'], $expiresAt)->plainTextToken;

        return response()->json([
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

    private function failRedirect(string $client): RedirectResponse
    {
        if ($client === 'mobile') {
            return redirect()->away('securets://auth/google?error=oauth_failed');
        }

        return redirect()->route('login')
            ->withErrors(['email' => 'Google authentication failed or was cancelled. Please try again.']);
    }
}
