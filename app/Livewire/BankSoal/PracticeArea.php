<?php

namespace App\Livewire\BankSoal;

use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\UserPracticeProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class PracticeArea extends Component
{
    public Subject $subject;
    public ?Topic $currentTopic = null;
    public ?Question $currentQuestion = null;

    /** @var array<int,int> */
    public array $questionIds = [];

    /** @var array<int,int> */
    public array $questionIndexMap = [];

    /** @var array<int,\App\Models\Question> */
    protected array $questionCache = [];

    /** @var array<int,array{answer:?string,correct:?bool}> */
    public array $questionAnswers = [];

    /** @var array<int> */
    public array $flaggedQuestions = [];

    /** @var array<int> */
    public array $completedQuestions = [];

    public int $currentQuestionIndex = 0;
    public int $totalQuestions = 0;
    public ?string $userAnswer = null;
    public bool $showExplanation = false;
    public bool $showTopicList = false;
    public bool $isAnswered = false;
    public ?bool $isCorrect = null;
    public ?UserPracticeProgress $userProgress = null;
    public int $correctCount = 0;
    public int $incorrectCount = 0;
    public string $filterMode = 'all';
    public bool $showResetModal = false;

    protected $listeners = ['topicSelected' => 'handleTopicSelected'];

    public function mount(Subject $subject): void
    {
        $topics = Cache::remember(
            "subject-topics-with-count-{$subject->id}",
            now()->addMinutes(10),
            fn () => $subject->topics()->withCount('questions')->orderBy('name')->get()
        );

        $subject->setRelation('topics', $topics);
        $this->subject = $subject;
    }

    public function handleTopicSelected($payload): void
    {
        $topicId = $payload['topicId'] ?? $payload;
        $this->selectTopic((int) $topicId);
    }

    public function toggleTopicList(): void
    {
        $this->showTopicList = ! $this->showTopicList;
    }

    public function selectTopic(int $topicId): void
    {
        $this->currentTopic = Topic::query()->select('id', 'name')->findOrFail($topicId);
        $this->showTopicList = false;
        $this->filterMode = 'all';

        $this->loadUserProgress();
        $this->loadQuestionsWithConsistentOrder();

        if (
            $this->userProgress
            && $this->userProgress->last_viewed_question_id
            && isset($this->questionIndexMap[$this->userProgress->last_viewed_question_id])
        ) {
            $this->currentQuestionIndex = $this->questionIndexMap[$this->userProgress->last_viewed_question_id];
        } else {
            $this->currentQuestionIndex = 0;
        }

        $this->loadCurrentQuestion();
        $this->dispatch('topicChanged', topicId: $topicId);
    }

    public function setFilterMode(string $mode): void
    {
        if (in_array($mode, ['all', 'unanswered', 'incorrect', 'flagged'], true)) {
            $this->filterMode = $mode;
        }
    }

    public function toggleFlag(?int $questionId = null): void
    {
        $questionId = $questionId ?? $this->currentQuestion?->id;
        if (!$questionId) {
            return;
        }

        if (in_array($questionId, $this->flaggedQuestions, true)) {
            $this->flaggedQuestions = array_values(array_diff($this->flaggedQuestions, [$questionId]));
        } else {
            $this->flaggedQuestions[] = $questionId;
        }

        $this->persistProgressState();
    }

    public function isQuestionFlagged(int $questionId): bool
    {
        return in_array($questionId, $this->flaggedQuestions, true);
    }

    public function shouldShowQuestion(int $questionId): bool
    {
        return match ($this->filterMode) {
            'unanswered' => !isset($this->questionAnswers[$questionId]),
            'incorrect' => isset($this->questionAnswers[$questionId]) && $this->questionAnswers[$questionId]['correct'] === false,
            'flagged' => in_array($questionId, $this->flaggedQuestions, true),
            default => true,
        };
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < $this->totalQuestions - 1) {
            $this->currentQuestionIndex++;
            $this->loadCurrentQuestion();
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->loadCurrentQuestion();
        }
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < $this->totalQuestions) {
            $this->currentQuestionIndex = $index;
            $this->loadCurrentQuestion();
        }
    }

    public function selectAnswer(string $answer): void
    {
        if (! $this->currentQuestion) {
            return;
        }

        $questionId = $this->currentQuestion->id;

        if (isset($this->questionAnswers[$questionId])) {
            return;
        }

        $isCorrect = $answer === $this->currentQuestion->correct_answer;

        $this->userAnswer = $answer;
        $this->isCorrect = $isCorrect;
        $this->isAnswered = true;
        $this->showExplanation = true;

        $this->questionAnswers[$questionId] = [
            'answer' => $answer,
            'correct' => $isCorrect,
        ];

        if (!in_array($questionId, $this->completedQuestions, true)) {
            $this->completedQuestions[] = $questionId;
        }

        if ($isCorrect) {
            $this->correctCount++;
        } else {
            $this->incorrectCount++;
        }

        $this->persistProgressState();
    }

    public function requestReset(): void
    {
        $this->showResetModal = true;
    }

    public function cancelReset(): void
    {
        $this->showResetModal = false;
    }

    public function confirmReset(): void
    {
        $this->performProgressReset();
        $this->showResetModal = false;
        session()->flash('message', 'Progress latihan telah direset.');
    }

    public function getProgressPercentage(): int
    {
        if ($this->totalQuestions === 0) {
            return 0;
        }

        return (int) round((count($this->completedQuestions) / $this->totalQuestions) * 100);
    }

    public function getAttemptedCountProperty(): int
    {
        return $this->correctCount + $this->incorrectCount;
    }

    public function getAccuracyProperty(): ?float
    {
        $attempted = $this->attemptedCount;

        if ($attempted === 0) {
            return null;
        }

        return round(($this->correctCount / $attempted) * 100, 1);
    }

    public function render()
    {
        return view('livewire.bank-soal.practice-area');
    }

    private function loadUserProgress(): void
    {
        $defaults = [
            'last_viewed_question_id' => null,
            'completed_questions_list' => json_encode([]),
            'completed_questions' => 0,
            'questions_order' => null,
            'correct_count' => 0,
            'incorrect_count' => 0,
            'question_answers' => json_encode([]),
            'flagged_questions_list' => json_encode([]),
        ];

        $this->userProgress = UserPracticeProgress::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'topic_id' => $this->currentTopic->id,
            ],
            $defaults
        );

        $this->correctCount = (int) ($this->userProgress->correct_count ?? 0);
        $this->incorrectCount = (int) ($this->userProgress->incorrect_count ?? 0);

        $decodedAnswers = json_decode($this->userProgress->question_answers ?? '[]', true) ?? [];
        $this->questionAnswers = [];
        foreach ($decodedAnswers as $key => $data) {
            $questionId = (int) $key;
            $this->questionAnswers[$questionId] = [
                'answer' => $data['answer'] ?? null,
                'correct' => array_key_exists('correct', $data) ? (bool) $data['correct'] : null,
            ];
        }

        $legacyCompleted = json_decode($this->userProgress->completed_questions_list ?? '[]', true) ?? [];
        $this->completedQuestions = array_values(array_unique(array_map('intval', array_merge(array_keys($this->questionAnswers), $legacyCompleted))));

        $this->flaggedQuestions = array_map(
            'intval',
            json_decode($this->userProgress->flagged_questions_list ?? '[]', true) ?? []
        );
    }

    private function loadQuestionsWithConsistentOrder(bool $resetOrder = false): void
    {
        $availableIds = Question::query()
            ->where('topic_id', $this->currentTopic->id)
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($availableIds)) {
            $this->questionIds = [];
            $this->questionIndexMap = [];
            $this->totalQuestions = 0;
            $this->questionCache = [];
            return;
        }

        $storedOrder = [];
        if (
            ! $resetOrder
            && $this->userProgress
            && $this->userProgress->questions_order
        ) {
            $storedOrder = array_map('intval', json_decode($this->userProgress->questions_order, true) ?? []);
        }

        $questionIds = [];

        if (!empty($storedOrder)) {
            $existingIds = array_flip($availableIds);
            foreach ($storedOrder as $id) {
                if (isset($existingIds[$id])) {
                    $questionIds[] = $id;
                }
            }
            $missing = array_values(array_diff($availableIds, $questionIds));
            $questionIds = array_merge($questionIds, $missing);
        } else {
            $questionIds = $availableIds;
            shuffle($questionIds);
        }

        $this->questionIds = $questionIds;
        $this->questionIndexMap = array_flip($questionIds);
        $this->totalQuestions = count($questionIds);
        $this->questionCache = [];

        if ($this->userProgress) {
            $this->userProgress->update([
                'questions_order' => json_encode($this->questionIds),
            ]);
        }
    }

    private function loadCurrentQuestion(): void
    {
        if (!isset($this->questionIds[$this->currentQuestionIndex])) {
            $this->currentQuestion = null;
            $this->userAnswer = null;
            $this->isCorrect = null;
            $this->isAnswered = false;
            $this->showExplanation = false;
            return;
        }

        $questionId = $this->questionIds[$this->currentQuestionIndex];
        $this->currentQuestion = $this->getQuestionModel($questionId);

        if (!$this->currentQuestion) {
            $this->userAnswer = null;
            $this->isCorrect = null;
            $this->isAnswered = false;
            $this->showExplanation = false;
            return;
        }

        $answerData = $this->questionAnswers[$questionId] ?? null;

        $this->userAnswer = $answerData['answer'] ?? null;
        $this->isCorrect = $answerData['correct'] ?? null;
        $this->isAnswered = $answerData !== null;
        $this->showExplanation = $this->isAnswered;

        $this->updateCheckpoint($questionId);
    }

    private function getQuestionModel(int $questionId): ?Question
    {
        if (!isset($this->questionCache[$questionId])) {
            $this->questionCache[$questionId] = Question::query()
                ->select([
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
                ])
                ->with('topic:id,name')
                ->find($questionId);
        }

        return $this->questionCache[$questionId];
    }

    private function persistProgressState(): void
    {
        if (! $this->userProgress) {
            return;
        }

        $answersPayload = [];
        foreach ($this->questionAnswers as $questionId => $data) {
            $answersPayload[(string) $questionId] = [
                'answer' => $data['answer'],
                'correct' => $data['correct'],
            ];
        }

        $this->userProgress->update([
            'last_viewed_question_id' => $this->currentQuestion?->id,
            'completed_questions_list' => json_encode($this->completedQuestions),
            'completed_questions' => count($this->completedQuestions),
            'correct_count' => $this->correctCount,
            'incorrect_count' => $this->incorrectCount,
            'questions_order' => json_encode($this->questionIds),
            'question_answers' => json_encode($answersPayload),
            'flagged_questions_list' => json_encode(array_values($this->flaggedQuestions)),
        ]);
    }

    private function updateCheckpoint(int $questionId): void
    {
        if ($this->userProgress) {
            $this->userProgress->update([
                'last_viewed_question_id' => $questionId,
            ]);
        }
    }

    private function performProgressReset(): void
    {
        $this->questionAnswers = [];
        $this->flaggedQuestions = [];
        $this->completedQuestions = [];
        $this->correctCount = 0;
        $this->incorrectCount = 0;
        $this->currentQuestionIndex = 0;
        $this->questionCache = [];
        $this->filterMode = 'all';
        $this->userAnswer = null;
        $this->isAnswered = false;
        $this->isCorrect = null;
        $this->showExplanation = false;

        if ($this->userProgress) {
            $this->userProgress->update([
                'last_viewed_question_id' => null,
                'completed_questions_list' => json_encode([]),
                'completed_questions' => 0,
                'questions_order' => null,
                'correct_count' => 0,
                'incorrect_count' => 0,
                'question_answers' => json_encode([]),
                'flagged_questions_list' => json_encode([]),
            ]);
        }

        $this->loadQuestionsWithConsistentOrder(resetOrder: true);
        $this->loadCurrentQuestion();
        $this->persistProgressState();
    }
}
