<?php

namespace App\Livewire\BankSoal;

use Livewire\Component;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\UserPracticeProgress;
use Illuminate\Support\Facades\Auth;

class ShowPage extends Component
{
    public Subject $subject;
    public $selectedTopicId = null;
    public $selectedTopic = null;
    public $currentQuestion = null;
    public $currentQuestionIndex = 0;
    public $questions = [];
    public $userAnswer = null;
    public $showExplanation = false;
    public $userProgress = null;
    public $isCorrect = null;

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
        
        // Auto-select first topic if available
        $firstTopic = $this->subject->topics()->first();
        if ($firstTopic) {
            $this->selectTopic($firstTopic->id);
        }
    }

    public function selectTopic($topicId)
    {
        $this->selectedTopicId = $topicId;
        $this->selectedTopic = Topic::find($topicId);
        $this->questions = $this->selectedTopic->questions()->get();
        
        // Reset question state
        $this->currentQuestionIndex = 0;
        $this->userAnswer = null;
        $this->showExplanation = false;
        
        // Load user progress for this topic
        $this->loadUserProgress();
        
        // Set current question
        if ($this->questions->count() > 0) {
            $this->setCurrentQuestion();
        }
    }

    public function loadUserProgress()
    {
        if (Auth::check() && $this->selectedTopic) {
            $this->userProgress = UserPracticeProgress::where('user_id', Auth::id())
                ->where('topic_id', $this->selectedTopic->id)
                ->first();
                
            // If user has progress, continue from last question
            if ($this->userProgress && $this->userProgress->lastQuestion) {
                $lastQuestionIndex = $this->questions->search(function($question) {
                    return $question->id === $this->userProgress->last_question_id;
                });
                
                if ($lastQuestionIndex !== false) {
                    $this->currentQuestionIndex = $lastQuestionIndex;
                }
            }
        }
    }

    public function setCurrentQuestion()
    {
        if (isset($this->questions[$this->currentQuestionIndex])) {
            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
            $this->userAnswer = null;
            $this->showExplanation = false;
            $this->isCorrect = null;
        }
    }

    public function selectAnswer($answer)
    {
        $this->userAnswer = $answer;
        // Don't show explanation immediately, wait for nextQuestion click
    }

    public function checkAnswer()
    {
        if ($this->userAnswer && $this->currentQuestion) {
            $this->isCorrect = ($this->userAnswer === $this->currentQuestion->correct_answer);
            $this->showExplanation = true;
            
            // Save progress
            $this->saveProgress();
        } else {
            // No answer selected
            $this->isCorrect = null;
            $this->showExplanation = true;
        }
    }

    public function saveProgress()
    {
        if (Auth::check() && $this->selectedTopic && $this->currentQuestion) {
            UserPracticeProgress::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'topic_id' => $this->selectedTopic->id,
                ],
                [
                    'last_question_id' => $this->currentQuestion->id,
                ]
            );
        }
    }

    public function nextQuestion()
    {
        if (!$this->showExplanation) {
            // First click: Check answer and show explanation
            $this->checkAnswer();
        } else {
            // Second click: Move to next question
            if ($this->currentQuestionIndex < $this->questions->count() - 1) {
                $this->currentQuestionIndex++;
                $this->setCurrentQuestion();
            } else {
                // End of questions, loop back to first question
                $this->currentQuestionIndex = 0;
                $this->setCurrentQuestion();
            }
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
        if ($index >= 0 && $index < $this->questions->count()) {
            $this->currentQuestionIndex = $index;
            $this->setCurrentQuestion();
        }
    }

    public function render()
    {
        $topics = $this->subject->topics()->get();
        
        return view('livewire.bank-soal.show-page', [
            'topics' => $topics,
        ])->layout('layouts.app', ['title' => 'Latihan: ' . $this->subject->name]);
    }
}
