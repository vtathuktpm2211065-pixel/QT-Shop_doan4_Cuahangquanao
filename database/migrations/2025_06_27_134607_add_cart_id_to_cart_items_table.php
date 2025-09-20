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
    Schema::table('cart_items', function (Blueprint $table) {
        $table->foreignId('cart_id')->nullable()->constrained('carts')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('cart_items', function (Blueprint $table) {
        $table->dropForeign(['cart_id']);
        $table->dropColumn('cart_id');
    });
}

};
