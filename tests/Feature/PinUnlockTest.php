<?php

use App\Livewire\Mobile\AppUnlock;
use App\Livewire\Mobile\PinChange;
use App\Livewire\Mobile\PinConfirm;
use App\Livewire\Mobile\PinCreate;
use App\Livewire\Mobile\PinRemove;
use App\Services\MobileAuth\AppUnlockStateService;
use App\Services\MobileAuth\PinUnlockService;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Native\Mobile\SecureStorage;

beforeEach(function (): void {
    $this->startSession();

    config([
        'cache.default' => 'array',
        'mobile_auth.storage.secure_key_prefix' => 'testing_mobile_auth',
        'mobile_auth.pin.max_attempts' => 5,
        'mobile_auth.pin.lockout_seconds' => 300,
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

    $this->app->instance(SecureStorage::class, $this->secureStorage);
});

test('pin creation validates and stores only a secure hash after confirmation', function (): void {
    Livewire::test(PinCreate::class)
        ->set('pin', '12ab')
        ->call('create')
        ->assertHasErrors(['pin' => 'regex']);

    Livewire::test(PinCreate::class)
        ->set('pin', '1234')
        ->call('create')
        ->assertRedirect(route('mobile.pin.confirm'));

    expect($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.pin_hash');

    Livewire::test(PinConfirm::class)
        ->assertSet('hasPendingSetup', true)
        ->set('pin', '1234')
        ->call('confirm')
        ->assertRedirect(route('mobile.settings'));

    $hash = $this->secureStorage->values['testing_mobile_auth.pin_hash'] ?? null;

    expect($hash)->toBeString()
        ->and($hash)->not->toBe('1234')
        ->and(Hash::check('1234', $hash))->toBeTrue();
});

test('pin confirmation must match before anything is saved', function (): void {
    Livewire::test(PinCreate::class)
        ->set('pin', '1234')
        ->call('create');

    Livewire::test(PinConfirm::class)
        ->set('pin', '5678')
        ->call('confirm')
        ->assertHasErrors('pin')
        ->assertSet('toastVariant', 'error');

    expect($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.pin_hash');
});

test('protected routes redirect to unlock and accept the correct pin', function (): void {
    $pinUnlocks = app(PinUnlockService::class);
    $unlockState = app(AppUnlockStateService::class);

    $pinUnlocks->startCreation('1234');
    expect($pinUnlocks->confirmCreation('1234'))->toBeTrue();
    $unlockState->lock();

    $this->withoutVite()
        ->get(route('mobile.dashboard'))
        ->assertRedirect(route('mobile.unlock'));

    expect(session(AppUnlockStateService::INTENDED_URL_SESSION_KEY))->toBe(route('mobile.dashboard'));

    Livewire::test(AppUnlock::class)
        ->set('pin', '1234')
        ->call('unlockWithPin')
        ->assertRedirect(route('mobile.dashboard'));

    expect($unlockState->isUnlocked())->toBeTrue();
});

test('pin unlock locks out after repeated failed attempts', function (): void {
    $pinUnlocks = app(PinUnlockService::class);
    $unlockState = app(AppUnlockStateService::class);

    $pinUnlocks->startCreation('1234');
    $pinUnlocks->confirmCreation('1234');
    $unlockState->lock();

    $component = Livewire::test(AppUnlock::class);

    foreach (range(1, 5) as $attempt) {
        $component
            ->set('pin', '9999')
            ->call('unlockWithPin')
            ->assertHasErrors('pin')
            ->assertSet('toastVariant', 'error');
    }

    expect($pinUnlocks->isLockedOut())->toBeTrue()
        ->and($unlockState->isUnlocked())->toBeFalse();

    Livewire::test(AppUnlock::class)
        ->set('pin', '1234')
        ->call('unlockWithPin')
        ->assertHasErrors('pin')
        ->assertSet('toastVariant', 'error');

    expect($unlockState->isUnlocked())->toBeFalse();
});

test('pin can be changed and removed after current pin verification', function (): void {
    $pinUnlocks = app(PinUnlockService::class);

    $pinUnlocks->startCreation('1234');
    $pinUnlocks->confirmCreation('1234');

    Livewire::test(PinChange::class)
        ->assertSet('hasPin', true)
        ->set('currentPin', '1234')
        ->set('pin', '2468')
        ->set('pin_confirmation', '2468')
        ->call('change')
        ->assertRedirect(route('mobile.settings'));

    $changedHash = $this->secureStorage->values['testing_mobile_auth.pin_hash'] ?? null;

    expect(Hash::check('2468', $changedHash))->toBeTrue()
        ->and(Hash::check('1234', $changedHash))->toBeFalse();

    Livewire::test(PinRemove::class)
        ->assertSet('hasPin', true)
        ->set('currentPin', '2468')
        ->call('remove')
        ->assertRedirect(route('mobile.settings'));

    expect($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.pin_hash');
});
