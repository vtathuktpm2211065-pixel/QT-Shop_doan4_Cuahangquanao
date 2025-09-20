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
    Schema::table('vouchers', function (Blueprint $table) {
        $table->decimal('order_amount', 15, 2)->nullable()->after('usage_limit');
    });
}

public function down()
{
    Schema::table('vouchers', function (Blueprint $table) {
        $table->dropColumn('order_amount');
    });
}

};
