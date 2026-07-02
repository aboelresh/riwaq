<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmChannel extends Model
{
    protected $fillable = ['user_one_id', 'user_two_id', 'team_id'];

    public function userOne() { return $this->belongsTo(User::class, 'user_one_id'); }
    public function userTwo() { return $this->belongsTo(User::class, 'user_two_id'); }
    public function team() { return $this->belongsTo(Team::class); }

    public function getOtherUser($userId)
    {
        return $this->user_one_id === $userId ? $this->userTwo : $this->userOne;
    }

    public function messages()
    {
        return ChatMessage::where('channel_type', 'dm')->where('channel_id', $this->id);
    }
}
