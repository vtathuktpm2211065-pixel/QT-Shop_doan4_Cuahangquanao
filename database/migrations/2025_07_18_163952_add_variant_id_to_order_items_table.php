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
    Schema::table('order_items', function (Blueprint $table) {
        $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');

        // Nếu muốn có ràng buộc khóa ngoại:
        $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('order_items', function (Blueprint $table) {
        $table->dropForeign(['variant_id']);
        $table->dropColumn('variant_id');
    });
}

};
