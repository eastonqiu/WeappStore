<?php

use Illuminate\Database\Migrations\Migration;

class AddUserInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('openid')->default(NULL);
            $table->string('nickname')->default(NULL);
            $table->integer('platform')->default('0');
            $table->string('avatar')->default('users/default.png');
            $table->boolean('sex')->default(true); // male true
            $table->string('country')->default('中国');
            $table->string('province')->default(NULL);
            $table->string('city')->default(NULL);
            $table->string('area')->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('openid');
            $table->dropColumn('nickname');
            $table->dropColumn('platform');
            $table->dropColumn('avatar');
            $table->dropColumn('sex');
            $table->dropColumn('country');
            $table->dropColumn('province');
            $table->dropColumn('city');
            $table->dropColumn('area');
        });
    }
}
