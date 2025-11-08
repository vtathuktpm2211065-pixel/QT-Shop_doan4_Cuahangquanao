<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAdminToSupportRepliesTable extends Migration
{
    public function up()
    {
        Schema::table('support_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('support_replies', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('phone');
            }
            if (!Schema::hasColumn('support_replies', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('is_admin');
            }
        });
    }

    public function down()
    {
        Schema::table('support_replies', function (Blueprint $table) {
            if (Schema::hasColumn('support_replies', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            if (Schema::hasColumn('support_replies', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
}