<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up()
{
    Schema::table('support_replies', function (Blueprint $table) {
        $table->boolean('is_read')->default(false); // Bỏ after()
    });
}

public function down()
{
    Schema::table('support_replies', function (Blueprint $table) {
        $table->dropColumn('is_read');
    });
}

};
