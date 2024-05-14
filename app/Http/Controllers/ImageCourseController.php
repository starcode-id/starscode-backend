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
        $rules = [
            "image" => "required|url",
            "course_id" => "required|integer|exists:courses,id",
        ];
        $messages = [
            'image.required' => 'The image field is required.',
            'image.url' => 'The image must be a valid URL.',
            'course_id.required' => 'The course field is required.',
            'course_id.exists' => 'The course is not found.',
        ];
        $data = $request->all();
        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
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
