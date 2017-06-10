<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // =========== generate data ===============//
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);

        // =========== generate data ===============//

        // init relations
        $allPerms = Permission::all();

        $userShowPerm = Permission::where('name', 'user-show')->first();
        $userUpdatePerm = Permission::where('name', 'user-update')->first();
        $userDestroyPerm = Permission::where('name', 'user-destroy')->first();
        $postShowPerm = Permission::where('name', 'post-show')->first();

        $rootRole = Role::where('name', 'Root')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        $rootUser = User::where('email', 'root@163.com')->first();
        $adminUser = User::where('email', 'admin@163.com')->first();

        // root -> all perms
        $rootRole->attachPermissions($allPerms);

        // admin -> all user  and only show of post
        $adminRole->attachPermission($userShowPerm);
        $adminRole->attachPermission($userUpdatePerm);
        $adminRole->attachPermission($userDestroyPerm);
        $adminRole->attachPermission($postShowPerm);

        // root_user -> root
        $rootUser->attachRole($rootRole);

        // admin_user -> admin
        $adminUser->attachRole($adminRole);

        // nothing for jack, he is a normal user.
    }
}
