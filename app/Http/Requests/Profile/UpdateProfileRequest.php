<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'sometimes|string|max:255|regex:/^[\p{L}\s\-\.]+$/u',
            'username'      => [
                'sometimes',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username,' . auth()->id(),
            ],
            'bio'           => 'nullable|string|max:500',
            'goals'         => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'          => 'Name may only contain letters, spaces, hyphens, and dots.',
            'username.min'        => 'Username must be at least 3 characters.',
            'username.max'        => 'Username must not exceed 30 characters.',
            'username.regex'      => 'Username may only contain letters, numbers, and underscores.',
            'username.unique'     => 'This username is already taken.',
            'bio.max'             => 'Bio must not exceed 500 characters.',
            'goals.max'           => 'Goals must not exceed 500 characters.',
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.mimes' => 'Profile photo must be: jpg, jpeg, png, or webp.',
            'profile_photo.max'   => 'Profile photo must not exceed 2MB.',
        ];
    }
}