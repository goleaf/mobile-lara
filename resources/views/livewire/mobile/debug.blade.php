<section class="safe-x safe-pb flex min-h-full flex-col gap-4 py-6">
    <x-mobile.card title="Runtime">
        <div class="grid gap-3">
            @forelse ($debugRows as $debugRow)
                <div
                    wire:key="debug-row-{{ $debugRow['key'] }}"
                    class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                >
                    <p class="text-sm font-medium text-app-muted dark:text-zinc-400">{{ $debugRow['label'] }}</p>
                    <p class="mt-1 break-words text-base font-semibold text-app-ink dark:text-zinc-100">{{ $debugRow['value'] }}</p>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No runtime data"
                    description="Debug rows are not available."
                />
            @endforelse
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
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                    <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">{{ $dialogStatus }}</p>
                </div>
            @endif

            @if ($dialogResultRows !== [])
                <div class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                    <h3 class="text-sm font-semibold text-app-ink dark:text-zinc-100">Last payload</h3>
                    <dl class="mt-3 grid gap-3">
                        @forelse ($dialogResultRows as $dialogResultRow)
                            <div wire:key="dialog-result-{{ $dialogResultRow['key'] }}" class="grid gap-1">
                                <dt class="text-xs font-semibold uppercase text-app-muted dark:text-zinc-500">{{ $dialogResultRow['label'] }}</dt>
                                <dd class="break-words text-sm text-app-ink dark:text-zinc-100">{{ $dialogResultRow['value'] }}</dd>
                            </div>
                        @empty
                            <div class="text-sm text-app-muted dark:text-zinc-400">
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
                <div class="rounded-lg border border-sky-200 bg-sky-50 p-4 dark:border-sky-400/30 dark:bg-sky-400/10">
                    <p class="text-sm font-semibold text-sky-950 dark:text-sky-100">{{ $toastActionStatus }}</p>
                </div>
            @endif
        </div>
    </x-mobile.card>
</section>
