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
            $table->unsignedInteger('completed_questions')->default(0)->after('last_viewed_question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_practice_progress', function (Blueprint $table) {
            $table->dropColumn('completed_questions');
        });
    }
};
