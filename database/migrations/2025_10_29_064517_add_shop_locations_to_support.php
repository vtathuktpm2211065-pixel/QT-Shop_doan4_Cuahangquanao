<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopLocationsToSupport extends Migration
{
    public function up()
    {
        Schema::table('support_requests', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('attachment');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('address')->nullable()->after('longitude');
        });

        // Tạo bảng cửa hàng
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
    }

    public function down()
    {
        Schema::table('support_requests', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'address']);
        });
        Schema::dropIfExists('shop_locations');
    }
}