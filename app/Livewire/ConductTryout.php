<?php

namespace App\Livewire;

use App\Models\UserTryout;
use App\Models\UserTryoutAnswer;
use App\Models\Question;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ConductTryout extends Component
{
    public $userTryout;
    public $questions;
    public $currentQuestionIndex = 0;
    public $currentQuestion;
    public $totalQuestions;
    public $userAnswers = [];
    public $timeRemaining;
    public $showConfirmSubmit = false;

    public function mount($userTryoutId)
    {
        // Load the user tryout
        $this->userTryout = UserTryout::with(['tryoutPackage.questions', 'userTryoutAnswers'])
            ->where('id', $userTryoutId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if tryout is still ongoing
        if ($this->userTryout->status !== 'ongoing') {
            return redirect()->route('tryout.result', $this->userTryout->id);
        }

        // Load questions for this tryout package
        $this->questions = $this->userTryout->tryoutPackage->questions;
        $this->totalQuestions = $this->questions->count();

        // Load existing answers
        foreach ($this->userTryout->userTryoutAnswers as $answer) {
            $this->userAnswers[$answer->question_id] = $answer->user_answer;
        }

        // Set current question
        $this->setCurrentQuestion();

        // Calculate time remaining
        $this->calculateTimeRemaining();
    }

    public function setCurrentQuestion()
    {
        if ($this->currentQuestionIndex < $this->totalQuestions) {
            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        }
    }

    public function calculateTimeRemaining()
    {
        $startTime = $this->userTryout->start_time;
        $durationMinutes = $this->userTryout->tryoutPackage->duration_minutes;
        $endTime = $startTime->addMinutes($durationMinutes);
        
        $this->timeRemaining = now()->diffInSeconds($endTime, false);
        
        // If time is up, auto submit
        if ($this->timeRemaining <= 0) {
            $this->submitTryout();
        }
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->totalQuestions - 1) {
            $this->currentQuestionIndex++;
            $this->setCurrentQuestion();
        }
    }

    public function prevQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->setCurrentQuestion();
        }
    }

    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < $this->totalQuestions) {
            $this->currentQuestionIndex = $index;
            $this->setCurrentQuestion();
        }
    }

    public function selectAnswer($questionId, $answer)
    {
        $this->userAnswers[$questionId] = $answer;

        // Save or update answer in database
        UserTryoutAnswer::updateOrCreate(
            [
                'user_tryout_id' => $this->userTryout->id,
                'question_id' => $questionId,
            ],
            [
                'user_answer' => $answer,
            ]
        );
    }

    public function confirmSubmit()
    {
        $this->showConfirmSubmit = true;
    }

    public function cancelSubmit()
    {
        $this->showConfirmSubmit = false;
    }

    public function submitTryout()
    {
        // Update tryout status
        $this->userTryout->update([
            'end_time' => now(),
            'status' => 'completed',
        ]);

        // Calculate score
        $this->calculateScore();

        return redirect()->route('tryout.result', $this->userTryout->id);
    }

    private function calculateScore()
    {
        $correctAnswers = 0;
        $totalQuestions = $this->totalQuestions;

        foreach ($this->userTryout->userTryoutAnswers as $answer) {
            $question = $this->questions->firstWhere('id', $answer->question_id);
            $isCorrect = $question && $answer->user_answer === $question->correct_answer;
            
            // Update the is_correct field
            $answer->update(['is_correct' => $isCorrect]);
            
            if ($isCorrect) {
                $correctAnswers++;
            }
        }

        // Calculate score as percentage
        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        
        $this->userTryout->update(['score' => $score]);
    }

    public function render()
    {
        return view('livewire.conduct-tryout')->layout('layouts.app');
    }
}
