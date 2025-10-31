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
        Schema::table('support_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('support_requests', 'status')) {
                $table->enum('status', ['pending', 'processing', 'resolved'])->default('pending');
            }
            if (!Schema::hasColumn('support_requests', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            }
            if (!Schema::hasColumn('support_requests', 'type')) {
                $table->enum('type', ['general', 'order', 'product', 'shipping', 'payment'])->default('general');
            }
        });

        Schema::table('support_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('support_replies', 'is_admin')) {
                $table->boolean('is_admin')->default(false);
            }
            if (!Schema::hasColumn('support_replies', 'attachment')) {
                $table->string('attachment')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_requests', function (Blueprint $table) {
            if (Schema::hasColumn('support_requests', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('support_requests', 'priority')) {
                $table->dropColumn('priority');
            }
            if (Schema::hasColumn('support_requests', 'type')) {
                $table->dropColumn('type');
            }
        });

        Schema::table('support_replies', function (Blueprint $table) {
            if (Schema::hasColumn('support_replies', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            if (Schema::hasColumn('support_replies', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};
