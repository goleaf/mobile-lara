<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Forgot password"
        description="Enter your email to prepare password reset instructions."
        back-href="{{ route('mobile.login') }}"
    />

    <form wire:submit="sendResetLink" class="grid gap-5">
        <x-mobile.loading-state target="email" message="Checking email..." />

        <x-mobile.card title="Reset link" description="We will validate the email before sending instructions.">
            <div class="grid gap-4">
                <x-mobile.input
                    name="email"
                    label="Email"
                    type="email"
                    autocomplete="email"
                    inputmode="email"
                    wire:model.live.blur="email"
                />

                <div aria-live="polite" class="min-h-6">
                    @if ($status)
                        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800   ">
                            {{ $status }}
                        </p>
                    @endif
                </div>
            </div>

            <x-slot:footer>
                <x-mobile.submit-button target="sendResetLink" loading-label="Checking email..." :disabled="! $this->canSubmit">
                    Continue
                </x-mobile.submit-button>
            </x-slot:footer>
        </x-mobile.card>
    </form>

    <div class="grid gap-3 text-center text-sm">
        <a href="{{ route('mobile.password.reset') }}" wire:navigate class="font-semibold text-app-accent ">
            I have a reset token
        </a>
        <a href="{{ route('mobile.login') }}" wire:navigate class="font-semibold text-app-muted ">
            Back to login
        </a>
    </div>
</section>
