<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Create PIN"
        description="Create a local unlock code for this device."
        back-href="{{ route('mobile.settings') }}"
    />

    <x-mobile.loading-state target="create" message="Preparing PIN confirmation..." />

    <form wire:submit="create" class="grid gap-5">
        <x-mobile.card title="New PIN" description="Use a short numeric code that is easy for you to enter.">
            <div class="grid gap-4">
                <x-mobile.input
                    name="pin"
                    label="PIN"
                    type="password"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    wire:model="pin"
                />

                @if ($error)
                    <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800   ">
                        {{ $error }}
                    </p>
                @endif
            </div>

            <x-slot:footer>
                <x-mobile.submit-button target="create" loading-label="Continuing...">
                    Continue
                </x-mobile.submit-button>
            </x-slot:footer>
        </x-mobile.card>
    </form>
</section>
