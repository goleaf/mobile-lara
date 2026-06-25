<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\SaveMobileRemoteConfigAction;
use App\Models\MobileRemoteConfig;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Remote Config')]
final class RemoteConfigs extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $editingConfigId = null;

    /**
     * @var array{key: string, value_json: string, version: string, description: string, is_sensitive: bool, confirmed: bool}
     */
    public array $form = [
        'key' => '',
        'value_json' => "{\n    \"enabled\": true\n}",
        'version' => 'global-default',
        'description' => '',
        'is_sensitive' => false,
        'confirmed' => false,
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $configId): void
    {
        $config = MobileRemoteConfig::query()
            ->select([
                'id',
                'key',
                'category',
                'value',
                'version',
                'description',
                'is_sensitive',
                'metadata',
            ])
            ->findOrFail($configId);

        $this->editingConfigId = $config->id;
        $this->form = [
            'key' => $config->key,
            'value_json' => $this->prettyJson($config->value),
            'version' => $config->version,
            'description' => $config->description ?? '',
            'is_sensitive' => $config->is_sensitive,
            'confirmed' => false,
        ];

        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function save(): void
    {
        $user = auth()->user();

        abort_unless($user instanceof User && $user->is_platform_admin, 403);

        /** @var array{form: array<string, mixed>} $validated */
        $validated = $this->validate($this->rules(), attributes: $this->validationAttributes());

        $config = $this->editingConfigId === null
            ? null
            : MobileRemoteConfig::query()
                ->select([
                    'id',
                    'key',
                    'category',
                    'value',
                    'version',
                    'description',
                    'is_sensitive',
                    'metadata',
                ])
                ->findOrFail($this->editingConfigId);

        app(SaveMobileRemoteConfigAction::class)->handle($validated['form'], $user, request(), $config);

        session()->flash('status', 'Remote config saved.');

        $this->resetForm();
        $this->resetPage();
    }

    public function restoreFromAudit(int $auditEventId): void
    {
        $user = auth()->user();

        abort_unless($user instanceof User && $user->is_platform_admin, 403);

        $event = SecurityAuditEvent::query()
            ->select(['id', 'event', 'metadata', 'created_at'])
            ->whereIn('event', $this->auditEventNames())
            ->findOrFail($auditEventId);

        $snapshot = $event->metadata['before'] ?? null;

        if (! is_array($snapshot)) {
            session()->flash('status', 'No restorable config snapshot exists for that audit event.');

            return;
        }

        app(SaveMobileRemoteConfigAction::class)->restore($snapshot, $user, request(), $event);

        session()->flash('status', 'Remote config restored from audit history.');

        $this->resetForm();
        $this->resetPage();
    }

    public function configTone(MobileRemoteConfig $config): string
    {
        return $config->is_sensitive ? 'danger' : 'success';
    }

    public function render(): View
    {
        return view('livewire.admin.remote-configs', [
            'configs' => MobileRemoteConfig::query()
                ->forAdminIndex()
                ->matchingAdminSearch($this->search)
                ->paginate(10)
                ->withQueryString(),
            'summary' => $this->summary(),
            'impactPreview' => $this->impactPreview(),
            'auditEvents' => $this->auditEvents(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.key' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:_[a-z0-9]+)*$/',
                Rule::unique('mobile_remote_configs', 'key')->ignore($this->editingConfigId),
            ],
            'form.value_json' => [
                'required',
                'string',
                'max:4000',
                'json',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $decoded = $this->decodedValue($value);

                    if ($decoded === null || array_is_list($decoded)) {
                        $fail('The config value must be a JSON object.');
                    }
                },
            ],
            'form.version' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/',
            ],
            'form.description' => ['nullable', 'string', 'max:255'],
            'form.is_sensitive' => ['boolean'],
            'form.confirmed' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.key' => 'config key',
            'form.value_json' => 'config JSON',
            'form.version' => 'config version',
            'form.description' => 'description',
            'form.is_sensitive' => 'sensitive flag',
            'form.confirmed' => 'confirmation',
        ];
    }

    private function resetForm(): void
    {
        $this->editingConfigId = null;
        $this->form = [
            'key' => '',
            'value_json' => "{\n    \"enabled\": true\n}",
            'version' => 'global-default',
            'description' => '',
            'is_sensitive' => false,
            'confirmed' => false,
        ];
    }

    /**
     * @return array{total: int, exposed: int, sensitive: int}
     */
    private function summary(): array
    {
        return [
            'total' => MobileRemoteConfig::query()->count(),
            'exposed' => MobileRemoteConfig::query()
                ->where('category', 'mobile')
                ->where('is_sensitive', false)
                ->count(),
            'sensitive' => MobileRemoteConfig::query()->where('is_sensitive', true)->count(),
        ];
    }

    /**
     * @return array{tone: string, headline: string, detail: string, keys: string}
     */
    private function impactPreview(): array
    {
        $decoded = $this->decodedValue($this->form['value_json']);

        if ($decoded === null || array_is_list($decoded)) {
            return [
                'tone' => 'danger',
                'headline' => 'Config JSON is not mobile-safe yet.',
                'detail' => 'Remote config values must be JSON objects so mobile can merge them with foundation defaults.',
                'keys' => 'none',
            ];
        }

        if ((bool) $this->form['is_sensitive']) {
            return [
                'tone' => 'danger',
                'headline' => 'Sensitive values are stored but not exposed.',
                'detail' => 'The resolver excludes sensitive config from mobile bootstrap and the /config endpoint.',
                'keys' => implode(', ', array_keys($decoded)),
            ];
        }

        return [
            'tone' => 'success',
            'headline' => 'Mobile clients receive this global default.',
            'detail' => 'The value is merged over foundation defaults and below tenant overrides for the matching config key.',
            'keys' => implode(', ', array_keys($decoded)),
        ];
    }

    /**
     * @return Collection<int, SecurityAuditEvent>
     */
    private function auditEvents(): Collection
    {
        return SecurityAuditEvent::query()
            ->select(['id', 'user_id', 'event', 'severity', 'metadata', 'created_at'])
            ->with('user:id,name,email')
            ->whereIn('event', $this->auditEventNames())
            ->latest()
            ->limit(8)
            ->get();
    }

    /**
     * @return array<int, string>
     */
    private function auditEventNames(): array
    {
        return [
            'admin_mobile_remote_config_created',
            'admin_mobile_remote_config_updated',
            'admin_mobile_remote_config_restored',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodedValue(mixed $value): ?array
    {
        $decoded = json_decode(is_string($value) ? $value : '', true);

        return is_array($decoded) ? $decoded : null;
    }

    private function prettyJson(mixed $value): string
    {
        $encoded = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return is_string($encoded) ? $encoded : '{}';
    }
}
