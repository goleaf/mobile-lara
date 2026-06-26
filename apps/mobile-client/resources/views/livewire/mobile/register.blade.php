<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Create account"
        description="Set up your mobile profile in a few fields."
        back-href="{{ route('mobile.login') }}"
    />

    <form wire:submit="register" class="grid gap-5">
        <x-mobile.loading-state target="name, email, password, password_confirmation, termsAccepted" message="Checking account details..." />

        <x-mobile.card>
            <div class="grid gap-4">
                <x-mobile.input
                    name="name"
                    label="Name"
                    autocomplete="name"
                    wire:model.live.blur="name"
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
                    label="Password"
                    type="password"
                    autocomplete="new-password"
                    wire:model.live.blur="password"
                />

                <x-mobile.input
                    name="password_confirmation"
                    label="Confirm password"
                    type="password"
                    autocomplete="new-password"
                    wire:model.live.blur="password_confirmation"
                />

                <label class="grid gap-2 rounded-lg border border-app-line bg-app-bg p-4  ">
                    <span class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            wire:model.live="termsAccepted"
                            class="mt-0.5 size-5 rounded border-app-line text-app-accent focus:ring-app-accent  "
                        >
                        <span class="text-sm font-medium leading-5 text-app-ink ">
                            I agree to continue with this mobile app account.
                        </span>
                    </span>

                    @error('termsAccepted')
                        <span class="text-sm font-medium text-red-600 ">{{ $message }}</span>
                    @enderror
                </label>

                <div aria-live="polite" class="min-h-6">
                    @if ($status)
                        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800   ">
                            {{ $status }}
                        </p>
                    @endif
                </div>
            </div>

            <x-slot:footer>
                <x-mobile.submit-button target="register" loading-label="Creating account..." :disabled="! $this->canSubmit">
                    Create account
                </x-mobile.submit-button>
            </x-slot:footer>
        </x-mobile.card>
    </form>

    <a href="{{ route('mobile.login') }}" wire:navigate class="text-center text-sm font-semibold text-app-accent ">
        Back to login
    </a>
</section>
