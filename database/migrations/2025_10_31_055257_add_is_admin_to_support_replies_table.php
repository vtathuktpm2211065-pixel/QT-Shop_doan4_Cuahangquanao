<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAdminToSupportRepliesTable extends Migration
{
    public function up()
    {
        Schema::table('support_replies', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('phone');
        });
    }

    public function down()
    {
        Schema::table('support_replies', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
}