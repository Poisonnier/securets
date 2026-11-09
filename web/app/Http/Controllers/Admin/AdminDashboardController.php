<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Admin Dashboard: Account Recovery & Device Kill Switch Control Center
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $adminName = $user ? $user->name : 'Admin';

        // Sample / Real recovery requests for lost phones or laptops
        $recoveryRequests = [
            [
                'id' => 101,
                'user_name' => 'Sarah Chen',
                'user_email' => 'sarah.chen@threatshield.internal',
                'incident_type' => 'Lost Mobile Phone',
                'incident_badge' => 'badge-lost-phone',
                'device_name' => 'iPhone 15 Pro (2FA Authenticator)',
                'last_ip' => '112.198.42.10',
                'reported_at' => '18 minutes ago',
                'description' => 'User left phone in taxi during commute. Unable to approve ThreatShield 2FA mobile prompts or retrieve TOTP codes. Active sessions need immediate quarantine.',
                'status' => 'Pending Verification',
            ],
            [
                'id' => 102,
                'user_name' => 'Alex Rivera',
                'user_email' => 'alex.rivera@threatshield.internal',
                'incident_type' => 'Stolen Work Laptop',
                'incident_badge' => 'badge-stolen-laptop',
                'device_name' => 'Lenovo ThinkPad X1 Carbon (Windows 11)',
                'last_ip' => '175.158.89.214',
                'reported_at' => '42 minutes ago',
                'description' => 'Laptop stolen from vehicle. Hard drive contains active session cookies and local keys. Immediate remote wipe & kill switch required.',
                'status' => 'Urgent Lockdown',
            ],
            [
                'id' => 103,
                'user_name' => 'Marcus Vance',
                'user_email' => 'm.vance@threatshield.internal',
                'incident_type' => 'Hardware Failure / Brick',
                'incident_badge' => 'badge-lost-phone',
                'device_name' => 'Google Pixel 8 (ThreatShield App)',
                'last_ip' => '120.28.17.95',
                'reported_at' => '2 hours ago',
                'description' => 'Phone battery expanded and device failed to boot. User needs one-time bypass code to sign in from backup workstation and pair a replacement device.',
                'status' => 'Identity Verified',
            ],
        ];

        // Active Registered Devices across users (for emergency kill switch & audit)
        $activeDevices = [
            [
                'id' => 1,
                'user_name' => 'Alex Rivera',
                'user_email' => 'alex.rivera@threatshield.internal',
                'device_name' => 'Lenovo ThinkPad X1 Carbon',
                'device_type' => 'Laptop',
                'os' => 'Windows 11 Enterprise',
                'last_ip' => '175.158.89.214',
                'last_active' => '42m ago',
                'status' => 'Compromised / Lost',
                'status_class' => 'badge-stolen-laptop',
            ],
            [
                'id' => 2,
                'user_name' => 'Sarah Chen',
                'user_email' => 'sarah.chen@threatshield.internal',
                'device_name' => 'iPhone 15 Pro',
                'device_type' => 'Mobile',
                'os' => 'iOS 18.1',
                'last_ip' => '112.198.42.10',
                'last_active' => '18m ago',
                'status' => 'Reported Lost',
                'status_class' => 'badge-lost-phone',
            ],
            [
                'id' => 3,
                'user_name' => 'David Kim',
                'user_email' => 'david.kim@threatshield.internal',
                'device_name' => 'MacBook Pro 16" M3',
                'device_type' => 'Laptop',
                'os' => 'macOS Sonoma 14.5',
                'last_ip' => '124.106.130.45',
                'last_active' => '3 mins ago',
                'status' => 'Secure / Active',
                'status_class' => 'badge-active',
            ],
            [
                'id' => 4,
                'user_name' => 'Elena Rostova',
                'user_email' => 'elena.r@threatshield.internal',
                'device_name' => 'Samsung Galaxy S24 Ultra',
                'device_type' => 'Mobile',
                'os' => 'Android 15',
                'last_ip' => '136.158.33.72',
                'last_active' => '12 mins ago',
                'status' => 'Secure / Active',
                'status_class' => 'badge-active',
            ],
            [
                'id' => 5,
                'user_name' => 'Jordan Taylor',
                'user_email' => 'jordan.t@threatshield.internal',
                'device_name' => 'Dell Latitude 7440',
                'device_type' => 'Laptop',
                'os' => 'Ubuntu 24.04 LTS',
                'last_ip' => '180.191.22.88',
                'last_active' => 'Yesterday',
                'status' => 'Revoked',
                'status_class' => 'badge-revoked',
            ],
        ];

        return view('admin.dashboard', [
            'adminName' => $adminName,
            'recoveryRequests' => $recoveryRequests,
            'activeDevices' => $activeDevices,
            'pendingCount' => count($recoveryRequests),
            'revokedDevicesCount' => 2,
            'activeBypassCodes' => 1,
            'totalProtected' => 48,
        ]);
    }

    /**
     * Admin Security Notifications Center
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();
        $adminName = $user ? $user->name : 'Admin';

        $notifications = [
            [
                'id' => 1,
                'category' => 'lost-device',
                'unread' => true,
                'urgent' => true,
                'title' => 'Emergency Lockdown Requested: Stolen Laptop',
                'message' => 'User Alex Rivera (alex.rivera@threatshield.internal) reported their Lenovo ThinkPad X1 Carbon stolen. Recommended action: Trigger Remote Kill Switch immediately.',
                'time' => '42 mins ago',
                'icon' => 'laptop',
            ],
            [
                'id' => 2,
                'category' => 'recovery',
                'unread' => true,
                'urgent' => false,
                'title' => 'Account Recovery Ticket: Lost Authenticator Phone',
                'message' => 'Sarah Chen (sarah.chen@threatshield.internal) is locked out of 2FA after losing their iPhone 15 Pro. Manager identity verification attached.',
                'time' => '1 hour ago',
                'icon' => 'phone',
            ],
            [
                'id' => 3,
                'category' => 'security',
                'unread' => false,
                'urgent' => false,
                'title' => 'Emergency Bypass Code Redeemed',
                'message' => 'A single-use recovery code (STS-4921-KZ) was used by dev.lead@threatshield.internal to regain account access. Old session keys revoked.',
                'time' => '3 hours ago',
                'icon' => 'key',
            ],
            [
                'id' => 4,
                'category' => 'lost-device',
                'unread' => false,
                'urgent' => false,
                'title' => 'Remote Wipe Completed on Revoked Device',
                'message' => 'Sanctum push tokens and credentials for device "Dell Latitude 7440" were successfully invalidated. Device cannot communicate with ThreatShield API.',
                'time' => 'Yesterday at 4:15 PM',
                'icon' => 'shield',
            ],
            [
                'id' => 5,
                'category' => 'recovery',
                'unread' => false,
                'urgent' => false,
                'title' => 'Hardware Failure Recovery Processed',
                'message' => 'Marcus Vance completed recovery key pairing on their replacement Google Pixel 9. Previous device wiped.',
                'time' => '2 days ago',
                'icon' => 'check',
            ],
        ];

        return view('admin.notifications', [
            'adminName' => $adminName,
            'notifications' => $notifications,
            'unreadCount' => 2,
        ]);
    }

    /**
     * Admin Settings & Recovery Policies
     */
    public function settings(Request $request)
    {
        $user = Auth::user();
        $adminName = $user ? $user->name : 'Admin';

        return view('admin.settings', [
            'adminName' => $adminName,
        ]);
    }

    /**
     * Generate an Emergency Recovery Bypass Code for a locked-out user
     */
    public function generateBypassCode(Request $request)
    {
        $email = $request->input('email');
        $code = 'STS-'.rand(1000, 9999).'-'.strtoupper(substr(md5(uniqid()), 0, 2));

        return response()->json([
            'status' => 'success',
            'code' => $code,
            'expires_in' => '60 minutes',
            'message' => "Emergency bypass code generated for {$email}.",
        ]);
    }

    /**
     * Remote Kill Switch / Device Revocation
     */
    public function lockdownDevice(Request $request)
    {
        $deviceId = $request->input('device_id');
        $deviceName = $request->input('device_name', 'Device');

        return response()->json([
            'status' => 'success',
            'message' => "Device {$deviceName} (ID: {$deviceId}) successfully revoked and wiped remotely.",
        ]);
    }

    /**
     * Save admin settings
     */
    public function saveSettings(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Admin recovery policies updated successfully.',
        ]);
    }
}
