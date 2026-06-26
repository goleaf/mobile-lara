<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAccess\MobileAccessPolicy;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Search')]
class Search extends Component
{
    public string $query = '';

    public bool $hasNetworkError = false;

    /**
     * @var list<array{title: string, route: string, description: string, feature: string, permission?: string}>
     */
    public array $availableResults = [
        ['title' => 'Dashboard', 'route' => 'mobile.dashboard', 'description' => 'Mobile overview', 'feature' => 'settings'],
        ['title' => 'Profile', 'route' => 'mobile.profile', 'description' => 'Account details', 'feature' => 'profile'],
        ['title' => 'Records', 'route' => 'mobile.records.index', 'description' => 'Local-first cached records', 'feature' => 'records', 'permission' => 'records.view'],
        ['title' => 'Settings', 'route' => 'mobile.settings', 'description' => 'App preferences', 'feature' => 'settings'],
        ['title' => 'Debug', 'route' => 'mobile.debug', 'description' => 'Stack diagnostics', 'feature' => 'diagnostics'],
    ];

    private MobileAccessPolicy $accessPolicy;

    public function boot(MobileAccessPolicy $accessPolicy): void
    {
        $this->accessPolicy = $accessPolicy;
    }

    public function search(): void
    {
        $this->hasNetworkError = false;
    }

    public function retrySearch(): void
    {
        $this->hasNetworkError = false;
    }

    public function render(): View
    {
        return view('livewire.mobile.search', [
            'results' => $this->filteredResults(),
        ]);
    }

    /**
     * @return list<array{title: string, route: string, description: string, feature: string, permission?: string}>
     */
    private function filteredResults(): array
    {
        $query = mb_strtolower(trim($this->query));
        $results = $this->accessPolicy->filterActions($this->availableResults);

        if ($query === '') {
            return $results;
        }

        return array_values(array_filter(
            $results,
            fn (array $result): bool => str_contains(mb_strtolower($result['title']), $query)
                || str_contains(mb_strtolower($result['description']), $query),
        ));
    }
}
