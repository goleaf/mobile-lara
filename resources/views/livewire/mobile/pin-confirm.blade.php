<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Confirm PIN"
        description="Enter the same PIN again to save it securely."
        back-href="{{ route('mobile.pin.create') }}"
    />

    <x-mobile.loading-state target="confirm" message="Saving PIN securely..." />

    @if (! $hasPendingSetup)
        <x-mobile.empty-state title="No PIN setup pending" description="Start by creating a new local PIN.">
            <x-slot:action>
                <a href="{{ route('mobile.pin.create') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white">
                    Create PIN
                </a>
            </x-slot:action>
        </x-mobile.empty-state>
    @else
        <form wire:submit="confirm" class="grid gap-5">
            <x-mobile.card title="Confirm PIN" description="The PIN hash is only saved after this confirmation matches.">
                <div class="grid gap-4">
                    <x-mobile.input
                        name="pin"
                        label="Confirm PIN"
                        type="password"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        wire:model="pin"
                    />

                    @if ($error)
                        <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-200">
                            {{ $error }}
                        </p>
                    @endif
                </div>

                <x-slot:footer>
                    <x-mobile.submit-button target="confirm" loading-label="Saving PIN...">
                        Save PIN
                    </x-mobile.submit-button>
                </x-slot:footer>
            </x-mobile.card>
        </form>
    @endif
</section>
