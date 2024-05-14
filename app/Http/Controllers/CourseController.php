<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\MyCourse;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query();
        $q = $request->query('q');
        $status = $request->query('status');
        $type = $request->query('type');
        $level = $request->query('level');
        $courses->when($q, function ($query) use ($q) {
            $query->where('name', 'like', '%' . strtolower($q) . '%');
        });
        $courses->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        });
        $courses->when($type, function ($query) use ($type) {
            $query->where('type', $type);
        });
        $courses->when($level, function ($query) use ($level) {
            $query->where('level', $level);
        });
        return response()->json([
            'status' => true,
            'data' => $courses->paginate(10)
        ]);
    }
    public function show($id)
    {
        $course = Course::with('chapters.lessons')->with('mentor')->with('images')->find($id);
        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found'
            ], 404);
        }
        $reviews = Review::where('course_id', $id)->get()->toArray();
        if (count($reviews) > 0) {
            $userId = array_column($reviews, 'user_id');
            $users = User::whereIn('id', $userId)->get();
            // echo "<pre>" . print_r($users) . "</pre>";
            foreach ($reviews as $key => $review) {
                $userIndex = array_search($review['user_id'], array_column($users->toArray(), 'id'));

                if ($userIndex !== false) {
                    $reviews[$key]['user'] = (object)$users[$userIndex];
                } else {
                    $reviews[$key]['user'] = null; // Handle case where user is not found
                }
            }
        }
        $totalStudent = MyCourse::where('course_id', $id)->count();
        $totalVideos = Chapter::where('course_id', $id)->withCount('lessons')->get()->toArray();
        $finalVideos = array_sum(array_column($totalVideos, 'lessons_count'));
        $course['reviews'] = $reviews;
        $course['total_student'] = $totalStudent;
        $course['total_videos'] = $finalVideos;
        return response()->json([
            'status' => true,
            'data' => $course
        ]);
    }
    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'certificate' => 'required|boolean',
            'thumbnail' => 'string|url',
            'type' => 'required|in:free,premium',
            'status' => 'required|in:draft,published',
            'price' => 'integer|required_if:type,premium',
            'level' => 'required|in:all-level,beginner,intermediate,advance',
            'description' => 'string',
            'mentor_id' => 'required|integer|exists:mentors,id',
        ];
        $messages = [
            'name.required' => 'Course name is required',
            'name.string' => 'Course name must be a string',
            'name.max' => 'Course name must be less than 255 characters',
            'certificate.required' => 'Certificate is required',
            'certificate.boolean' => 'Certificate must be a boolean',
            'thumbnail.string' => 'Thumbnail must be a string',
            'thumbnail.url' => 'Thumbnail must be a valid URL',
            'type.required' => 'Type is required',
            'type.in' => 'Type must be free or premium',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be draft or published',
            'price.required_if' => 'price can not be empty if type is premium',
            'level.in' => 'Level must be all-level, beginner, intermediate, or advance',
            'level.required' => 'Level is required',
            'mentor_id.required' => 'Mentor is required',
            'mentor_id.exists' => 'Mentor not found',

        ];
        $data = $request->all();
        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }
        $course = Course::create($data);
        return response()->json([
            'status' => true,
            'data' => $course
        ]);
    }
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'string|max:255',
            'certificate' => 'boolean',
            'thumbnail' => 'string|url',
            'type' => 'in:free,premium',
            'status' => 'in:draft,published',
            'price' => 'integer|required_if:type,premium',
            'level' => 'in:all-level,beginner,intermediate,advance',
            'description' => 'string',
            'mentor_id' => 'integer|exists:mentors,id|required',
        ];
        $messages = [
            'name.string' => 'Course name must be a string',
            'name.max' => 'Course name must be less than 255 characters',
            'certificate.boolean' => 'Certificate must be a boolean',
            'thumbnail.string' => 'Thumbnail must be a string',
            'thumbnail.url' => 'Thumbnail must be a valid URL',
            'type.in' => 'Type must be free or premium',
            'status.in' => 'Status must be draft or published',
            'price.required_if' => 'price can not be empty if type is premium',
            'level.in' => 'Level must be all-level, beginner, intermediate, or advance',
            'mentor_id.exists' => 'Mentor not found',
            'mentor_id.required' => 'Mentor is required',
        ];
        $data = $request->all();
        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $request->all();
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found'
            ], 404);
        }
        $course->fill($data);
        $course->save();

        return response()->json([
            'status' => true,
            'data' => $course
        ]);
    }
    public function destroy($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found'
            ], 404);
        }
        $course->delete();
        return response()->json([
            'status' => true,
            'message' => 'Course deleted successfully'
        ], 200);
    }
}
