<?php

use App\Enums\MobileFeatureState;
use App\Livewire\Admin\FeatureFlags;
use App\Models\MobileFeatureFlag;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest feature flag control requests redirect to login', function (): void {
    $this->get('/admin/mobile/features')
        ->assertRedirect('/login');
});

test('platform admins can view the feature flag control page', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();

    MobileFeatureFlag::factory()->create([
        'key' => 'records',
        'name' => 'Records',
        'default_state' => MobileFeatureState::Disabled,
    ]);

    $this->actingAs($admin)
        ->get('/admin/mobile/features')
        ->assertOk()
        ->assertSeeLivewire(FeatureFlags::class)
        ->assertSee('Feature Flags')
        ->assertSee('records')
        ->assertSee('Records');
});

test('platform admins can create global feature flags', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(FeatureFlags::class)
        ->set('form.key', 'records')
        ->set('form.name', 'Records')
        ->set('form.default_state', MobileFeatureState::Visible->value)
        ->set('form.reason', 'admin_enabled')
        ->set('form.message', 'Records are available on mobile.')
        ->set('form.required_plans', 'foundation, enterprise')
        ->set('form.allowed_platforms', 'ios, android')
        ->set('form.allowed_device_ids', 'device-a, device-b')
        ->set('form.offline_behavior', 'queueable')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('records');

    $flag = MobileFeatureFlag::query()->firstWhere('key', 'records');

    expect($flag)->not->toBeNull()
        ->and($flag?->default_state)->toBe(MobileFeatureState::Visible)
        ->and($flag?->required_plans)->toBe(['foundation', 'enterprise'])
        ->and($flag?->device_constraints)->toBe([
            'platforms' => ['ios', 'android'],
            'device_ids' => ['device-a', 'device-b'],
        ])
        ->and($flag?->offline_behavior)->toBe('queueable')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_mobile_feature_flag_created')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();
});

test('platform admins can update global feature flags', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $flag = MobileFeatureFlag::factory()->create([
        'key' => 'support',
        'name' => 'Support',
        'default_state' => MobileFeatureState::Disabled,
        'reason' => 'support_pending',
    ]);

    Livewire::actingAs($admin)
        ->test(FeatureFlags::class)
        ->call('edit', $flag->id)
        ->set('form.default_state', MobileFeatureState::Beta->value)
        ->set('form.reason', 'support_pilot')
        ->set('form.message', 'Support is enabled for a pilot group.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('support_pilot');

    $flag->refresh();

    expect($flag->default_state)->toBe(MobileFeatureState::Beta)
        ->and($flag->reason)->toBe('support_pilot')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_mobile_feature_flag_updated')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();
});

test('feature flag control validates keys and states', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(FeatureFlags::class)
        ->set('form.key', 'Bad Key')
        ->set('form.name', '')
        ->set('form.default_state', 'not_a_state')
        ->call('save')
        ->assertHasErrors([
            'form.key',
            'form.name',
            'form.default_state',
        ]);
});
