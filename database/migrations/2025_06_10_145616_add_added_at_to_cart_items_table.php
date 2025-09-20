<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddedAtToCartItemsTable extends Migration
{
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'added_at')) {
                $table->timestamp('added_at')->nullable()->after('quantity');
            }
        });
    }

    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'added_at')) {
                $table->dropColumn('added_at');
            }
        });
    }
}