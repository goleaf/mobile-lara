<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="saveProfile, retryProfile, shareProfile, logout" message="Updating profile..." />

    <x-mobile.page-skeleton wire:loading.delay wire:target="saveProfile, retryProfile, shareProfile, logout" />

    <div wire:loading.remove wire:target="saveProfile, retryProfile, shareProfile, logout" class="contents">
        @if ($hasNetworkError)
            <x-mobile.network-error-state retry-action="retryProfile" />
        @elseif (! $hasProfile)
            <x-mobile.empty-state title="No profile loaded" description="Retry sync to load the local mobile account.">
                <x-slot:action>
                    <x-mobile.retry-button wire:click="retryProfile" target="retryProfile">
                        Retry sync
                    </x-mobile.retry-button>
                </x-slot:action>
            </x-mobile.empty-state>
        @else
            <div class="grid gap-5">
                <x-mobile.card title="Profile" description="Your mobile account overview.">
                    <div class="flex items-start gap-4">
                        <x-mobile.avatar
                            :src="$avatarUrl"
                            :alt="$displayName.' avatar'"
                            :initials="$avatarInitials"
                            size="lg"
                            status="online"
                        />

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="break-words text-xl font-semibold text-app-ink dark:text-zinc-100">{{ $displayName }}</h2>
                                <x-mobile.badge variant="success" dot>
                                    {{ $accountStatus }}
                                </x-mobile.badge>
                            </div>

                            <p class="mt-1 break-words text-sm font-medium text-app-muted dark:text-zinc-400">{{ $email }}</p>
                            <p class="mt-3 text-sm leading-6 text-app-ink dark:text-zinc-200">{{ $bio }}</p>
                        </div>
                    </div>

                    <dl class="mt-5 grid gap-3">
                        @forelse ($profileRows as $row)
                            <div wire:key="profile-row-{{ $row['key'] }}" class="flex min-h-12 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                                <dt class="text-sm font-medium text-app-muted dark:text-zinc-400">{{ $row['label'] }}</dt>
                                <dd class="max-w-[62%] break-words text-right text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $row['value'] }}</dd>
                            </div>
                        @empty
                            <div class="text-sm text-app-muted dark:text-zinc-400">
                                No profile details available.
                            </div>
                        @endforelse
                    </dl>

                    <x-slot:footer>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <a
                                href="{{ route('mobile.profile.edit') }}"
                                wire:navigate
                                class="inline-flex min-h-12 w-full items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 active:bg-app-ink/80 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white dark:active:bg-zinc-200"
                            >
                                Edit profile
                            </a>

                            <x-mobile.button
                                wire:click="shareProfile"
                                wire:loading.attr="disabled"
                                wire:target="shareProfile"
                                variant="secondary"
                                full
                            >
                                <span wire:loading.remove wire:target="shareProfile">Share profile</span>
                                <span wire:loading wire:target="shareProfile">Sharing</span>
                            </x-mobile.button>
                        </div>
                    </x-slot:footer>
                </x-mobile.card>

                @if ($isEditingProfile)
                    <form wire:submit="saveProfile" class="grid gap-5">
                        <x-mobile.card title="Edit profile" description="Local profile fields until the profile API is connected.">
                            <div class="grid gap-4">
                                <x-mobile.input name="displayName" label="Name" wire:model="displayName" />
                                <x-mobile.input name="phone" label="Phone" wire:model="phone" />
                                <x-mobile.textarea name="bio" label="Bio" rows="3" wire:model="bio" />
                            </div>

                            <x-slot:footer>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <x-mobile.button wire:click="cancelProfileEdit" variant="secondary" full>
                                        Cancel
                                    </x-mobile.button>
                                    <x-mobile.submit-button target="saveProfile" loading-label="Saving profile...">
                                        Save profile
                                    </x-mobile.submit-button>
                                </div>
                            </x-slot:footer>
                        </x-mobile.card>
                    </form>
                @endif

                <x-mobile.card title="Shortcuts" description="Jump into the next account task.">
                    <div class="grid gap-3">
                        @forelse ($profileShortcuts as $shortcut)
                            <a
                                wire:key="profile-shortcut-{{ $shortcut['key'] }}"
                                href="{{ route($shortcut['route']) }}"
                                wire:navigate
                                class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 text-left transition hover:bg-app-surface dark:border-zinc-800 dark:bg-zinc-950 dark:hover:bg-zinc-900"
                            >
                                <span class="min-w-0">
                                    <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $shortcut['label'] }}</span>
                                    <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $shortcut['description'] }}</span>
                                </span>

                                <span class="flex shrink-0 items-center gap-2">
                                    <x-mobile.badge variant="accent">
                                        {{ $shortcut['badge'] }}
                                    </x-mobile.badge>
                                    <span aria-hidden="true" class="text-lg font-semibold text-app-muted dark:text-zinc-500">›</span>
                                </span>
                            </a>
                        @empty
                            <x-mobile.empty-state title="No shortcuts" description="Profile shortcuts will appear here once configured." />
                        @endforelse
                    </div>
                </x-mobile.card>

                <form wire:submit="logout">
                    <x-mobile.submit-button variant="danger" target="logout" loading-label="Logging out...">
                        Logout
                    </x-mobile.submit-button>
                </form>
            </div>
        @endif
    </div>
</section>
