<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class PermmissionSeeder extends Seeder
{
    public function run()
    {
        Permission::create([
            'role_id' => 1,
            'is_create' => 1,
            'is_update' => 1,
            'is_inactive' => 1,
            'is_delete' => 1
        ]);
    }
}
