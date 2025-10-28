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
        Schema::table('user_practice_progress', function (Blueprint $table) {
            $table->json('questions_order')->nullable()->after('completed_questions_list');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_practice_progress', function (Blueprint $table) {
            $table->dropColumn('questions_order');
        });
    }
};
