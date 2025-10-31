<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class VerifyFixedStructure extends Migration
{
    public function up()
    {
        $columns = DB::select('SHOW COLUMNS FROM support_requests');
        echo "CẤU TRÚC BẢNG SUPPORT_REQUESTS SAU KHI SỬA:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})";
            if ($column->Null === 'NO') echo " NOT NULL";
            if ($column->Default) echo " DEFAULT '{$column->Default}'";
            echo "\n";
        }
    }

    public function down()
    {
        // Không làm gì
    }
}