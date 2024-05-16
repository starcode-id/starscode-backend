<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\forgetpassword;
use App\Models\ResetPassword;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    //
    public function  store(Request $request)
    {
        try {

            $rules = [
                'email' => 'required|email:dns|exists:users'
            ];
            $messages = [
                'email.required' => 'email is required',
                'email.email' => 'email is not valid ',
                'email.exists' => 'email not found'
            ];
            $data = $request->all();
            $validator = validator($data, $rules,  $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ]);
            }
            $token = Str::random(64);
            $domain = URL::to('/');
            $url =  $domain . '/reset-password?token=' . $token;
            $data['url'] = $url;
            $data['email'] = $request->email;
            $data['title'] = "Reset Password";
            $isExistEmail = ResetPassword::where('email', $data['email'])->first();
            if ($isExistEmail) {
                $isExistEmail->token = $token;
                $isExistEmail->save();
            }
            $resetPassword = ResetPassword::updateOrCreate([
                'email' => $data['email'],
                'token' => $token
            ]);
            if (!$resetPassword) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create reset password entry'
                ]);
            }

            // Mail::to($data['email'])->send(new forgetpassword($token));
            Mail::send('emails', ['data' => $data], function ($message) use ($data) {
                $message->to($data['email'])->subject($data['title']);
            });
            Log::info('Password reset link sent to ' . $data['email']);
            return response()->json([
                'status' => true,
                'message' => 'Reset password link sent to your email',
                'token' => $token
            ]);
        } catch (Exception $e) {
            Log::error('Error in ResetPasswordController: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function  update(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email:dns|exists:users',
                'password' => 'required|min:8|max:255|string',
                'password_confirmation' => 'required|min:8|max:255|string|same:password',
                'token' => 'required|string|exists:password_reset_tokens,token'
            ];
            $messages = [
                'email.exists' => 'Email does not exist',
                'email.required' => 'Email is required',
                'email.email' => 'Email is not valid',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters',
                'password.max' => 'Password must be less than 20 characters',
                'password_confirmation.required' => 'Password confirmation is required',
                'password_confirmation.min' => 'Password confirmation must be at least 8 characters',
                'password_confirmation.max' => 'Password confirmation must be less than 20 characters',
                'password_confirmation.same' => 'Password confirmation does not match',
                'token.required' => 'Token is required',
                'token.exists' => 'Token not found'

            ];
            $data = $request->all();
            $validator = validator($data, $rules, $messages);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $user = User::where('email', $request->email)->first();
            $password = $data['password'];
            $confirm_password = $data['password_confirmation'];

            if ($password !== $confirm_password) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password and confirm password does not match'
                ]);
            }
            $user->password = Hash::make($password);
            $user->save();
            ResetPassword::where('email', $request->email)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            Log::error('Error in ResetPasswordController: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}
