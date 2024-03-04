<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'is_create',
        'is_update',
        'is_inactive',
        'is_delete'
    ];

    public function checkPermission($role, $create, $update, $inactive, $delete) {
        $query = DB::table('permissions')->where('role_id', $role);
        if($create > 0)
            $query = $query->where('is_create', $create);
        if($update > 0)
            $query = $query->where('is_update', $update);
        if($inactive > 0)
            $query = $query->where('is_inactive', $inactive);
        if($delete > 0)
            $query = $query->where('is_delete', $delete);

        return $query->first();
    }
}
