<?php

use App\Models\Integration;
use App\Models\User;

function extIntegration(User $user, array $overrides = []): Integration
{
    return $user->integrations()->create(array_merge([
        'name' => 'Facebook',
        'url' => 'https://www.facebook.com/',
        'secret_key' => 'EXTSECRETKEY1234',
        'is_active' => true,
    ], $overrides));
}

test('the extension config rejects a missing or invalid pairing token', function () {
    $this->getJson('/api/extension/config')->assertUnauthorized();
    $this->getJson('/api/extension/config', ['X-Extension-Token' => 'not-a-real-token'])->assertUnauthorized();
});

test('the extension config returns the active gated urls for the paired user', function () {
    $user = User::factory()->create(['extension_token' => 'pair-token-abc']);
    $active = extIntegration($user, ['name' => 'Facebook', 'url' => 'https://www.facebook.com/']);
    extIntegration($user, ['name' => 'Paused App', 'url' => 'https://paused.example.com/', 'is_active' => false]);

    $response = $this->getJson('/api/extension/config', ['X-Extension-Token' => 'pair-token-abc'])
        ->assertOk()
        ->assertJsonCount(1, 'integrations')
        ->assertJsonPath('integrations.0.name', 'Facebook')
        ->assertJsonPath('integrations.0.host', 'www.facebook.com')
        ->assertJsonPath('integrations.0.url', 'https://www.facebook.com/')
        ->assertJsonPath('integrations.0.gate_url', $active->gateUrl());

    // The integration secret must never leave the server.
    expect($response->json())->not->toHaveKey('secret_key')
        ->and(json_encode($response->json()))->not->toContain($active->secret_key);
});

test('the extension config never exposes another user\'s integrations', function () {
    $owner = User::factory()->create(['extension_token' => 'owner-token']);
    extIntegration($owner);

    User::factory()->create(['extension_token' => 'stranger-token']);

    $this->getJson('/api/extension/config', ['X-Extension-Token' => 'stranger-token'])
        ->assertOk()
        ->assertJsonCount(0, 'integrations');
});

test('the pairing token is issued once and rotation revokes the old one', function () {
    $user = User::factory()->create();
    expect($user->extension_token)->toBeNull();

    $issued = $this->actingAs($user)->getJson('/web/extension-token')
        ->assertOk()
        ->json('token');

    expect($issued)->toBeString()->not->toBeEmpty()
        ->and($user->fresh()->extension_token)->toBe($issued);

    // Reading again must not rotate the token.
    $this->actingAs($user)->getJson('/web/extension-token')->assertJsonPath('token', $issued);

    $rotated = $this->actingAs($user)->postJson('/web/extension-token/rotate')
        ->assertOk()
        ->json('token');

    expect($rotated)->not->toBe($issued)
        ->and($user->fresh()->extension_token)->toBe($rotated);

    // The superseded token can no longer read the config.
    $this->getJson('/api/extension/config', ['X-Extension-Token' => $issued])->assertUnauthorized();
    $this->getJson('/api/extension/config', ['X-Extension-Token' => $rotated])->assertOk();
});

test('the pairing token endpoints require an authenticated session', function () {
    $this->getJson('/web/extension-token')->assertUnauthorized();
    $this->postJson('/web/extension-token/rotate')->assertUnauthorized();
});

test('the gate page advertises extension mode only when ext is requested', function () {
    $user = User::factory()->create();
    $integration = extIntegration($user);

    $this->get('/i/'.$integration->gate_token)
        ->assertOk()
        ->assertSee('data-ext="0"', false);

    $this->get('/i/'.$integration->gate_token.'?ext=1')
        ->assertOk()
        ->assertSee('data-ext="1"', false);
});
