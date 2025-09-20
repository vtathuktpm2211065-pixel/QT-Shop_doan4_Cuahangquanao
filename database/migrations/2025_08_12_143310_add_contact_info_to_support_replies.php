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
    Schema::table('support_replies', function (Blueprint $table) {
        $table->string('name')->nullable();
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
    });
}

public function down()
{
    Schema::table('support_replies', function (Blueprint $table) {
        $table->dropColumn(['name', 'email', 'phone']);
    });
}

};
