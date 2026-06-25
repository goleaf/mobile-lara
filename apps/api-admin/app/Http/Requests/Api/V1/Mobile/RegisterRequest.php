<?php

namespace App\Http\Requests\Api\V1\Mobile;

use Illuminate\Validation\Rules\Password;

final class RegisterRequest extends MobileApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'device_id' => ['required', 'string', 'max:120'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:40'],
            'app_version' => ['required', 'string', 'max:40'],
        ];
    }
}
