<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureAdmin;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AdminAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        $user = Auth::guard('web')->user();

        return $user && EnsureAdmin::isAdmin($user->email)
            ? redirect()->route('admin.dashboard')
            : view('admin.login', ['allowlistEmpty' => config('admin.emails') === []]);
    }

    /**
     * Session login for the admin web dashboard.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! EnsureAdmin::isAdmin($credentials['email']) || ! Auth::guard('web')->attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match an administrator account.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Token login for the admin mobile app. Issues an expiring token scoped to "admin".
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->whereNull('archived_at')->first();

        if (! $user || ! EnsureAdmin::isAdmin($user->email) || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match an administrator account.']);
        }

        $token = $user->createToken(
            'admin-mobile:'.$credentials['device_name'],
            ['admin'],
            now()->addMinutes(config('admin.token_ttl_minutes'))
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
            'name' => $user->name,
        ]);
    }

    public function apiLogout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json(['message' => 'Signed out.']);
    }
}
