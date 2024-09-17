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
            $table->string('sender_name');
            $table->string('sender_address');
            $table->string('sender_phone');
            $table->string('item');
            $table->enum('package_size', ['0-5kg', '6-9kg', '10-14kg', '15-20kg', '21-26kg']);
            $table->enum('delivery_type', ['bike', 'car', 'cargo', 'truck']);
            $table->enum('delivery_time', ['Andrew delivery', 'express delivery', 'standard delivery']);
            $table->enum('pickup_time', ['immediate', 'scheduled']);
            $table->string('receiver_name');
            $table->string('receiver_address');
            $table->string('receiver_phone');
            $table->text('additional_info')->nullable();
            $table->enum('payment_method', ['wallet', 'paystack', 'cash_on_pickup']);
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
