<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            "user_id" => "required|exists:users,id",
            "course_id" => "required|exists:courses,id",
            "rating" => "required|integer|min:1|max:5",
            "review" => "required|string|max:255",
        ];
        $messages = [
            "user_id.required" => "User ID is required.",
            "user_id.exists" => "User ID does not exist.",
            "course_id.required" => "Course ID is required.",
            "course_id.exists" => "Course ID does not exist.",
            "rating.required" => "Rating is required.",
            "rating.integer" => "Rating must be an integer.",
            "rating.min" => "Rating must be at least 1.",
            "rating.max" => "Rating must be at most 5.",
            "review.required" => "Review is required.",
            "review.string" => "Review must be a string.",
            "review.max" => "Review must not exceed 255 characters.",
        ];
        $data = $request->all();
        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ]);
        }
        $courseId = $request->input("course_id");
        $userId = $request->input("user_id");
        $isExistReview = Review::where('course_id', $courseId)->where('user_id', $userId)->exists();

        if ($isExistReview) {
            return response()->json([
                "status" => false,
                "message" => "review already exists"
            ]);
        }
        $review = Review::create($data);
        return  response()->json([
            "status" => true,
            "message" => "review created successfully"
        ]);
    }
    public function update(Request $request, $id)
    {
        $rules =  [
            "rating" => "integer|min:1|max:5",
            "review" => "string|max:255",
        ];
        $messages = [
            "rating.integer" => "Rating must be an integer.",
            "rating.min" => "Rating must be at least 1.",
            "rating.max" => "Rating must be at most 5.",
            "review.string" => "Review must be a string.",
            "review.max" => "Review must not exceed 255 characters.",
        ];
        $data = $request->except(["user_id", "course_id"]);
        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ]);
        }
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                "status" => false,
                "message" => "review not found"
            ], 404);
        }
        $review->fill($data);
        $review->save();
        return response()->json([
            "status" => true,
            "message" => "review updated successfully"
        ]);
    }
    public function destroy($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                "status" => false,
                "message" => "review not found"
            ], 404);
        }
        $review->delete();
        return response()->json([
            "status" => true,
            "message" => "review deleted successfully"
        ]);
    }
}
