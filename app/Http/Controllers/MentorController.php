<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    //
    public function index()
    {
        $mentors = Mentor::all();
        return response()->json([
            'status' => true,
            'data' => $mentors
        ]);
    }
    public function show($id)
    {
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json([
                'status' => false,
                'message' => 'Mentor not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $mentor
        ]);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'profile' => 'required|url',
            'profession' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $data = $request->all();
        $mentor = Mentor::create($data);

        return response()->json([
            'status' => true,
            'data' => $mentor
        ]);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'profile' => 'url',
            'profession' => 'string|max:255',
            'email' => 'string|email|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }
        $data = $request->all();
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json([
                'status' => false,
                'message' => 'Mentor not found',
            ], 404);
        }
        $mentor->fill($data);
        $mentor->save();
        return response()->json([
            'status' => true,
            'data' => $mentor
        ]);
    }
    public function destroy($id)
    {
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json([
                'status' => false,
                'message' => 'Mentor not found',
            ], 404);
        }
        $mentor->delete();
        return response()->json([
            'status' => true,
            'message' => 'Mentor deleted successfully',
        ]);
    }
}
