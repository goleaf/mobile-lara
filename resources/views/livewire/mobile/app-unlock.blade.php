<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col justify-center gap-6 py-6">
    <x-mobile.page-header
        title="Unlock app"
        description="Enter your local PIN or use biometrics before protected content opens."
        back-href="{{ route('mobile.welcome') }}"
    />

    <x-mobile.loading-state target="requestUnlock, unlockWithPin" message="Checking unlock details..." />

    <x-mobile.card title="Protected content" description="One successful local unlock method opens protected screens for this session.">
        <div class="grid place-items-center gap-5 text-center">
            <div class="grid size-20 place-items-center rounded-full border border-app-line bg-app-bg text-3xl font-semibold text-app-ink dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100">
                PIN
            </div>

            <div class="grid gap-2">
                @if ($error)
                    <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-200">
                        {{ $error }}
                    </p>
                @elseif ($status)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                        {{ $status }}
                    </p>
                @elseif (! $biometricEnabled && ! $pinEnabled)
                    <p class="text-sm leading-5 text-app-muted dark:text-zinc-400">
                        No local unlock method is currently enabled. Create a PIN or turn on biometrics in settings.
                    </p>
                @else
                    <p class="text-sm leading-5 text-app-muted dark:text-zinc-400">
                        Protected screens are locked until PIN or biometric confirmation succeeds.
                    </p>
                @endif
            </div>
        </div>

        <x-slot:footer>
            <div class="grid gap-3">
                @if ($pinEnabled)
                    <form wire:submit="unlockWithPin" class="grid gap-3">
                        <x-mobile.input
                            name="pin"
                            label="Local PIN"
                            type="password"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            maxlength="6"
                            wire:model="pin"
                            :disabled="$pinLocked"
                        />

                        @if ($pinLocked)
                            <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-200">
                                Try again in {{ $pinLockoutSeconds }} seconds.
                            </p>
                        @endif

                        <x-mobile.submit-button target="unlockWithPin" loading-label="Unlocking..." :disabled="$pinLocked">
                            Unlock with PIN
                        </x-mobile.submit-button>
                    </form>
                @endif

                @if ($biometricEnabled)
                    <x-mobile.button wire:click="requestUnlock" wire:loading.attr="disabled" wire:target="requestUnlock" full>
                        <span wire:loading.remove wire:target="requestUnlock">Use biometrics</span>
                        <span wire:loading wire:target="requestUnlock">Opening prompt...</span>
                    </x-mobile.button>
                @endif

                @if (! $pinEnabled && ! $biometricEnabled)
                    <a href="{{ route('mobile.pin.create') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white">
                        Create PIN
                    </a>
                @endif

                <a href="{{ route('mobile.login') }}" wire:navigate class="text-center text-sm font-semibold text-app-muted transition hover:text-app-ink dark:text-zinc-400 dark:hover:text-zinc-100">
                    Sign in another way
                </a>
            </div>
        </x-slot:footer>
    </x-mobile.card>
</section>
