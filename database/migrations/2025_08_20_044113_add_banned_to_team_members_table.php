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
    Schema::table('team_members', function (Blueprint $table) {
        $table->boolean('banned')->default(false)->after('permissions'); 
    });
}

public function down()
{
    Schema::table('team_members', function (Blueprint $table) {
        $table->dropColumn('banned');
    });
}


};
