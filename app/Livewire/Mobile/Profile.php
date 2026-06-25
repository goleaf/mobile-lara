<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profile')]
class Profile extends Component
{
    public function render(): View
    {
        return view('livewire.mobile.profile');
    }
}
