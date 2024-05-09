<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $lessons = Lesson::query();
        $chapterId = $request->query('chapter_id');
        $lessons->when($chapterId, function ($query) use ($chapterId) {
            return $query->where('chapter_id', $chapterId);
        });

        return response()->json([
            "success" => true,
            "data" => $lessons->get()
        ]);
    }
    public function show($id)
    {
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                "success" => false,
                "message" => "lesson not found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "data" => $lesson
        ]);
    }
    public function  create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "video_url" => "required|string|max:255",
            "chapter_id" => "required|integer",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }
        $data = $request->all();
        $chapterId = $request->input('chapter_id');
        $chapter = Chapter::find($chapterId);
        if (!$chapter) {
            return response()->json([
                "success" => false,
                "message" => "chapter not found"
            ], 404);
        }
        $lesson = Lesson::create($data);
        return response()->json([
            "success" => true,
            "data" => $lesson
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "string|max:255",
            "video_url" => "string|max:255",
            "chapter_id" => "integer",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }
        $data = $request->all();
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                "success" => false,
                "message" => "lesson not found"
            ], 404);
        }
        $chapterId = $request->input('chapter_id');
        $chapter = Chapter::find($chapterId);
        if (!$chapter) {
            return response()->json([
                "success" => false,
                "message" => "chapter not found"
            ], 404);
        }
        $lesson->fill($data);
        $lesson->save();
        return response()->json([
            "success" => true,
            "data" => $lesson
        ]);
    }
    public function destroy($id)
    {
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                "success" => false,
                "message" => "lesson not found"
            ], 404);
        }
        $lesson->delete();
        return response()->json([
            "success" => true,
            "message" => "lesson deleted successfully"
        ]);
    }
}
