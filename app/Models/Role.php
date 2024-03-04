<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_delete'
    ];

    public function checkRole($type, $name, $id) {
        $query = DB::table('roles');
        if($type == 1)
            $query = $query->where('name', $name) ;
        else if($type == 2)
            $query = $query->where('name', $name)->where('id', '!=' , $id) ;
        else if($type == 3)
            $query = $query->where('name', $name)->where('is_delete', 0) ;

        return $query->first();
    }

}
