<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataController extends Controller
{
    /**
     * Return metrics and threat defense status for mobile dashboard.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        $activeShields = $user->integrations()->where('is_active', true)->count();
        $threatsBlocked = $user->loginEvents()->where('status', 'declined')->count();
        $pendingAlerts = $user->loginEvents()->where('status', 'pending')->count();
        $recentEvents = $user->loginEvents()
            ->with('integration:id,name')
            ->where('is_archived', false)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'status' => $activeShields > 0 ? 'active' : 'standby',
            'timestamp' => now()->toIso8601String(),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'metrics' => [
                'threats_blocked' => $threatsBlocked,
                'active_shields' => $activeShields,
                'pending_alerts' => $pendingAlerts,
                'system_health' => $pendingAlerts > 0 ? 'Attention Required' : 'Optimal',
                'threat_level' => $pendingAlerts > 0 ? 'ELEVATED' : 'LOW',
            ],
            'recent_activity' => $recentEvents->map(fn ($e) => [
                'id' => $e->id,
                'event' => $e->integration->name.' login '.$e->status,
                'time' => $e->created_at?->diffForHumans(),
                'severity' => match ($e->status) {
                    'declined' => 'warning',
                    'approved', 'auto' => 'success',
                    default => 'info',
                },
            ])->values(),
        ]);
    }
}
