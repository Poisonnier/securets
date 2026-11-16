<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SystemLockdownMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Keep the health probe available so the admin console can report the
        // system state while normal application traffic is frozen.
        if ($request->is('up') || ! Schema::hasTable('system_controls')) {
            return $next($request);
        }

        $rawState = DB::table('system_controls')
            ->where('key', 'system_lockdown')
            ->value('value');
        $state = is_string($rawState) ? json_decode($rawState, true) : null;

        if (! is_array($state) || ! ($state['active'] ?? false)) {
            return $next($request);
        }

        $payload = [
            'message' => 'System lockdown is active. Authentication and application traffic are temporarily suspended.',
            'lockdown' => $state,
        ];

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json($payload, 423);
        }

        return response($payload['message'], 423);
    }
}
