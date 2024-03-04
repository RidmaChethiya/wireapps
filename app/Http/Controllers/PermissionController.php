<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use Auth;

class PermissionController extends Controller
{
    public function __construct(Permission $permissions) {
		$this->permissions = $permissions;
	}

    public function index()
    {
        return Permission::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'role_id' => 'required'
        ]);

        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 1, 0, 0, 0);
        $roleCheck = $this->permissions->checkPermission($fields['role_id'], 0, 0, 0, 0);
        if ($permissionCheck) {
            if ($roleCheck) {
                return response()->json([
                    'massage' => 'Sorry. Duplicate permissions.!'
                ]);
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not add permissions.!'
            ]);
        }

        try {
            $save = Permission::create($request->all());
        } catch (Exception $e) {
            return $e->getMessage();  
        }
        return response()->json([
            'massage' => 'Permissions saved successfully.!',
            'update' => $save
        ]);
    }

    public function update(Request $request, $id)
    {
        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 1, 0, 0);
        if ($permissionCheck) {
            try {
                $permission = Permission::find($id);
                $permission->update($request->all());
            } catch (Exception $e) {
                return $e->getMessage();  
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not update permission.!'
            ]);
        }

        return response()->json([
            'massage' => 'Permission updated successfully.!',
            'update' => $permission
        ]);
    }

    public function destroy($id)
    {
        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 0, 0, 1);
            if ($permissionCheck) {
                $delete = Permission::destroy($id);
            } else {
                return response()->json([
                    'massage' => 'Sorry. You can not delete permission.!'
                ]);
            }

        if ($delete) {
            return response()->json([
                'massage' => 'Permission deleted successfully.!'
            ]);
        } else {
            return response()->json([
                'massage' => 'Permission deleted error.!'
            ]);
        }
    }
}
