<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->default(0)->index();
            $table->unsignedInteger('fee_strategy_id')->default(0)->index();
            $table->string('lbsid')->default('');
            $table->string('name');
            $table->string('address');
            $table->string('province');
            $table->string('city');
            $table->string('area');
            $table->unsignedInteger('cost')->default(0);
            $table->string('stime')->default('');
            $table->string('etime')->default('');
            $table->string('logo', 1000)->default('');
            $table->text('images');
            $table->unsignedTinyInteger('status')->default(0)->index();
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
        Schema::dropIfExists('shops');
    }
}
