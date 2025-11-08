<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // KIẾM NGHỊ: Sửa lại phần kiểm tra bảng cho an toàn hơn
        if (Schema::hasTable('shop_locations')) {
            Schema::dropIfExists('shop_locations');
        }

        // Tạo bảng mới (phần này của bạn đã đúng)
        Schema::create('shop_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('business_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Thêm dữ liệu mẫu (đúng)
        DB::table('shop_locations')->insert([
            [
                'name' => 'QT Shop Cần Thơ',
                'address' => '256 Nguyễn Văn Cừ, An Hòa, Ninh Kiều, Cần Thơ',
                'latitude' => 10.04680154, 
                'longitude' => 105.76803812,
                'phone' => '028 3823 4567',
                'email' => 'q1@qtshop.com',
                'business_hours' => 'Thứ 2 - Chủ Nhật: 8:00 - 22:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('shop_locations');
    }
};