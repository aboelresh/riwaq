<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index(): JsonResponse
{
    $courses = Course::with(['creator', 'topics', 'tracks'])->paginate(20);

    return response()->json([
        'success' => true,
        'data'    => CourseResource::collection($courses->items()),
        'meta'    => [
            'current_page' => $courses->currentPage(),
            'per_page'     => $courses->perPage(),
            'total'        => $courses->total(),
            'last_page'    => $courses->lastPage(),
        ],
    ]);
}
    public function store(StoreCourseRequest $request): JsonResponse
    {
        $course = Course::create([
            'title'       => $request->title,
            'description' => $request->description,
            'created_by'  => auth()->id(),
        ]);

        if ($request->filled('topic_ids')) {
            $topics = [];
            foreach ($request->topic_ids as $i => $tid) {
                $topics[$tid] = ['order' => $i + 1];
            }
            $course->topics()->attach($topics);
        }

        if ($request->filled('track_id')) {
            DB::table('track_courses')->insert([
                'track_id'  => $request->track_id,
                'course_id' => $course->id,
                'order'     => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data'    => new CourseResource($course->load('topics')),
        ], 201);
    }

    public function update(UpdateCourseRequest $request, $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $course->update([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        if ($request->filled('topic_ids')) {
            $topics = [];
            foreach ($request->topic_ids as $i => $tid) {
                $topics[$tid] = ['order' => $i + 1];
            }
            $course->topics()->sync($topics);
        }

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data'    => new CourseResource($course->load('topics')),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully',
        ]);
    }
}