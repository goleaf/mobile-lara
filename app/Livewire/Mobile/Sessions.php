<?php

namespace App\Livewire\Mobile;

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

    public function boot(MobileSessionService $mobileSessions): void
    {
        $this->mobileSessions = $mobileSessions;
    }

    public function logout(): void
    {
        $this->mobileSessions->logoutCurrentSession();

        $this->redirect(route('mobile.login'), true);
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
}
