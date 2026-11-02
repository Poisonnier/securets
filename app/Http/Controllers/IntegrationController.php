<?php

namespace App\Http\Controllers;

use App\Events\LoginAttemptBroadcast;
use App\Models\Device;
use App\Models\Integration;
use App\Models\LoginEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;

class IntegrationController extends Controller
{
    public function index(Request $request)
    {
        $integrations = Integration::where('user_id', $request->user()->id)
            ->withCount('loginEvents')
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json($integrations->map(fn ($i) => $this->formatIntegration($i)));
        }

        return view('integrations', compact('integrations'));
    }

    public function webIndex(Request $request)
    {
        $integrations = Integration::where('user_id', $request->user()->id)
            ->withCount('loginEvents')
            ->latest()
            ->get();

        return view('integrations', compact('integrations'));
    }

    public function integrationsData(Request $request)
    {
        return response()->json(
            Integration::where('user_id', $request->user()->id)->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'url' => 'required|url:http,https|max:255',
        ]);

        $g2fa = new Google2FA();
        $host = parse_url($data['url'], PHP_URL_HOST) ?? $data['url'];

        $integration = $request->user()->integrations()->create([
            'name' => $data['name'],
            'url' => $data['url'],
            'secret_key' => $g2fa->generateSecretKey(),
            'favicon_url' => "https://www.google.com/s2/favicons?domain={$host}&sz=64",
        ]);

        return response()->json($this->formatIntegration($integration), 201);
    }

    public function destroy(Request $request, Integration $integration)
    {
        $this->authorizeOwner($request->user(), $integration);
        $integration->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function toggle(Request $request, Integration $integration)
    {
        $this->authorizeOwner($request->user(), $integration);
        $integration->update(['is_active' => ! $integration->is_active]);

        return response()->json($this->formatIntegration($integration->fresh()));
    }

    /** Sanctum: owner-triggered login event (dev / older clients). */
    public function loginEvent(Request $request, Integration $integration)
    {
        $this->authorizeOwner($request->user(), $integration);

        return $this->startLoginEvent($request, $integration);
    }

    /** Target App: authenticate with the integration secret, not the user's phone token. */
    public function publicLoginEvent(Request $request)
    {
        $integration = $this->integrationFromSecret($request);

        return $this->startLoginEvent($request, $integration);
    }

    public function publicEventStatus(Request $request, LoginEvent $event)
    {
        $integration = $this->integrationFromSecret($request);
        abort_unless($event->integration_id === $integration->id, 403);

        return response()->json($this->eventPayload($event->load('integration')));
    }

    /**
     * Long-poll variant for external (Target App) integrations.
     * Authenticated via secret_key, mirrors gateLongPoll behaviour.
     */
    public function publicEventLongPoll(Request $request, LoginEvent $event)
    {
        $integration = $this->integrationFromSecret($request);
        abort_unless($event->integration_id === $integration->id, 403);

        return $this->waitForEventResolution($event);
    }

    public function gateShow(Integration $integration)
    {
        abort_unless($integration->is_active, 403);

        return view('gate', [
            'integration' => $integration,
            'isFirst' => ! $integration->hasCompletedFirstLogin(),
        ]);
    }

    public function gateAttempt(Request $request, Integration $integration)
    {
        abort_unless($integration->is_active, 403);

        $isFirst = ! $integration->hasCompletedFirstLogin();
        $data = $request->validate([
            'credential_username' => $isFirst ? 'required|string|max:255' : 'nullable|string|max:255',
            'credential_password' => $isFirst ? 'required|string|max:255' : 'nullable|string',
        ]);

        // ── Deduplication guard ──────────────────────────────────────────────
        // If the user refreshes the gate page we must NOT create a second
        // pending event for the same integration. Reuse any pending event
        // that was created within the last 2 minutes.
        if (! $isFirst) {
            $existing = LoginEvent::where('integration_id', $integration->id)
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subMinutes(2))
                ->latest()
                ->first();

            if ($existing) {
                $existing->setRelation('integration', $integration);
                return response()->json($this->eventPayload($existing));
            }
        }

        $event = $this->recordLoginAttempt(
            $integration,
            $request,
            $data['credential_username'] ?? null,
        );

        return response()->json($this->eventPayload($event));
    }

    public function gateEventStatus(Integration $integration, LoginEvent $event)
    {
        abort_unless($event->integration_id === $integration->id, 404);

        return response()->json($this->eventPayload($event->load('integration')));
    }

    /**
     * Long-poll: hold the HTTP connection open until the event is resolved
     * (approved / declined / expired) or 10 seconds have elapsed.
     *
     * Releases the session lock immediately and exits early on client disconnect
     * so concurrent requests cannot exhaust PHP workers.
     */
    public function gateLongPoll(Request $request, Integration $integration, LoginEvent $event)
    {
        abort_unless($event->integration_id === $integration->id, 404);
        abort_unless($integration->is_active, 403);

        if ($request->hasSession()) {
            $request->session()->save();
        }

        return $this->waitForEventResolution($event);
    }

    private function waitForEventResolution(LoginEvent $event)
    {
        ignore_user_abort(false);
        set_time_limit(15);

        $maxIterations = 10; // 10 × 1 s = 10 s bounded wait window

        for ($i = 0; $i < $maxIterations; $i++) {
            if (connection_aborted()) {
                break;
            }

            $event->refresh();

            if ($event->status !== 'pending') {
                return response()->json($this->eventPayload($event->load('integration')));
            }

            usleep(1_000_000);

            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }

        return response()->json($this->eventPayload($event->load('integration')));
    }

    public function respondToEvent(Request $request, LoginEvent $event)
    {
        abort_unless($event->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'decision' => 'required|in:approved,declined',
        ]);

        abort_unless($event->status === 'pending', 409, 'This login is no longer pending.');

        $event->update(['status' => $data['decision']]);

        return response()->json([
            'event_id' => $event->id,
            'status' => $event->status,
        ]);
    }

    public function history(Request $request)
    {
        $events = LoginEvent::with('integration:id,name,favicon_url')
            ->where('user_id', $request->user()->id)
            ->where('is_archived', false)
            ->latest()
            ->take(50)
            ->get();

        return response()->json($events);
    }

    public function archiveEvent(Request $request, LoginEvent $event)
    {
        abort_unless($event->user_id === $request->user()->id, 403);
        $event->update(['is_archived' => true]);

        return response()->json(['message' => 'Event archived']);
    }

    public function destroyEvent(Request $request, LoginEvent $event)
    {
        abort_unless($event->user_id === $request->user()->id, 403);
        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }

    public function pendingApprovals(Request $request)
    {
        $events = LoginEvent::with('integration:id,name,favicon_url')
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json($events);
    }

    public function totpCode(Request $request, Integration $integration)
    {
        $this->authorizeOwner($request->user(), $integration);
        $g2fa = new Google2FA();
        $code = $g2fa->getCurrentOtp($integration->secret_key);
        $remaining = 30 - (time() % 30);

        // ── TOTP Access Tracking ─────────────────────────────────────────────
        // Since we cannot monitor external sites directly, we treat TOTP code 
        // access as a "Login Attempt". We throttle this to once every 30 seconds 
        // to avoid spamming the database during frontend polling.
        $existing = LoginEvent::where('integration_id', $integration->id)
            ->where('status', 'auto')
            ->where('created_at', '>=', now()->subSeconds(25))
            ->first();

        if (!$existing) {
            $this->recordLoginAttempt(
                $integration,
                $request,
                null, // username unknown
                $request->ip(),
                $request->userAgent() . ' (TOTP Access)'
            );
        }

        return response()->json(['code' => $code, 'expires_in' => $remaining]);
    }

    private function startLoginEvent(Request $request, Integration $integration)
    {
        if (! $integration->is_active) {
            return response()->json(['message' => 'Integration is disabled'], 403);
        }

        $data = $request->validate([
            'ip_address' => 'nullable|string|max:45',
            'device' => 'nullable|string|max:255',
            'credential_username' => 'nullable|string|max:255',
        ]);

        $event = $this->recordLoginAttempt(
            $integration,
            $request,
            $data['credential_username'] ?? null,
            $data['ip_address'] ?? null,
            $data['device'] ?? null,
        );

        return response()->json($this->eventPayload($event), 201);
    }

    private function recordLoginAttempt(
        Integration $integration,
        Request $request,
        ?string $username = null,
        ?string $ip = null,
        ?string $device = null,
    ): LoginEvent {
        $isFirst = ! $integration->hasCompletedFirstLogin();

        $event = LoginEvent::create([
            'integration_id' => $integration->id,
            'user_id' => $integration->user_id,
            'ip_address' => $ip ?? $request->ip(),
            'device' => $device ?? $request->userAgent(),
            'status' => $isFirst ? 'auto' : 'pending',
            'is_first_login' => $isFirst,
            'credential_username' => $username,
        ]);

        if (! $isFirst) {
            // 1. Real-time WebSocket broadcast (instant, for online devices)
            LoginAttemptBroadcast::dispatch($event, $integration);

            // 2. Expo push notification (for offline / background devices)
            $this->sendPushApproval($integration->user, $integration, $event);
        }

        $event->setRelation('integration', $integration);

        return $event;
    }

    private function eventPayload(LoginEvent $event): array
    {
        $allowed = in_array($event->status, ['approved', 'auto'], true);

        return [
            'event_id' => $event->id,
            'is_first' => (bool) $event->is_first_login,
            'status' => $event->status,
            'requires_approval' => $event->status === 'pending',
            'redirect_url' => $allowed ? $this->safeRedirectUrl($event->integration->url ?? null) : null,
        ];
    }

    private function safeRedirectUrl(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return is_string($scheme) && in_array(strtolower($scheme), ['http', 'https'], true)
            ? $url
            : null;
    }

    private function integrationFromSecret(Request $request): Integration
    {
        $secret = $request->header('X-Integration-Secret') ?? $request->post('secret_key');
        abort_unless(is_string($secret) && $secret !== '', 401, 'Invalid secret key');

        $integration = Integration::where('secret_key', $secret)->first();
        abort_unless($integration && hash_equals((string) $integration->secret_key, $secret), 401, 'Invalid secret key');

        return $integration;
    }

    private function authorizeOwner($user, Integration $integration): void
    {
        abort_unless($integration->user_id === $user->id, 403);
    }

    private function formatIntegration(Integration $i): array
    {
        return [
            'id' => $i->id,
            'name' => $i->name,
            'url' => $i->url,
            'secret_key' => $i->secret_key,
            'gate_token' => $i->gate_token,
            'gate_url' => $i->gateUrl(),
            'is_active' => $i->is_active,
            'favicon_url' => $i->favicon_url ?? $i->favicon,
            'created_at' => $i->created_at,
        ];
    }

    private function sendPushApproval($user, Integration $integration, LoginEvent $event): void
    {
        $devices = Device::where('user_id', $user->id)
            ->whereNotNull('expo_push_token')
            ->pluck('expo_push_token');

        if ($devices->isEmpty()) {
            return;
        }

        $messages = $devices->map(fn ($token) => [
            'to' => $token,
            'title' => '🔐 Login Attempt Detected',
            'body' => "Someone is trying to log in to {$integration->name}. Approve?",
            'data' => [
                'event_id' => $event->id,
                'integration_name' => $integration->name,
                'type' => 'login_approval',
            ],
            'sound' => 'default',
            'priority' => 'high',
        ])->values()->toArray();

        try {
            $response = Http::post('https://exp.host/--/api/v2/push/send', $messages);
            if (!$response->ok()) {
                Log::error('Expo Push Error: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::warning('Push notification failed: '.$e->getMessage());
        }
    }
}
