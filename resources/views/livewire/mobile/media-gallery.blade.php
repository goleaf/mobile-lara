<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshGallery,setFilter,shareMediaItem" message="Refreshing media..." />

    <x-mobile.page-header
        title="Media gallery"
        description="Local media stored on this device for offline-first sync."
        :back-href="route('mobile.settings.developer')"
    >
        <x-slot:action>
            <a
                href="{{ route('mobile.media.capture') }}"
                wire:navigate
                class="inline-flex min-h-10 items-center justify-center rounded-lg border border-app-line bg-app-surface px-3 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800"
            >
                Capture
            </a>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Media storage unavailable"
            message="Run the mobile local storage migrations before viewing the local media gallery."
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshGallery" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        <x-mobile.card title="Library summary" description="Quick totals from the local media_items table.">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($metrics as $metric)
                    <div
                        wire:key="media-gallery-metric-{{ $metric['label'] }}"
                        class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <p class="text-xs font-semibold uppercase tracking-normal text-app-muted dark:text-zinc-500">
                            {{ $metric['label'] }}
                        </p>
                        <p class="mt-2 text-2xl font-semibold tracking-normal text-app-ink dark:text-zinc-100">
                            {{ $metric['value'] }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-app-muted dark:text-zinc-400">
                            {{ $metric['description'] }}
                        </p>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No metrics"
                        description="Media totals will appear after local storage is available."
                    />
                @endforelse
            </div>
        </x-mobile.card>

        <x-mobile.card title="Media items" description="Filter local files by media type or sync state.">
            <x-slot:action>
                <x-mobile.badge variant="neutral">
                    {{ $galleryCount }} shown
                </x-mobile.badge>
            </x-slot:action>

            <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
                @forelse ($filters as $filterOption)
                    <button
                        type="button"
                        wire:key="media-gallery-filter-{{ $filterOption['key'] }}"
                        wire:click="setFilter('{{ $filterOption['key'] }}')"
                        @class([
                            'inline-flex min-h-10 shrink-0 items-center gap-2 rounded-lg border px-3 text-sm font-semibold transition',
                            'border-app-ink bg-app-ink text-white dark:border-zinc-100 dark:bg-zinc-100 dark:text-zinc-950' => $filterOption['active'],
                            'border-app-line bg-app-bg text-app-ink hover:bg-app-surface dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:bg-zinc-900' => ! $filterOption['active'],
                        ])
                    >
                        <span>{{ $filterOption['label'] }}</span>
                        <span class="rounded-full bg-current/10 px-1.5 py-0.5 text-[11px]">
                            {{ $filterOption['count'] }}
                        </span>
                    </button>
                @empty
                    <span class="text-sm font-medium text-app-muted dark:text-zinc-400">No filters available</span>
                @endforelse
            </div>

            <div class="grid gap-3">
                @forelse ($mediaItems as $mediaItem)
                    <article
                        wire:key="media-gallery-item-{{ $mediaItem->id }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">
                                    {{ $mediaItem->displayName() }}
                                </p>
                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                                    {{ $mediaItem->mime ?: 'Unknown MIME' }}
                                    @if ($mediaItem->dimensions())
                                        / {{ $mediaItem->dimensions() }}
                                    @endif
                                    @if ($mediaItem->formattedDuration())
                                        / {{ $mediaItem->formattedDuration() }}
                                    @endif
                                </p>
                            </div>

                            <x-mobile.badge :variant="$mediaItem->isVideo() ? 'accent' : ($mediaItem->isImage() ? 'success' : 'neutral')">
                                {{ ucfirst($mediaItem->type) }}
                            </x-mobile.badge>
                        </div>

                        @if ($mediaItem->caption)
                            <p class="text-sm leading-6 text-app-ink dark:text-zinc-200">
                                {{ $mediaItem->caption }}
                            </p>
                        @endif

                        <p class="break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                            {{ $mediaItem->path }}
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <x-mobile.badge :variant="$mediaItem->sync_status === 'failed' ? 'danger' : ($mediaItem->sync_status === 'synced' ? 'success' : 'warning')" size="sm" dot>
                                {{ $mediaItem->sync_status }}
                            </x-mobile.badge>

                            @if ($mediaItem->formattedSize())
                                <x-mobile.badge variant="neutral" size="sm">
                                    {{ $mediaItem->formattedSize() }}
                                </x-mobile.badge>
                            @endif

                            @if ($mediaItem->relatedEntityLabel())
                                <x-mobile.badge variant="neutral" size="sm">
                                    {{ $mediaItem->relatedEntityLabel() }}
                                </x-mobile.badge>
                            @endif

                            <x-mobile.badge variant="neutral" size="sm">
                                {{ $mediaItem->created_at?->diffForHumans() ?? 'Time unknown' }}
                            </x-mobile.badge>
                        </div>

                        <div class="flex justify-end">
                            <x-mobile.button
                                size="sm"
                                variant="secondary"
                                wire:click="shareMediaItem({{ $mediaItem->id }})"
                                wire:loading.attr="disabled"
                                wire:target="shareMediaItem({{ $mediaItem->id }})"
                            >
                                <span wire:loading.remove wire:target="shareMediaItem({{ $mediaItem->id }})">Share</span>
                                <span wire:loading wire:target="shareMediaItem({{ $mediaItem->id }})">Sharing</span>
                            </x-mobile.button>
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No media items"
                        description="Captured, selected, or imported media will appear here after it is stored locally."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="refreshGallery" variant="secondary" full>
                    Refresh gallery
                </x-mobile.button>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</section>
