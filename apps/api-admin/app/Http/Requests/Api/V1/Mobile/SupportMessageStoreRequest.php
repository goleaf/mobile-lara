<?php

namespace App\Http\Requests\Api\V1\Mobile;

use Illuminate\Foundation\Http\FormRequest;

final class SupportMessageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'diagnostic_report_id' => ['nullable', 'string', 'max:120'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*.local_id' => ['nullable', 'string', 'max:120'],
            'attachments.*.file_name' => ['required_with:attachments', 'string', 'max:180'],
            'attachments.*.mime_type' => ['nullable', 'string', 'max:120'],
            'attachments.*.size_bytes' => ['nullable', 'integer', 'min:0', 'max:52428800'],
            'attachments.*.metadata' => ['nullable', 'array'],
        ];
    }
}
