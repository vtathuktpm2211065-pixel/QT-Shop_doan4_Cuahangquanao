<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->index();
            $table->text('message');
            $table->text('response');
            $table->string('message_type')->default('text');
            $table->string('intent')->nullable();
            $table->decimal('confidence', 3, 2)->default(0);
            $table->json('context')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            // Thêm index để tối ưu truy vấn
            $table->index(['session_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_conversations');
    }
};