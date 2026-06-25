<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Mobile Control Dashboard"
        description="Server-owned controls for the managed NativePHP mobile client."
    />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($controlAreas as $area)
            <article wire:key="control-area-{{ str($area['label'])->slug() }}" class="grid min-h-40 gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-start justify-between gap-3">
                    @if (isset($area['route']))
                        <a href="{{ route($area['route']) }}" class="text-base font-semibold text-zinc-950 hover:text-zinc-700 dark:text-zinc-100 dark:hover:text-zinc-300">
                            {{ $area['label'] }}
                        </a>
                    @else
                        <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">
                            {{ $area['label'] }}
                        </h2>
                    @endif

                    <x-admin.status-badge :tone="$area['tone']">
                        {{ $area['status'] }}
                    </x-admin.status-badge>
                </div>

                <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                    {{ $area['detail'] }}
                </p>
            </article>
        @empty
            <div class="rounded-lg border border-zinc-200 bg-white p-5 text-sm text-zinc-500 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400 dark:shadow-none">
                No control areas are available.
            </div>
        @endforelse
    </div>
</section>
