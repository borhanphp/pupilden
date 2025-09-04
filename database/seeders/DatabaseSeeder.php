<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            OrganizationSeed::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class
        ]);
    }
}