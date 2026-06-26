<?php

namespace App\Livewire\Admin;

use App\Models\MobileDiagnosticReport;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Mobile Diagnostics')]
final class MobileDiagnosticReports extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $selectedReportId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function selectReport(int $reportId): void
    {
        $report = MobileDiagnosticReport::query()
            ->forAdminDetail()
            ->findOrFail($reportId);

        Gate::authorize('view', $report);

        $this->selectedReportId = $report->id;
    }

    public function clearSelectedReport(): void
    {
        $this->selectedReportId = null;
    }

    public function render(): View
    {
        Gate::authorize('viewAny', MobileDiagnosticReport::class);

        return view('livewire.admin.mobile-diagnostic-reports', [
            'reports' => MobileDiagnosticReport::query()
                ->forAdminIndex()
                ->matchingAdminSearch($this->search)
                ->paginate(10)
                ->withQueryString(),
            'summary' => $this->summary(),
            'selectedReport' => $this->selectedReport(),
        ]);
    }

    /**
     * @return array{total: int, recent: int, failed_sync: int}
     */
    private function summary(): array
    {
        return [
            'total' => MobileDiagnosticReport::query()->count(),
            'recent' => MobileDiagnosticReport::query()
                ->where('received_at', '>=', now()->subDay())
                ->count(),
            'failed_sync' => MobileDiagnosticReport::query()
                ->where('failed_sync_actions_count', '>', 0)
                ->count(),
        ];
    }

    private function selectedReport(): ?MobileDiagnosticReport
    {
        if ($this->selectedReportId === null) {
            return null;
        }

        $report = MobileDiagnosticReport::query()
            ->forAdminDetail()
            ->find($this->selectedReportId);

        if (! $report instanceof MobileDiagnosticReport) {
            $this->selectedReportId = null;

            return null;
        }

        Gate::authorize('view', $report);

        return $report;
    }

    /**
     * @return array{network: string, sync: string, feature_version: string, config_version: string, device: string}
     */
    public function snapshotSummary(MobileDiagnosticReport $report): array
    {
        $snapshot = is_array($report->snapshot) ? $report->snapshot : [];

        $pending = $this->integerValue(Arr::get($snapshot, 'sync.pending_actions'));
        $failed = $this->integerValue(Arr::get($snapshot, 'sync.failed_actions'));
        $conflicts = $this->integerValue(Arr::get($snapshot, 'sync.conflict_actions'));
        $deviceModel = $this->stringValue(Arr::get($snapshot, 'device.device_model'), 'Unknown device');
        $osVersion = $this->stringValue(Arr::get($snapshot, 'device.os_version'), 'Unknown OS');

        return [
            'network' => $this->stringValue(Arr::get($snapshot, 'network.state'), 'Unknown'),
            'sync' => "{$pending} pending / {$failed} failed / {$conflicts} conflicts",
            'feature_version' => $this->stringValue(Arr::get($snapshot, 'features.version'), 'none'),
            'config_version' => $this->stringValue(Arr::get($snapshot, 'remote_config.version'), 'none'),
            'device' => "{$deviceModel} / {$osVersion}",
        ];
    }

    public function snapshotJson(MobileDiagnosticReport $report): string
    {
        $snapshot = is_array($report->snapshot) ? $report->snapshot : [];

        return json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    private function integerValue(mixed $value): int
    {
        if (is_int($value)) {
            return max(0, $value);
        }

        if (is_numeric($value)) {
            return max(0, (int) $value);
        }

        return 0;
    }

    private function stringValue(mixed $value, string $fallback): string
    {
        if (! is_scalar($value)) {
            return $fallback;
        }

        $value = trim((string) $value);

        return $value === '' ? $fallback : $value;
    }
}
