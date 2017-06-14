<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name' => 'permission-all',
            'display_name' => 'permission-all',
            'description' => 'Permission All',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'name' => 'role-all',
            'display_name' => 'role-all',
            'description' => 'Role All',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'name' => 'user-show',
            'display_name' => 'user-show',
            'description' => 'Show User',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'name' => 'user-update',
            'display_name' => 'user-update',
            'description' => 'Update User',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'name' => 'user-destroy',
            'display_name' => 'user-destroy',
            'description' => 'Destroy user',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'name' => 'maintain_device',
            'display_name' => '维护权限',
            'description' => '维护设备',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
