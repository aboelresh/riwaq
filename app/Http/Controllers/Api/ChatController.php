<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\DmChannel;
use App\Events\ChatMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function getChannels(Request $request, $teamId)
    {
        $user = auth()->user();
        $isMember = DB::table('team_members')->where('team_id', $teamId)->where('user_id', $user->id)->exists();
        if (!$isMember && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not a member'], 403);
        }

        $team = \App\Models\Team::select('id', 'name')->find($teamId);

        $sections = DB::table('team_sections')
            ->where('team_sections.team_id', $teamId)
            ->where(function ($q) use ($user) {
                $q->whereExists(function ($sub) use ($user) {
                    $sub->select(DB::raw(1))->from('team_section_members')
                        ->whereColumn('team_section_members.section_id', 'team_sections.id')
                        ->where('team_section_members.user_id', $user->id);
                })->orWhere(function () use ($user) {
                    if ($user->role === 'admin') return true;
                });
            })
            ->select('id', 'name', 'is_general')
            ->orderBy('is_general', 'desc')
            ->orderBy('name')
            ->get();

        $dms = DmChannel::where('team_id', $teamId)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
            })
            ->with(['userOne:id,name,profile_photo', 'userTwo:id,name,profile_photo'])
            ->get()
            ->map(function ($dm) use ($user) {
                $other = $dm->user_one_id === $user->id ? $dm->userTwo : $dm->userOne;
                return [
                    'id' => $dm->id,
                    'other_user' => $other ? ['id' => $other->id, 'name' => $other->name, 'profile_photo' => $other->profile_photo] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'team' => $team,
                'sections' => $sections,
                'dms' => $dms,
            ],
        ]);
    }

    public function getMessages(Request $request, $teamId)
    {
        $user = auth()->user();
        $isMember = DB::table('team_members')->where('team_id', $teamId)->where('user_id', $user->id)->exists();
        if (!$isMember && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not a member'], 403);
        }

        $type = $request->query('channel_type', 'team');
        $channelId = $request->query('channel_id');
        $before = $request->query('before');

        $query = ChatMessage::where('channel_type', $type)
            ->where('channel_id', $channelId)
            ->with(['user:id,name,profile_photo', 'replyTo:id,content,user_id']);

        if ($before) {
            $query->where('created_at', '<', $before);
        }

        $messages = $query->orderBy('created_at', 'desc')->limit(30)->get();

        return response()->json(['success' => true, 'data' => $messages]);
    }


    public function sendMessage(Request $request, $teamId)
    {
        $user = auth()->user();
        $isMember = DB::table('team_members')->where('team_id', $teamId)->where('user_id', $user->id)->exists();
        if (!$isMember && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not a member'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'channel_type' => 'required|in:team,section,dm',
            'channel_id' => 'required|integer',
            'reply_to_id' => 'nullable|integer|exists:chat_messages,id',
        ]);

        if ($validated['channel_type'] === 'section') {
            $inSection = DB::table('team_section_members')
                ->where('section_id', $validated['channel_id'])
                ->where('user_id', $user->id)->exists();
            if (!$inSection && $user->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Not in this section'], 403);
            }
        }

        if ($validated['channel_type'] === 'dm') {
            $dm = DmChannel::find($validated['channel_id']);
            if (!$dm || ($dm->user_one_id !== $user->id && $dm->user_two_id !== $user->id)) {
                return response()->json(['success' => false, 'message' => 'Invalid DM channel'], 403);
            }
        }

        $message = ChatMessage::create([
            'channel_type' => $validated['channel_type'],
            'channel_id' => $validated['channel_id'],
            'user_id' => $user->id,
            'content' => $validated['content'],
            'type' => 'text',
            'reply_to_id' => $validated['reply_to_id'] ?? null,
        ]);

        $message->load('user:id,name,profile_photo', 'replyTo:id,content,user_id');

    try {
         broadcast(new ChatMessageSent($message))->toOthers();
        } catch (\Throwable $e) {
         \Illuminate\Support\Facades\Log::warning('Chat broadcast failed', [
         'message_id' => $message->id,
         'error'      => $e->getMessage(),]);

        return response()->json(['success' => true, 'data' => $message]);
        }
    }


    public function getOrCreateDm(Request $request, $teamId)
    {
        $user = auth()->user();
        $validated = $request->validate(['user_id' => 'required|integer']);
        $otherUserId = $validated['user_id'];

        $bothMembers = DB::table('team_members')->where('team_id', $teamId)
            ->whereIn('user_id', [$user->id, $otherUserId])
            ->count() >= 2;
        if (!$bothMembers && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Both must be team members'], 400);
        }

        $userOneId = min($user->id, $otherUserId);
        $userTwoId = max($user->id, $otherUserId);

        $dm = DmChannel::firstOrCreate(
            ['user_one_id' => $userOneId, 'user_two_id' => $userTwoId, 'team_id' => $teamId],
        );

        $dm->load(['userOne:id,name,profile_photo', 'userTwo:id,name,profile_photo']);
        $other = $dm->user_one_id === $user->id ? $dm->userTwo : $dm->userOne;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $dm->id,
                'other_user' => $other ? ['id' => $other->id, 'name' => $other->name] : null,
            ],
        ]);
    }
}
