<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\UserTryout;
use App\Models\UserSubtestProgress;
use App\Models\UserTryoutAnswer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert UserTryout scores from old format to new format
        $userTryouts = UserTryout::whereNotNull('score')->get();
        
        foreach ($userTryouts as $userTryout) {
            // Recalculate score using new formula: (correct/total) * 1000
            $totalQuestions = $userTryout->tryoutPackage->questions->count();
            $correctAnswers = UserTryoutAnswer::where('user_tryout_id', $userTryout->id)
                ->where('is_correct', true)
                ->count();
            
            $newScore = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 1000, 2) : 0;
            
            $userTryout->update(['score' => $newScore]);
        }
        
        // Convert UserSubtestProgress scores from old format to new format
        $subtestProgresses = UserSubtestProgress::whereNotNull('score')->get();
        
        foreach ($subtestProgresses as $progress) {
            // Recalculate subtest score using new formula: (correct/total) * 1000
            $userTryout = $progress->userTryout;
            $allPackageQuestions = $userTryout->tryoutPackage->questions;
            
            $questionsInSubtest = $allPackageQuestions->filter(function ($question) use ($progress) {
                return $question->topic->subject_id == $progress->subject_id;
            });
            $totalQuestionsInSubtest = $questionsInSubtest->count();
            
            $correctAnswersInSubtest = UserTryoutAnswer::where('user_tryout_id', $userTryout->id)
                ->whereIn('question_id', $questionsInSubtest->pluck('id'))
                ->where('is_correct', true)
                ->count();
            
            $newScore = $totalQuestionsInSubtest > 0 ? round(($correctAnswersInSubtest / $totalQuestionsInSubtest) * 1000, 2) : 0;
            
            $progress->update(['score' => $newScore]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to old format (divide by 10)
        DB::statement("UPDATE user_tryouts SET score = score / 10 WHERE score IS NOT NULL");
        DB::statement("UPDATE user_subtest_progress SET score = score / 10 WHERE score IS NOT NULL");
    }
};
