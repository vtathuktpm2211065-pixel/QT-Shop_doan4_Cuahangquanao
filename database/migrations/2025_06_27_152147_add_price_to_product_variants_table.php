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
    Schema::table('product_variants', function (Blueprint $table) {
        $table->decimal('price', 10, 2)->after('color')->default(0);
    });
}

public function down()
{
    Schema::table('product_variants', function (Blueprint $table) {
        $table->dropColumn('price');
    });
}

};
