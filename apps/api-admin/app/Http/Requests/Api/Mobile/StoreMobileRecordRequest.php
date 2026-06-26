<?php

namespace App\Http\Requests\Api\Mobile;

use App\Models\TenantRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMobileRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'string', Rule::in([
                TenantRecord::STATUS_DRAFT,
                TenantRecord::STATUS_ACTIVE,
                TenantRecord::STATUS_REVIEW,
                TenantRecord::STATUS_DONE,
            ])],
            'priority' => ['nullable', 'string', Rule::in([
                TenantRecord::PRIORITY_LOW,
                TenantRecord::PRIORITY_NORMAL,
                TenantRecord::PRIORITY_HIGH,
                TenantRecord::PRIORITY_URGENT,
            ])],
            'metadata' => ['nullable', 'array'],
            'category_id' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'array'],
            'category.name' => ['required_with:category', 'string', 'max:80'],
            'category.color' => ['nullable', 'string', 'max:32'],
            'category.description' => ['nullable', 'string', 'max:500'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:60'],
            'note' => ['nullable', 'string', 'max:3000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*.local_id' => ['nullable', 'string', 'max:120'],
            'attachments.*.file_name' => ['required_with:attachments', 'string', 'max:180'],
            'attachments.*.mime_type' => ['nullable', 'string', 'max:120'],
            'attachments.*.size_bytes' => ['nullable', 'integer', 'min:0', 'max:52428800'],
            'attachments.*.metadata' => ['nullable', 'array'],
        ];
    }
}
