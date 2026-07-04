<?php

namespace App\Http\Requests\AiChat;

use Illuminate\Foundation\Http\FormRequest;

class AiChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message'          => 'required|string|max:1000',
            'topic_id'         => 'nullable|integer|exists:topics,id',
            'history'          => 'nullable|array|max:10',
            'history.*.role'   => 'required|in:user,assistant',
            'history.*.content'=> 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required'         => 'Message is required.',
            'message.max'              => 'Message must not exceed 1000 characters.',
            'topic_id.exists'          => 'The selected topic does not exist.',
            'history.max'              => 'Conversation history cannot exceed 10 messages.',
            'history.*.role.in'        => 'Each history message role must be: user or assistant.',
            'history.*.content.max'    => 'Each history message must not exceed 2000 characters.',
        ];
    }
}