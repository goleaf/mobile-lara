<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Sessions"
        description="Review this device and prepare remote session management."
        back-href="{{ route('mobile.settings') }}"
    />

    <x-mobile.loading-state target="logout, retryRemoteSessions" message="Updating sessions..." />

    <x-mobile.card title="Current device session" description="Local session details for this app install.">
        <div class="grid gap-4">
            <div class="flex items-start justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                <div class="flex min-w-0 items-start gap-3">
                    <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-app-ink text-white dark:bg-zinc-100 dark:text-zinc-950">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="7" y="2.5" width="10" height="19" rx="2.5" stroke="currentColor" stroke-width="1.8" />
                            <path d="M10.5 18.5h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <p class="text-base font-semibold text-app-ink dark:text-zinc-100">{{ $currentSession['device_name'] }}</p>
                        <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                            Session {{ $currentSession['session_reference'] }}
                        </p>
                    </div>
                </div>

                <x-mobile.badge variant="success" dot>
                    Current
                </x-mobile.badge>
            </div>

            <dl class="grid gap-3">
                <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-surface px-4 py-3 dark:border-zinc-800 dark:bg-zinc-900">
                    <dt class="text-sm font-medium text-app-muted dark:text-zinc-400">Last login time</dt>
                    <dd class="max-w-[58%] text-right text-sm font-semibold text-app-ink dark:text-zinc-100">
                        {{ $currentSession['last_login_label'] }}
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-surface px-4 py-3 dark:border-zinc-800 dark:bg-zinc-900">
                    <dt class="text-sm font-medium text-app-muted dark:text-zinc-400">App version</dt>
                    <dd class="max-w-[58%] text-right text-sm font-semibold text-app-ink dark:text-zinc-100">
                        {{ $currentSession['app_version'] }}
                        <span class="font-medium text-app-muted dark:text-zinc-400">({{ $currentSession['app_version_code'] }})</span>
                    </dd>
                </div>
            </dl>
        </div>

        <x-slot:footer>
            <form wire:submit="logout">
                <x-mobile.submit-button variant="danger" target="logout" loading-label="Logging out...">
                    Logout
                </x-mobile.submit-button>
            </form>
        </x-slot:footer>
    </x-mobile.card>

    @if ($hasNetworkError)
        <x-mobile.network-error-state retry-action="retryRemoteSessions" />
    @else
        <x-mobile.card title="Remote sessions" description="Placeholder rows for the future sessions API.">
            <div class="grid gap-3">
                @forelse ($remoteSessions as $remoteSession)
                    <article wire:key="remote-session-{{ $remoteSession['id'] }}" class="rounded-lg border border-dashed border-app-line bg-app-bg p-4 dark:border-zinc-700 dark:bg-zinc-950">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-base font-semibold text-app-ink dark:text-zinc-100">{{ $remoteSession['device_name'] }}</p>
                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $remoteSession['platform'] }}</p>
                            </div>

                            <x-mobile.badge variant="warning">
                                {{ $remoteSession['status_label'] }}
                            </x-mobile.badge>
                        </div>

                        <dl class="mt-4 grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-app-muted dark:text-zinc-400">Last active</dt>
                                <dd class="text-right font-medium text-app-ink dark:text-zinc-100">{{ $remoteSession['last_active_label'] }}</dd>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-app-muted dark:text-zinc-400">Source</dt>
                                <dd class="text-right font-mono text-xs font-semibold text-app-ink dark:text-zinc-100">{{ $remoteSession['source'] }}</dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <x-mobile.empty-state title="No remote sessions" description="Remote device sessions will appear after the mobile API is connected." />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button variant="secondary" full wire:click="retryRemoteSessions">
                    Refresh remote sessions
                </x-mobile.button>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</section>
