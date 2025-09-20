<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
    $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
    $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
    $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
    $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
});
    }
};
