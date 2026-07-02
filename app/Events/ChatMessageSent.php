<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message->load('user:id,name,profile_photo', 'replyTo:id,content,user_id');
    }

    public function broadcastOn(): Channel
    {
        return match ($this->message->channel_type) {
            'team' => new PresenceChannel("chat.team.{$this->message->channel_id}"),
            'section' => new PresenceChannel("chat.section.{$this->message->channel_id}"),
            'dm' => new PrivateChannel("chat.dm.{$this->message->channel_id}"),
            default => new PrivateChannel("chat.team.{$this->message->channel_id}"),
        };
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'channel_type' => $this->message->channel_type,
                'channel_id' => $this->message->channel_id,
                'user_id' => $this->message->user_id,
                'content' => $this->message->content,
                'type' => $this->message->type,
                'reply_to_id' => $this->message->reply_to_id,
                'reply_to' => $this->message->replyTo ? [
                    'id' => $this->message->replyTo->id,
                    'content' => $this->message->replyTo->content,
                ] : null,
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'profile_photo' => $this->message->user->profile_photo,
                ],
                'created_at' => $this->message->created_at->toISOString(),
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
