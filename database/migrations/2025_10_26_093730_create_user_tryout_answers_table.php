<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_tryout_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_tryout_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->char('user_answer', 1)->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamps();
            
            $table->unique(['user_tryout_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tryout_answers');
    }
};
