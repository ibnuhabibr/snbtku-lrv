<?php

namespace App\Livewire\BankSoal;

use Livewire\Component;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\UserPracticeProgress;
use Illuminate\Support\Facades\Auth;

class PracticeArea extends Component
{
    public Subject $subject;
    public ?Topic $currentTopic = null;
    public ?Question $currentQuestion = null;
    public $currentQuestionIndex = 0;
    public $totalQuestions = 0;
    public $userAnswer = null;
    public $showExplanation = false;
    public $showTopicList = false;
    public $isAnswered = false;
    public $isCorrect = false;
    public $completedQuestions = [];
    public $questions = [];
    public $userProgress = null;

    protected $listeners = ['topicSelected' => 'handleTopicSelected'];

    public function mount(Subject $subject)
    {
        $this->subject = $subject->load(['topics' => fn ($query) => $query->withCount('questions')->orderBy('name')]);
        $this->showTopicList = false;
    }

    public function handleTopicSelected($data)
    {
        $topicId = $data['topicId'] ?? $data;
        $this->selectTopic($topicId);
    }

    public function toggleTopicList()
    {
        $this->showTopicList = !$this->showTopicList;
    }

    private function loadQuestionsWithConsistentOrder()
    {
        // Cek apakah sudah ada urutan tersimpan
        if ($this->userProgress && $this->userProgress->questions_order) {
            $questionIds = json_decode($this->userProgress->questions_order, true);
            $this->questions = [];
            
            // Load questions sesuai urutan tersimpan
            foreach ($questionIds as $questionId) {
                $question = $this->currentTopic->questions->where('id', $questionId)->first();
                if ($question) {
                    $this->questions[] = $question->toArray();
                }
            }
        } else {
            // Shuffle dan simpan urutan baru
            $this->questions = $this->currentTopic->questions->shuffle()->toArray();
            $questionIds = array_column($this->questions, 'id');
            
            if ($this->userProgress) {
                $this->userProgress->update([
                    'questions_order' => json_encode($questionIds)
                ]);
            }
        }
        
        $this->totalQuestions = count($this->questions);
    }

    public function selectTopic($topicId)
    {
        $this->currentTopic = Topic::with('questions')->findOrFail($topicId);
        $this->showTopicList = false;
        
        // Load atau buat progress user
        $this->loadUserProgress();
        
        // Load questions dengan urutan yang konsisten
        $this->loadQuestionsWithConsistentOrder();
        
        // Set soal pertama atau lanjutkan dari checkpoint
        if ($this->userProgress && $this->userProgress->last_viewed_question_id) {
            $this->continueFromCheckpoint();
        } else {
            $this->currentQuestionIndex = 0;
            $this->loadCurrentQuestion();
        }
        
        // Emit event untuk update sidebar
        $this->dispatch('topicChanged', topicId: $topicId);
    }

    private function loadUserProgress()
    {
        $this->userProgress = UserPracticeProgress::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'topic_id' => $this->currentTopic->id,
            ],
            [
                'last_viewed_question_id' => null,
                'completed_questions_list' => json_encode([]),
                'completed_questions' => 0,
                'questions_order' => null,
            ]
        );

        $this->completedQuestions = json_decode($this->userProgress->completed_questions_list, true) ?? [];
    }

    private function continueFromCheckpoint()
    {
        $lastQuestionId = $this->userProgress->last_viewed_question_id;
        
        // Cari index soal terakhir yang dilihat
        foreach ($this->questions as $index => $question) {
            if ($question['id'] == $lastQuestionId) {
                $this->currentQuestionIndex = $index;
                break;
            }
        }
        
        $this->loadCurrentQuestion();
    }

    private function loadCurrentQuestion()
    {
        if (isset($this->questions[$this->currentQuestionIndex])) {
            $questionData = $this->questions[$this->currentQuestionIndex];
            $this->currentQuestion = Question::find($questionData['id']);
            
            // Reset state untuk soal baru
            $this->userAnswer = null;
            $this->showExplanation = false;
            $this->isAnswered = in_array($this->currentQuestion->id, $this->completedQuestions);
            $this->isCorrect = false;
            
            // Update checkpoint
            $this->updateCheckpoint();
        }
    }

    private function updateCheckpoint()
    {
        if ($this->userProgress && $this->currentQuestion) {
            $this->userProgress->update([
                'last_viewed_question_id' => $this->currentQuestion->id,
            ]);
        }
    }

    public function selectAnswer($answer)
    {
        if ($this->isAnswered) return;
        
        $this->userAnswer = $answer;
        $this->isCorrect = ($answer === $this->currentQuestion->correct_answer);
        $this->isAnswered = true;
        $this->showExplanation = true;
        
        // Tambahkan ke daftar soal yang sudah dijawab
        if (!in_array($this->currentQuestion->id, $this->completedQuestions)) {
            $this->completedQuestions[] = $this->currentQuestion->id;
            
            // Update progress di database
            $this->userProgress->update([
                'completed_questions_list' => json_encode($this->completedQuestions),
                'completed_questions' => count($this->completedQuestions),
            ]);
        }
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->totalQuestions - 1) {
            $this->currentQuestionIndex++;
            $this->loadCurrentQuestion();
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->loadCurrentQuestion();
        }
    }

    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < $this->totalQuestions) {
            $this->currentQuestionIndex = $index;
            $this->loadCurrentQuestion();
        }
    }

    public function resetProgress()
    {
        if ($this->userProgress) {
            $this->userProgress->update([
                'last_viewed_question_id' => null,
                'completed_questions_list' => json_encode([]),
                'completed_questions' => 0,
                'questions_order' => null,
            ]);
        }
        
        $this->completedQuestions = [];
        $this->currentQuestionIndex = 0;
        
        // Reload questions dengan urutan baru
        $this->loadQuestionsWithConsistentOrder();
        $this->loadCurrentQuestion();
        
        session()->flash('message', 'Progress latihan telah direset.');
    }

    public function getProgressPercentage()
    {
        if ($this->totalQuestions === 0) return 0;
        return round((count($this->completedQuestions) / $this->totalQuestions) * 100);
    }

    public function render()
    {
        return view('livewire.bank-soal.practice-area');
    }
}



