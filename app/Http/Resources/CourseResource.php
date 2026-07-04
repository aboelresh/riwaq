<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'topics_count' => $this->whenCounted('topics'),
            'topics'       => TopicResource::collection($this->whenLoaded('topics')),
            'tracks'       => $this->whenLoaded('tracks', fn() =>
                $this->tracks->map(fn($t) => ['id' => $t->id, 'title' => $t->title])
            ),
            'created_at'   => $this->created_at?->toDateTimeString(),
        ];
    }
}