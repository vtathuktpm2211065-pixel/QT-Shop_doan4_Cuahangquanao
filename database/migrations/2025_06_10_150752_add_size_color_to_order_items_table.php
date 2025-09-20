<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeColorToOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'size')) {
                $table->string('size')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('order_items', 'color')) {
                $table->string('color')->nullable()->after('size');
            }
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'size')) {
                $table->dropColumn('size');
            }
            if (Schema::hasColumn('order_items', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
}