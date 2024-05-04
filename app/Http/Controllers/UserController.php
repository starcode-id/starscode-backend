<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function update(Request $request, int $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ]);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email:dns|max:255|unique:users,email,' . $id,
                'password' => 'min:8|max:255|string',
                'avatar' => 'nullable|string|base64image',
                'role' => 'string|max:255',
                'profession' => 'string|max:255'
            ]);
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->fill($request->only('name', 'email', 'avatar', 'role', 'profession', 'password'))->save();


            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
    public function getUsers(Request $request)
    {
        $role = $request->input('role');
        $profession = $request->input('profession');
        $id = $request->input('id');

        $usersQuery = User::query();

        if ($id) {
            $usersQuery->where('id', $id);
        }

        if ($role) {
            $usersQuery->where('role', $role);
        }

        if ($profession) {
            $usersQuery->where('profession', $profession);
        }

        $users = $usersQuery->get();
        $dataList = [];

        foreach ($users as $user) {
            $dataList[] = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'profession' => $user->profession,
                'role' => $user->role,
                'avatar' => $user->avatar
            ];
        }

        return response()->json([
            "status" => true,
            'data' => $dataList
        ], 200);
    }
    public function getUserById($id)
    {

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "User not found"
            ]);
        }
        $dataUser = [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'profession' => $user->profession,
            'role' => $user->role,
            'avatar' => $user->avatar
        ];
        return response()->json([
            "status" => true,
            'data' => $dataUser
        ]);
    }
}
