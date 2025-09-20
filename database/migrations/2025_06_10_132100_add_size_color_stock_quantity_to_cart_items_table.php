<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeColorStockQuantityToCartItemsTable extends Migration
{
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('size')->nullable()->after('product_id');
            $table->string('color')->nullable()->after('size');
            $table->integer('stock_quantity')->default(0)->after('color');
        });
    }

    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['size', 'color', 'stock_quantity']);
        });
    }
}