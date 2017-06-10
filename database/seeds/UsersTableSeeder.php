<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        foreach(range(5,20) as $i) {
            User::create([
                'name'           => 'Admin',
                'email'          => "12{$i}@123.com",
                'password'       => bcrypt('123'),
                'remember_token' => str_random(60),
                'platform'       => 0,
                'avatar'         => 'http://m.vstou.com/img/201512/hz8_4.jpg',
                'sex'            => true,
                'country'        => '中国',
                'province'       => '广东省',
                'city'           => '深圳市',
                'area'           => '福田区',
            ]);
        }
    }
}
