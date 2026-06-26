<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Permissions center"
        description="Review native access, request permissions, and recover denied permissions from app settings."
        :back-href="route('mobile.settings')"
    />

    <x-mobile.card title="Native permissions" description="Camera, microphone, location, notifications, biometrics, files, and network readiness.">
        <div class="grid gap-4">
            @forelse ($permissionRows as $permission)
                <article
                    wire:key="permission-center-{{ $permission['key'] }}"
                    class="grid gap-4 rounded-lg border border-app-line bg-app-bg p-4  "
                >
                    <div class="flex items-start justify-between gap-4">
                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="text-base font-semibold text-app-ink ">{{ $permission['label'] }}</span>
                                <x-mobile.badge variant="neutral" size="sm">
                                    {{ $permission['badge'] }}
                                </x-mobile.badge>
                            </span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted ">{{ $permission['description'] }}</span>
                        </span>

                        <x-mobile.badge :variant="$permission['status_variant']" dot>
                            {{ $permission['status'] }}
                        </x-mobile.badge>
                    </div>

                    <div class="rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2  ">
                        <p class="text-xs font-semibold uppercase text-app-muted ">Explanation</p>
                        <p class="mt-1 text-sm leading-5 text-app-ink ">{{ $permission['explanation'] }}</p>
                    </div>

                    <dl class="grid gap-2 sm:grid-cols-2">
                        @foreach ($permission['details'] as $detail)
                            <div
                                wire:key="permission-center-{{ $permission['key'] }}-detail-{{ $loop->index }}"
                                class="rounded-lg border border-app-line bg-app-surface px-3 py-2  "
                            >
                                <dt class="text-xs font-semibold uppercase text-app-muted ">{{ $detail['label'] }}</dt>
                                <dd class="mt-1 break-words text-sm font-medium text-app-ink ">{{ $detail['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="grid gap-3 sm:grid-cols-2">
                        @if ($permission['can_request'])
                            <x-mobile.button
                                wire:click="requestPermission('{{ $permission['key'] }}')"
                                wire:loading.attr="disabled"
                                wire:target="requestPermission('{{ $permission['key'] }}')"
                                variant="primary"
                                full
                            >
                                <span wire:loading.remove wire:target="requestPermission('{{ $permission['key'] }}')">{{ $permission['request_label'] }}</span>
                                <span wire:loading wire:target="requestPermission('{{ $permission['key'] }}')">Requesting</span>
                            </x-mobile.button>
                        @else
                            <x-mobile.button variant="primary" full disabled>
                                {{ $permission['request_label'] }}
                            </x-mobile.button>
                        @endif

                        <x-mobile.button
                            wire:click="openAppSettings('{{ $permission['key'] }}')"
                            wire:loading.attr="disabled"
                            wire:target="openAppSettings"
                            variant="secondary"
                            full
                        >
                            <span wire:loading.remove wire:target="openAppSettings">{{ $permission['recovery_label'] }}</span>
                            <span wire:loading wire:target="openAppSettings">Opening settings</span>
                        </x-mobile.button>
                    </div>

                    <p class="text-xs leading-5 text-app-muted ">{{ $permission['recovery_note'] }}</p>
                </article>
            @empty
                <x-mobile.empty-state
                    title="No permissions configured"
                    description="Permission center rows are not configured."
                />
            @endforelse
        </div>

        <x-slot:footer>
            <div aria-live="polite" class="grid min-h-6 gap-3">
                @if ($permissionError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900   ">
                        {{ $permissionError }}
                    </p>
                @elseif ($permissionStatus)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800   ">
                        {{ $permissionStatus }}
                    </p>
                @endif

                @if ($settingsError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900   ">
                        {{ $settingsError }}
                    </p>
                @elseif ($settingsStatus)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800   ">
                        {{ $settingsStatus }}
                    </p>
                @endif
            </div>
        </x-slot:footer>
    </x-mobile.card>

    <x-mobile.card title="Platform" :description="$systemSnapshot['recovery_description']">
        <div class="grid gap-3">
            @forelse ($platformRows as $platformRow)
                <div
                    wire:key="platform-helper-{{ $platformRow['key'] }}"
                    class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  "
                >
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-app-muted ">{{ $platformRow['label'] }}</span>
                        <span class="mt-1 block break-words text-base font-semibold text-app-ink ">{{ $platformRow['value'] }}</span>
                    </span>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No platform data"
                    description="Native platform helpers are not available."
                />
            @endforelse
        </div>
    </x-mobile.card>

    <x-mobile.card title="Connected screens" description="Debug diagnostics remain available for deeper NativePHP checks.">
        <a
            href="{{ route('mobile.debug') }}"
            wire:navigate
            class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 text-left transition hover:bg-app-surface   "
        >
            <span class="min-w-0">
                <span class="block text-base font-semibold text-app-ink ">Developer debug</span>
                <span class="mt-1 block text-sm leading-5 text-app-muted ">Open runtime, device, network, storage, camera, and notification checks.</span>
            </span>

            <span class="flex shrink-0 items-center gap-2">
                <x-mobile.badge variant="accent">
                    Live
                </x-mobile.badge>
                <span aria-hidden="true" class="text-lg font-semibold text-app-muted ">›</span>
            </span>
        </a>
    </x-mobile.card>
</section>
