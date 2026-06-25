<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web'])
    ->prefix('dev')
    ->name('dev.')
    ->group(function (): void {
        Route::view('/tailwind', 'mobile.tailwind-test')->name('tailwind');
    });
