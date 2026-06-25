<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\MobileLocal\CategoryRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Record categories')]
class RecordCategories extends Component
{
    use DispatchesToasts;

    public string $label = '';

    public string $color = '#64748b';

    public string $sortOrder = '';

    public ?int $editingCategoryId = null;

    public ?string $storageError = null;

    private CategoryRepository $categories;

    public function boot(CategoryRepository $categories): void
    {
        $this->categories = $categories;
    }

    public function saveCategory(): void
    {
        $validated = $this->validate();

        try {
            if ($this->editingCategoryId === null) {
                $this->categories->create(
                    label: $validated['label'],
                    color: $validated['color'],
                    sortOrder: $this->validatedSortOrder($validated['sortOrder']),
                );

                $this->toastSuccess('Category created locally.', 'Category created');
            } else {
                $category = $this->categories->find($this->editingCategoryId);

                $this->categories->update(
                    category: $category,
                    label: $validated['label'],
                    color: $validated['color'],
                    sortOrder: $this->validatedSortOrder($validated['sortOrder']),
                );

                $this->toastSuccess('Category updated locally.', 'Category updated');
            }
        } catch (ModelNotFoundException) {
            $this->toastWarning('Category is no longer available on this device.', 'Category unavailable');

            $this->cancelEdit();

            return;
        } catch (QueryException) {
            $this->storageError = 'Category storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Category not saved');

            return;
        }

        $this->cancelEdit();
    }

    public function editCategory(int $categoryId): void
    {
        try {
            $category = $this->categories->find($categoryId);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Category is no longer available on this device.', 'Edit unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Category storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Edit unavailable');

            return;
        }

        $this->editingCategoryId = $category->id;
        $this->label = $category->label;
        $this->color = $category->color;
        $this->sortOrder = (string) $category->sort_order;
    }

    public function deleteCategory(int $categoryId): void
    {
        try {
            $category = $this->categories->find($categoryId);
            $deleted = $this->categories->delete($category);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Category is no longer available on this device.', 'Delete unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Category storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Delete unavailable');

            return;
        }

        if (! $deleted) {
            $this->toastWarning('Category could not be deleted from this device.', 'Delete unavailable');

            return;
        }

        if ($this->editingCategoryId === $categoryId) {
            $this->cancelEdit();
        }

        $this->toastSuccess('Category deleted locally. Existing records were left uncategorized.', 'Category deleted');
    }

    public function cancelEdit(): void
    {
        $this->resetValidation();
        $this->editingCategoryId = null;
        $this->label = '';
        $this->color = '#64748b';
        $this->sortOrder = '';
    }

    public function reorderPlaceholder(): void
    {
        $this->toastInfo('Reorder controls are reserved for the drag-and-drop implementation.', 'Reorder pending');
    }

    public function refreshCategories(): void
    {
        $this->storageError = null;
    }

    public function render(): View
    {
        try {
            $categories = $this->categories->list();
            $storageAvailable = true;
        } catch (QueryException) {
            $categories = new Collection;
            $storageAvailable = false;
        }

        return view('livewire.mobile.record-categories', [
            'categories' => $categories,
            'categoryCount' => $categories->count(),
            'colorPreview' => $this->colorPreview(),
            'editing' => $this->editingCategoryId !== null,
            'recordCount' => $categories->sum(fn ($category): int => (int) ($category->records_count ?? 0)),
            'storageAvailable' => $storageAvailable && $this->storageError === null,
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:80'],
            'color' => ['required', 'string', 'max:20', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sortOrder' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'editingCategoryId' => ['nullable', 'integer', Rule::exists('mobile_local.categories', 'id')],
        ];
    }

    private function validatedSortOrder(mixed $sortOrder): ?int
    {
        return is_numeric($sortOrder) ? (int) $sortOrder : null;
    }

    private function colorPreview(): string
    {
        return preg_match('/^#[0-9a-fA-F]{6}$/', $this->color) === 1 ? $this->color : '#64748b';
    }
}
