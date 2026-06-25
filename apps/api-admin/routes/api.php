<?php

use App\Http\Controllers\Api\V1\Mobile\StatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        Route::prefix('mobile')
            ->name('mobile.')
            ->group(function (): void {
                Route::get('/status', StatusController::class)->name('status');
            });
    });
