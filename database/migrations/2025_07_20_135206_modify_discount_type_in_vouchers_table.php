<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDiscountTypeInVouchersTable extends Migration
{
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Thay đổi cột discount_type thành varchar(10) hoặc ENUM
            $table->string('discount_type', 10)->change();

            // Hoặc nếu muốn dùng ENUM:
            // $table->enum('discount_type', ['percent', 'fixed'])->change();
        });
    }

    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Nếu rollback, có thể để lại kiểu cũ, ví dụ varchar(3)
            $table->string('discount_type', 3)->change();
        });
    }
}
