<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('product_reviews', function (Blueprint $table) {
        $table->text('admin_reply')->nullable();

        // after('review') để thêm cột ngay sau cột review (có thể đổi)
    });
}

public function down()
{
    Schema::table('product_reviews', function (Blueprint $table) {
        $table->dropColumn('admin_reply');
    });
}

};
