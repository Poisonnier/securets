<?php

use App\Models\Device;
use App\Models\Integration;
use App\Models\User;

test('guests are redirected to login when trying to view settings', function () {
    $this->get(route('settings'))
        ->assertRedirect(route('login'));
});

test('settings page is displayed for authenticated user with stats', function () {
    $user = User::factory()->create([
        'preferences' => [
            'email_notifications' => true,
            'mobile_push' => false,
            'theme' => 'dark',
        ],
    ]);

    Device::create([
        'user_id' => $user->id,
        'device_name' => 'Test iPhone',
        'expo_push_token' => 'ExponentPushToken[xyz]',
        'last_seen_at' => now(),
    ]);

    Integration::create([
        'user_id' => $user->id,
        'name' => 'Test App',
        'url' => 'https://example.com',
        'secret_key' => 'secret_12345',
        'gate_token' => 'token_12345',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('settings'))
        ->assertOk()
        ->assertSee('Settings')
        ->assertSee('Account Security')
        ->assertSee('1 device')
        ->assertSee('1 app')
        ->assertSee('Paired Devices')
        ->assertSee('Test iPhone')
        ->assertSee('new-securets-logo-dark.png', false)
        ->assertSee('My Apps')
        ->assertDontSee('Light Mode')
        ->assertDontSee('System Auto');
});

test('user can update settings preferences', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->patch(route('settings.update'), [
            'email_notifications' => '1',
            'mobile_push' => '0',
        ]);

    $response->assertSessionHas('status')
        ->assertRedirect();

    $user->refresh();
    expect($user->preferences['email_notifications'])->toBeTrue()
        ->and($user->preferences['mobile_push'])->toBeFalse()
        ->and($user->preferences['theme'])->toBe('dark');
});

test('invalid theme value is rejected', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->patch(route('settings.update'), [
            'theme' => 'unsupported-theme',
        ]);

    $response->assertSessionHasErrors('theme');
});

test('owning user can revoke a paired device from web settings while other users get 403', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $device = Device::create([
        'user_id' => $owner->id,
        'device_name' => 'Owner Pixel 9',
        'expo_push_token' => 'ExponentPushToken[owner-pixel]',
        'last_seen_at' => now(),
    ]);

    $this->actingAs($other)
        ->deleteJson("/web/devices/{$device->id}")
        ->assertForbidden();

    $this->actingAs($owner)
        ->deleteJson("/web/devices/{$device->id}")
        ->assertOk();

    $this->assertDatabaseMissing('devices', ['id' => $device->id]);
});

test('core views use vite assets and do not load cdn.tailwindcss.com', function () {
    $user = User::factory()->create();
    $integration = Integration::create([
        'user_id' => $user->id,
        'name' => 'Core App',
        'url' => 'https://core.example.com',
        'secret_key' => 'CORESECRET123456',
        'gate_token' => 'core_gate_token_12345',
        'is_active' => true,
    ]);

    $publicUrls = ['/', '/login', '/register', "/i/{$integration->gate_token}"];
    foreach ($publicUrls as $url) {
        $this->get($url)
            ->assertOk()
            ->assertDontSee('cdn.tailwindcss.com');
    }

    $authUrls = [route('dashboard'), route('integrations'), route('profile'), route('settings')];
    foreach ($authUrls as $url) {
        $this->actingAs($user)
            ->get($url)
            ->assertOk()
            ->assertDontSee('cdn.tailwindcss.com');
    }
});
