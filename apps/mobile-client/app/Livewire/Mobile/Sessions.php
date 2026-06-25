<?php

namespace App\Livewire\Mobile;

use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\MobileAuthApiService;
use App\Services\MobileAuth\MobileSessionService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sessions')]
class Sessions extends Component
{
    public bool $hasNetworkError = false;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    protected MobileSessionService $mobileSessions;

    protected MobileAuthApiService $authApi;

    public function boot(MobileSessionService $mobileSessions, MobileAuthApiService $authApi): void
    {
        $this->mobileSessions = $mobileSessions;
        $this->authApi = $authApi;
    }

    public function logout(): void
    {
        try {
            $this->authApi->logout();
        } catch (MobileApiException) {
        }

        $this->finishLocalLogout();
    }

    public function logoutAllDevices(): void
    {
        try {
            $this->authApi->logoutAllDevices();
        } catch (MobileApiException) {
        }

        $this->finishLocalLogout();
    }

    public function retryRemoteSessions(): void
    {
        $this->hasNetworkError = false;
        $this->toastMessage = 'Remote sessions API placeholder refreshed.';
        $this->toastVariant = 'success';
    }

    public function render(): View
    {
        return view('livewire.mobile.sessions', [
            'currentSession' => $this->mobileSessions->currentDeviceSession(),
            'remoteSessions' => $this->mobileSessions->remoteDeviceSessions(),
        ]);
    }

    private function finishLocalLogout(): void
    {
        $this->mobileSessions->logoutCurrentSession();

        $this->redirect(route('mobile.login'), true);
    }
}
