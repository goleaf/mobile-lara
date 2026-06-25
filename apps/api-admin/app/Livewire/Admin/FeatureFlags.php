<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\SaveMobileFeatureFlagAction;
use App\Enums\MobileFeatureState;
use App\Models\MobileFeatureFlag;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Feature Flags')]
final class FeatureFlags extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $editingFeatureFlagId = null;

    /**
     * @var array{key: string, name: string, default_state: string, reason: string, message: string, minimum_app_version: string, offline_behavior: string}
     */
    public array $form = [
        'key' => '',
        'name' => '',
        'default_state' => 'disabled',
        'reason' => '',
        'message' => '',
        'minimum_app_version' => '',
        'offline_behavior' => 'online_only',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function stateTone(string $state): string
    {
        return match ($state) {
            MobileFeatureState::Visible->value,
            MobileFeatureState::Beta->value,
            MobileFeatureState::Deprecated->value,
            MobileFeatureState::OfflineLimited->value => 'success',
            MobileFeatureState::EmergencyDisabled->value,
            MobileFeatureState::Blocked->value,
            MobileFeatureState::UpdateRequired->value => 'danger',
            default => 'neutral',
        };
    }

    public function edit(int $featureFlagId): void
    {
        $featureFlag = MobileFeatureFlag::query()
            ->select([
                'id',
                'key',
                'name',
                'default_state',
                'reason',
                'message',
                'minimum_app_version',
                'offline_behavior',
            ])
            ->findOrFail($featureFlagId);

        $this->editingFeatureFlagId = $featureFlag->id;
        $this->form = [
            'key' => $featureFlag->key,
            'name' => $featureFlag->name,
            'default_state' => $featureFlag->default_state->value,
            'reason' => $featureFlag->reason ?? '',
            'message' => $featureFlag->message ?? '',
            'minimum_app_version' => $featureFlag->minimum_app_version ?? '',
            'offline_behavior' => $featureFlag->offline_behavior,
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

        /** @var array{form: array{key: string, name: string, default_state: string, reason?: string|null, message?: string|null, minimum_app_version?: string|null, offline_behavior: string}} $validated */
        $validated = $this->validate($this->rules(), attributes: $this->validationAttributes());

        $featureFlag = $this->editingFeatureFlagId === null
            ? null
            : MobileFeatureFlag::query()
                ->select([
                    'id',
                    'key',
                    'name',
                    'default_state',
                    'reason',
                    'message',
                    'minimum_app_version',
                    'offline_behavior',
                    'metadata',
                ])
                ->findOrFail($this->editingFeatureFlagId);

        app(SaveMobileFeatureFlagAction::class)->handle($validated['form'], $user, request(), $featureFlag);

        session()->flash('status', 'Feature flag saved.');

        $this->resetForm();
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.admin.feature-flags', [
            'featureFlags' => MobileFeatureFlag::query()
                ->forAdminIndex()
                ->matchingAdminSearch($this->search)
                ->paginate(10)
                ->withQueryString(),
            'stateOptions' => $this->stateOptions(),
            'offlineBehaviorOptions' => $this->offlineBehaviorOptions(),
            'summary' => $this->summary(),
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
                Rule::unique('mobile_feature_flags', 'key')->ignore($this->editingFeatureFlagId),
            ],
            'form.name' => ['required', 'string', 'max:120'],
            'form.default_state' => ['required', Rule::in($this->stateValues())],
            'form.reason' => ['nullable', 'string', 'max:120'],
            'form.message' => ['nullable', 'string', 'max:240'],
            'form.minimum_app_version' => ['nullable', 'string', 'max:40'],
            'form.offline_behavior' => ['required', Rule::in(array_keys($this->offlineBehaviorOptions()))],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.key' => 'feature key',
            'form.name' => 'feature name',
            'form.default_state' => 'default state',
            'form.reason' => 'reason',
            'form.message' => 'mobile message',
            'form.minimum_app_version' => 'minimum app version',
            'form.offline_behavior' => 'offline behavior',
        ];
    }

    private function resetForm(): void
    {
        $this->editingFeatureFlagId = null;
        $this->form = [
            'key' => '',
            'name' => '',
            'default_state' => MobileFeatureState::Disabled->value,
            'reason' => '',
            'message' => '',
            'minimum_app_version' => '',
            'offline_behavior' => 'online_only',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function stateOptions(): array
    {
        return collect(MobileFeatureState::cases())
            ->mapWithKeys(static fn (MobileFeatureState $state): array => [
                $state->value => str($state->value)->replace('_', ' ')->title()->toString(),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function stateValues(): array
    {
        return array_keys($this->stateOptions());
    }

    /**
     * @return array<string, string>
     */
    private function offlineBehaviorOptions(): array
    {
        return [
            'online_only' => 'Online only',
            'queueable' => 'Queueable',
            'queue_local_only' => 'Queue local only',
            'device_local' => 'Device local',
            'read_only_cache' => 'Read-only cache',
        ];
    }

    /**
     * @return array{total: int, enabled: int, disabled: int}
     */
    private function summary(): array
    {
        return [
            'total' => MobileFeatureFlag::query()->count(),
            'enabled' => MobileFeatureFlag::query()
                ->whereIn('default_state', [
                    MobileFeatureState::Visible->value,
                    MobileFeatureState::Beta->value,
                    MobileFeatureState::Deprecated->value,
                    MobileFeatureState::OfflineLimited->value,
                ])
                ->count(),
            'disabled' => MobileFeatureFlag::query()
                ->whereIn('default_state', [
                    MobileFeatureState::Hidden->value,
                    MobileFeatureState::Disabled->value,
                    MobileFeatureState::Blocked->value,
                    MobileFeatureState::UpdateRequired->value,
                    MobileFeatureState::EmergencyDisabled->value,
                ])
                ->count(),
        ];
    }
}
