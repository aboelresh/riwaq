<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::all();

        return response()->json([
            'success' => true,
            'data' => $topics
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:article,video',
            'content' => 'required_if:type,article|string',
            'video_url' => 'required_if:type,video|string',
            'video_duration' => 'nullable|integer',
            'course_id' => 'nullable|integer|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = ['title' => $request->input('title'), 'type' => $request->input('type'), 'created_by' => auth()->id()];

        if ($request->input('type') === 'article') {
            $data['content'] = $request->input('content');
        } else {
            $data['video_url'] = $request->input('video_url');
            if ($request->has('video_duration')) $data['video_duration'] = $request->input('video_duration');
        }

        $topic = Topic::create($data);

        // Link to course via pivot
        if ($request->input('course_id')) {
            $topic->courses()->attach($request->input('course_id'));
        }

        return response()->json(['success' => true, 'message' => 'Topic created successfully', 'data' => $topic], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:article,video',
            'content' => 'required_if:type,article|string',
            'video_url' => 'required_if:type,video|string',
            'video_duration' => 'nullable|integer',
            'course_id' => 'nullable|integer|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $topic = Topic::findOrFail($id);

        $data = ['title' => $request->input('title'), 'type' => $request->input('type')];

        if ($request->input('type') === 'article') {
            $data['content'] = $request->input('content');
            $data['video_url'] = null; $data['video_duration'] = null;
        } else {
            $data['video_url'] = $request->input('video_url');
            $data['video_duration'] = $request->input('video_duration');
            $data['content'] = null;
        }

        $topic->update($data);

        // Sync course association
        if ($request->has('course_id')) {
            $cid = $request->input('course_id');
            $topic->courses()->sync($cid ? [$cid] : []);
        }

        return response()->json(['success' => true, 'message' => 'Topic updated successfully', 'data' => $topic]);
    }

    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topic deleted successfully'
        ]);
    }
}