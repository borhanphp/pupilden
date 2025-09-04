<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //  
        Role::create([
            'name' => 'superadmin',
            'guard_name' => 'web',
        ]);
        Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $superAdmin = Role::where('name', 'superadmin')->first();
        $superAdmin->givePermissionTo(Permission::all());
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo(Permission::all());
    }
}
