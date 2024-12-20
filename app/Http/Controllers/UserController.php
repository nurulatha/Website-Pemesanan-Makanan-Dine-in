<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'role_ids' => 'required',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => $request->password
            ]);

            $role_ids = is_array($request->role_ids) ? $request->role_ids : [$request->role_ids];

            $roleUsers = [];
            foreach ($role_ids as $role_id) {
                $roleUsers[] = [
                    'role_id' => $role_id,
                    'user_id' => $user->id
                ];
            }

            DB::table('role_users')->insert($roleUsers);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function show(User $user)
    {
        if (!$user->exists) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required',
            'username' => 'sometimes|required|unique:users,username,' . $user->username,
            'current_password' => 'sometimes|required',
            'new_password' => 'sometimes|required',
            'role_ids' => 'sometimes|required',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            if ($request->has('new_password')) {
                if (!$request->has('current_password')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Current password is required to change the password.'
                    ], 422);
                }

                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Current password is incorrect.'
                    ], 422);
                }
            }

            $data = $request->only(['name', 'username', 'new_password']);

            if ($request->has('new_password')) {
                $data['password'] = $request->new_password;
                unset($data['new_password']);
            }

            $user->update($data);

            if ($request->has('role_ids')) {
                $role_ids = is_array($request->role_ids) ? $request->role_ids : [$request->role_ids];

                DB::table('role_users')->where('user_id', $user->id)->delete();

                $roleUsers = [];
                foreach ($role_ids as $role_id) {
                    $roleUsers[] = [
                        'role_id' => $role_id,
                        'user_id' => $user->id
                    ];
                }

                DB::table('role_users')->insert($roleUsers);
            }

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        if (!$user->exists) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        try {

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
