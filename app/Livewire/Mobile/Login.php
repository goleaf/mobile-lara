<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Login')]
class Login extends Component
{
    public function render(): View
    {
        return view('livewire.mobile.login');
    }
}
