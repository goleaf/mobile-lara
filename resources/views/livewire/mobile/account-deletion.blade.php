<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Delete account"
        description="Confirm this destructive account action."
        back-href="{{ route('mobile.settings') }}"
    />

    <x-mobile.loading-state target="deleteAccount, confirmWithBiometric" message="Confirming account deletion..." />

    <x-mobile.card title="Permanent deletion" description="This action is destructive.">
        <div class="grid gap-4">
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-400/30 dark:bg-red-400/10">
                <p class="text-base font-semibold text-red-900 dark:text-red-100">
                    Deleting your account will permanently remove server account access when the deletion API is connected.
                </p>
                <p class="mt-2 text-sm leading-6 text-red-800 dark:text-red-200/80">
                    Local tokens, remote sessions, profile data, saved preferences, and recovery access should be treated as unavailable after deletion. This placeholder does not delete the account yet.
                </p>
            </div>

            <fieldset class="grid gap-3">
                <legend class="text-sm font-semibold text-app-ink dark:text-zinc-100">Confirmation method</legend>

                <label class="flex items-start gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                    <input
                        type="radio"
                        value="password"
                        wire:model.live="confirmationMethod"
                        class="mt-1 size-4 border-app-line text-app-accent focus:ring-app-accent dark:border-zinc-700 dark:bg-zinc-900"
                    >
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-app-ink dark:text-zinc-100">Password confirmation</span>
                        <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">Use the password for the signed-in server account.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                    <input
                        type="radio"
                        value="biometric"
                        wire:model.live="confirmationMethod"
                        class="mt-1 size-4 border-app-line text-app-accent focus:ring-app-accent dark:border-zinc-700 dark:bg-zinc-900"
                    >
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-app-ink dark:text-zinc-100">Biometric confirmation</span>
                        <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">Use NativePHP biometrics on this device.</span>
                    </span>
                </label>
            </fieldset>

            @if ($confirmationMethod === 'password')
                <x-mobile.input
                    name="password"
                    label="Current password"
                    type="password"
                    autocomplete="current-password"
                    wire:model.live.blur="password"
                />
            @else
                <div class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                    <div class="flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-app-ink dark:text-zinc-100">Native biometric check</p>
                            <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                                {{ $biometricConfirmed ? 'Biometric confirmation is ready for the final delete action.' : 'Start biometric confirmation before deleting the account.' }}
                            </p>
                        </div>

                        <x-mobile.badge :variant="$biometricConfirmed ? 'success' : 'neutral'" dot>
                            {{ $biometricConfirmed ? 'Confirmed' : 'Required' }}
                        </x-mobile.badge>
                    </div>

                    <x-mobile.button variant="secondary" full wire:click="confirmWithBiometric">
                        Confirm with biometrics
                    </x-mobile.button>

                    @error('confirmationMethod')
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <label class="flex items-start gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                <input
                    type="checkbox"
                    wire:model.live="confirmationAccepted"
                    class="mt-1 size-5 rounded border-app-line text-red-600 focus:ring-red-500 dark:border-zinc-700 dark:bg-zinc-900"
                >
                <span class="text-sm leading-5 text-app-ink dark:text-zinc-100">
                    I understand this account deletion is permanent once the server deletion API is connected.
                </span>
            </label>

            @error('confirmationAccepted')
                <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <div aria-live="polite" class="min-h-6">
                @if ($error)
                    <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-200">
                        {{ $error }}
                    </p>
                @elseif ($status)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                        {{ $status }}
                    </p>
                @endif
            </div>

            @if ($deletionRequest !== [])
                <dl class="grid gap-2 rounded-lg border border-dashed border-app-line bg-app-bg p-4 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-app-muted dark:text-zinc-400">Endpoint</dt>
                        <dd class="text-right font-mono text-xs font-semibold text-app-ink dark:text-zinc-100">{{ $deletionRequest['server_endpoint'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-app-muted dark:text-zinc-400">Confirmed by</dt>
                        <dd class="text-right font-semibold text-app-ink dark:text-zinc-100">{{ $deletionRequest['confirmed_by'] }}</dd>
                    </div>
                </dl>
            @endif
        </div>

        <x-slot:footer>
            <form wire:submit="deleteAccount">
                <x-mobile.submit-button variant="danger" target="deleteAccount" loading-label="Deleting account..." :disabled="! $this->canSubmit">
                    Delete account
                </x-mobile.submit-button>
            </form>
        </x-slot:footer>
    </x-mobile.card>
</section>
