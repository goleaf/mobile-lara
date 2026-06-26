<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshTenantContext, switchTenant, refreshInvitations, acceptInvitation, declineInvitation" message="Updating workspace..." />

    <x-mobile.page-header
        title="Workspace settings"
        description="Current tenant context from the Admin/API bootstrap."
        :back-href="route('mobile.settings')"
    >
        <x-slot:action>
            <x-mobile.button variant="secondary" size="sm" wire:click="refreshTenantContext" wire:target="refreshTenantContext" wire:loading.attr="disabled">
                Refresh
            </x-mobile.button>
        </x-slot:action>
    </x-mobile.page-header>

    <div class="grid gap-5">
        <x-mobile.card title="Current workspace" description="API-derived tenant context cached on this device.">
            @if ($currentTenant)
                <div class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-lg font-semibold text-app-ink dark:text-zinc-100">{{ $currentTenant['name'] ?? 'Workspace' }}</p>
                            <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                                {{ $currentTenant['role_summary']['label'] ?? 'Mobile access' }}
                            </p>
                        </div>

                        <x-mobile.badge variant="success" dot>
                            {{ $currentTenant['status'] ?? 'active' }}
                        </x-mobile.badge>
                    </div>
                </div>
            @else
                <x-mobile.empty-state
                    title="No workspace selected"
                    description="Refresh after login or ask an admin to assign a tenant."
                />
            @endif

            <x-slot:footer>
                <p class="text-sm leading-5 text-app-muted dark:text-zinc-400">
                    {{ $cachedAt ? 'Cached '.$cachedAt : 'No cached bootstrap context yet.' }}
                </p>
            </x-slot:footer>
        </x-mobile.card>

        <x-mobile.card title="Pending invitations" description="API-confirmed tenant invitations for this account.">
            <x-slot:action>
                <x-mobile.button variant="secondary" size="sm" wire:click="refreshInvitations" wire:target="refreshInvitations" wire:loading.attr="disabled">
                    Check
                </x-mobile.button>
            </x-slot:action>

            <div class="grid gap-3">
                @forelse ($invitations as $invitation)
                    @php
                        $tenant = is_array($invitation['tenant'] ?? null) ? $invitation['tenant'] : [];
                        $roleSummary = is_array($invitation['role_summary'] ?? null) ? $invitation['role_summary'] : [];
                        $tenantId = is_string($tenant['id'] ?? null) ? $tenant['id'] : '';
                        $tenantName = is_string($tenant['name'] ?? null) ? $tenant['name'] : 'Workspace invitation';
                        $roleLabel = is_string($roleSummary['label'] ?? null) ? $roleSummary['label'] : 'Mobile access';
                        $invitedAt = is_string($invitation['invited_at'] ?? null) ? $invitation['invited_at'] : null;
                    @endphp

                    <div wire:key="workspace-invitation-{{ $tenantId }}" class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-base font-semibold text-app-ink dark:text-zinc-100">{{ $tenantName }}</p>
                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $roleLabel }}</p>
                                @if ($invitedAt)
                                    <p class="mt-1 text-xs font-medium text-app-muted dark:text-zinc-500">Invited {{ $invitedAt }}</p>
                                @endif
                            </div>

                            <x-mobile.badge variant="warning">Invited</x-mobile.badge>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <x-mobile.button
                                variant="accent"
                                size="sm"
                                wire:click="acceptInvitation(@js($tenantId))"
                                wire:target="acceptInvitation, declineInvitation, refreshInvitations"
                                wire:loading.attr="disabled"
                                :disabled="$tenantId === ''"
                            >
                                Accept
                            </x-mobile.button>

                            <x-mobile.button
                                variant="secondary"
                                size="sm"
                                wire:click="declineInvitation(@js($tenantId))"
                                wire:target="acceptInvitation, declineInvitation, refreshInvitations"
                                wire:loading.attr="disabled"
                                :disabled="$tenantId === ''"
                            >
                                Decline
                            </x-mobile.button>
                        </div>
                    </div>
                @empty
                    @if ($invitationsLoaded)
                        <x-mobile.empty-state
                            title="No pending invitations"
                            description="Accepted and declined invitations are removed after API confirmation."
                        />
                    @else
                        <x-mobile.empty-state
                            title="Invitations not checked"
                            description="Check online before accepting or declining tenant access."
                        />
                    @endif
                @endforelse
            </div>
        </x-mobile.card>

        <form wire:submit="switchTenant" class="grid gap-5">
            <x-mobile.card title="Switch workspace" description="Only Admin/API-approved memberships can be selected.">
                <div class="grid gap-3">
                    @forelse ($availableTenants as $tenant)
                        @php
                            $tenantId = is_string($tenant['id'] ?? null) ? $tenant['id'] : '';
                            $tenantName = is_string($tenant['name'] ?? null) ? $tenant['name'] : 'Workspace';
                            $switchable = (bool) ($tenant['switchable'] ?? false);
                            $isSelected = $selectedTenantId === $tenantId;
                            $isCurrent = (bool) ($tenant['current'] ?? false);
                        @endphp

                        <button
                            type="button"
                            wire:key="workspace-tenant-{{ $tenantId }}"
                            wire:click="selectTenant(@js($tenantId))"
                            @disabled(! $switchable)
                            @class([
                                'flex min-h-20 items-center justify-between gap-4 rounded-lg border px-4 py-3 text-left transition disabled:cursor-not-allowed disabled:opacity-60',
                                'border-app-ink bg-app-bg dark:border-zinc-100 dark:bg-zinc-950' => $isSelected,
                                'border-app-line bg-app-bg hover:bg-app-surface dark:border-zinc-800 dark:bg-zinc-950 dark:hover:bg-zinc-900' => ! $isSelected,
                            ])
                        >
                            <span class="min-w-0">
                                <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $tenantName }}</span>
                                <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">
                                    {{ $tenant['role_summary']['label'] ?? 'Mobile access' }}
                                </span>
                                @if (! $switchable && ($tenant['disabled_reason'] ?? null))
                                    <span class="mt-1 block text-xs font-medium text-red-700 dark:text-red-300">{{ $tenant['disabled_reason'] }}</span>
                                @endif
                            </span>

                            <span class="flex shrink-0 flex-col items-end gap-2">
                                <x-mobile.badge :variant="$switchable ? ($isCurrent ? 'success' : 'accent') : 'neutral'">
                                    {{ $isCurrent ? 'Current' : ($switchable ? 'Available' : 'Blocked') }}
                                </x-mobile.badge>
                                @if ($isSelected)
                                    <span class="text-xs font-semibold text-app-ink dark:text-zinc-100">Selected</span>
                                @endif
                            </span>
                        </button>
                    @empty
                        <x-mobile.empty-state
                            title="No workspaces available"
                            description="Refresh context after login or contact your tenant admin."
                        />
                    @endforelse
                </div>

                @error('selectedTenantId')
                    <p class="mt-3 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <x-slot:footer>
                    <div class="grid gap-3">
                        <div aria-live="polite" class="min-h-6">
                            @if ($workspaceError)
                                <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-200">
                                    {{ $workspaceError }}
                                </p>
                            @elseif ($workspaceStatus)
                                <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                                    {{ $workspaceStatus }}
                                </p>
                            @endif
                        </div>

                        <x-mobile.submit-button target="switchTenant" loading-label="Switching workspace..." :disabled="count($availableTenants) === 0">
                            Switch workspace
                        </x-mobile.submit-button>
                    </div>
                </x-slot:footer>
            </x-mobile.card>
        </form>
    </div>
</section>
