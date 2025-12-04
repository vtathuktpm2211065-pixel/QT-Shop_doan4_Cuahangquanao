<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckSupportRequestsTable extends Migration
{
    public function up()
    {
        // Hiển thị cấu trúc bảng một cách tương thích với nhiều driver
        try {
            $columns = Schema::getColumnListing('support_requests');
            echo "Các cột trong bảng support_requests:\n";
            foreach ($columns as $column) {
                echo "- {$column}\n";
            }
        } catch (\Exception $e) {
            // Nếu bảng chưa tồn tại hoặc driver không hỗ trợ, bỏ qua
            echo "Không thể liệt kê cột cho support_requests: " . $e->getMessage() . "\n";
        }
    }

    public function down()
    {
        // Không làm gì
    }
}