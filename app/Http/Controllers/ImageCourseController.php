<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ImageCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "course_id" => "required|integer",
            "image" => "required|url",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }
        $data = $request->all();
        $courseId = $request->input("course_id");
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                "success" => false,
                "message" => "Course not found"
            ], 404);
        }
        $ImageCourse = ImageCourse::create($data);
        return response()->json([
            "success" => true,
            "data" => $ImageCourse
        ]);
    }
    public function destroy($id)
    {
        $ImageCourse = ImageCourse::find($id);
        if (!$ImageCourse) {
            return response()->json([
                "success" => false,
                "message" => "ImageCourse not found"
            ], 404);
        }
        $ImageCourse->delete();
        return response()->json([
            "success" => true,
            "message" => "ImageCourse deleted successfully"
        ]);
    }
}
