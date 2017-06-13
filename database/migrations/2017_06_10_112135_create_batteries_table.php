<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batteries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('device_id')->default(0)->index();
            $table->unsignedSmallInteger('slot')->default(0);
            $table->unsignedTinyInteger('power')->default(0);
            $table->unsignedInteger('voltage')->default(0);
            $table->unsignedInteger('current')->default(0);
            $table->unsignedTinyInteger('temperature')->default(0);
            $table->string('orderid')->default('');
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
        Schema::dropIfExists('batteries');
    }
}
