<?php

namespace App\Livewire\Admin;

use App\Actions\Records\ArchiveTenantRecordAction;
use App\Actions\Records\RestoreTenantRecordAction;
use App\Actions\Records\SaveTenantRecordAction;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Records Management')]
final class TenantRecords extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $tenantId = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $archived = 'active';

    public ?int $selectedRecordId = null;

    public ?int $editingRecordId = null;

    public bool $isCreating = false;

    /**
     * @var array{tenant_id: string, title: string, description: string, status: string, priority: string, category_name: string, tags: string, note: string}
     */
    public array $form = [
        'tenant_id' => '',
        'title' => '',
        'description' => '',
        'status' => TenantRecord::STATUS_ACTIVE,
        'priority' => TenantRecord::PRIORITY_NORMAL,
        'category_name' => '',
        'tags' => '',
        'note' => '',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTenantId(): void
    {
        $this->tenantId = $this->tenantId === '' ? '' : (string) (int) $this->tenantId;
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->status = in_array($this->status, TenantRecord::statuses(), true) ? $this->status : '';
        $this->resetPage();
    }

    public function updatedArchived(): void
    {
        if (! in_array($this->archived, ['active', 'with', 'only'], true)) {
            $this->archived = 'active';
        }

        $this->resetPage();
    }

    public function createRecord(): void
    {
        Gate::authorize('create', TenantRecord::class);

        $this->selectedRecordId = null;
        $this->editingRecordId = null;
        $this->isCreating = true;
        $this->resetForm();

        if ($this->tenantId !== '') {
            $this->form['tenant_id'] = $this->tenantId;
        }
    }

    public function selectRecord(int $recordId): void
    {
        $record = TenantRecord::query()
            ->forAdminDetail()
            ->findOrFail($recordId);

        Gate::authorize('view', $record);

        $this->selectedRecordId = $record->id;
        $this->editingRecordId = null;
        $this->isCreating = false;
        $this->resetForm();
    }

    public function editRecord(int $recordId): void
    {
        $record = TenantRecord::query()
            ->forAdminDetail()
            ->findOrFail($recordId);

        Gate::authorize('update', $record);

        $this->selectedRecordId = $record->id;
        $this->editingRecordId = $record->id;
        $this->isCreating = false;
        $this->form = $this->formFromRecord($record);
        $this->resetValidation();
    }

    public function clearPanel(): void
    {
        $this->selectedRecordId = null;
        $this->editingRecordId = null;
        $this->isCreating = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function save(SaveTenantRecordAction $records): void
    {
        /** @var User|null $admin */
        $admin = Auth::user();

        abort_unless($admin instanceof User, 403);

        /** @var array{form: array{tenant_id: string, title: string, description?: string|null, status: string, priority: string, category_name?: string|null, tags?: string|null, note?: string|null}} $validated */
        $validated = $this->validate($this->rules(), attributes: $this->validationAttributes());

        if ($this->editingRecordId !== null) {
            $record = TenantRecord::query()
                ->forAdminDetail()
                ->findOrFail($this->editingRecordId);

            Gate::authorize('update', $record);

            $tenant = Tenant::query()
                ->select(['id', 'public_id', 'name'])
                ->findOrFail($record->tenant_id);

            $savedRecord = $records->update(
                $record,
                $this->payloadFromForm($validated['form']),
                $tenant,
                $admin,
                request(),
                source: 'admin_panel',
                auditPrefix: 'admin',
            );
        } else {
            Gate::authorize('create', TenantRecord::class);

            $tenant = Tenant::query()
                ->select(['id', 'public_id', 'name'])
                ->findOrFail((int) $validated['form']['tenant_id']);

            $savedRecord = $records->create(
                $this->payloadFromForm($validated['form']),
                $tenant,
                $admin,
                request(),
                source: 'admin_panel',
                auditPrefix: 'admin',
            );
        }

        $this->selectedRecordId = $savedRecord->id;
        $this->editingRecordId = null;
        $this->isCreating = false;
        $this->resetForm();
        $this->dispatch('admin-notify', type: 'success', message: 'Record saved.');
    }

    public function archiveSelected(ArchiveTenantRecordAction $records): void
    {
        $record = $this->selectedRecord();

        if (! $record instanceof TenantRecord) {
            return;
        }

        Gate::authorize('update', $record);

        /** @var User|null $admin */
        $admin = Auth::user();

        abort_unless($admin instanceof User, 403);

        $tenant = Tenant::query()
            ->select(['id', 'public_id', 'name'])
            ->findOrFail($record->tenant_id);

        $records->archive($record, $tenant, $admin, request(), source: 'admin_panel', auditPrefix: 'admin');

        $this->selectedRecordId = $record->id;
        $this->dispatch('admin-notify', type: 'success', message: 'Record archived.');
    }

    public function restoreSelected(RestoreTenantRecordAction $records): void
    {
        $record = $this->selectedRecord();

        if (! $record instanceof TenantRecord) {
            return;
        }

        Gate::authorize('restore', $record);

        /** @var User|null $admin */
        $admin = Auth::user();

        abort_unless($admin instanceof User, 403);

        $tenant = Tenant::query()
            ->select(['id', 'public_id', 'name'])
            ->findOrFail($record->tenant_id);

        $records->restore($record, $tenant, $admin, request(), source: 'admin_panel', auditPrefix: 'admin');

        $this->selectedRecordId = $record->id;
        $this->dispatch('admin-notify', type: 'success', message: 'Record restored.');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', TenantRecord::class);

        return view('livewire.admin.tenant-records', [
            'archiveOptions' => $this->archiveOptions(),
            'priorityOptions' => $this->priorityOptions(),
            'records' => $this->recordsQuery()
                ->paginate(10)
                ->withQueryString(),
            'selectedRecord' => $this->selectedRecord(),
            'statusOptions' => $this->statusOptions(),
            'summary' => $this->summary(),
            'tenants' => $this->tenantOptions(),
        ]);
    }

    public function statusTone(string $status): string
    {
        return match ($status) {
            TenantRecord::STATUS_ACTIVE => 'success',
            TenantRecord::STATUS_REVIEW => 'warning',
            TenantRecord::STATUS_DONE => 'neutral',
            TenantRecord::STATUS_DRAFT => 'neutral',
            default => 'neutral',
        };
    }

    public function priorityTone(string $priority): string
    {
        return match ($priority) {
            TenantRecord::PRIORITY_HIGH => 'warning',
            TenantRecord::PRIORITY_URGENT => 'danger',
            default => 'neutral',
        };
    }

    public function countLabel(int $count, string $singular): string
    {
        return $count.' '.$singular.($count === 1 ? '' : 's');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'form.title' => ['required', 'string', 'max:255'],
            'form.description' => ['nullable', 'string', 'max:10000'],
            'form.status' => ['required', 'string', Rule::in(TenantRecord::statuses())],
            'form.priority' => ['required', 'string', Rule::in(TenantRecord::priorities())],
            'form.category_name' => ['nullable', 'string', 'max:120'],
            'form.tags' => ['nullable', 'string', 'max:500'],
            'form.note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.tenant_id' => 'tenant',
            'form.title' => 'title',
            'form.description' => 'description',
            'form.status' => 'status',
            'form.priority' => 'priority',
            'form.category_name' => 'category name',
            'form.tags' => 'tags',
            'form.note' => 'note',
        ];
    }

    /**
     * @return Builder<TenantRecord>
     */
    private function recordsQuery(): Builder
    {
        return TenantRecord::query()
            ->forAdminIndex()
            ->matchingAdminSearch($this->search)
            ->forStatus($this->status)
            ->archivedFilter($this->archived === 'active' ? null : $this->archived)
            ->when($this->tenantId !== '', function (Builder $query): void {
                $query->where('tenant_id', (int) $this->tenantId);
            });
    }

    /**
     * @return array{total: int, active: int, archived: int, review: int}
     */
    private function summary(): array
    {
        return [
            'total' => TenantRecord::query()->count(),
            'active' => TenantRecord::query()->whereNull('archived_at')->count(),
            'archived' => TenantRecord::query()->whereNotNull('archived_at')->count(),
            'review' => TenantRecord::query()->where('status', TenantRecord::STATUS_REVIEW)->count(),
        ];
    }

    /**
     * @return Collection<int, Tenant>
     */
    private function tenantOptions(): Collection
    {
        return Tenant::query()
            ->forAdminOptions()
            ->limit(200)
            ->get();
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return collect(TenantRecord::statuses())
            ->mapWithKeys(fn (string $status): array => [
                $status => str($status)->replace('_', ' ')->title()->toString(),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function priorityOptions(): array
    {
        return collect(TenantRecord::priorities())
            ->mapWithKeys(fn (string $priority): array => [
                $priority => str($priority)->title()->toString(),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function archiveOptions(): array
    {
        return [
            'active' => 'Active only',
            'with' => 'Include archived',
            'only' => 'Archived only',
        ];
    }

    private function selectedRecord(): ?TenantRecord
    {
        if ($this->selectedRecordId === null) {
            return null;
        }

        $record = TenantRecord::query()
            ->forAdminDetail()
            ->find($this->selectedRecordId);

        if (! $record instanceof TenantRecord) {
            $this->clearPanel();

            return null;
        }

        Gate::authorize('view', $record);

        return $record;
    }

    /**
     * @return array{tenant_id: string, title: string, description: string, status: string, priority: string, category_name: string, tags: string, note: string}
     */
    private function formFromRecord(TenantRecord $record): array
    {
        return [
            'tenant_id' => (string) $record->tenant_id,
            'title' => $record->title,
            'description' => (string) ($record->description ?? ''),
            'status' => $record->status,
            'priority' => $record->priority,
            'category_name' => (string) ($record->category?->name ?? ''),
            'tags' => $record->tags->pluck('name')->implode(', '),
            'note' => '',
        ];
    }

    private function resetForm(): void
    {
        $this->form = [
            'tenant_id' => '',
            'title' => '',
            'description' => '',
            'status' => TenantRecord::STATUS_ACTIVE,
            'priority' => TenantRecord::PRIORITY_NORMAL,
            'category_name' => '',
            'tags' => '',
            'note' => '',
        ];
    }

    /**
     * @param  array{tenant_id: string, title: string, description?: string|null, status: string, priority: string, category_name?: string|null, tags?: string|null, note?: string|null}  $form
     * @return array<string, mixed>
     */
    private function payloadFromForm(array $form): array
    {
        $categoryName = $this->nullableString($form['category_name'] ?? null);

        return [
            'title' => $form['title'],
            'description' => $this->nullableString($form['description'] ?? null),
            'status' => $form['status'],
            'priority' => $form['priority'],
            'category' => $categoryName === null ? [] : ['name' => $categoryName],
            'tags' => $this->tagsFromString($form['tags'] ?? ''),
            'note' => $this->nullableString($form['note'] ?? null),
        ];
    }

    /**
     * @return list<string>
     */
    private function tagsFromString(?string $tags): array
    {
        return collect(explode(',', (string) $tags))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
