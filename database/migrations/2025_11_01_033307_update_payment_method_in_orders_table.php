<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Bước 1: sửa dữ liệu cũ
        DB::table('orders')
            ->whereNotIn('payment_method', ['cod', 'credit_card', 'vnpay', 'bank_transfer', 'momo'])
            ->update(['payment_method' => 'cod']);

        // Bước 2: đổi ENUM (cần doctrine/dbal)
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cod', 'credit_card', 'vnpay', 'bank_transfer', 'momo'])
                  ->default('cod')
                  ->change();
        });
    }

    public function down(): void
    {
        // Có thể rollback về ENUM cũ nếu cần
    }
};
