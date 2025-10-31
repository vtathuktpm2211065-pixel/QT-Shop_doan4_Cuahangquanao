<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixSupportRequestsTableStructure extends Migration
{
    public function up()
    {
        // Sửa cột status từ varchar thành enum
        DB::statement("ALTER TABLE support_requests MODIFY status ENUM('pending', 'processing', 'resolved') DEFAULT 'pending'");
        
        // Sửa cột type từ varchar thành enum
        DB::statement("ALTER TABLE support_requests MODIFY type ENUM('general', 'order', 'product', 'shipping', 'payment', 'technical', 'other') DEFAULT 'general'");
        
        // Thêm cột priority nếu chưa có
        if (!Schema::hasColumn('support_requests', 'priority')) {
            Schema::table('support_requests', function (Blueprint $table) {
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('type');
            });
        }
    }

    public function down()
    {
        // Khôi phục về varchar nếu cần rollback
        DB::statement("ALTER TABLE support_requests MODIFY status VARCHAR(255)");
        DB::statement("ALTER TABLE support_requests MODIFY type VARCHAR(255)");
        
        if (Schema::hasColumn('support_requests', 'priority')) {
            Schema::table('support_requests', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }
    }
}