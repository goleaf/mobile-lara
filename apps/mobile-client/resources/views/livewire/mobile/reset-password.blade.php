<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Reset password"
        description="Use your reset token and choose a new password."
        back-href="{{ route('mobile.password.request') }}"
    />

    <form wire:submit="resetPassword" class="grid gap-5">
        <x-mobile.loading-state target="token, email, password, password_confirmation" message="Checking reset details..." />

        <x-mobile.card title="New password" description="Passwords are checked with Laravel's configured password rules.">
            <div class="grid gap-4">
                <x-mobile.input
                    name="token"
                    label="Reset token"
                    autocomplete="one-time-code"
                    wire:model.live.blur="token"
                />

                <x-mobile.input
                    name="email"
                    label="Email"
                    type="email"
                    autocomplete="email"
                    inputmode="email"
                    wire:model.live.blur="email"
                />

                <x-mobile.input
                    name="password"
                    label="New password"
                    type="password"
                    autocomplete="new-password"
                    hint="Use at least 8 characters."
                    wire:model.live.blur="password"
                />

                <x-mobile.input
                    name="password_confirmation"
                    label="Confirm new password"
                    type="password"
                    autocomplete="new-password"
                    wire:model.live.blur="password_confirmation"
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
                <x-mobile.submit-button target="resetPassword" loading-label="Validating password..." :disabled="! $this->canSubmit">
                    Reset password
                </x-mobile.submit-button>
            </x-slot:footer>
        </x-mobile.card>
    </form>

    <a href="{{ route('mobile.login') }}" wire:navigate class="text-center text-sm font-semibold text-app-accent ">
        Back to login
    </a>
</section>
