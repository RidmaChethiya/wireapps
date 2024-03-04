<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role_id',
        'is_delete'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function checkUser($type, $name, $id) {
        $query = DB::table('users');
        if($type == 1)
            $query = $query->where('username', $name);
        else if($type == 2)
            $query = $query->where('username', $name)->where('id', '!=' , $id) ;
        else if($type == 3)
            $query = $query->where('username', $name)->where('is_delete', 0) ;

        return $query->first();
    }

}
