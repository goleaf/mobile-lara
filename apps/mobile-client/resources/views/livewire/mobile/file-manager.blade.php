<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="writeCurrentFile,readFile,copyCurrentFile,moveCurrentFile,deleteFile,importFile,exportFile,shareFile,refreshFiles" message="Working with files..." />

    <x-mobile.page-header
        title="File manager"
        description="Read, write, import, export, share, and move local app files."
        :back-href="route('mobile.settings.developer')"
    />

    <x-mobile.card
        title="File bridge"
        description="NativePHP handles copy, move, and sharing when the native bridge is available."
    >
        <div class="grid gap-3">
            <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4  ">
                <div class="min-w-0">
                    <p class="text-base font-semibold text-app-ink ">
                        {{ $snapshot['native_available'] ? 'Native bridge available' : 'Browser fallback active' }}
                    </p>
                    <p class="mt-1 break-words text-sm leading-5 text-app-muted ">
                        {{ $snapshot['root_path'] }}
                    </p>
                </div>

                <x-mobile.badge :variant="$snapshot['native_available'] ? 'success' : 'warning'" dot>
                    {{ $snapshot['native_available'] ? 'Native' : 'Local' }}
                </x-mobile.badge>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Files</p>
                    <p class="mt-2 text-2xl font-semibold tracking-normal text-app-ink ">{{ $snapshot['file_count'] }}</p>
                </div>

                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Size</p>
                    <p class="mt-2 text-2xl font-semibold tracking-normal text-app-ink ">{{ $snapshot['total_size_label'] }}</p>
                </div>
            </div>
        </div>
    </x-mobile.card>

    @if ($lastOperationMessage)
        <div @class([
            'rounded-lg border px-4 py-3 text-sm font-medium',
            'border-emerald-200 bg-emerald-50 text-emerald-900   ' => $lastOperationStatus === 'success',
            'border-amber-200 bg-amber-50 text-amber-900   ' => $lastOperationStatus !== 'success',
        ])>
            {{ $lastOperationMessage }}
        </div>
    @endif

    <x-mobile.card title="Editor" description="Create or update a UTF-8 text file inside the local sandbox.">
        @if (! $filePolicy['files']['allowed'])
            <x-mobile.error-state
                title="File manager disabled"
                :message="$filePolicy['files']['message']"
            />
        @else
            <form wire:submit="writeCurrentFile" class="grid gap-4">
                <x-mobile.input
                    name="filePath"
                    label="File path"
                    wire:model.blur="filePath"
                    :error="$errors->first('filePath')"
                    hint="Relative paths only, for example notes/demo.txt."
                />

                <x-mobile.textarea
                    name="fileContents"
                    label="Contents"
                    rows="7"
                    wire:model.blur="fileContents"
                    :error="$errors->first('fileContents')"
                >{{ $fileContents }}</x-mobile.textarea>

                <div class="grid grid-cols-2 gap-3">
                    <x-mobile.button type="submit" wire:target="writeCurrentFile" wire:loading.attr="disabled" full>
                        <span wire:loading.remove wire:target="writeCurrentFile">Write</span>
                        <span wire:loading wire:target="writeCurrentFile">Writing</span>
                    </x-mobile.button>

                    <x-mobile.button type="button" wire:click="readCurrentFile" wire:target="readCurrentFile" wire:loading.attr="disabled" variant="secondary" full>
                        Read
                    </x-mobile.button>
                </div>
            </form>
        @endif
    </x-mobile.card>

    <x-mobile.card title="Copy and move" description="Use NativePHP File.Copy and File.Move when the bridge is active.">
        @if (! $filePolicy['files']['allowed'])
            <x-mobile.error-state
                title="File actions disabled"
                :message="$filePolicy['files']['message']"
            />
        @else
            <div class="grid gap-4">
                <x-mobile.input
                    name="copyTo"
                    label="Copy destination"
                    wire:model.blur="copyTo"
                    :error="$errors->first('copyTo')"
                />

                <x-mobile.button wire:click="copyCurrentFile" wire:target="copyCurrentFile" wire:loading.attr="disabled" variant="secondary" full>
                    Copy current file
                </x-mobile.button>

                <x-mobile.input
                    name="moveTo"
                    label="Move destination"
                    wire:model.blur="moveTo"
                    :error="$errors->first('moveTo')"
                />

                <x-mobile.button wire:click="moveCurrentFile" wire:target="moveCurrentFile" wire:loading.attr="disabled" variant="secondary" full>
                    Move current file
                </x-mobile.button>
            </div>
        @endif
    </x-mobile.card>

    <x-mobile.card title="Import" description="Import a local upload into the app sandbox for offline use.">
        @if (! $filePolicy['files']['allowed'])
            <x-mobile.error-state
                title="File import disabled"
                :message="$filePolicy['files']['message']"
            />
        @else
            <form wire:submit="importFile" class="grid gap-4">
                <x-mobile.input
                    name="importDirectory"
                    label="Import directory"
                    wire:model.blur="importDirectory"
                    :error="$errors->first('importDirectory')"
                />

                <div class="grid gap-2">
                    <label for="importUpload" class="text-sm font-medium text-app-ink ">
                        File
                    </label>
                    <input
                        id="importUpload"
                        name="importUpload"
                        type="file"
                        wire:model="importUpload"
                        class="block min-h-12 w-full rounded-lg border border-app-line bg-app-surface px-3.5 py-2 text-sm text-app-ink shadow-[0_12px_24px_-22px_rgba(15,23,42,0.55)] file:mr-3 file:rounded-lg file:border-0 file:bg-app-ink file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white focus:border-app-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-app-accent/20"
                    >
                    @error('importUpload')
                        <p class="text-sm font-medium text-red-600 ">{{ $message }}</p>
                    @enderror
                </div>

                <x-mobile.button type="submit" wire:target="importFile,importUpload" wire:loading.attr="disabled" full>
                    <span wire:loading.remove wire:target="importFile,importUpload">Import file</span>
                    <span wire:loading wire:target="importFile,importUpload">Importing</span>
                </x-mobile.button>
            </form>
        @endif
    </x-mobile.card>

    <x-mobile.card title="Capabilities" description="Native and fallback file operations exposed by the service.">
        <div class="grid gap-3">
            @forelse ($capabilities as $capability)
                <div
                    wire:key="file-capability-{{ $capability['key'] }}"
                    class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  "
                >
                    <span class="min-w-0">
                        <span class="block text-base font-semibold text-app-ink ">{{ $capability['label'] }}</span>
                        <span class="mt-1 block text-sm leading-5 text-app-muted ">{{ $capability['description'] }}</span>
                        <span class="mt-1 block text-xs font-medium text-app-muted ">{{ $capability['driver'] }}</span>
                    </span>

                    <x-mobile.badge :variant="$capability['supported'] ? 'success' : 'neutral'">
                        {{ $capability['supported'] ? 'Ready' : 'Native only' }}
                    </x-mobile.badge>
                </div>
            @empty
                <x-mobile.empty-state title="No file capabilities" description="File operations are not configured." />
            @endforelse
        </div>
    </x-mobile.card>

    <x-mobile.card title="Local files" description="Files currently stored in the mobile file sandbox.">
        <x-slot:action>
            @if ($filePolicy['files']['allowed'])
                <x-mobile.button size="sm" variant="secondary" wire:click="refreshFiles" wire:target="refreshFiles" wire:loading.attr="disabled">
                    Refresh
                </x-mobile.button>
            @endif
        </x-slot:action>

        @if (! $filePolicy['files']['allowed'])
            <x-mobile.error-state
                title="Local files disabled"
                :message="$filePolicy['files']['message']"
            />
        @else
            <div class="grid gap-3">
                @forelse ($fileRows as $file)
                    <div
                        wire:key="managed-file-{{ $file['path'] }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4  "
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink ">{{ $file['name'] }}</p>
                                <p class="mt-1 break-words text-sm leading-5 text-app-muted ">{{ $file['path'] }}</p>
                            </div>

                            <x-mobile.badge variant="neutral">
                                {{ $file['size_label'] }}
                            </x-mobile.badge>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <x-mobile.badge variant="neutral" size="sm">{{ $file['mime'] }}</x-mobile.badge>
                            @if ($file['modified_at'])
                                <x-mobile.badge variant="neutral" size="sm">{{ $file['modified_at'] }}</x-mobile.badge>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <x-mobile.button size="sm" variant="secondary" wire:click="readFile('{{ $file['path'] }}')">Read</x-mobile.button>
                            <x-mobile.button size="sm" variant="secondary" wire:click="exportFile('{{ $file['path'] }}')">Export</x-mobile.button>
                            @if ($filePolicy['share']['allowed'])
                                <x-mobile.button size="sm" variant="secondary" wire:click="shareFile('{{ $file['path'] }}')">Share</x-mobile.button>
                            @endif
                            <x-mobile.button size="sm" variant="danger" wire:click="deleteFile('{{ $file['path'] }}')">Delete</x-mobile.button>
                        </div>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No local files"
                        description="Write or import a file to populate the local file sandbox."
                    />
                @endforelse
            </div>
        @endif

        <x-slot:footer>
            <p class="break-words text-xs font-medium text-app-muted ">
                Exports are copied to {{ $snapshot['export_path'] }}.
            </p>
        </x-slot:footer>
    </x-mobile.card>
</section>
