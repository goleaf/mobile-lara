<?php

use App\Livewire\Mobile\RecordCategories;
use App\Models\MobileLocalCategory;
use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\CategoryRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-record-categories.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('category repository seeds editable default categories', function (): void {
    $repository = app(CategoryRepository::class);

    expect($repository->options())->toBe([
        '1' => 'General',
        '2' => 'Work',
        '3' => 'Client',
        '4' => 'Field',
        '5' => 'Support',
    ])
        ->and(MobileLocalCategory::query()->count())->toBe(5)
        ->and($repository->labelFor(4))->toBe('Field');
});

test('category management screen creates edits deletes and shows record counts', function (): void {
    $category = MobileLocalCategory::factory()->create([
        'label' => 'Existing',
        'slug' => 'existing',
        'color' => '#64748b',
        'sort_order' => 10,
    ]);

    MobileLocalRecord::factory()->create([
        'category_id' => $category->id,
        'title' => 'Categorized record',
    ]);

    Livewire::test(RecordCategories::class)
        ->assertSee('Record categories')
        ->assertSee('Existing')
        ->assertSee('1 record')
        ->assertSee('wire:confirm="Delete this category and leave matching records uncategorized?"', false)
        ->set('label', 'Field Visits')
        ->set('color', '#14b8a6')
        ->set('sortOrder', '20')
        ->call('saveCategory')
        ->assertHasNoErrors()
        ->assertDispatched('mobile-toast')
        ->assertSee('Field Visits')
        ->call('editCategory', MobileLocalCategory::query()->where('slug', 'field-visits')->value('id'))
        ->assertSet('label', 'Field Visits')
        ->assertSet('color', '#14b8a6')
        ->set('label', 'Priority Field')
        ->set('color', '#f97316')
        ->call('saveCategory')
        ->assertHasNoErrors()
        ->assertSee('Priority Field')
        ->call('reorderPlaceholder')
        ->assertDispatched('mobile-toast')
        ->call('deleteCategory', MobileLocalCategory::query()->where('slug', 'priority-field')->value('id'))
        ->assertDispatched('mobile-toast')
        ->assertDontSee('Priority Field');

    expect(MobileLocalCategory::query()->where('slug', 'priority-field')->exists())->toBeFalse()
        ->and(MobileLocalRecord::query()->where('title', 'Categorized record')->value('category_id'))->toBe($category->id);
});

test('category management form validates required label color and sort order', function (): void {
    Livewire::test(RecordCategories::class)
        ->set('label', '')
        ->set('color', 'blue')
        ->set('sortOrder', '-1')
        ->call('saveCategory')
        ->assertHasErrors(['label', 'color', 'sortOrder']);
});

test('deleting a category leaves matching records uncategorized', function (): void {
    $category = MobileLocalCategory::factory()->create([
        'label' => 'Client',
        'slug' => 'client',
    ]);

    $record = MobileLocalRecord::factory()->create([
        'category_id' => $category->id,
    ]);

    Livewire::test(RecordCategories::class)
        ->call('deleteCategory', $category->id)
        ->assertDispatched('mobile-toast');

    expect($category->fresh())->toBeNull()
        ->and($record->fresh()?->category_id)->toBeNull();
});
