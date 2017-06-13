<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('device_id')->default(0);
            $table->unsignedSmallInteger('slot')->default(0);
            $table->unsignedTinyInteger('status')->default(0)->index();
            $table->string('sensors')->default('');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['device_id', 'slot']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slots');
    }
}
