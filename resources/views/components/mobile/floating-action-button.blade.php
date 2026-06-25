@props([
    'label',
    'route' => null,
    'parameters' => [],
    'href' => null,
    'action' => null,
    'target' => null,
    'disabled' => false,
    'loadingLabel' => 'Working...',
    'navigate' => true,
])

@php
    $target ??= $action;
    $resolvedHref = $href ?? ($route ? route($route, $parameters) : null);
    $isLink = is_string($resolvedHref) && ! $disabled;

    $buttonClasses = [
        'pointer-events-auto inline-flex min-h-14 max-w-full items-center justify-center gap-3 rounded-full bg-app-ink px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-zinc-950/15 transition data-loading:pointer-events-none data-loading:opacity-80 hover:bg-app-ink/90 active:bg-app-ink/80 disabled:pointer-events-none disabled:opacity-50 dark:bg-zinc-100 dark:text-zinc-950 dark:shadow-black/30 dark:hover:bg-white dark:active:bg-zinc-200',
    ];
@endphp

<div class="pointer-events-none fixed inset-x-0 bottom-24 z-30 mx-auto flex w-full max-w-md justify-end px-4">
    @if ($isLink)
        <a
            href="{{ $resolvedHref }}"
            @if ($navigate) wire:navigate @endif
            aria-label="{{ $label }}"
            {{ $attributes->class($buttonClasses) }}
        >
            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-white/15 dark:bg-zinc-950/10">
                @isset($icon)
                    {{ $icon }}
                @else
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                @endisset
            </span>

            <span class="truncate">{{ $label }}</span>
        </a>
    @else
        <button
            @if ($action) wire:click="{{ $action }}" @endif
            @if ($target) wire:target="{{ $target }}" @endif
            @if ($target) wire:loading.attr="disabled" @endif
            @disabled($disabled)
            aria-label="{{ $label }}"
            {{ $attributes
                ->class($buttonClasses)
                ->merge(['type' => 'button']) }}
        >
            @if ($target)
                <span
                    wire:loading.remove
                    wire:target="{{ $target }}"
                    class="inline-flex min-w-0 items-center gap-3"
                >
                    <span class="grid size-8 shrink-0 place-items-center rounded-full bg-white/15 dark:bg-zinc-950/10">
                        @isset($icon)
                            {{ $icon }}
                        @else
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        @endisset
                    </span>

                    <span class="truncate">{{ $label }}</span>
                </span>

                <span
                    wire:loading.flex
                    wire:target="{{ $target }}"
                    class="items-center gap-2"
                >
                    <x-mobile.loading-spinner label="{{ $loadingLabel }}" />
                    <span>{{ $loadingLabel }}</span>
                </span>
            @else
                <span class="grid size-8 shrink-0 place-items-center rounded-full bg-white/15 dark:bg-zinc-950/10">
                    @isset($icon)
                        {{ $icon }}
                    @else
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    @endisset
                </span>

                <span class="truncate">{{ $label }}</span>
            @endif
        </button>
    @endif
</div>
