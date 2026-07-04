<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_time'    => 'required|numeric|min:0',
            'duration'        => 'required|numeric|min:1',
            'watched_seconds' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'current_time.required'    => 'current_time is required.',
            'current_time.numeric'     => 'current_time must be a number.',
            'current_time.min'         => 'current_time must be 0 or greater.',
            'duration.required'        => 'duration is required.',
            'duration.min'             => 'duration must be at least 1 second.',
            'watched_seconds.required' => 'watched_seconds is required.',
            'watched_seconds.min'      => 'watched_seconds must be 0 or greater.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $currentTime = (float) $this->current_time;
            $duration    = (float) $this->duration;

            if ($currentTime > $duration) {
                $validator->errors()->add(
                    'current_time',
                    "current_time ({$currentTime}s) cannot exceed duration ({$duration}s)."
                );
            }
        });
    }
}