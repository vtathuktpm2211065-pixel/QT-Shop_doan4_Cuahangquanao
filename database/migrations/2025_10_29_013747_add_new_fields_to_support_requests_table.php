<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_requests', function (Blueprint $table) {
            // Thêm cột nếu chưa có
            if (!Schema::hasColumn('support_requests', 'status')) {
                $table->enum('status', ['pending', 'processing', 'resolved'])->default('pending')->after('is_read');
            }

            if (!Schema::hasColumn('support_requests', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('status');
            }

            if (!Schema::hasColumn('support_requests', 'type')) {
                $table->enum('type', ['general', 'order', 'product', 'shipping', 'payment'])->default('general')->after('priority');
            }

            if (!Schema::hasColumn('support_requests', 'attachment')) {
                $table->string('attachment')->nullable()->after('type');
            }
        });
    }

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
            if (Schema::hasColumn('support_requests', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};
