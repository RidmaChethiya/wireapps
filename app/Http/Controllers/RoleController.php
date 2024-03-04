<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Auth;

class RoleController extends Controller
{
    public function __construct(Role $roles, Permission $permissions) {
		$this->roles = $roles;
		$this->permissions = $permissions;
	}

    public function index()
    {
        return Role::where('is_delete', 0)->get();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255'
        ]);

        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 1, 0, 0, 0);
        if ($permissionCheck) {
            $roleCheck = $this->roles->checkRole(2, $fields['name'], 0);
            if ($roleCheck) {
                return response()->json([
                    'massage' => 'Sorry. Duplicate role.!'
                ]);
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not add role.!'
            ]);
        }

        try {
            $save = Role::create($request->all());
        } catch (Exception $e) {
            return $e->getMessage();  
        }
        return response()->json([
            'massage' => 'Role saved successfully.!',
            'update' => $save
        ]);
    }

    public function update(Request $request, $id)
    {
        $fields = $request->validate([
            'name' => 'required|max:255'
        ]);

        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 1, 0, 0);
        if ($permissionCheck) {
            $roleCheck = $this->roles->checkRole(2, $fields['name'], $id);
            if ($roleCheck) {
                return response()->json([
                    'massage' => 'Sorry. Duplicate role.!'
                ]);
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not update role.!'
            ]);
        }

        try {
            $role = Role::find($id);
            $role->update($request->all());
        } catch (Exception $e) {
            return $e->getMessage();  
        }
        return response()->json([
            'massage' => 'Role updated successfully.!',
            'update' => $role
        ]);
    }

    public function destroy($id)
    {
        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 0, 0, 1);
        $permissionCheckNew = $this->permissions->checkPermission($currentUserRole, 0, 0, 1, 0);
        $roleCheck = $this->permissions->checkPermission($id, 0, 0, 0, 0);

        if ($roleCheck) {
            return response()->json([
                'massage' => 'Sorry. There are other relation in permissions.!'
            ]);
        } else {
            if ($permissionCheck) {
                $delete = Role::destroy($id);
            } else if ($permissionCheckNew) {
                $role = Role::find($id);
                $delete = $role->update([
                    'is_delete' => 1
                ]);
            } else {
                return response()->json([
                    'massage' => 'Sorry. Sorry. You can not delete role.!'
                ]);
            }
        }

        if ($delete) {
            return response()->json([
                'massage' => 'Role deleted successfully.!'
            ]);
        } else {
            return response()->json([
                'massage' => 'Role deleted error.!'
            ]);
        }
    }
}
