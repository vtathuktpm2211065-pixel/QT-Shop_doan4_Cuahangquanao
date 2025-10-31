<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckSupportRequestsTable extends Migration
{
    public function up()
    {
        // Hiển thị cấu trúc bảng
        $columns = DB::select('SHOW COLUMNS FROM support_requests');
        echo "Các cột trong bảng support_requests:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
    }

    public function down()
    {
        // Không làm gì
    }
}