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
        $rules = [
            'name' => 'required|string|max:255',
            'course_id' => 'required|integer|exists:courses,id',

        ];
        $messages = [
            'name.required' => 'Name is required.',
            'name.max' => 'Name is too long.  must be less than 255 characters.',
            'course_id.required' => 'Course is required.',
            'course_id.exists' => 'Course not found.',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 400);
        }
        $chapter = Chapter::create($data);
        return response()->json([
            'status' => true,
            'data' => $chapter
        ]);
    }
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'string|max:255',
            'course_id' => 'required|integer|exists:courses,id',
        ];
        $messages = [
            'name.max' => 'Name is too long.  must be less than 255 characters.',
            'course_id.required' => 'Course is required.',
            'course_id.exists' => 'Course not found.',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 400);
        }
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => false,
                'message' => 'Chapter not found.',
            ], 404);
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
