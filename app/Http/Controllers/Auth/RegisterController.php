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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:dns|unique:users',
            'password' => 'required|min:8|max:20|string',
            'avatar' => 'base64image',
            'role' => 'string|max:255',
            'profession' => 'string|max:255'
        ];
        $messages = [
            'name.required' => 'Name is required',
            'name.max' => 'Name must not exceed 255 characters',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'email.email' => 'Email is not valid',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.max' => 'Password must not exceed 20 characters',
            'profession.max' => 'Profession must not exceed 255 characters',
            'avatar.base64image' => 'Avatar must be an image',
        ];
        $data = $request->all();
        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
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


        // $token = $user->createToken('auth_token', ['*'], now()->addWeek())->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'create user successfully',
            'data' => $user
        ], 200);
    }
}
