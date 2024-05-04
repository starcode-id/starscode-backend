<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string|max:255",
            "email" => "required|string|email:dns|unique:users",
            'password' => 'required|min:8|max:255|string',
            'avatar' => 'base64image',
            'role' => 'string|max:255',
            'profession' => 'string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'profession' => $request->profession,
            'avatar' => $request->avatar,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('auth_token', ['*'], now()->addWeek())->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'create user successfully',
            'data' => $user
        ], 200);
    }
}
