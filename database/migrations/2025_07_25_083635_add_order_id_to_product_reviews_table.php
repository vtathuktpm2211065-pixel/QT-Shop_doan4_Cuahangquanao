<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Kiểm tra xem bảng orders đã tồn tại chưa
        if (!Schema::hasTable('orders')) {
            throw new Exception('Bảng orders không tồn tại. Vui lòng tạo bảng orders trước.');
        }

        Schema::table('product_reviews', function (Blueprint $table) {
            // Kiểm tra xem cột đã tồn tại chưa
            if (!Schema::hasColumn('product_reviews', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('user_id');
                
                // Thêm index
                $table->index('order_id');
                
                // Thêm khóa ngoại
                $table->foreign('order_id')
                      ->references('id')
                      ->on('orders')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            // Xóa khóa ngoại nếu tồn tại
            if (Schema::hasColumn('product_reviews', 'order_id')) {
                // Kiểm tra xem khóa ngoại có tồn tại không
                $foreignKeys = DB::select(
                    "SELECT CONSTRAINT_NAME 
                     FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                     WHERE TABLE_NAME = 'product_reviews' 
                     AND COLUMN_NAME = 'order_id' 
                     AND REFERENCED_TABLE_NAME IS NOT NULL"
                );
                
                if (!empty($foreignKeys)) {
                    $table->dropForeign(['order_id']);
                }
                
                $table->dropColumn('order_id');
            }
        });
    }
};