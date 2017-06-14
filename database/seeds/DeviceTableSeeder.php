<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DeviceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('devices')->insert([
            'id' => 1,
            'mac' => 'aabbccddee',
            'push_id' => '1qaz2wsx',
            'total' => 10,
            'usable' => 5,
            'empty' => 3,
            'soft_ver' => 1,
            'device_ver' => 1,
            'device_strategy_id' => 0,
        ]);
    }
}
