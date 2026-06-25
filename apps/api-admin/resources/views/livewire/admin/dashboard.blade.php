<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Mobile Control Dashboard"
        description="Server-owned controls for the managed NativePHP mobile client."
    />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($controlAreas as $area)
            <article wire:key="control-area-{{ str($area['label'])->slug() }}" class="grid min-h-40 gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">
                        {{ $area['label'] }}
                    </h2>

                    <x-admin.status-badge :tone="$area['tone']">
                        {{ $area['status'] }}
                    </x-admin.status-badge>
                </div>

                <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                    {{ $area['detail'] }}
                </p>
            </article>
        @endforeach
    </div>
</section>
