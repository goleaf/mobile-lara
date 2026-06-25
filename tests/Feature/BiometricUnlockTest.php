<?php

use App\Livewire\Mobile\AppUnlock;
use App\Livewire\Mobile\Dashboard;
use App\Livewire\Mobile\Settings;
use App\Services\MobileAuth\BiometricUnlockService;
use Livewire\Livewire;
use Native\Mobile\Biometrics;
use Native\Mobile\PendingBiometric;
use Native\Mobile\SecureStorage;

beforeEach(function (): void {
    $this->startSession();

    config([
        'mobile_auth.storage.secure_key_prefix' => 'testing_mobile_auth',
    ]);

    $this->secureStorage = new class extends SecureStorage
    {
        /** @var array<string, string> */
        public array $values = [];

        public function set(string $key, ?string $value): bool
        {
            if (is_null($value)) {
                unset($this->values[$key]);

                return true;
            }

            $this->values[$key] = $value;

            return true;
        }

        public function get(string $key): ?string
        {
            return $this->values[$key] ?? null;
        }

        public function delete(string $key): bool
        {
            unset($this->values[$key]);

            return true;
        }
    };

    $this->biometrics = new class extends Biometrics
    {
        public ?string $lastPromptId = null;

        public bool $shouldStartPrompt = true;

        public function prompt(): PendingBiometric
        {
            return new class($this) extends PendingBiometric
            {
                public function __construct(private readonly Biometrics $biometrics)
                {
                    parent::__construct();
                }

                public function prompt(): bool
                {
                    if ($this->started) {
                        return false;
                    }

                    $this->started = true;
                    $this->biometrics->lastPromptId = $this->getId();

                    return $this->biometrics->shouldStartPrompt;
                }
            };
        }
    };

    $this->app->instance(SecureStorage::class, $this->secureStorage);
    $this->app->instance(Biometrics::class, $this->biometrics);
});

test('settings can enable and disable biometric unlock in secure storage', function (): void {
    Livewire::test(Settings::class)
        ->assertSet('biometricUnlock', false)
        ->set('biometricUnlock', true)
        ->call('saveSettings')
        ->assertSet('settingsStatus', 'Biometric unlock enabled.')
        ->assertSet('toastVariant', 'success');

    expect($this->secureStorage->values['testing_mobile_auth.biometric_unlock_enabled'])->toBe('1');

    Livewire::test(Settings::class)
        ->assertSet('biometricUnlock', true)
        ->set('biometricUnlock', false)
        ->call('saveSettings')
        ->assertSet('settingsStatus', 'Biometric unlock disabled.');

    expect($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.biometric_unlock_enabled');
});

test('protected mobile routes redirect to unlock when biometric unlock is enabled and locked', function (): void {
    $biometricUnlocks = app(BiometricUnlockService::class);

    $biometricUnlocks->setEnabled(true);
    $biometricUnlocks->lock();

    $this->withoutVite()
        ->get(route('mobile.dashboard'))
        ->assertRedirect(route('mobile.unlock'));

    expect(session(BiometricUnlockService::INTENDED_URL_SESSION_KEY))->toBe(route('mobile.dashboard'));

    $biometricUnlocks->unlock();

    $this->withoutVite()
        ->get(route('mobile.dashboard'))
        ->assertOk()
        ->assertSeeLivewire(Dashboard::class);
});

test('unlock screen starts a native biometric prompt and unlocks matching completions', function (): void {
    $biometricUnlocks = app(BiometricUnlockService::class);

    $biometricUnlocks->setEnabled(true);
    $biometricUnlocks->lock();
    session()->put(BiometricUnlockService::INTENDED_URL_SESSION_KEY, route('mobile.profile'));

    Livewire::test(AppUnlock::class)
        ->call('requestUnlock')
        ->assertSet('status', 'Check your device to continue.')
        ->call('handleBiometricCompleted', true, $this->biometrics->lastPromptId)
        ->assertRedirect(route('mobile.profile'));

    expect($biometricUnlocks->isUnlocked())->toBeTrue();
});

test('unlock screen rejects failed or mismatched biometric completions', function (): void {
    $biometricUnlocks = app(BiometricUnlockService::class);

    $biometricUnlocks->setEnabled(true);
    $biometricUnlocks->lock();

    Livewire::test(AppUnlock::class)
        ->call('requestUnlock')
        ->call('handleBiometricCompleted', true, 'wrong-prompt-id')
        ->assertSet('error', 'Biometric confirmation failed. Try again.')
        ->assertSet('toastVariant', 'error');

    expect($biometricUnlocks->isUnlocked())->toBeFalse();
});
