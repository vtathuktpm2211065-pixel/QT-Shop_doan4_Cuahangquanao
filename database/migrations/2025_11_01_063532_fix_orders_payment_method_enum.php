<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Cập nhật cột payment_method thêm 'cod'
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cod','credit_card','vnpay','bank_transfer','momo'])
                  ->default('cod')
                  ->change();
        });
    }

    public function down(): void
    {
        // Nếu rollback, bỏ 'cod' (tuỳ bạn)
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['credit_card','vnpay','bank_transfer','momo'])
                  ->default('credit_card')
                  ->change();
        });
    }
};
