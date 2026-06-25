<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Verify email')]
class EmailVerification extends Component
{
    public string $email = '';

    public ?string $status = null;

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    public function sendVerification(): void
    {
        $this->status = null;

        $this->validate();

        $this->status = 'Verification email details validated.';
    }

    public function render(): View
    {
        return view('livewire.mobile.email-verification');
    }
}
