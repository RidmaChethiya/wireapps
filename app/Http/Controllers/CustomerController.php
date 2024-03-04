<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Permission;
use Auth;

class CustomerController extends Controller
{
    public function __construct(Permission $permissions) {
		$this->permissions = $permissions;
	}

    public function index()
    {
        return Customer::where('is_delete', 0)->get();
    }

    public function store(Request $request)
    {
        $currentUserId = Auth::user()->id;
        $fields = $request->validate([
            'name' => 'required|max:255',
            'age' => 'required',
            'phone_no' => 'required'
        ]);

        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 1, 0, 0, 0);
        if ($permissionCheck) {
            try {
                $request->merge(['user_id' => $currentUserId]);
                $save = Customer::create($request->all());
            } catch (Exception $e) {
                return $e->getMessage();  
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not add customer.!'
            ]);
        }

        return response()->json([
            'massage' => 'Customer saved successfully.!',
            'update' => $save
        ]);
    }

    public function update(Request $request, $id)
    {
        $currentUserId = Auth::user()->id;
        $fields = $request->validate([
            'name' => 'required|max:255',
            'age' => 'required',
            'phone_no' => 'required'
        ]);

        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 1, 0, 0);
        if ($permissionCheck) {
            try {
                $request->merge(['user_id' => $currentUserId]);
                $Customer = Customer::find($id);
                $Customer->update($request->all());
            } catch (Exception $e) {
                return $e->getMessage();  
            }
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not update customer.!'
            ]);
        }

        return response()->json([
            'massage' => 'Customer updated successfully.!',
            'update' => $Customer
        ]);
    }

    public function destroy($id)
    {
        $currentUserRole = Auth::user()->role_id;
        $permissionCheck = $this->permissions->checkPermission($currentUserRole, 0, 0, 0, 1);
        $permissionCheckNew = $this->permissions->checkPermission($currentUserRole, 0, 0, 1, 0);

        if ($permissionCheck) {
            $delete = Customer::destroy($id);
        } else if ($permissionCheckNew) {
            $medication = Customer::find($id);
            $delete = $medication->update([
                'is_delete' => 1
            ]);
        } else {
            return response()->json([
                'massage' => 'Sorry. You can not delete customer.!'
            ]);
        }
        
        if ($delete) {
            return response()->json([
                'massage' => 'Customer deleted successfully.!'
            ]);
        } else {
            return response()->json([
                'massage' => 'Customer deleted error.!'
            ]);
        }
    }
}
