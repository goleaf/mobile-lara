@props([
    'show' => false,
    'title' => null,
    'description' => null,
    'maxWidth' => 'sm',
])

@php
    $widthClasses = [
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
    ][$maxWidth] ?? 'max-w-sm';
@endphp

@if ($show)
    <div {{ $attributes->class(['fixed inset-0 z-50']) }} role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-app-ink/45 "></div>

        <div class="safe-x fixed inset-0 grid place-items-center p-4">
            <section class="{{ $widthClasses }} w-full rounded-lg border border-app-line bg-app-surface p-5 shadow-2xl  ">
                @if ($title || $description || isset($action))
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            @if ($title)
                                <h2 class="text-lg font-semibold text-app-ink ">{{ $title }}</h2>
                            @endif

                            @if ($description)
                                <p class="mt-1 text-sm leading-5 text-app-muted ">{{ $description }}</p>
                            @endif
                        </div>

                        @isset($action)
                            <div class="shrink-0">
                                {{ $action }}
                            </div>
                        @endisset
                    </div>
                @endif

                <div class="text-sm leading-6 text-app-ink ">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        {{ $footer }}
                    </div>
                @endisset
            </section>
        </div>
    </div>
@endif
