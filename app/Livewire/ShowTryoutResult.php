<?php

namespace App\Livewire;

use App\Models\UserTryout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ShowTryoutResult extends Component
{
    public $userTryout;
    public $questions;
    public $userAnswers;
    public $correctAnswers;
    public $incorrectAnswers;
    public $unansweredQuestions;
    public $showExplanations = false;

    public function mount($userTryoutId)
    {
        // Load the user tryout with all related data
        $this->userTryout = UserTryout::with([
            'tryoutPackage.questions',
            'userTryoutAnswers.question'
        ])
        ->where('id', $userTryoutId)
        ->where('user_id', Auth::id())
        ->firstOrFail();

        // Check if tryout is completed
        if ($this->userTryout->status !== 'completed') {
            return redirect()->route('tryout.conduct', $this->userTryout->id);
        }

        $this->loadResultData();
    }

    private function loadResultData()
    {
        $this->questions = $this->userTryout->tryoutPackage->questions;
        $this->userAnswers = $this->userTryout->userTryoutAnswers->keyBy('question_id');

        // Categorize answers
        $this->correctAnswers = $this->userTryout->userTryoutAnswers->where('is_correct', true);
        $this->incorrectAnswers = $this->userTryout->userTryoutAnswers->where('is_correct', false);
        
        // Find unanswered questions
        $answeredQuestionIds = $this->userAnswers->keys()->toArray();
        $this->unansweredQuestions = $this->questions->whereNotIn('id', $answeredQuestionIds);
    }

    public function toggleExplanations()
    {
        $this->showExplanations = !$this->showExplanations;
    }

    public function backToTryouts()
    {
        return redirect()->route('tryouts.index');
    }

    public function render()
    {
        return view('livewire.show-tryout-result')->layout('layouts.app');
    }
}
