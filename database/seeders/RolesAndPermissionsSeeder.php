<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);

        Permission::firstOrCreate(['name' => 'delete posts'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'restore posts'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'update posts'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'edit posts'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'lock posts'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'unlock posts'])->assignRole($admin);

        Permission::firstOrCreate(['name' => 'delete comments'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'restore comments'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'update comments'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'lock comments'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'unlock comments'])->assignRole($admin);

        Permission::firstOrCreate(['name' => 'ban users'])->assignRole($admin);
        Permission::firstOrCreate(['name' => 'unban users'])->assignRole($admin);
    }
}
