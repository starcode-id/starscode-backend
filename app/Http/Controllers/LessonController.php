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
        $rules = [
            "name" => "required|string|max:255",
            "video_url" => "required|string|max:255",
            "chapter_id" => "required|integer|exists:chapters,id",
        ];
        $messages = [
            "name.required" => "name is required",
            "name.string" => "name must be a string",
            "name.max" => "name must be less than 255 characters",
            "video_url.required" => "video url is required",
            "video_url.string" => "video url must be a string",
            "video_url.max" => "video url must be less than 255 characters",
            "chapter_id.required" => "chapter id is required",
            "chapter_id.integer" => "chapter id must be an integer",
            "chapter_id.exists" => "chapter not found",
        ];
        $data = $request->all();
        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }

        $lesson = Lesson::create($data);
        return response()->json([
            "success" => true,
            "data" => $lesson
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $rules = [
            "name" => "string|max:255",
            "video_url" => "string|max:255",
            "chapter_id" => "integer|exists:chapters,id|required",
        ];
        $messages = [
            "name.string" => "name must be a string",
            "name.max" => "name must be less than 255 characters",
            "video_url.string" => "video url must be a string",
            "video_url.max" => "video url must be less than 255 characters",
            "chapter_id.integer" => "chapter id must be an integer",
            "chapter_id.exists" => "chapter not found",
        ];
        $data = request()->all();
        $validator = validator($data, $rules, $messages);
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
