<?php

use App\Models\SecurityAuditEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('guest admin dashboard requests redirect to login', function (): void {
    $this->get('/admin/dashboard')
        ->assertRedirect('/login');
});

test('admin login screen renders', function (): void {
    $this->withoutVite();

    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('Mobile Lara Admin')
        ->assertSee('Email')
        ->assertSee('Password');
});

test('platform admin can authenticate and view dashboard', function (): void {
    $this->withoutVite();

    User::factory()->platformAdmin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password-secret'),
    ]);

    $this->post('/admin/login', [
        'email' => 'admin@example.com',
        'password' => 'password-secret',
    ])->assertRedirect('/admin/dashboard');

    $this->assertAuthenticated();

    $this->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Mobile Control Dashboard');

    expect(SecurityAuditEvent::query()->where('event', 'admin_login_succeeded')->exists())->toBeTrue();
});

test('non admin users cannot authenticate to the admin panel', function (): void {
    User::factory()->create([
        'email' => 'worker@example.com',
        'password' => Hash::make('password-secret'),
    ]);

    $this->from('/admin/login')
        ->post('/admin/login', [
            'email' => 'worker@example.com',
            'password' => 'password-secret',
        ])
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
    expect(SecurityAuditEvent::query()->where('event', 'admin_login_failed')->exists())->toBeTrue();
});

test('platform admin can logout', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    $this->actingAs($admin)
        ->post('/admin/logout')
        ->assertRedirect('/admin/login');

    $this->assertGuest();
    expect(SecurityAuditEvent::query()->where('event', 'admin_logout_succeeded')->exists())->toBeTrue();
});
