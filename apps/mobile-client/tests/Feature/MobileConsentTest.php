<?php

use App\Livewire\Mobile\ConsentAcceptance;
use App\Livewire\Mobile\ConsentHistory;
use App\Livewire\Mobile\PrivacyPolicy;
use App\Livewire\Mobile\TermsOfService;
use App\Services\MobileConsent\MobileConsentService;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    config([
        'app.timezone' => 'UTC',
        'nativephp.version' => '2.3.4',
        'nativephp.version_code' => 23,
    ]);

    session()->forget(config('mobile_consent.storage.session_key'));
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('terms and privacy pages render current policy versions', function (): void {
    Livewire::test(TermsOfService::class)
        ->assertSee('Terms of Service')
        ->assertSee('2026.06.25')
        ->assertSee('Account access')
        ->assertSee('Accept consent');

    Livewire::test(PrivacyPolicy::class)
        ->assertSee('Privacy Policy')
        ->assertSee('2026.06.25')
        ->assertSee('Server sync')
        ->assertSee('Accept consent');
});

test('consent acceptance requires both current policies', function (): void {
    Livewire::test(ConsentAcceptance::class)
        ->call('acceptConsents')
        ->assertHasErrors([
            'termsAccepted' => 'accepted',
            'privacyAccepted' => 'accepted',
        ]);
});

test('consent acceptance stores accepted versions locally with sync fields', function (): void {
    $component = Livewire::withHeaders(['User-Agent' => 'Mozilla/5.0 (Android 14; Mobile)'])
        ->test(ConsentAcceptance::class)
        ->set('termsAccepted', true)
        ->set('privacyAccepted', true)
        ->call('acceptConsents')
        ->assertHasNoErrors()
        ->assertSet('status', 'Consent accepted locally. Ready to sync when the server API is connected.')
        ->assertSet('toastVariant', 'success')
        ->assertSee('POST /api/mobile/consents');

    expect($component->instance()->canSubmit())->toBeTrue();

    $records = session()->get(config('mobile_consent.storage.session_key'));

    expect($records)->toHaveKeys(['terms', 'privacy'])
        ->and($records['terms'])->toMatchArray([
            'policy_key' => 'terms',
            'version' => '2026.06.25',
            'accepted_at' => '2026-06-25T12:00:00+00:00',
            'locale' => 'en',
            'app_version' => '2.3.4',
            'app_version_code' => '23',
            'device_label' => 'Mobile app session',
            'sync_status' => 'pending_server_sync',
            'sync_endpoint' => 'POST /api/mobile/consents',
        ])
        ->and($records['privacy']['policy_key'])->toBe('privacy');
});

test('consent history shows empty and accepted states', function (): void {
    Livewire::test(ConsentHistory::class)
        ->assertSee('No consent history')
        ->assertSee('Accept consent');

    app(MobileConsentService::class)->acceptLatest();

    Livewire::test(ConsentHistory::class)
        ->assertSee('Terms of Service')
        ->assertSee('Privacy Policy')
        ->assertSee('Jun 25, 2026 12:00 PM')
        ->assertSee('pending_server_sync')
        ->assertSee('POST /api/mobile/consents')
        ->assertSee('2.3.4 (23)');
});

test('consent service reports current acceptance versions', function (): void {
    $service = app(MobileConsentService::class);

    expect($service->hasAcceptedCurrentVersions())->toBeFalse();

    $service->acceptLatest();

    expect($service->hasAcceptedCurrentVersions())->toBeTrue()
        ->and($service->syncPayload()['records'])->toHaveCount(2);
});
