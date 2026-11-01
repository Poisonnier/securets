<?php

use App\Models\Integration;
use App\Models\LoginEvent;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Http::fake();
});

function makeIntegration(User $user, array $overrides = []): Integration
{
    return $user->integrations()->create(array_merge([
        'name' => 'Target App',
        'url' => 'https://target.example/login',
        'secret_key' => 'TESTSECRETKEY1234',
        'is_active' => true,
    ], $overrides));
}

test('rejects login events that use a bad secret', function () {
    $user = User::factory()->create();
    makeIntegration($user);

    $this->postJson('/api/integrations/login-event', [
        'secret_key' => 'wrong-secret',
    ])->assertUnauthorized();
});

test('first login with a valid secret is auto-approved', function () {
    $user = User::factory()->create();
    $integration = makeIntegration($user);

    $this->postJson('/api/integrations/login-event', [
        'secret_key' => $integration->secret_key,
        'credential_username' => 'alex@example.com',
        'device' => 'Chrome',
    ])->assertCreated()->assertJson([
        'is_first' => true,
        'status' => 'auto',
        'requires_approval' => false,
        'redirect_url' => 'https://target.example/login',
    ]);

    $this->assertDatabaseHas('login_events', [
        'integration_id' => $integration->id,
        'status' => 'auto',
        'is_first_login' => true,
        'credential_username' => 'alex@example.com',
    ]);
});

test('later login with the same secret requires approval', function () {
    $user = User::factory()->create();
    $integration = makeIntegration($user);

    $this->postJson('/api/integrations/login-event', [
        'secret_key' => $integration->secret_key,
    ])->assertCreated();

    $second = $this->postJson('/api/integrations/login-event', [
        'secret_key' => $integration->secret_key,
        'ip_address' => '203.0.113.9',
    ])->assertCreated()->assertJson([
        'is_first' => false,
        'status' => 'pending',
        'requires_approval' => true,
        'redirect_url' => null,
    ]);

    $eventId = $second->json('event_id');

    $this->getJson('/api/login-events/'.$eventId.'?secret_key='.$integration->secret_key)
        ->assertOk()
        ->assertJson(['status' => 'pending', 'event_id' => $eventId]);

    Sanctum::actingAs($user);
    $this->patchJson('/api/login-events/'.$eventId.'/respond', ['decision' => 'approved'])
        ->assertOk()
        ->assertJson(['status' => 'approved']);

    $this->getJson('/api/login-events/'.$eventId.'?secret_key='.$integration->secret_key)
        ->assertOk()
        ->assertJson([
            'status' => 'approved',
            'redirect_url' => 'https://target.example/login',
        ]);
});

test('the owner can decline a pending login', function () {
    $user = User::factory()->create();
    $integration = makeIntegration($user);
    $this->postJson('/api/integrations/login-event', ['secret_key' => $integration->secret_key]);

    $eventId = $this->postJson('/api/integrations/login-event', [
        'secret_key' => $integration->secret_key,
    ])->json('event_id');

    Sanctum::actingAs($user);
    $this->patchJson('/api/login-events/'.$eventId.'/respond', ['decision' => 'declined'])
        ->assertOk()
        ->assertJson(['status' => 'declined']);

    $this->getJson('/api/login-events/'.$eventId.'?secret_key='.$integration->secret_key)
        ->assertOk()
        ->assertJson([
            'status' => 'declined',
            'redirect_url' => null,
        ]);
});

test('the hosted gate collects credentials on first visit', function () {
    $user = User::factory()->create();
    $integration = makeIntegration($user);

    $this->get('/i/'.$integration->gate_token)
        ->assertOk()
        ->assertSee('First-time setup');

    $this->postJson('/i/'.$integration->gate_token, [
        'credential_username' => 'alex@example.com',
        'credential_password' => 'not-stored',
    ], ['X-CSRF-TOKEN' => session()->token()])
        ->assertOk()
        ->assertJson(['status' => 'auto', 'is_first' => true]);

    expect(LoginEvent::first()->credential_username)->toBe('alex@example.com');
});

test('the hosted gate shows the linked app area beside the login flow', function () {
    $user = User::factory()->create();
    $integration = makeIntegration($user);

    $this->get('/i/'.$integration->gate_token)
        ->assertOk()
        ->assertSee('App Preview')
        ->assertSee('will open here after SecureTS finishes the current login check')
        ->assertSee('Open '.$integration->name);
});

test('pending approvals endpoint returns matching events and is not shadowed by the public event route', function () {
    // REGRESSION: before the ->whereNumber('event') fix on the public route,
    // GET /login-events/pending was consumed by GET /login-events/{event}
    // (with event = "pending"), causing route-model binding to fail with 404.
    // This test fails on old code and passes after the fix.

    $user = User::factory()->create();
    $integration = makeIntegration($user);

    // First login — auto-approved
    $this->postJson('/api/integrations/login-event', [
        'secret_key' => $integration->secret_key,
    ])->assertCreated();

    // Second login — produces a pending event
    $second = $this->postJson('/api/integrations/login-event', [
        'secret_key' => $integration->secret_key,
    ])->assertCreated();

    $eventId = $second->json('event_id');

    // Authenticated mobile call — must NOT be intercepted by the parameterized public route
    Sanctum::actingAs($user);
    $response = $this->getJson('/api/login-events/pending');

    $response->assertOk(); // was 404 before the whereNumber() fix

    $ids = collect($response->json())->pluck('id');
    expect($ids)->toContain($eventId);
});
