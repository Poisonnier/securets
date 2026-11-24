<?php

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

function makeGoogleState(string $client = 'web', ?int $exp = null): string
{
    return Crypt::encryptString((string) json_encode([
        'client' => $client,
        'exp' => $exp ?? now()->addMinutes(10)->timestamp,
        'nonce' => Str::random(16),
    ]));
}

function mockSocialiteGoogleUser(string $googleId, string $email, string $name = 'Ada Lovelace'): void
{
    $socialiteUser = Mockery::mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getId')->andReturn($googleId);
    $socialiteUser->shouldReceive('getEmail')->andReturn($email);
    $socialiteUser->shouldReceive('getName')->andReturn($name);
    $socialiteUser->shouldReceive('getNickname')->andReturn($name);

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

test('login and register pages link Continue with Google to /auth/google/redirect', function () {
    $redirectPath = route('auth.google.redirect', absolute: false);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Continue with Google')
        ->assertSee($redirectPath, false);

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Continue with Google')
        ->assertSee($redirectPath, false);
});

test('web google redirect returns a redirect to Google with an encrypted state parameter', function () {
    $response = $this->get(route('auth.google.redirect'));

    $response->assertRedirect();
    $location = (string) $response->headers->get('Location');
    expect($location)->toContain('accounts.google.com')
        ->and($location)->toContain('state=');

    parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
    $decoded = json_decode(Crypt::decryptString((string) ($query['state'] ?? '')), true);

    expect($decoded)->toBeArray()
        ->and($decoded['client'])->toBe('web')
        ->and($decoded['exp'])->toBeGreaterThan(now()->timestamp);
});

test('web google callback creates a new user, authenticates them, and redirects to integrations', function () {
    mockSocialiteGoogleUser('google-uid-1001', 'newgoogle@example.com', 'Grace Hopper');

    $state = makeGoogleState('web');

    $response = $this->get(route('auth.google.callback', [
        'state' => $state,
        'code' => 'fake-google-auth-code',
    ]));

    $response->assertRedirect(route('integrations'));
    $this->assertAuthenticated();

    $user = User::where('email', 'newgoogle@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('google-uid-1001')
        ->and($user->name)->toBe('Grace Hopper')
        ->and($user->email_verified_at)->not->toBeNull();
});

test('web google callback links google_id to an existing user with the same email without duplicating', function () {
    $existing = User::factory()->create([
        'email' => 'existing@example.com',
        'email_verified_at' => null,
        'google_id' => null,
    ]);

    mockSocialiteGoogleUser('google-uid-2002', 'existing@example.com', 'Existing User');

    $state = makeGoogleState('web');

    $this->get(route('auth.google.callback', [
        'state' => $state,
        'code' => 'fake-google-auth-code',
    ]))->assertRedirect(route('integrations'));

    expect(User::where('email', 'existing@example.com')->count())->toBe(1);

    $existing->refresh();
    expect($existing->google_id)->toBe('google-uid-2002')
        ->and($existing->email_verified_at)->not->toBeNull();
});

test('mobile google callback stores a hashed oauth_exchange_code and redirects to securets deep link', function () {
    mockSocialiteGoogleUser('google-uid-3003', 'mobilegoogle@example.com', 'Mobile User');

    $state = makeGoogleState('mobile');

    $response = $this->get(route('auth.google.callback', [
        'state' => $state,
        'code' => 'fake-google-auth-code',
    ]));

    $response->assertRedirect();
    $location = (string) $response->headers->get('Location');
    expect($location)->toStartWith('securets://auth/google?code=');

    parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
    $plainCode = (string) ($query['code'] ?? '');
    expect(strlen($plainCode))->toBe(64);

    $user = User::where('email', 'mobilegoogle@example.com')->firstOrFail();
    expect($user->oauth_exchange_code)->toBe(hash('sha256', $plainCode))
        ->and($user->oauth_exchange_expires_at)->not->toBeNull()
        ->and($user->oauth_exchange_expires_at->isFuture())->toBeTrue();
});

test('mobile google exchange returns a valid Sanctum token once and returns 422 when reused or expired', function () {
    $plainCode = Str::random(64);

    $user = User::factory()->create([
        'email' => 'exchange@example.com',
        'google_id' => 'google-uid-4004',
        'oauth_exchange_code' => hash('sha256', $plainCode),
        'oauth_exchange_expires_at' => now()->addSeconds(60),
    ]);

    // First exchange succeeds and matches POST /api/mobile/login JSON shape
    $response = $this->postJson('/api/mobile/auth/google/exchange', [
        'code' => $plainCode,
        'device_name' => 'ios_mobile_app',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'token',
            'expires_at',
            'expires_in_minutes',
            'user' => ['id', 'name', 'email', 'theme'],
        ])
        ->assertJsonPath('user.email', 'exchange@example.com');

    $user->refresh();
    expect($user->oauth_exchange_code)->toBeNull()
        ->and($user->oauth_exchange_expires_at)->toBeNull();

    // Reusing the same code fails with 422
    $this->postJson('/api/mobile/auth/google/exchange', [
        'code' => $plainCode,
    ])->assertStatus(422);

    // Expired code fails with 422
    $expiredPlainCode = Str::random(64);
    $user->update([
        'oauth_exchange_code' => hash('sha256', $expiredPlainCode),
        'oauth_exchange_expires_at' => now()->subSecond(),
    ]);

    $this->postJson('/api/mobile/auth/google/exchange', [
        'code' => $expiredPlainCode,
    ])->assertStatus(422);
});
