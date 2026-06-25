@props([
    'lines' => 3,
    'avatar' => false,
])

<div {{ $attributes->class(['animate-pulse rounded-lg border border-app-line bg-app-surface p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none']) }}>
    <div class="flex gap-4">
        @if ($avatar)
            <div class="size-12 shrink-0 rounded-full bg-app-line dark:bg-zinc-800"></div>
        @endif

        <div class="grid flex-1 gap-3">
            @for ($line = 0; $line < $lines; $line++)
                <div @class([
                    'h-3 rounded-full bg-app-line dark:bg-zinc-800',
                    'w-2/3' => $line === 0,
                    'w-full' => $line > 0 && $line % 2 === 1,
                    'w-5/6' => $line > 0 && $line % 2 === 0,
                ])></div>
            @endfor
        </div>
    </div>
</div>
