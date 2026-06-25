<?php

use App\Livewire\Mobile\AccountDeletion;
use App\Models\User;
use App\Services\MobileAuth\BiometricUnlockService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Native\Mobile\Biometrics;
use Native\Mobile\PendingBiometric;
use Native\Mobile\SecureStorage;

uses(LazilyRefreshDatabase::class);

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

test('account deletion screen renders the destructive confirmation flow', function (): void {
    Livewire::test(AccountDeletion::class)
        ->assertSee('Delete account')
        ->assertSee('Permanent deletion')
        ->assertSee('Password confirmation')
        ->assertSee('Biometric confirmation')
        ->assertSee('I understand this account deletion is permanent')
        ->assertSee('Delete account');
});

test('account deletion requires explicit confirmation and password by default', function (): void {
    Livewire::test(AccountDeletion::class)
        ->call('deleteAccount')
        ->assertHasErrors([
            'confirmationAccepted' => 'accepted',
            'password' => 'required',
        ]);
});

test('account deletion rejects incorrect password confirmation', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password'),
    ]);

    Livewire::actingAs($user)
        ->test(AccountDeletion::class)
        ->set('password', 'wrong-password')
        ->set('confirmationAccepted', true)
        ->call('deleteAccount')
        ->assertHasErrors('password')
        ->assertSet('error', 'The provided password does not match this account.')
        ->assertSet('toastVariant', 'error');
});

test('account deletion can be requested after password confirmation', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password'),
    ]);

    $component = Livewire::actingAs($user)
        ->test(AccountDeletion::class)
        ->set('password', 'correct-password')
        ->set('confirmationAccepted', true)
        ->call('deleteAccount')
        ->assertHasNoErrors()
        ->assertSet('status', 'Account deletion API placeholder reached. No account has been deleted yet.')
        ->assertSet('toastVariant', 'success');

    expect($component->instance()->deletionRequest)->toMatchArray([
        'status' => 'placeholder',
        'server_endpoint' => 'DELETE /api/mobile/account',
        'confirmed_by' => 'password',
        'user_id' => (string) $user->getKey(),
    ]);
});

test('account deletion can be confirmed with biometrics before requesting deletion', function (): void {
    app(BiometricUnlockService::class)->setEnabled(true);

    $component = Livewire::test(AccountDeletion::class)
        ->set('confirmationAccepted', true)
        ->call('confirmWithBiometric')
        ->assertSet('confirmationMethod', 'biometric')
        ->assertSet('status', 'Check your device to confirm account deletion.')
        ->call('handleBiometricCompleted', true, $this->biometrics->lastPromptId)
        ->assertSet('biometricConfirmed', true)
        ->assertSet('status', 'Biometric confirmation received.')
        ->call('deleteAccount')
        ->assertHasNoErrors()
        ->assertSet('status', 'Account deletion API placeholder reached. No account has been deleted yet.');

    expect($component->instance()->deletionRequest)->toMatchArray([
        'status' => 'placeholder',
        'server_endpoint' => 'DELETE /api/mobile/account',
        'confirmed_by' => 'biometric',
        'user_id' => null,
    ]);
});

test('biometric confirmation reports when biometrics are not enabled', function (): void {
    Livewire::test(AccountDeletion::class)
        ->call('confirmWithBiometric')
        ->assertSet('confirmationMethod', 'biometric')
        ->assertSet('error', 'Biometric confirmation is not enabled on this device.')
        ->assertHasErrors('confirmationMethod')
        ->assertSet('toastVariant', 'error');
});
