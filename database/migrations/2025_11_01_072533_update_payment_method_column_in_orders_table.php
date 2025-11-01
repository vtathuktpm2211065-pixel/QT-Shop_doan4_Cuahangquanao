<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Thay đổi cột payment_method thành VARCHAR đủ dài
            $table->string('payment_method', 50)->change();
            
            // Hoặc nếu muốn dùng ENUM cho rõ ràng
            // $table->enum('payment_method', ['cod', 'vnpay', 'momo', 'credit_card', 'paypal', 'bank_transfer'])->change();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 20)->change();
        });
    }
};