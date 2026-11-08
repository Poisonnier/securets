<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Register or refresh an Expo push token for the authenticated user.
     * Called by the mobile app right after login.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'expo_push_token' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        Device::updateOrCreate(
            ['expo_push_token' => $data['expo_push_token']],
            [
                'user_id' => $request->user()->id,
                'device_name' => $data['device_name'] ?? 'Unknown Device',
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['message' => 'Device registered']);
    }

    /**
     * List all devices for the current user.
     */
    public function index(Request $request): JsonResponse
    {
        $devices = Device::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($devices);
    }

    /**
     * Remove a registered device (remote logout from device).
     */
    public function destroy(Request $request, Device $device): JsonResponse
    {
        abort_unless($device->user_id === $request->user()->id, 403);
        $device->delete();

        return response()->json(['message' => 'Device removed']);
    }
}
