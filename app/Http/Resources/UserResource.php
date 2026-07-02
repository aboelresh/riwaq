<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'username'          => $this->username,
            'email'             => $this->email,
            'role'              => $this->role,
            'profile_photo'     => $this->profile_photo,
            'bio'               => $this->bio,
            'goals'             => $this->goals,
            'profile_completed' => (bool) $this->profile_completed,
            'created_at'        => $this->created_at?->toDateTimeString(),
            // لا: password, verification_code,
            //     verification_code_expires_at, remember_token
        ];
    }
}