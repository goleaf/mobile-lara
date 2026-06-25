<?php

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Livewire\Mobile\Debug;
use App\Livewire\Mobile\NetworkStatus;
use App\Services\MobileLocal\MobileNetworkStatus as MobileNetworkStatusData;
use App\Services\MobileLocal\NativeMobileNetworkState;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Native\Mobile\Network;

test('native network status tracks connection type and metered state', function (): void {
    config(['mobile_local.network.fallback_check.enabled' => false]);

    $status = (new NativeMobileNetworkState(new MobileNetworkStatusFakeNativeNetwork((object) [
        'connected' => true,
        'type' => 'cellular',
        'isExpensive' => true,
        'isConstrained' => false,
    ])))->status();

    expect($status->isOnline)->toBeTrue()
        ->and($status->connectionType)->toBe('cellular')
        ->and($status->connectionTypeLabel())->toBe('Cellular')
        ->and($status->isMetered)->toBeTrue()
        ->and($status->meteredLabel())->toBe('Metered')
        ->and($status->isConstrained)->toBeFalse()
        ->and($status->source)->toBe('nativephp')
        ->and($status->nativeStatusAvailable)->toBeTrue();
});

test('native network status reports offline when native network is disconnected', function (): void {
    config(['mobile_local.network.fallback_check.enabled' => true]);
    Http::preventStrayRequests();

    $status = (new NativeMobileNetworkState(new MobileNetworkStatusFakeNativeNetwork((object) [
        'connected' => false,
        'type' => 'wifi',
        'isExpensive' => false,
    ])))->status();

    expect($status->isOnline)->toBeFalse()
        ->and($status->connectionTypeLabel())->toBe('Wi-Fi')
        ->and($status->meteredLabel())->toBe('Unmetered')
        ->and($status->fallbackCheckUsed)->toBeFalse();
});

test('livewire network status component renders tracked network fields', function (): void {
    $this->app->instance(MobileNetworkState::class, new MobileNetworkStatusFakeState(
        new MobileNetworkStatusData(
            isOnline: true,
            connectionType: 'cellular',
            isMetered: true,
            isConstrained: true,
            source: 'nativephp',
            nativeStatusAvailable: true,
        ),
    ));

    Livewire::test(NetworkStatus::class)
        ->assertSet('isOnline', true)
        ->assertSet('connectionTypeLabel', 'Cellular')
        ->assertSet('meteredLabel', 'Metered')
        ->assertSet('constrainedLabel', 'Low data mode')
        ->assertSee('Network status')
        ->assertSee('Online')
        ->assertSee('Connection type')
        ->assertSee('Cellular')
        ->assertSee('Metered connection')
        ->assertSee('Metered')
        ->assertSee('Low data mode')
        ->assertSee('NativePHP');
});

test('debug screen exposes network status details and component', function (): void {
    $this->app->instance(MobileNetworkState::class, new MobileNetworkStatusFakeState(
        new MobileNetworkStatusData(
            isOnline: false,
            connectionType: 'wifi',
            isMetered: false,
            isConstrained: null,
            source: 'nativephp',
            nativeStatusAvailable: true,
        ),
    ));

    Livewire::test(Debug::class)
        ->assertSee('Network status')
        ->assertSee('Offline')
        ->assertSee('Connection type')
        ->assertSee('Wi-Fi')
        ->assertSee('Metered connection')
        ->assertSee('Unmetered')
        ->assertSee('Network source')
        ->assertSee('NativePHP')
        ->assertSeeLivewire(NetworkStatus::class);
});

final class MobileNetworkStatusFakeNativeNetwork extends Network
{
    public function __construct(
        private readonly ?object $status,
    ) {}

    public function status(): ?object
    {
        return $this->status;
    }
}

final class MobileNetworkStatusFakeState implements MobileNetworkState
{
    public function __construct(
        private readonly MobileNetworkStatusData $status,
    ) {}

    public function isAvailable(): bool
    {
        return $this->status->isOnline;
    }

    public function status(): MobileNetworkStatusData
    {
        return $this->status;
    }
}
