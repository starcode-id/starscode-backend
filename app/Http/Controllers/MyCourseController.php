<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends Controller
{
    public function index(Request $request)
    {
        $mycourses = MyCourse::query()->with('course');
        $userId = $request->query('user_id');
        $mycourses->when($userId, function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
        return response()->json([
            "status" => true,
            "data" => $mycourses->paginate(10),
        ]);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "course_id" => "required|integer",
            "user_id" => "required|integer",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors(),
            ]);
        }
        $data = $request->all();
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                "status" => false,
                "message" => "course not found",
            ], 404);
        }
        $userId = $request->input('user_id');
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "user not found",
            ], 404);
        }
        $isExistMyCourse = MyCourse::where('course_id', $courseId)->where('user_id', $userId)->exists();
        if ($isExistMyCourse) {
            return response()->json([
                "status" => false,
                "message" => "user already enrolled in this course",
            ], 409);
        }
        $mycourse = MyCourse::create($data);
        return response()->json([
            "status" => true,
            "data" => $mycourse,
        ]);
    }
}
