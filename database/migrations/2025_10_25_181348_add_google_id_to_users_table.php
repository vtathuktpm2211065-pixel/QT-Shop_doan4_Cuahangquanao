<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // trong file migration
public function up()
{
    Schema::table('users', function ($table) {
        $table->string('google_id')->nullable()->after('password');
        $table->timestamp('email_verified_at')->nullable()->change();
    });
}

public function down()
{
    Schema::table('users', function ($table) {
        $table->dropColumn('google_id');
    });
}
};
