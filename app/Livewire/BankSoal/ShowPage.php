<?php

namespace App\Livewire\BankSoal;

use Livewire\Component;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\UserPracticeProgress;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShowPage extends Component
{
    public Subject $subject;
    public ?int $selectedTopicId = null;
    public ?Topic $selectedTopic = null;

    public ?Question $currentQuestion = null;
    public ?int $currentQuestionId = null;
    public int $currentQuestionIndex = 0;

    /**
     * Ordered list of question IDs for the active topic.
     *
     * @var array<int>
     */
    public array $questionIds = [];
    public int $questionsCount = 0;

    /**
     * Cache for loaded question models keyed by their ID.
     *
     * @var array<int, \App\Models\Question>
     */
    protected array $questionCache = [];

    public ?string $selectedAnswer = null;
    public bool $showExplanation = false;
    public ?bool $isCorrect = null;

    public ?UserPracticeProgress $userProgress = null;
    public array $completedQuestionIds = [];
    public array $questionAnswers = [];
    public int $correctCount = 0;
    public int $incorrectCount = 0;

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
        $this->selectedTopic = Topic::query()->withCount('questions')->find($topicId);

        $this->resetQuestionState();

        if (! $this->selectedTopic) {
            return;
        }

        $this->questionIds = Question::query()
            ->where('topic_id', $this->selectedTopic->id)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $this->questionsCount = count($this->questionIds);

        if ($this->questionsCount === 0) {
            return;
        }

        $this->loadUserProgress();

        $this->determineStartingIndex();

        $this->setCurrentQuestion();
    }

    protected function resetQuestionState(): void
    {
        $this->questionIds = [];
        $this->questionsCount = 0;
        $this->questionCache = [];

        $this->currentQuestion = null;
        $this->currentQuestionId = null;
        $this->currentQuestionIndex = 0;

        $this->selectedAnswer = null;
        $this->showExplanation = false;
        $this->isCorrect = null;

        $this->completedQuestionIds = [];
        $this->questionAnswers = [];
        $this->correctCount = 0;
        $this->incorrectCount = 0;
    }

    public function loadUserProgress(): void
    {
        if (! Auth::check() || ! $this->selectedTopic) {
            return;
        }

        $this->userProgress = UserPracticeProgress::where('user_id', Auth::id())
            ->where('topic_id', $this->selectedTopic->id)
            ->first();

        if (! $this->userProgress) {
            return;
        }

        $this->completedQuestionIds = $this->decodeList($this->userProgress->completed_questions_list);
        $this->questionAnswers = $this->decodeAssociativeList($this->userProgress->question_answers);
        $this->correctCount = (int) ($this->userProgress->correct_count ?? 0);
        $this->incorrectCount = (int) ($this->userProgress->incorrect_count ?? 0);

        $savedOrder = $this->decodeList($this->userProgress->questions_order);

        if (! empty($savedOrder)) {
            $orderedIds = array_values(array_intersect($savedOrder, $this->questionIds));
            $missingIds = array_values(array_diff($this->questionIds, $orderedIds));
            $this->questionIds = array_merge($orderedIds, $missingIds);
            $this->questionsCount = count($this->questionIds);
        }
    }

    protected function determineStartingIndex(): void
    {
        if (! $this->userProgress || $this->questionsCount === 0) {
            return;
        }

        $lastViewedId = $this->userProgress->last_viewed_question_id;

        if (! $lastViewedId) {
            return;
        }

        $index = array_search($lastViewedId, $this->questionIds, true);

        if ($index !== false) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function setCurrentQuestion(): void
    {
        $questionId = $this->questionIds[$this->currentQuestionIndex] ?? null;

        if (! $questionId) {
            $this->currentQuestion = null;
            $this->currentQuestionId = null;
            return;
        }

        $this->currentQuestionId = $questionId;
        $this->currentQuestion = $this->resolveQuestion($questionId);
        $this->selectedAnswer = Arr::get($this->questionAnswers, $questionId.'.answer');
        $this->showExplanation = false;
        $this->isCorrect = null;

        $this->persistProgress();
    }

    protected function resolveQuestion(int $questionId): ?Question
    {
        if (! isset($this->questionCache[$questionId])) {
            $this->questionCache[$questionId] = Question::select([
                'id',
                'topic_id',
                'question_text',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'option_e',
                'correct_answer',
                'explanation',
            ])->find($questionId);
        }

        return $this->questionCache[$questionId] ?? null;
    }

    public function selectAnswer(string $answer): void
    {
        if (! $this->currentQuestion) {
            return;
        }

        $this->selectedAnswer = $answer;
        $this->isCorrect = null;
        $this->showExplanation = false;
    }

    public function checkAnswer(): void
    {
        if (! $this->currentQuestion) {
            return;
        }

        if ($this->selectedAnswer === null) {
            $this->isCorrect = null;
            $this->showExplanation = true;
            $this->persistProgress();

            return;
        }

        $this->isCorrect = Str::lower($this->selectedAnswer) === Str::lower($this->currentQuestion->correct_answer);
        $this->showExplanation = true;

        $this->registerAnswer();
        $this->persistProgress();
    }

    protected function registerAnswer(): void
    {
        if (! $this->currentQuestionId) {
            return;
        }

        $this->questionAnswers[$this->currentQuestionId] = [
            'answer' => $this->selectedAnswer,
            'is_correct' => $this->isCorrect,
        ];

        if (! in_array($this->currentQuestionId, $this->completedQuestionIds, true)) {
            $this->completedQuestionIds[] = $this->currentQuestionId;
        }

        $this->correctCount = collect($this->questionAnswers)->where('is_correct', true)->count();
        $this->incorrectCount = collect($this->questionAnswers)->where('is_correct', false)->count();
    }

    protected function persistProgress(): void
    {
        if (! Auth::check() || ! $this->selectedTopic || ! $this->currentQuestionId) {
            return;
        }

        UserPracticeProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'topic_id' => $this->selectedTopic->id,
            ],
            [
                'last_viewed_question_id' => $this->currentQuestionId,
                'completed_questions' => count($this->completedQuestionIds),
                'completed_questions_list' => $this->encodeList($this->completedQuestionIds),
                'questions_order' => $this->encodeList($this->questionIds),
                'question_answers' => $this->encodeList($this->questionAnswers),
                'correct_count' => $this->correctCount,
                'incorrect_count' => $this->incorrectCount,
            ]
        );
    }

    public function nextQuestion(): void
    {
        if (! $this->currentQuestion) {
            return;
        }

        if (! $this->showExplanation) {
            $this->checkAnswer();

            return;
        }

        $nextIndex = $this->currentQuestionIndex + 1;

        if ($nextIndex >= $this->questionsCount) {
            $nextIndex = 0;
        }

        $this->currentQuestionIndex = $nextIndex;
        $this->setCurrentQuestion();
    }

    public function prevQuestion(): void
    {
        if (! $this->currentQuestion) {
            return;
        }

        if ($this->currentQuestionIndex <= 0) {
            $this->currentQuestionIndex = 0;
        } else {
            $this->currentQuestionIndex--;
        }

        $this->setCurrentQuestion();
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < $this->questionsCount) {
            $this->currentQuestionIndex = $index;
            $this->setCurrentQuestion();
        }
    }

    public function render()
    {
        $topics = $this->subject
            ->topics()
            ->withCount('questions')
            ->orderBy('name')
            ->get();

        return view('livewire.bank-soal.show-page', [
            'topics' => $topics,
            'questionCount' => $this->questionsCount,
        ])->layout('layouts.app', ['title' => 'Latihan: ' . $this->subject->name]);
    }

    /**
     * @return array<int>
     */
    protected function decodeList($value): array
    {
        if (! $value) {
            return [];
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_unique($decoded));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function decodeAssociativeList($value): array
    {
        if (! $value) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  array<mixed>  $value
     */
    protected function encodeList(array $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return json_encode($value);
    }
}
