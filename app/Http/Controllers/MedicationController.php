<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medication;
use App\Models\Permission;
use Auth;

class MedicationController extends Controller
{
    public function __construct(Permission $permissions) {
		$this->permissions = $permissions;
	}

    public function index()
    {
        return Medication::where('is_delete', 0)->get();
    }

    public function store(Request $request)
    {
        $currentUserId = Auth::user()->id;
        $fields = $request->validate([
            'name' => 'required|max:255',
            'discription' => 'required|max:255',
            'quantity' => 'required'
        ]);

        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 1, 0, 0, 0);
        if ($permissionCheck) {
            try {
                $request->merge(['user_id' => $currentUserId]);
                $save = Medication::create($request->all());
            } catch (Exception $e) {
                return $e->getMessage();  
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not add medication.!'
            ]);
        }

        return response()->json([
            'massage' => 'Medication saved successfully.!',
            'update' => $save
        ]);
    }

    public function update(Request $request, $id)
    {
        $currentUserId = Auth::user()->id;
        $fields = $request->validate([
            'name' => 'required|max:255',
            'discription' => 'required|max:255',
            'quantity' => 'required'
        ]);

        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 1, 0, 0);
        if ($permissionCheck) {
            try {
                $request->merge(['user_id' => $currentUserId]);
                $medication = Medication::find($id);
                $medication->update($request->all());
            } catch (Exception $e) {
                return $e->getMessage();  
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not update medication.!'
            ]);
        }

        return response()->json([
            'massage' => 'Medication updated successfully.!',
            'update' => $medication
        ]);
    }

    public function destroy($id)
    {
        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 0, 0, 1);
        $permissionCheckNew = $this->permissions->checkPermission($currentUserRole, 0, 0, 1, 0);

        if ($permissionCheck) {
            $delete = Medication::destroy($id);
        } else if ($permissionCheckNew) {
            $medication = Medication::find($id);
            $delete = $medication->update([
                'is_delete' => 1
            ]);
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not delete medication.!'
            ]);
        }

        if ($delete) {
            return response()->json([
                'massage' => 'Medication deleted successfully.!'
            ]);
        } else {
            return response()->json([
                'massage' => 'Medication deleted error.!'
            ]);
        }
    }
}
