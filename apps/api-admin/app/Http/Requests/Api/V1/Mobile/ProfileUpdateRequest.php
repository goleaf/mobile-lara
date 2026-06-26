<?php

namespace App\Http\Requests\Api\V1\Mobile;

use Illuminate\Validation\Rule;

final class ProfileUpdateRequest extends MobileApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'username' => ['sometimes', 'nullable', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:32', 'regex:/^[0-9+\-\s().]*$/'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:280'],
            'location' => ['sometimes', 'nullable', 'string', 'max:80'],
            'website' => ['sometimes', 'nullable', 'url', 'max:255'],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ];
    }
}
