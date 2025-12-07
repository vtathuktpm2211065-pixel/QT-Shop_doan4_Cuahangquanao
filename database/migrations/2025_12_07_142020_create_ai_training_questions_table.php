<?php
// database/migrations/2025_01_20_000000_create_ai_training_questions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_training_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question'); // Câu hỏi
            $table->text('answer');   // Câu trả lời
            $table->string('category')->default('general'); // Danh mục
            $table->string('intent')->default('faq'); // Ý định
            $table->json('tags')->nullable(); // Tags JSON
            $table->json('embedding')->nullable(); // Vector embedding
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0); // Số lần sử dụng
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('category');
            $table->index('intent');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_training_questions');
    }
};