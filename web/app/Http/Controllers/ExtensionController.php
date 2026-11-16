<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExtensionController extends Controller
{
    /**
     * Browser extension: read-only list of the user's active gated URLs.
     * Authenticated with the pairing token, never with the account session.
     */
    public function config(Request $request): JsonResponse
    {
        $user = $this->userFromExtensionToken($request);

        $integrations = $user->integrations()
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (Integration $integration): array => [
                'id' => $integration->id,
                'name' => $integration->name,
                'url' => $integration->url,
                'host' => strtolower((string) parse_url((string) $integration->url, PHP_URL_HOST)),
                'gate_url' => $integration->gateUrl(),
            ])
            ->values();

        return response()->json([
            'base_url' => rtrim(url('/'), '/'),
            'integrations' => $integrations,
        ]);
    }

    /** Web dashboard: return the pairing token, issuing one on first request. */
    public function showToken(Request $request): JsonResponse
    {
        $user = $request->user();

        if (blank($user->extension_token)) {
            $user->update(['extension_token' => Str::random(48)]);
        }

        return response()->json([
            'token' => $user->extension_token,
            'base_url' => rtrim(url('/'), '/'),
        ]);
    }

    /** Web dashboard: replace the pairing token, revoking every paired browser. */
    public function rotateToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->update(['extension_token' => Str::random(48)]);

        return response()->json(['token' => $user->extension_token]);
    }

    private function userFromExtensionToken(Request $request): User
    {
        $token = $request->header('X-Extension-Token');
        abort_unless(is_string($token) && $token !== '', 401, 'Missing extension token');

        $user = User::where('extension_token', $token)->first();
        abort_unless($user && hash_equals((string) $user->extension_token, $token), 401, 'Invalid extension token');

        return $user;
    }
}
