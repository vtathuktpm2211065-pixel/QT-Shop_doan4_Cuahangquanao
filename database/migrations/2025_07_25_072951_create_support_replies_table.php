<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('support_replies', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('support_request_id');
        $table->text('reply');
        $table->timestamps();

        // Khóa ngoại tới bảng support_requests
        $table->foreign('support_request_id')->references('id')->on('support_requests')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_replies');
    }
};
