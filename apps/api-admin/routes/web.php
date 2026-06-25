<?php

use App\Livewire\Admin\Dashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->group(function (): void {
        Route::redirect('/', '/admin/dashboard')->name('home');

        Route::prefix('admin')
            ->name('admin.')
            ->group(function (): void {
                Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
            });
    });
