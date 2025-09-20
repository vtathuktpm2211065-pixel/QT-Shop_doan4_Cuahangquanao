<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Gán id là AUTO_INCREMENT và PRIMARY KEY
        DB::statement('ALTER TABLE team_members MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    public function down(): void
    {
        // Nếu rollback, bỏ AUTO_INCREMENT và PRIMARY KEY (tùy bạn muốn làm gì)
        DB::statement('ALTER TABLE team_members MODIFY id BIGINT UNSIGNED NOT NULL');
    }
};
