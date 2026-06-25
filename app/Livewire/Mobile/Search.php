<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Search')]
class Search extends Component
{
    public string $query = '';

    public bool $hasNetworkError = false;

    /**
     * @var array<int, array{title: string, route: string, description: string}>
     */
    public array $availableResults = [
        ['title' => 'Dashboard', 'route' => 'mobile.dashboard', 'description' => 'Mobile overview'],
        ['title' => 'Profile', 'route' => 'mobile.profile', 'description' => 'Account details'],
        ['title' => 'Settings', 'route' => 'mobile.settings', 'description' => 'App preferences'],
        ['title' => 'Debug', 'route' => 'mobile.debug', 'description' => 'Stack diagnostics'],
    ];

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
     * @return array<int, array{title: string, route: string, description: string}>
     */
    private function filteredResults(): array
    {
        $query = mb_strtolower(trim($this->query));

        if ($query === '') {
            return $this->availableResults;
        }

        return array_values(array_filter(
            $this->availableResults,
            fn (array $result): bool => str_contains(mb_strtolower($result['title']), $query)
                || str_contains(mb_strtolower($result['description']), $query),
        ));
    }
}
