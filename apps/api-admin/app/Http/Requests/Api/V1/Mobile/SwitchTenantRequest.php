<?php

namespace App\Http\Requests\Api\V1\Mobile;

use Illuminate\Contracts\Validation\ValidationRule;

class SwitchTenantRequest extends MobileApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'max:80'],
        ];
    }
}
