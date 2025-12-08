<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ai_training_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_training_questions', 'keywords')) {
                // store keywords as JSON array
                $table->json('keywords')->nullable()->after('category');
            }

            if (!Schema::hasColumn('ai_training_questions', 'priority')) {
                $table->tinyInteger('priority')->default(1)->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('ai_training_questions', function (Blueprint $table) {
            if (Schema::hasColumn('ai_training_questions', 'keywords')) {
                $table->dropColumn('keywords');
            }

            if (Schema::hasColumn('ai_training_questions', 'priority')) {
                // Only drop if this migration added it — cannot easily detect origin,
                // but dropping here may affect other migrations. We keep drop for symmetry.
                $table->dropColumn('priority');
            }
        });
    }
};
