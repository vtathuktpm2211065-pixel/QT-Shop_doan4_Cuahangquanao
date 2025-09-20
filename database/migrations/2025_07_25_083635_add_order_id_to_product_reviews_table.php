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
        $table->unsignedBigInteger('order_id')->nullable()->after('user_id');
    });
}

public function down()
{
    Schema::table('product_reviews', function (Blueprint $table) {
        $table->dropColumn('order_id');
    });
}


    /**
     * Reverse the migrations.
     */
   
};
