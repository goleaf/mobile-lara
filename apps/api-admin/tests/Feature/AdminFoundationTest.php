<?php

use App\Livewire\Admin\Dashboard;

test('admin dashboard renders the control plane shell', function (): void {
    $this->withoutVite();

    $this->get('/admin/dashboard')
        ->assertOk()
        ->assertSeeLivewire(Dashboard::class)
        ->assertSee('Mobile Control Dashboard')
        ->assertSee('Tenant authority')
        ->assertSee('Mobile API');
});

test('root route redirects to the admin dashboard', function (): void {
    $this->get('/')
        ->assertRedirect('/admin/dashboard');
});
