<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->text('order_note');
            $table->integer('pay_method')->comment('1:shipCOD , 2:Banking');
            $table->integer('status')->comment('1:đặt hàng, 2:Đã nhận đơn, 3:đang vận chuyển, 4:đơn hàng thành công, 5:giao hàng thất bại, 6:người dùng hủy');
            $table->text('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
