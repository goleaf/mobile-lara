<?php

namespace App\Http\Requests\Api\V1\Mobile;

use App\Models\MobileSupportTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SupportTicketStoreRequest extends FormRequest
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
            'subject' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:5000'],
            'priority' => ['nullable', 'string', Rule::in([
                MobileSupportTicket::PRIORITY_LOW,
                MobileSupportTicket::PRIORITY_NORMAL,
                MobileSupportTicket::PRIORITY_HIGH,
                MobileSupportTicket::PRIORITY_URGENT,
            ])],
            'category' => ['nullable', 'string', 'max:80'],
            'support_context' => ['nullable', 'array'],
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
