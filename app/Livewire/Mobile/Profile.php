<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profile')]
class Profile extends Component
{
    public string $displayName = 'Mobile Lara';

    public string $bio = 'Local mobile account';

    public bool $hasNetworkError = false;

    public bool $hasProfile = true;

    public function saveProfile(): void
    {
        $this->hasNetworkError = false;
        $this->hasProfile = true;
    }

    public function retryProfile(): void
    {
        $this->hasNetworkError = false;
        $this->hasProfile = true;
    }

    public function render(): View
    {
        return view('livewire.mobile.profile');
    }
}
