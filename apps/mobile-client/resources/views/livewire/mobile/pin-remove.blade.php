<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Remove PIN"
        description="Confirm the current PIN before local PIN unlock is removed."
        back-href="{{ route('mobile.settings') }}"
    />

    <x-mobile.loading-state target="remove" message="Removing PIN..." />

    @if (! $hasPin)
        <x-mobile.empty-state title="No local PIN" description="There is no local PIN saved on this device.">
            <x-slot:action>
                <a href="{{ route('mobile.pin.create') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90   ">
                    Create PIN
                </a>
            </x-slot:action>
        </x-mobile.empty-state>
    @else
        <form wire:submit="remove" class="grid gap-5">
            <x-mobile.card title="Remove local PIN" description="Biometric unlock remains separate and can stay enabled.">
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

                    @if ($lockoutSeconds > 0 && $remainingAttempts === 0)
                        <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800   ">
                            Try again in {{ $lockoutSeconds }} seconds.
                        </p>
                    @endif
                </div>

                <x-slot:footer>
                    <x-mobile.submit-button target="remove" variant="danger" loading-label="Removing PIN...">
                        Remove PIN
                    </x-mobile.submit-button>
                </x-slot:footer>
            </x-mobile.card>
        </form>
    @endif
</section>
