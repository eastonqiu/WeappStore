<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBorrowOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrow_orders', function (Blueprint $table) {
            $table->string('orderid');
            $table->unsignedInteger('user_id')->index();
            $table->string('openid')->index();
            $table->unsignedTinyInteger('platform')->index();
            $table->unsignedInteger('price')->default(0); // fen
            $table->unsignedInteger('product_id')->default(0)->index();
            $table->unsignedInteger('paid')->default(0);
            $table->unsignedInteger('usefee')->default(0);

            $table->unsignedTinyInteger('status')->index();
            $table->unsignedTinyInteger('sub_status')->default(0)->index();

            $table->unsignedBigInteger('battery_id')->default(0)->index();
            $table->timestamp('borrow_time')->useCurrent();
            $table->timestamp('return_time')->nullable();
            $table->unsignedInteger('borrow_device_id')->default(0)->index();
            $table->unsignedInteger('return_device_id')->default(0)->index();
            $table->unsignedInteger('borrow_device_ver')->default(0)->index();
            $table->unsignedInteger('return_device_ver')->default(0)->index();
            $table->unsignedInteger('borrow_soft_ver')->default(0)->index();
            $table->unsignedInteger('return_soft_ver')->default(0)->index();
            $table->unsignedInteger('borrow_station_id')->default(0)->index();
            $table->unsignedInteger('return_station_id')->default(0)->index();
            $table->unsignedInteger('borrow_shop_id')->default(0)->index();
            $table->unsignedInteger('return_shop_id')->default(0)->index();
            $table->string('borrow_station_name')->default('');
            $table->string('return_station_name')->default('');

            $table->text('fee_strategy');
            $table->text('msg');

            $table->unsignedInteger('refund_no')->default(0);
            $table->unsignedInteger('refundable')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('orderid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('borrow_orders');
    }
}
