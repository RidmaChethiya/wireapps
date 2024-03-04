<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Auth;

class UserController extends Controller
{
    public function __construct(User $users, Permission $permissions) {
		$this->users = $users;
		$this->permissions = $permissions;
	}

    public function register(Request $request) {
        $userRole = null;
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string'
        ]);
        $userCheck = $this->users->checkUser(1, $fields['username'], 0);
        if ($userCheck) {
            return response()->json([
                'massage' => 'Sorry. Duplicate user.!'
            ]);
        }
        $userAll = User::all();
        if(sizeof($userAll) == 0){
            $userRole = Role::all();
            $user = User::create([
                'name' => $fields['name'],
                'username' => $fields['username'],
                'password' => bcrypt($fields['password']),
                'role_id' => $userRole[0]['id']
            ]);
        } else {
            $user = User::create([
                'name' => $fields['name'],
                'username' => $fields['username'],
                'password' => bcrypt($fields['password'])
            ]);
        }
        // $token = $user->createToken('userToken')->plainTextToken;
        $response = [
            'user' => $user
        ];
        return response($response, 201);
    }

    // public function update(Request $request, $id)
    // {
    //     $fields = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'username' => 'required|string|max:255',
    //         'password' => 'required|string'
    //     ]);

    //     $currentUserRole = Auth::user()->role_id;
    //     $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 0, 1, 0);
    //     if ($permissionCheck) {
    //         $userCheck = $this->users->checkUser(2, $fields['username'], $id);
    //         if ($userCheck) {
    //             return response()->json([
    //                 'massage' => 'Sorry. Duplicate username.!'
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'massage' => 'Sorry. You can not update user.!'
    //         ]);
    //     }

    //     try {
    //         $user = User::find($id);
    //         $user->update($request->all());
    //     } catch (Exception $e) {
    //         return $e->getMessage();  
    //     }
    //     return response()->json([
    //         'massage' => 'User updated successfully.!',
    //         'update' => $user
    //     ]);
    // }

    public function updateRole(Request $request, $id)
    {
        $fields = $request->validate([
            'role_id' => 'required'
        ]);

        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 1, 0, 0, 0);
        if ($permissionCheck) {
            try {
                $user = User::find($id);
                $update = $user->update([
                    'role_id' => $request->role_id
                ]);
            } catch (Exception $e) {
                return $e->getMessage();  
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not update user role.!'
            ]);
        }

        return response()->json([
            'massage' => 'User updated successfully.!',
            'update' => $user
        ]);
    }
    
    public function login(Request $request) {
        $fields = $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string'
        ]);
        $userCheck = $this->users->checkUser(3, $fields['username'], 0);
        if (!$userCheck || !Hash::check($fields['password'], $userCheck->password)) {
            return response()->json([
                'massage' => 'Invalid email or password.!'
            ], 401);
        }
        $user = User::find($userCheck->id);
        $token = $user->createToken('userToken')->plainTextToken;
        // dd($token);
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();
        return response()->json([
            'massage' => 'Logged Out.!'
        ]);
    }

}
