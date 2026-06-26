<?php

namespace App\Livewire\Mobile\Settings;

use App\Services\MobileApi\MobileApiException;
use App\Services\MobileBootstrap\MobileBootstrapService;
use App\Services\MobileTenancy\MobileTenantApiService;
use App\Services\MobileTenancy\MobileTenantContextStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Workspace settings')]
final class Workspace extends Component
{
    public ?string $selectedTenantId = null;

    public ?string $workspaceStatus = null;

    public ?string $workspaceError = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $invitations = [];

    public bool $invitationsLoaded = false;

    protected MobileTenantContextStore $tenantContext;

    protected MobileTenantApiService $tenantApi;

    protected MobileBootstrapService $bootstrap;

    public function boot(
        MobileTenantContextStore $tenantContext,
        MobileTenantApiService $tenantApi,
        MobileBootstrapService $bootstrap,
    ): void {
        $this->tenantContext = $tenantContext;
        $this->tenantApi = $tenantApi;
        $this->bootstrap = $bootstrap;
    }

    public function mount(): void
    {
        $this->syncSelectedTenantFromContext();
    }

    public function selectTenant(string $tenantId): void
    {
        $this->selectedTenantId = $tenantId;
        $this->workspaceError = null;
        $this->workspaceStatus = null;
    }

    public function refreshTenantContext(): void
    {
        $this->clearFeedback();

        try {
            $this->bootstrap->refresh();
            $this->syncSelectedTenantFromContext();
        } catch (MobileApiException $exception) {
            $this->workspaceError = $exception->getMessage();
            $this->showToast($this->workspaceError, 'error');

            return;
        }

        $this->workspaceStatus = 'Workspace context refreshed.';
        $this->showToast($this->workspaceStatus, 'success');
    }

    public function refreshInvitations(): void
    {
        $this->clearFeedback();

        try {
            $response = $this->tenantApi->invitations();
        } catch (MobileApiException $exception) {
            $this->workspaceError = $exception->getMessage();
            $this->showToast($this->workspaceError, 'error');

            return;
        }

        $this->invitations = $this->normalizeInvitations($response['data']['invitations'] ?? []);
        $this->invitationsLoaded = true;
        $this->workspaceStatus = count($this->invitations) > 0 ? 'Pending invitations refreshed.' : 'No pending invitations.';
        $this->showToast($this->workspaceStatus, 'success');
    }

    public function switchTenant(): void
    {
        $this->clearFeedback();

        $this->validate([
            'selectedTenantId' => ['required', 'string', 'max:80'],
        ]);

        try {
            $this->tenantApi->switch((string) $this->selectedTenantId);
            $this->bootstrap->refresh();
            $this->syncSelectedTenantFromContext();
        } catch (MobileApiException $exception) {
            $this->workspaceError = $exception->getMessage();
            $this->showToast($this->workspaceError, 'error');

            return;
        }

        $this->workspaceStatus = 'Workspace switched.';
        $this->showToast($this->workspaceStatus, 'success');
    }

    public function acceptInvitation(string $tenantId): void
    {
        $this->respondToInvitation($tenantId, 'accept');
    }

    public function declineInvitation(string $tenantId): void
    {
        $this->respondToInvitation($tenantId, 'decline');
    }

    public function render(): View
    {
        $context = $this->tenantContext->context();

        return view('livewire.mobile.settings.workspace', [
            'currentTenant' => $context['current_tenant'],
            'availableTenants' => $context['available_tenants'],
            'cachedAt' => $context['cached_at'],
            'invitations' => $this->invitations,
            'invitationsLoaded' => $this->invitationsLoaded,
        ]);
    }

    private function clearFeedback(): void
    {
        $this->workspaceStatus = null;
        $this->workspaceError = null;
        $this->toastMessage = null;
    }

    private function showToast(string $message, string $variant): void
    {
        $this->toastMessage = $message;
        $this->toastVariant = $variant;
    }

    private function respondToInvitation(string $tenantId, string $decision): void
    {
        $this->clearFeedback();

        if (trim($tenantId) === '') {
            $this->workspaceError = 'Invitation is not available.';
            $this->showToast($this->workspaceError, 'error');

            return;
        }

        try {
            $response = $decision === 'accept'
                ? $this->tenantApi->acceptInvitation($tenantId)
                : $this->tenantApi->declineInvitation($tenantId);

            $this->removeInvitation($tenantId);

            if (($response['data']['next_bootstrap_required'] ?? false) === true) {
                $this->bootstrap->refresh();
                $this->syncSelectedTenantFromContext();
            }
        } catch (MobileApiException $exception) {
            $this->workspaceError = $exception->getMessage();
            $this->showToast($this->workspaceError, 'error');

            return;
        }

        $this->invitationsLoaded = true;
        $this->workspaceStatus = $decision === 'accept' ? 'Invitation accepted.' : 'Invitation declined.';
        $this->showToast($this->workspaceStatus, 'success');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function normalizeInvitations(mixed $invitations): array
    {
        if (! is_array($invitations)) {
            return [];
        }

        return array_values(array_filter(
            $invitations,
            static fn (mixed $invitation): bool => is_array($invitation),
        ));
    }

    private function removeInvitation(string $tenantId): void
    {
        $this->invitations = array_values(array_filter(
            $this->invitations,
            static fn (array $invitation): bool => ($invitation['tenant']['id'] ?? null) !== $tenantId,
        ));
    }

    private function syncSelectedTenantFromContext(): void
    {
        $currentTenant = $this->tenantContext->currentTenant();
        $tenantId = is_array($currentTenant) ? ($currentTenant['id'] ?? null) : null;

        $this->selectedTenantId = is_string($tenantId) && trim($tenantId) !== '' ? $tenantId : null;
    }
}
