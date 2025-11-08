<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Kiểm tra bảng trước khi sửa
        if (Schema::hasTable('team_members')) {
            DB::statement('ALTER TABLE team_members MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('team_members')) {
            // Nếu rollback, bỏ AUTO_INCREMENT và PRIMARY KEY (tùy bạn cần hay không)
            DB::statement('ALTER TABLE team_members MODIFY id BIGINT UNSIGNED NOT NULL');
        }
    }
};
