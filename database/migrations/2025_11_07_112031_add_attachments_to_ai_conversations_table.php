<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_conversations', 'attachments')) {
                $table->json('attachments')->nullable()->after('context');
            }
        });
    }

    public function down()
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            if (Schema::hasColumn('ai_conversations', 'attachments')) {
                $table->dropColumn('attachments');
            }
        });
    }
};
