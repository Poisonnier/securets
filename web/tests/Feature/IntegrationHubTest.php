<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guests are sent to login when opening the integration hub', function () {
    $this->get(route('integrations'))
        ->assertRedirect(route('login'));
});

test('the landing page links to sign in', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Log in', false)
        ->assertSee(route('login', absolute: false), false)
        ->assertSee('Get Started', false)
        ->assertSee(route('register', absolute: false), false)
        ->assertSee('Pricing', false)
        ->assertSee('#pricing', false)
        ->assertSee('Subscription Plans')
        ->assertSee('Free')
        ->assertSee('Pro')
        ->assertSee('Ultra')
        ->assertSee('Ultra Plus')
        ->assertSee('Verify Session');
});

test('the register page renders a back to home link pointing to the landing page', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Back to home')
        ->assertSee('href="'.route('home').'"', false);
});

test('registration authenticates the user and redirects to the integration hub', function () {
    $response = $this->post('/register', [
        'name' => 'Jane Developer',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('integrations'));
    $this->assertAuthenticated();
});

test('dashboard renders integration name and credential username for recent login events', function () {
    $user = User::factory()->create();
    $integration = $user->integrations()->create([
        'name' => 'Billing Portal',
        'url' => 'https://billing.example/login',
        'secret_key' => 'SECRETKEY1234567',
        'is_active' => true,
    ]);

    $integration->loginEvents()->create([
        'user_id' => $user->id,
        'credential_username' => 'ops-admin@example.com',
        'ip_address' => '198.51.100.42',
        'status' => 'approved',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Billing Portal')
        ->assertSee('ops-admin@example.com');
});

test('a signed-in user can open the integration hub', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('integrations'))
        ->assertOk()
        ->assertSee('Integration Hub')
        ->assertSee('No apps integrated yet')
        ->assertDontSee('onclick="respond', false)
        ->assertDontSee('>Approve</button>', false)
        ->assertDontSee('>Decline</button>', false);
});

test('login lands on the integration hub', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('integrations'));
});

test('a user can add an external app on the web hub', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/web/integrations', [
            'name' => 'Payroll',
            'url' => 'https://payroll.example/login',
        ])
        ->assertCreated()
        ->assertJsonPath('name', 'Payroll')
        ->assertJsonPath('url', 'https://payroll.example/login')
        ->assertJsonStructure(['id', 'secret_key', 'gate_url', 'gate_token']);

    $this->actingAs($user)
        ->get(route('integrations'))
        ->assertOk()
        ->assertSee('Payroll')
        ->assertSee('https://payroll.example/login');
});

test('card actions hand the app id over as a data attribute instead of inline interpolation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/web/integrations', [
            'name' => 'Payroll',
            'url' => 'https://payroll.example/login',
        ])->assertCreated();

    $this->actingAs($user)
        ->get(route('integrations'))
        ->assertOk()
        ->assertSee('data-app-id=', false)
        ->assertSee('onclick="removeApp(this)"', false)
        ->assertSee('onchange="toggleApp(this)"', false);
});

test('integrations belong only to the owning account on web and mobile', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($owner)
        ->postJson('/web/integrations', [
            'name' => 'Owner App',
            'url' => 'https://owner.example/login',
        ])->assertCreated();

    $this->actingAs($other)
        ->get(route('integrations'))
        ->assertOk()
        ->assertDontSee('Owner App');

    Sanctum::actingAs($other);
    $this->getJson('/api/integrations')
        ->assertOk()
        ->assertJsonCount(0);

    Sanctum::actingAs($owner);
    $this->getJson('/api/integrations')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.name', 'Owner App');
});

test('the web hub cannot approve or decline logins', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patchJson('/web/login-events/1/respond', ['decision' => 'approved'])
        ->assertNotFound();
});
