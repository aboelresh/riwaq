<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:255',
            'type'           => 'required|in:article,video',
            'content'        => 'required_if:type,article|nullable|string',
            'video_url'      => 'required_if:type,video|nullable|string|url',
            'video_duration' => 'nullable|integer|min:1',
            'course_id'      => 'nullable|integer|exists:courses,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Topic title is required.',
            'type.required'         => 'Topic type is required.',
            'type.in'               => 'Topic type must be: article or video.',
            'content.required_if'   => 'Content is required when type is article.',
            'video_url.required_if' => 'Video URL is required when type is video.',
            'video_url.url'         => 'Video URL must be a valid URL.',
            'video_duration.integer'=> 'Video duration must be a number of seconds.',
            'course_id.exists'      => 'The selected course does not exist.',
        ];
    }
}