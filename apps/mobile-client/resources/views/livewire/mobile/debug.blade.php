<section class="safe-x safe-pb flex min-h-full flex-col gap-4 py-6">
    <x-mobile.page-header
        title="Developer Debug"
        description="Hidden mobile diagnostics and native capability checks."
        :back-href="route('mobile.settings.developer')"
    />

    <x-mobile.card title="Runtime" description="Current app, framework, NativePHP, storage, and worker configuration.">
        <div class="grid gap-3">
            @forelse ($debugRows as $debugRow)
                <div
                    wire:key="debug-row-{{ $debugRow['key'] }}"
                    class="rounded-lg border border-app-line bg-app-bg p-4  "
                >
                    <p class="text-sm font-medium text-app-muted ">{{ $debugRow['label'] }}</p>
                    <p class="mt-1 break-words text-base font-semibold text-app-ink ">{{ $debugRow['value'] }}</p>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No runtime data"
                    description="Debug rows are not available."
                />
            @endforelse
        </div>
    </x-mobile.card>

    <x-mobile.card title="Diagnostics export" description="Privacy-safe app, tenant, feature, config, network, device, and sync snapshot.">
        <div class="grid gap-4">
            <div class="grid gap-3">
                @forelse ($diagnosticsRows as $diagnosticsRow)
                    <div
                        wire:key="diagnostics-row-{{ $diagnosticsRow['key'] }}"
                        class="rounded-lg border border-app-line bg-app-bg p-4  "
                    >
                        <p class="text-sm font-medium text-app-muted ">{{ $diagnosticsRow['label'] }}</p>
                        <p class="mt-1 break-words text-base font-semibold text-app-ink ">{{ $diagnosticsRow['value'] }}</p>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No diagnostics available"
                        description="Diagnostics summary rows are not available."
                    />
                @endforelse
            </div>

            <x-mobile.button
                wire:click="exportDiagnosticsJson"
                wire:loading.attr="disabled"
                wire:target="exportDiagnosticsJson"
                variant="primary"
                class="w-full"
            >
                <span wire:loading.remove wire:target="exportDiagnosticsJson">
                    Export diagnostics JSON
                </span>
                <span wire:loading wire:target="exportDiagnosticsJson">
                    Exporting
                </span>
            </x-mobile.button>

            @if ($diagnosticsStatus)
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4  ">
                    <p class="text-sm font-semibold text-emerald-900 ">{{ $diagnosticsStatus }}</p>
                </div>
            @endif
        </div>
    </x-mobile.card>

    <livewire:mobile.network-status />

    <x-mobile.card
        title="Native tests"
        description="Quick checks for dialogs, secure storage, camera, notifications, and device hardware."
    >
        <div class="grid gap-4">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($testActions as $testAction)
                    <x-mobile.button
                        wire:key="native-test-action-{{ $testAction['action'] }}"
                        wire:click="{{ $testAction['action'] }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $testAction['action'] }}"
                        :variant="$testAction['variant']"
                        class="min-w-0"
                    >
                        <span wire:loading.remove wire:target="{{ $testAction['action'] }}">
                            {{ $testAction['label'] }}
                        </span>
                        <span wire:loading wire:target="{{ $testAction['action'] }}">
                            Testing
                        </span>
                    </x-mobile.button>
                @empty
                    <x-mobile.empty-state
                        title="No tests available"
                        description="Native test actions are not configured."
                    />
                @endforelse
            </div>

            @if ($dialogStatus)
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4  ">
                    <p class="text-sm font-semibold text-emerald-900 ">{{ $dialogStatus }}</p>
                </div>
            @endif

            @if ($testStatusRows !== [])
                <dl class="grid gap-3">
                    @forelse ($testStatusRows as $testStatusRow)
                        <div
                            wire:key="test-status-{{ $testStatusRow['key'] }}"
                            class="rounded-lg border border-app-line bg-app-bg p-4  "
                        >
                            <dt class="text-sm font-medium text-app-muted ">{{ $testStatusRow['label'] }}</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-app-ink ">{{ $testStatusRow['value'] }}</dd>
                        </div>
                    @empty
                        <div class="text-sm text-app-muted ">
                            No native test results recorded.
                        </div>
                    @endforelse
                </dl>
            @endif
        </div>
    </x-mobile.card>

    <x-mobile.card
        title="Native browser"
        description="Open external, in-app, OAuth, support, privacy, and billing placeholder links."
    >
        <div class="grid gap-4">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($browserActions as $browserAction)
                    <x-mobile.button
                        wire:key="browser-action-{{ $browserAction['action'] }}"
                        wire:click="{{ $browserAction['action'] }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $browserAction['action'] }}"
                        :variant="$browserAction['variant']"
                        class="min-w-0"
                    >
                        <span wire:loading.remove wire:target="{{ $browserAction['action'] }}">
                            {{ $browserAction['label'] }}
                        </span>
                        <span wire:loading wire:target="{{ $browserAction['action'] }}">
                            Opening
                        </span>
                    </x-mobile.button>
                @empty
                    <x-mobile.empty-state
                        title="No browser actions"
                        description="Native browser examples are not available."
                    />
                @endforelse
            </div>

            @if ($browserStatus)
                <div class="rounded-lg border border-sky-200 bg-sky-50 p-4  ">
                    <p class="text-sm font-semibold text-sky-950 ">{{ $browserStatus }}</p>
                </div>
            @endif
        </div>
    </x-mobile.card>

    <x-mobile.card
        title="Native sharing"
        description="Share text, links, and report placeholders through the NativePHP share sheet."
    >
        <div class="grid gap-4">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($shareActions as $shareAction)
                    <x-mobile.button
                        wire:key="share-action-{{ $shareAction['action'] }}"
                        wire:click="{{ $shareAction['action'] }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $shareAction['action'] }}"
                        :variant="$shareAction['variant']"
                        class="min-w-0"
                    >
                        <span wire:loading.remove wire:target="{{ $shareAction['action'] }}">
                            {{ $shareAction['label'] }}
                        </span>
                        <span wire:loading wire:target="{{ $shareAction['action'] }}">
                            Sharing
                        </span>
                    </x-mobile.button>
                @empty
                    <x-mobile.empty-state
                        title="No share actions"
                        description="Native share examples are not available."
                    />
                @endforelse
            </div>

            @if ($shareStatus)
                <div class="rounded-lg border border-sky-200 bg-sky-50 p-4  ">
                    <p class="text-sm font-semibold text-sky-950 ">{{ $shareStatus }}</p>
                </div>
            @endif
        </div>
    </x-mobile.card>

    <x-mobile.card
        title="Native dialogs"
        description="Alert, confirm, prompt fallback, toast, and snackbar calls through the Laravel wrapper."
    >
        <div class="grid gap-4">
            <x-mobile.input
                name="promptValue"
                label="Prompt default value"
                wire:model.blur="promptValue"
                :error="$errors->first('promptValue')"
            />

            <div class="grid grid-cols-2 gap-3">
                @forelse ($dialogActions as $dialogAction)
                    <x-mobile.button
                        wire:key="dialog-action-{{ $dialogAction['action'] }}"
                        wire:click="{{ $dialogAction['action'] }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $dialogAction['action'] }}"
                        :variant="$dialogAction['variant']"
                        class="min-w-0"
                    >
                        <span wire:loading.remove wire:target="{{ $dialogAction['action'] }}">
                            {{ $dialogAction['label'] }}
                        </span>
                        <span wire:loading wire:target="{{ $dialogAction['action'] }}">
                            Working
                        </span>
                    </x-mobile.button>
                @empty
                    <x-mobile.empty-state
                        title="No dialog actions"
                        description="Dialog examples are not available."
                    />
                @endforelse
            </div>

            @if ($dialogStatus)
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4  ">
                    <p class="text-sm font-semibold text-emerald-900 ">{{ $dialogStatus }}</p>
                </div>
            @endif

            @if ($dialogResultRows !== [])
                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <h3 class="text-sm font-semibold text-app-ink ">Last payload</h3>
                    <dl class="mt-3 grid gap-3">
                        @forelse ($dialogResultRows as $dialogResultRow)
                            <div wire:key="dialog-result-{{ $dialogResultRow['key'] }}" class="grid gap-1">
                                <dt class="text-xs font-semibold uppercase text-app-muted ">{{ $dialogResultRow['label'] }}</dt>
                                <dd class="break-words text-sm text-app-ink ">{{ $dialogResultRow['value'] }}</dd>
                            </div>
                        @empty
                            <div class="text-sm text-app-muted ">
                                No payload recorded.
                            </div>
                        @endforelse
                    </dl>
                </div>
            @endif
        </div>
    </x-mobile.card>

    <x-mobile.card
        title="Livewire toasts"
        description="Event-driven app notifications with auto-dismiss, persistent mode, and action buttons."
    >
        <div class="grid gap-4">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($toastActions as $toastAction)
                    <x-mobile.button
                        wire:key="toast-action-{{ $toastAction['action'] }}"
                        wire:click="{{ $toastAction['action'] }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $toastAction['action'] }}"
                        :variant="$toastAction['variant']"
                        class="min-w-0"
                    >
                        <span wire:loading.remove wire:target="{{ $toastAction['action'] }}">
                            {{ $toastAction['label'] }}
                        </span>
                        <span wire:loading wire:target="{{ $toastAction['action'] }}">
                            Working
                        </span>
                    </x-mobile.button>
                @empty
                    <x-mobile.empty-state
                        title="No toast actions"
                        description="Toast examples are not available."
                    />
                @endforelse
            </div>

            @if ($toastActionStatus)
                <div class="rounded-lg border border-sky-200 bg-sky-50 p-4  ">
                    <p class="text-sm font-semibold text-sky-950 ">{{ $toastActionStatus }}</p>
                </div>
            @endif
        </div>
    </x-mobile.card>
</section>
