<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => "required|string|email:dns|max:255|exists:users,email",
            'password' => 'required|min:8|max:20|string',
        ];
        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
            'email.exists' => 'Email not found',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.max' => 'Password must be less than 20 characters',
        ];
        $data = $request->all();
        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user = User::where('email', $request->email)->first();

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Email or Password in corect'
            ]);
        }
        $token = $user->createToken('auth_token', ['*'], now()->addWeek())->plainTextToken;
        return response()->json([
            'status' => true,
            'data' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
