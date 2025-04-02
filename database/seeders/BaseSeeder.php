<?php

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Seller::updateOrCreate([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('menamena'),
        ]);

        $guardName = 'seller';

        $superPermission = Permission::updateOrCreate([
            'name' => 'full_access',
            'guard_name' => $guardName,
        ]);

        $normalPermission = Permission::updateOrCreate([
            'name' => 'basic_access',
            'guard_name' => $guardName,
        ]);

        $adminRole = Role::updateOrCreate(['name' => 'admin', 'guard_name' => $guardName]);
        $normalRole = Role::updateOrCreate(['name' => 'seller', 'guard_name' => $guardName]);

        $adminRole->givePermissionTo($superPermission);
        $normalRole->givePermissionTo($normalPermission);
            $admin->assignRole('admin');
    }
}
