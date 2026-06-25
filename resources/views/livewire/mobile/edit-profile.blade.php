<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="saveProfile, takeAvatarPhoto, chooseAvatarFromGallery, removeAvatar" message="Updating avatar..." />

    <form wire:submit="saveProfile" class="grid gap-5">
        <x-mobile.page-header
            title="Edit profile"
            description="Update the profile details used by the mobile app."
            :back-href="route('mobile.profile')"
        />

        @if ($successMessage)
            <x-mobile.toast :message="$successMessage" title="Profile saved" variant="success" />
        @endif

        <x-mobile.card title="Avatar" description="Take a photo, choose from gallery, or upload an image. Save to apply it to your profile.">
            <div class="flex items-start gap-4">
                <x-mobile.avatar
                    :src="$avatarPreviewUrl"
                    :alt="$name.' avatar preview'"
                    :initials="$avatarInitials"
                    size="lg"
                    status="online"
                />

                <div class="grid min-w-0 flex-1 gap-2">
                    <label for="avatar" class="text-sm font-medium text-app-ink dark:text-zinc-100">
                        Profile photo
                    </label>

                    <div class="grid grid-cols-2 gap-2">
                        <x-mobile.button
                            wire:click="takeAvatarPhoto"
                            wire:loading.attr="disabled"
                            wire:target="takeAvatarPhoto"
                            variant="secondary"
                            full
                        >
                            Take photo
                        </x-mobile.button>

                        <x-mobile.button
                            wire:click="chooseAvatarFromGallery"
                            wire:loading.attr="disabled"
                            wire:target="chooseAvatarFromGallery"
                            variant="secondary"
                            full
                        >
                            Gallery
                        </x-mobile.button>
                    </div>

                    <input
                        id="avatar"
                        name="avatar"
                        type="file"
                        accept="image/*"
                        wire:model="avatar"
                        aria-describedby="avatar-hint"
                        class="block min-h-12 w-full rounded-lg border border-app-line bg-white px-3 py-2 text-sm text-app-ink shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-app-ink file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white focus:border-app-accent focus:outline-none focus:ring-2 focus:ring-app-accent/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:file:bg-zinc-100 dark:file:text-zinc-950"
                    >

                    <p id="avatar-hint" class="text-sm leading-5 text-app-muted dark:text-zinc-400">
                        JPG, PNG, or WebP up to 2 MB.
                    </p>

                    <p wire:loading wire:target="avatar, takeAvatarPhoto, chooseAvatarFromGallery" class="text-sm font-medium text-app-ink dark:text-zinc-100">
                        Preparing avatar...
                    </p>

                    @error('avatar')
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    @if ($nativeAvatarStatus)
                        <p class="rounded-lg border border-app-line bg-app-bg px-3 py-2 text-sm font-medium text-app-ink dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100">
                            {{ $nativeAvatarStatus }}
                        </p>
                    @elseif (! $nativeAvatarAvailable)
                        <p class="rounded-lg border border-app-line bg-app-bg px-3 py-2 text-sm text-app-muted dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
                            Native camera and gallery open inside the mobile app; this browser can use upload.
                        </p>
                    @endif

                    @if ($avatarMarkedForRemoval)
                        <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 dark:border-amber-400/30 dark:bg-amber-400/10 dark:text-amber-100">
                            Avatar marked for removal. Save profile to apply.
                        </p>
                    @endif

                    @if ($avatarUploadName)
                        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-900 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-100">
                            Avatar ready: {{ $avatarUploadName }}
                        </p>
                    @endif

                    @if ($canRemoveAvatar)
                        <x-mobile.button
                            wire:click="removeAvatar"
                            wire:loading.attr="disabled"
                            wire:target="removeAvatar"
                            variant="ghost"
                            full
                        >
                            Remove avatar
                        </x-mobile.button>
                    @endif
                </div>
            </div>
        </x-mobile.card>

        <x-mobile.card title="Profile details" description="These fields are validated now and ready for profile API persistence.">
            <div class="grid gap-4">
                <x-mobile.input
                    name="name"
                    label="Name"
                    wire:model.blur="name"
                    :error="$errors->first('name')"
                />

                <x-mobile.input
                    name="username"
                    label="Username"
                    wire:model.blur="username"
                    :error="$errors->first('username')"
                    hint="Letters, numbers, dots, dashes, and underscores."
                />

                <x-mobile.input
                    name="phone"
                    label="Phone"
                    type="tel"
                    wire:model.blur="phone"
                    :error="$errors->first('phone')"
                />

                <x-mobile.textarea
                    name="bio"
                    label="Bio"
                    rows="4"
                    wire:model.blur="bio"
                    :error="$errors->first('bio')"
                    hint="Up to 280 characters."
                >{{ $bio }}</x-mobile.textarea>

                <x-mobile.input
                    name="location"
                    label="Location"
                    wire:model.blur="location"
                    :error="$errors->first('location')"
                />

                <x-mobile.input
                    name="website"
                    label="Website"
                    type="url"
                    wire:model.blur="website"
                    :error="$errors->first('website')"
                    placeholder="https://example.com"
                />
            </div>

            <x-slot:footer>
                <div class="grid gap-3">
                    <x-mobile.submit-button target="saveProfile" loading-label="Saving profile...">
                        Save profile
                    </x-mobile.submit-button>

                    <a
                        href="{{ route('mobile.profile') }}"
                        wire:navigate
                        class="inline-flex min-h-12 w-full items-center justify-center rounded-lg border border-app-line bg-app-surface px-4 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800"
                    >
                        Cancel
                    </a>
                </div>
            </x-slot:footer>
        </x-mobile.card>
    </form>
</section>
