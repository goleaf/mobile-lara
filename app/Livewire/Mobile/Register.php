<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Register')]
class Register extends Component
{
    public function render(): View
    {
        return view('livewire.mobile.register');
    }
}
