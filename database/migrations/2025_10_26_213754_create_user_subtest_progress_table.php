<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subtest_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_tryout_id')->constrained('user_tryouts')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade'); // Link ke subtes

            $table->enum('status', ['locked', 'unlocked', 'ongoing', 'completed'])->default('locked');
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('time_remaining_seconds')->nullable(); // Sisa waktu jika ditinggal
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subtest_progress');
    }
};