<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_practice_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('user_practice_progress', 'correct_count')) {
                $table->unsignedInteger('correct_count')->default(0)->after('completed_questions');
            }

            if (!Schema::hasColumn('user_practice_progress', 'incorrect_count')) {
                $table->unsignedInteger('incorrect_count')->default(0)->after('correct_count');
            }

            if (!Schema::hasColumn('user_practice_progress', 'question_answers')) {
                $table->json('question_answers')->nullable()->after('questions_order');
            }

            if (!Schema::hasColumn('user_practice_progress', 'flagged_questions_list')) {
                $table->json('flagged_questions_list')->nullable()->after('question_answers');
            }
            $table->index('last_viewed_question_id', 'user_practice_progress_last_question_idx');
        });

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('user_practice_progress', function (Blueprint $table) {
                $table->foreign('last_viewed_question_id')
                    ->references('id')
                    ->on('questions')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('user_practice_progress', function (Blueprint $table) {
                $table->dropForeign(['last_viewed_question_id']);
            });
        }

        Schema::table('user_practice_progress', function (Blueprint $table) {
            if (Schema::hasColumn('user_practice_progress', 'correct_count')) {
                $table->dropColumn('correct_count');
            }
            if (Schema::hasColumn('user_practice_progress', 'incorrect_count')) {
                $table->dropColumn('incorrect_count');
            }
            if (Schema::hasColumn('user_practice_progress', 'question_answers')) {
                $table->dropColumn('question_answers');
            }
            if (Schema::hasColumn('user_practice_progress', 'flagged_questions_list')) {
                $table->dropColumn('flagged_questions_list');
            }
            $table->dropIndex('user_practice_progress_last_question_idx');
        });
    }
};
