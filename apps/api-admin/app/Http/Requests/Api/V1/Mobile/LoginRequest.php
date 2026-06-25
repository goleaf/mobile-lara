<?php

namespace App\Http\Requests\Api\V1\Mobile;

final class LoginRequest extends MobileApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return self::credentialRules();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function credentialRules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_id' => ['required', 'string', 'max:120'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:40'],
            'app_version' => ['required', 'string', 'max:40'],
        ];
    }
}
