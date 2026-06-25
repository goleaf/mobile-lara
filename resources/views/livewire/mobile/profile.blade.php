<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="saveProfile, retryProfile" message="Updating profile..." />

    <x-mobile.page-skeleton wire:loading.delay wire:target="saveProfile, retryProfile" />

    <div wire:loading.remove wire:target="saveProfile, retryProfile" class="contents">
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
            <form wire:submit="saveProfile" class="grid gap-5">
                <x-mobile.card title="Profile" description="Identity and app preferences.">
                    <div class="mb-5 flex items-center gap-4">
                        <x-mobile.avatar initials="ML" size="lg" status="online" />
                        <div class="min-w-0">
                            <p class="truncate text-lg font-semibold text-app-ink dark:text-zinc-100">{{ $displayName }}</p>
                            <p class="text-sm text-app-muted dark:text-zinc-400">{{ $bio }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4">
                        <x-mobile.input name="displayName" label="Display name" wire:model="displayName" />
                        <x-mobile.textarea name="bio" label="Bio" rows="3" wire:model="bio" />
                    </div>

                    <x-slot:footer>
                        <x-mobile.submit-button target="saveProfile" loading-label="Saving profile...">
                            Save profile
                        </x-mobile.submit-button>
                    </x-slot:footer>
                </x-mobile.card>
            </form>
        @endif
    </div>
</section>
