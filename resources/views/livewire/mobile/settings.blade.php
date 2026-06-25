<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="saveSettings, retrySettings" message="Saving settings..." />

    <x-mobile.page-skeleton wire:loading.delay wire:target="saveSettings, retrySettings" :cards="3" />

    <div wire:loading.remove wire:target="saveSettings, retrySettings" class="contents">
        @if ($hasNetworkError)
            <x-mobile.network-error-state retry-action="retrySettings" />
        @elseif (count($settings) === 0)
            <x-mobile.empty-state title="No settings available" description="Settings will appear here once mobile preferences are connected.">
                <x-slot:action>
                    <x-mobile.retry-button wire:click="retrySettings" target="retrySettings">
                        Reload settings
                    </x-mobile.retry-button>
                </x-slot:action>
            </x-mobile.empty-state>
        @else
            <div class="grid gap-5">
                <form wire:submit="saveSettings" class="grid gap-5">
                    <x-mobile.card title="Settings" description="Mobile configuration surface.">
                        <div class="grid gap-3">
                            @forelse ($settings as $setting)
                                <label wire:key="setting-{{ $setting['property'] }}" class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                                    <span class="min-w-0">
                                        <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $setting['label'] }}</span>
                                        <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $setting['description'] }}</span>
                                    </span>
                                    <input
                                        type="checkbox"
                                        wire:model="{{ $setting['property'] }}"
                                        class="size-5 rounded border-app-line text-app-accent focus:ring-app-accent dark:border-zinc-700 dark:bg-zinc-900"
                                    >
                                </label>
                            @empty
                                <p class="text-sm text-app-muted dark:text-zinc-400">No settings available.</p>
                            @endforelse
                        </div>

                        <x-slot:footer>
                            <div class="grid gap-3">
                                <div aria-live="polite" class="min-h-6">
                                    @if ($settingsError)
                                        <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-200">
                                            {{ $settingsError }}
                                        </p>
                                    @elseif ($settingsStatus)
                                        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                                            {{ $settingsStatus }}
                                        </p>
                                    @endif
                                </div>

                                <x-mobile.submit-button target="saveSettings" loading-label="Saving settings...">
                                    Save settings
                                </x-mobile.submit-button>
                            </div>
                        </x-slot:footer>
                    </x-mobile.card>
                </form>

                <x-mobile.card title="Local PIN" description="Manage the numeric unlock code stored securely on this device.">
                    <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                        <div class="min-w-0">
                            <p class="text-base font-semibold text-app-ink dark:text-zinc-100">
                                {{ $hasPinUnlock ? 'PIN unlock is on' : 'PIN unlock is off' }}
                            </p>
                            <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                                {{ $hasPinUnlock ? 'Change or remove the local PIN for this device.' : 'Create a local PIN for app unlock fallback.' }}
                            </p>
                        </div>
                        <x-mobile.badge :variant="$hasPinUnlock ? 'success' : 'neutral'" dot>
                            {{ $hasPinUnlock ? 'Enabled' : 'Off' }}
                        </x-mobile.badge>
                    </div>

                    <x-slot:footer>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @if ($hasPinUnlock)
                                <a href="{{ route('mobile.pin.change') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg border border-app-line bg-app-surface px-4 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800">
                                    Change PIN
                                </a>
                                <a href="{{ route('mobile.pin.remove') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-400">
                                    Remove PIN
                                </a>
                            @else
                                <a href="{{ route('mobile.pin.create') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white sm:col-span-2">
                                    Create PIN
                                </a>
                            @endif
                        </div>
                    </x-slot:footer>
                </x-mobile.card>
            </div>
        @endif
    </div>
</section>
