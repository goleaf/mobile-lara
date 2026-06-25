<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create')]
class Create extends Component
{
    public function render(): View
    {
        return view('livewire.mobile.create');
    }
}
