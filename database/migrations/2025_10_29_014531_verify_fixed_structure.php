<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class VerifyFixedStructure extends Migration
{
    public function up()
    {
        try {
            $columns = Schema::getColumnListing('support_requests');
            echo "CẤU TRÚC BẢNG SUPPORT_REQUESTS SAU KHI SỬA:\n";
            foreach ($columns as $column) {
                echo "- {$column}\n";
            }
        } catch (\Exception $e) {
            echo "Không thể liệt kê cấu trúc support_requests: " . $e->getMessage() . "\n";
        }
    }

    public function down()
    {
        // Không làm gì
    }
}