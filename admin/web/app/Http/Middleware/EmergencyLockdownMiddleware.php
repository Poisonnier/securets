<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EmergencyLockdownMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $lockdown = ['active' => false];
        if (Schema::hasTable('system_controls')) {
            $rawState = DB::table('system_controls')
                ->where('key', 'system_lockdown')
                ->value('value');
            $lockdown = is_string($rawState)
                ? (json_decode($rawState, true) ?: $lockdown)
                : $lockdown;
        }

        if (($lockdown['active'] ?? false) && ! in_array($routeName, [
            'admin.api.overview',
            'admin.api.system.unlock',
        ], true)) {
            return response()->json([
                'message' => 'Emergency lockdown is active. Administrative actions are temporarily suspended.',
                'lockdown' => $lockdown,
            ], 423);
        }

        return $next($request);
    }
}
