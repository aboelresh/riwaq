<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'type'      => $this->type,
            'content'   => $this->when($this->type === 'article', $this->content),
            'video_url' => $this->when($this->type === 'video', $this->video_url),
            'order'     => $this->pivot?->order ?? $this->order ?? null,
            'quiz'      => $this->whenLoaded('quiz', fn() => $this->quiz ? [
                'id'              => $this->quiz->id,
                'title'           => $this->quiz->title,
                'total_points'    => $this->quiz->total_points,
                'pass_percentage' => $this->quiz->pass_percentage,
            ] : null),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}