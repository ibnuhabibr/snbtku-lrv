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
            $table->json('completed_questions_list')->nullable()->after('completed_questions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_practice_progress', function (Blueprint $table) {
            $table->dropColumn('completed_questions_list');
        });
    }
};
