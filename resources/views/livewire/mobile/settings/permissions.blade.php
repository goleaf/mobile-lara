<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Permission settings"
        description="Native platform detection, permission recovery, and app settings access."
        :back-href="route('mobile.settings')"
    />

    <x-mobile.card title="Platform" :description="$systemSnapshot['recovery_description']">
        <div class="grid gap-3">
            @forelse ($platformRows as $platformRow)
                <div
                    wire:key="platform-helper-{{ $platformRow['key'] }}"
                    class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950"
                >
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-app-muted dark:text-zinc-400">{{ $platformRow['label'] }}</span>
                        <span class="mt-1 block break-words text-base font-semibold text-app-ink dark:text-zinc-100">{{ $platformRow['value'] }}</span>
                    </span>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No platform data"
                    description="Native platform helpers are not available."
                />
            @endforelse
        </div>

        <x-slot:footer>
            <div class="grid gap-3">
                <x-mobile.button
                    wire:click="openAppSettings"
                    wire:loading.attr="disabled"
                    wire:target="openAppSettings"
                    variant="primary"
                    full
                >
                    <span wire:loading.remove wire:target="openAppSettings">{{ $systemSnapshot['recovery_label'] }}</span>
                    <span wire:loading wire:target="openAppSettings">Opening settings</span>
                </x-mobile.button>

                <div aria-live="polite" class="min-h-6">
                    @if ($settingsError)
                        <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100">
                            {{ $settingsError }}
                        </p>
                    @elseif ($settingsStatus)
                        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                            {{ $settingsStatus }}
                        </p>
                    @endif
                </div>
            </div>
        </x-slot:footer>
    </x-mobile.card>

    <x-mobile.card title="Permission recovery" description="Open native app settings when a permission must be restored after denial.">
        <div class="grid gap-3">
            @forelse ($permissionRecoveryLinks as $link)
                <div
                    wire:key="permission-recovery-{{ $link['key'] }}"
                    class="grid gap-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                >
                    <div class="flex items-start justify-between gap-4">
                        <span class="min-w-0">
                            <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $link['label'] }}</span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $link['description'] }}</span>
                        </span>

                        <x-mobile.badge variant="accent">
                            {{ $link['badge'] }}
                        </x-mobile.badge>
                    </div>

                    <div class="rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 dark:border-zinc-800 dark:bg-zinc-900">
                        <p class="text-xs font-semibold uppercase text-app-muted dark:text-zinc-500">Recovery</p>
                        <p class="mt-1 text-sm leading-5 text-app-ink dark:text-zinc-100">{{ $link['platform_note'] }}</p>
                    </div>

                    <x-mobile.button
                        wire:click="openAppSettings('{{ $link['key'] }}')"
                        wire:loading.attr="disabled"
                        wire:target="openAppSettings"
                        variant="secondary"
                        full
                    >
                        <span wire:loading.remove wire:target="openAppSettings">{{ $link['recovery_label'] }}</span>
                        <span wire:loading wire:target="openAppSettings">Opening settings</span>
                    </x-mobile.button>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No recovery links"
                    description="Permission recovery entries are not configured."
                />
            @endforelse
        </div>
    </x-mobile.card>

    <x-mobile.card title="Connected screens" description="Debug diagnostics remain available for deeper NativePHP checks.">
        <a
            href="{{ route('mobile.debug') }}"
            wire:navigate
            class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 text-left transition hover:bg-app-surface dark:border-zinc-800 dark:bg-zinc-950 dark:hover:bg-zinc-900"
        >
            <span class="min-w-0">
                <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">Developer debug</span>
                <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">Open runtime, device, network, storage, camera, and notification checks.</span>
            </span>

            <span class="flex shrink-0 items-center gap-2">
                <x-mobile.badge variant="accent">
                    Live
                </x-mobile.badge>
                <span aria-hidden="true" class="text-lg font-semibold text-app-muted dark:text-zinc-500">›</span>
            </span>
        </a>
    </x-mobile.card>
</section>
