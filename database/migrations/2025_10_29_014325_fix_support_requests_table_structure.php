<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixSupportRequestsTableStructure extends Migration
{
    public function up()
    {
        $driver = DB::getDriverName();

        // Nếu là MySQL thì dùng các câu lệnh ALTER ... MODIFY
        if ($driver === 'mysql') {
            // Sửa cột status từ varchar thành enum
            DB::statement("ALTER TABLE support_requests MODIFY status ENUM('pending', 'processing', 'resolved') DEFAULT 'pending'");

            // Sửa cột type từ varchar thành enum
            DB::statement("ALTER TABLE support_requests MODIFY type ENUM('general', 'order', 'product', 'shipping', 'payment', 'technical', 'other') DEFAULT 'general'");
        } else {
            // Trên SQLite/Postgres/khác, không thực hiện MODIFY trực tiếp (có thể không hỗ trợ)
        }

        // Thêm cột priority nếu chưa có (Schema::table nên làm việc trên hầu hết DB)
        if (!Schema::hasColumn('support_requests', 'priority')) {
            Schema::table('support_requests', function (Blueprint $table) {
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('type');
            });
        }
    }

    public function down()
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            // Khôi phục về varchar nếu cần rollback (MySQL)
            DB::statement("ALTER TABLE support_requests MODIFY status VARCHAR(255)");
            DB::statement("ALTER TABLE support_requests MODIFY type VARCHAR(255)");
        }

        if (Schema::hasColumn('support_requests', 'priority')) {
            Schema::table('support_requests', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }
    }
}