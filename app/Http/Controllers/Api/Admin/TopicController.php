<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTopicRequest;
use App\Http\Requests\Admin\UpdateTopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;

class TopicController extends Controller
{
    public function index(): JsonResponse
{
    $topics = Topic::paginate(20);

    return response()->json([
        'success' => true,
        'data'    => TopicResource::collection($topics->items()),
        'meta'    => [
            'current_page' => $topics->currentPage(),
            'per_page'     => $topics->perPage(),
            'total'        => $topics->total(),
            'last_page'    => $topics->lastPage(),
        ],
    ]);
}

    public function store(StoreTopicRequest $request): JsonResponse
    {
        $data = [
            'title'      => $request->title,
            'type'       => $request->type,
            'created_by' => auth()->id(),
        ];

        if ($request->type === 'article') {
            $data['content'] = $request->content;
        } else {
            $data['video_url']      = $request->video_url;
            $data['video_duration'] = $request->video_duration;
        }

        $topic = Topic::create($data);

        if ($request->filled('course_id')) {
            $topic->courses()->attach($request->course_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Topic created successfully',
            'data'    => new TopicResource($topic),
        ], 201);
    }

    public function update(UpdateTopicRequest $request, $id): JsonResponse
    {
        $topic = Topic::findOrFail($id);

        $data = [
            'title' => $request->title,
            'type'  => $request->type,
        ];

        if ($request->type === 'article') {
            $data['content']        = $request->content;
            $data['video_url']      = null;
            $data['video_duration'] = null;
        } else {
            $data['video_url']      = $request->video_url;
            $data['video_duration'] = $request->video_duration;
            $data['content']        = null;
        }

        $topic->update($data);

        if ($request->has('course_id')) {
            $cid = $request->course_id;
            $topic->courses()->sync($cid ? [$cid] : []);
        }

        return response()->json([
            'success' => true,
            'message' => 'Topic updated successfully',
            'data'    => new TopicResource($topic),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topic deleted successfully',
        ]);
    }
}