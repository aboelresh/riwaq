<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackController extends Controller
{
    public function index()
    {
        $tracks = Track::with(['creator', 'courses'])->get();

        return response()->json([
            'success' => true,
            'data' => $tracks
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $track = Track::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        if ($request->has('course_ids')) {
            $courses = [];
            foreach ($request->course_ids as $index => $courseId) {
                $courses[$courseId] = ['order' => $index + 1];
            }
            $track->courses()->attach($courses);
        }

        return response()->json([
            'success' => true,
            'message' => 'Track created successfully',
            'data' => $track->load('courses')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $track = Track::findOrFail($id);

        $track->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->has('course_ids')) {
            $courses = [];
            foreach ($request->course_ids as $index => $courseId) {
                $courses[$courseId] = ['order' => $index + 1];
            }
            $track->courses()->sync($courses);
        }

        return response()->json([
            'success' => true,
            'message' => 'Track updated successfully',
            'data' => $track->load('courses')
        ]);
    }

    public function destroy($id)
    {
        $track = Track::findOrFail($id);
        $track->delete();

        return response()->json([
            'success' => true,
            'message' => 'Track deleted successfully'
        ]);
    }
}