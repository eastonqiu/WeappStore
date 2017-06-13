<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mac')->unique();
            $table->string('push_id')->default('');
            $table->unsignedTinyInteger('total')->default(0);
            $table->unsignedTinyInteger('usable')->default(0);
            $table->unsignedTinyInteger('empty')->default(0);
            $table->unsignedInteger('soft_ver')->default(0)->index();
            $table->unsignedInteger('device_ver')->default(0)->index();
            $table->unsignedInteger('device_strategy_id')->default(0)->index();
            $table->unsignedTinyInteger('status')->default(0)->index();
            $table->timestamp('last_sync')->nullable();
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
        Schema::dropIfExists('devices');
    }
}
