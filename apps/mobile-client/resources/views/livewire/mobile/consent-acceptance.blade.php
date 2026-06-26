<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Consent"
        description="Accept the current legal versions locally."
        back-href="{{ route('mobile.welcome') }}"
    >
        <x-slot:action>
            <x-mobile.badge :variant="$hasAcceptedCurrentVersions ? 'success' : 'warning'" dot>
                {{ $hasAcceptedCurrentVersions ? 'Current' : 'Required' }}
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    <x-mobile.loading-state target="acceptConsents" message="Saving consent..." />

    <form wire:submit="acceptConsents" class="grid gap-5">
        <x-mobile.card title="Current versions" description="Review and accept the active policy versions.">
            <div class="grid gap-3">
                @forelse ($policies as $policy)
                    <article wire:key="consent-policy-{{ $policy['key'] }}" class="rounded-lg border border-app-line bg-app-bg p-4  ">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-base font-semibold text-app-ink ">{{ $policy['title'] }}</p>
                                <p class="mt-1 text-sm leading-5 text-app-muted ">
                                    Version {{ $policy['version'] }} · effective {{ $policy['effective_date'] }}
                                </p>
                            </div>

                            <a
                                href="{{ $policy['key'] === 'terms' ? route('mobile.terms') : route('mobile.privacy') }}"
                                wire:navigate
                                class="shrink-0 text-sm font-semibold text-app-accent "
                            >
                                Review
                            </a>
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state title="No policies configured" description="Consent can be accepted after policy configuration is available." />
                @endforelse
            </div>

            <x-slot:footer>
                <div class="grid gap-3">
                    <label class="flex items-start gap-3 rounded-lg border border-app-line bg-app-bg p-4  ">
                        <input
                            type="checkbox"
                            wire:model.live="termsAccepted"
                            class="mt-1 size-5 rounded border-app-line text-app-accent focus:ring-app-accent  "
                        >
                        <span class="text-sm leading-5 text-app-ink ">
                            I accept the current Terms of Service version.
                        </span>
                    </label>

                    @error('termsAccepted')
                        <p class="text-sm font-medium text-red-600 ">{{ $message }}</p>
                    @enderror

                    <label class="flex items-start gap-3 rounded-lg border border-app-line bg-app-bg p-4  ">
                        <input
                            type="checkbox"
                            wire:model.live="privacyAccepted"
                            class="mt-1 size-5 rounded border-app-line text-app-accent focus:ring-app-accent  "
                        >
                        <span class="text-sm leading-5 text-app-ink ">
                            I accept the current Privacy Policy version.
                        </span>
                    </label>

                    @error('privacyAccepted')
                        <p class="text-sm font-medium text-red-600 ">{{ $message }}</p>
                    @enderror

                    <div aria-live="polite" class="min-h-6">
                        @if ($status)
                            <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800   ">
                                {{ $status }}
                            </p>
                        @endif
                    </div>

                    <x-mobile.submit-button target="acceptConsents" loading-label="Saving consent..." :disabled="! $this->canSubmit">
                        Accept current versions
                    </x-mobile.submit-button>
                </div>
            </x-slot:footer>
        </x-mobile.card>
    </form>

    <x-mobile.card title="Server sync fields" description="Prepared payload for future consent API sync.">
        <dl class="grid gap-3 text-sm">
            <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  ">
                <dt class="text-app-muted ">Endpoint</dt>
                <dd class="text-right font-mono text-xs font-semibold text-app-ink ">{{ $syncPayload['endpoint'] }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  ">
                <dt class="text-app-muted ">Method</dt>
                <dd class="font-semibold text-app-ink ">{{ $syncPayload['method'] }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  ">
                <dt class="text-app-muted ">Local records</dt>
                <dd class="font-semibold text-app-ink ">{{ count($syncPayload['records']) }}</dd>
            </div>
        </dl>

        <x-slot:footer>
            <a href="{{ route('mobile.consent.history') }}" wire:navigate class="inline-flex min-h-12 w-full items-center justify-center rounded-lg border border-app-line bg-app-surface px-4 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg    ">
                View consent history
            </a>
        </x-slot:footer>
    </x-mobile.card>
</section>
