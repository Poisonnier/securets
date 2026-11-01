<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_clients_can_log_in_through_current_and_legacy_paths(): void
    {
        User::factory()->create([
            'email' => 'mobile@securets.com',
            'password' => bcrypt('secret123'),
        ]);

        foreach (['/api/mobile/login', '/api/login'] as $path) {
            $response = $this->postJson($path, [
                'email' => 'mobile@securets.com',
                'password' => 'secret123',
                'device_name' => 'iphone_15_pro',
            ]);

            $response->assertOk()
                ->assertJsonStructure([
                    'token',
                    'expires_at',
                    'expires_in_minutes',
                    'user' => ['id', 'name', 'email'],
                ]);

            $token = $response->json('token');

            $this->withHeader('Authorization', 'Bearer '.$token)
                ->getJson('/api/mobile/me')
                ->assertOk()
                ->assertJson([
                    'email' => 'mobile@securets.com',
                ]);
        }
    }

    public function test_mobile_login_handles_case_insensitivity_and_formatting(): void
    {
        User::factory()->create([
            'email' => 'mobile@securets.com',
            'password' => bcrypt('secret123'),
        ]);

        // Test with caps and spaces
        $response = $this->postJson('/api/mobile/login', [
            'email' => ' MOBILE@SECURETS.COM ',
            'password' => 'secret123',
        ]);

        $response->assertOk();
    }

    public function test_mobile_login_returns_expected_error_format(): void
    {
        User::factory()->create([
            'email' => 'mobile@securets.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/mobile/login', [
            'email' => 'mobile@securets.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'email' => ['Incorrect email or password.']
                ]
            ]);
    }

    public function test_can_archive_and_delete_login_events(): void
    {
        $user = User::factory()->create();
        $integration = $user->integrations()->create([
            'name' => 'Test App',
            'url' => 'https://example.com',
            'secret_key' => 'ABC',
        ]);
        $event = $integration->loginEvents()->create([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $this->actingAs($user, 'sanctum');

        // Archive
        $this->patchJson("/api/login-events/{$event->id}/archive")
            ->assertOk();
        $this->assertTrue($event->fresh()->is_archived);

        // Delete
        $this->deleteJson("/api/login-events/{$event->id}")
            ->assertOk();
        $this->assertDatabaseMissing('login_events', ['id' => $event->id]);
    }

    public function test_mobile_client_can_reach_the_reverb_broadcast_auth_endpoint(): void
    {
        $user = User::factory()->create();

        // The mobile app authenticates Reverb private channels by POSTing to
        // `${EXPO_PUBLIC_API_URL}/broadcasting/auth`, i.e. /api/broadcasting/auth.
        // That endpoint used to be registered only on the session-based
        // `/broadcasting/auth` route, so every mobile request 404'd and realtime
        // sync silently degraded to polling.
        $payload = [
            'channel_name' => "private-user.{$user->id}",
            'socket_id' => '1234.5678',
        ];

        // The route must exist on the API surface and refuse anonymous callers.
        $this->postJson('/api/broadcasting/auth', $payload)
            ->assertUnauthorized();

        // A Sanctum bearer token gets past the guard.
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/broadcasting/auth', $payload)
            ->assertSuccessful();
    }
}
