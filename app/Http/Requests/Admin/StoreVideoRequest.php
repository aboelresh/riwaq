<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'video'    => 'required|file|mimes:mp4,avi,mov,wmv|max:524288',
            'topic_id' => 'nullable|integer|exists:topics,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'  => 'Video title is required.',
            'video.required'  => 'Video file is required.',
            'video.mimes'     => 'Video must be one of: mp4, avi, mov, wmv.',
            'video.max'       => 'Video must not exceed 512MB.',
            'topic_id.exists' => 'The selected topic does not exist.',
        ];
    }
}