<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $chapters = Chapter::query();
        $courseId = $request->query('course_id');
        $chapters->when($courseId, function ($query) use ($courseId) {
            $query->where('course_id', '=', $courseId);
        });
        return response()->json([
            'status' => true,
            'data' => $chapters->get()
        ], 200);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'course_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 400);
        }
        $data = $request->all();
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found.',
            ], 404);
        }
        $chapter = Chapter::create($data);
        return response()->json([
            'status' => true,
            'data' => $chapter
        ]);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'course_id' => 'integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 400);
        }
        $data = $request->all();
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => false,
                'message' => 'Chapter not found.',
            ], 404);
        }
        $courseId = $request->input('course_id');
        if ($courseId) {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json([
                    'status' => false,
                    'message' => 'Course not found.',
                ], 404);
            }
        }
        $chapter->fill($data);
        $chapter->save();
        return response()->json([
            'status' => true,
            'data' => $chapter
        ], 200);
    }
    public function show($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => false,
                'message' => 'Chapter not found.',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $chapter
        ], 200);
    }
    public function destroy($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => false,
                'message' => 'Chapter not found.',
            ], 404);
        }
        $chapter->delete();
        return response()->json([
            'status' => true,
            'message' => 'Chapter deleted successfully.',
        ], 200);
    }
}
