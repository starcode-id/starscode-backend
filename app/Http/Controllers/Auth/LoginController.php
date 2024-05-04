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
        $validator = Validator::make($request->all(), [
            'email' => "required|string|email:dns|max:255",
            'password' => 'required|min:8|max:255|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'email not found'
            ]);
        }
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
