<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrackResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'created_by'  => $this->created_by,
            'courses'     => CourseResource::collection($this->whenLoaded('courses')),
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}