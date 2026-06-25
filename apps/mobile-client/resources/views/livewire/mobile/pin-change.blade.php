<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Change PIN"
        description="Verify your current PIN before saving a new one."
        back-href="{{ route('mobile.settings') }}"
    />

    <x-mobile.loading-state target="change" message="Changing PIN..." />

    @if (! $hasPin)
        <x-mobile.empty-state title="No local PIN" description="Create a local PIN before changing it.">
            <x-slot:action>
                <a href="{{ route('mobile.pin.create') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white">
                    Create PIN
                </a>
            </x-slot:action>
        </x-mobile.empty-state>
    @else
        <form wire:submit="change" class="grid gap-5">
            <x-mobile.card title="PIN details" description="Failed current PIN checks count toward local lockout.">
                <div class="grid gap-4">
                    <x-mobile.input
                        name="currentPin"
                        label="Current PIN"
                        type="password"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        wire:model="currentPin"
                    />

                    <x-mobile.input
                        name="pin"
                        label="New PIN"
                        type="password"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        wire:model="pin"
                    />

                    <x-mobile.input
                        name="pin_confirmation"
                        label="Confirm new PIN"
                        type="password"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        wire:model="pin_confirmation"
                    />

                    @if ($lockoutSeconds > 0 && $remainingAttempts === 0)
                        <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-200">
                            Try again in {{ $lockoutSeconds }} seconds.
                        </p>
                    @endif
                </div>

                <x-slot:footer>
                    <x-mobile.submit-button target="change" loading-label="Changing PIN...">
                        Change PIN
                    </x-mobile.submit-button>
                </x-slot:footer>
            </x-mobile.card>
        </form>
    @endif
</section>
