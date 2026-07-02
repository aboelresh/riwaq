<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['creator', 'topics', 'tracks'])->get();

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'exists:topics,id',
            'track_id' => 'nullable|integer|exists:tracks,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        if ($request->has('topic_ids')) {
            $topics = [];
            foreach ($request->topic_ids as $i => $tid) { $topics[$tid] = ['order' => $i + 1]; }
            $course->topics()->attach($topics);
        }

        // Link to track via pivot
        if ($request->input('track_id')) {
            \DB::table('track_courses')->insert(['track_id' => $request->input('track_id'), 'course_id' => $course->id, 'order' => 0]);
        }

        return response()->json(['success' => true, 'message' => 'Course created successfully', 'data' => $course->load('topics')], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'exists:topics,id',
            'track_id' => 'nullable|integer|exists:tracks,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $course = Course::findOrFail($id);
        $course->update(['title' => $request->title, 'description' => $request->description]);

        if ($request->has('topic_ids')) {
            $topics = [];
            foreach ($request->topic_ids as $i => $tid) { $topics[$tid] = ['order' => $i + 1]; }
            $course->topics()->sync($topics);
        }

        // Sync track association
        if ($request->has('track_id')) {
            \DB::table('track_courses')->where('course_id', $course->id)->delete();
            if ($request->input('track_id')) {
                \DB::table('track_courses')->insert(['track_id' => $request->input('track_id'), 'course_id' => $course->id, 'order' => 0]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Course updated successfully', 'data' => $course->load('topics')]);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    }
}