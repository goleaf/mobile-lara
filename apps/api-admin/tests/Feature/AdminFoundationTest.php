<?php

use App\Livewire\Admin\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dashboard renders the control plane shell', function (): void {
    $this->withoutVite();
    $admin = User::factory()->platformAdmin()->create();

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSeeLivewire(Dashboard::class)
        ->assertSee('Mobile Control Dashboard')
        ->assertSee('Tenant authority')
        ->assertSee(route('admin.tenants'), false)
        ->assertSee('Mobile API');
});

test('root route redirects to the admin dashboard', function (): void {
    $this->get('/')
        ->assertRedirect('/admin/dashboard');
});
