<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('openid')->default('')->index();
            $table->string('nickname')->default('')->index();
            $table->integer('platform')->default('0')->index();
            $table->string('avatar')->default('users/default.png');
            $table->boolean('sex')->default(true); // male true
            $table->string('country')->default('中国');
            $table->string('province')->default('');
            $table->string('city')->default('');
            $table->string('area')->default('');
            $table->unsignedInteger('balance')->default(0);
            $table->unsignedInteger('deposit')->default(0);
            $table->unsignedInteger('refund')->default(0);
            $table->boolean('subscribe')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
