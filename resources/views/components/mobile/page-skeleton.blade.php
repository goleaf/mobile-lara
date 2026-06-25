@props([
    'cards' => 2,
    'withHeader' => true,
])

<div {{ $attributes->class(['grid gap-4']) }} aria-hidden="true">
    @if ($withHeader)
        <div class="animate-pulse rounded-lg border border-app-line bg-app-surface p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="h-3 w-24 rounded-full bg-app-line dark:bg-zinc-800"></div>
            <div class="mt-4 h-8 w-36 rounded-full bg-app-line dark:bg-zinc-800"></div>
            <div class="mt-3 h-3 w-full rounded-full bg-app-line dark:bg-zinc-800"></div>
        </div>
    @endif

    @for ($card = 0; $card < $cards; $card++)
        <x-mobile.loading-skeleton :lines="3" :avatar="$card === 0" />
    @endfor
</div>
