<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── Login ───────────────────────────────────────────────
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('integrations');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            if ($user->isArchived()) {
                $user->update(['archived_at' => null]);
                session()->flash('status', 'Welcome back! Your archived account has been reactivated.');
            }

            return redirect()->intended(route('integrations'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    // ── Register ─────────────────────────────────────────────
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('integrations');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('login')
            ->with('success', 'Account created successfully! Please log in.');
    }

    // ── Dashboard ─────────────────────────────────────────────
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        $activeIntegrationsCount = $user->integrations()->where('is_active', true)->count();
        $threatsBlockedCount = $user->loginEvents()->where('status', 'declined')->count();
        $pendingAlertsCount = $user->loginEvents()->where('status', 'pending')->count();
        $totalAlertsCount = $user->loginEvents()->where('is_archived', false)->count();

        return view('dashboard', [
            'protectionStatus' => $activeIntegrationsCount > 0 ? 'Active' : 'Standby',
            'threatsBlocked' => $threatsBlockedCount,
            'pendingAlertsCount' => $pendingAlertsCount,
            'alertsCount' => $totalAlertsCount,
        ]);
    }

    // ── Logout ────────────────────────────────────────────────
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
