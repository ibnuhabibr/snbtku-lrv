<?php

namespace App\Livewire;

use App\Models\Question;
use App\Models\UserTryout;
use App\Models\UserTryoutAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConductTryout extends Component
{
    public UserTryout $userTryout;

    /** @var Collection<int, Question> */
    public Collection $questions;

    public int $currentQuestionIndex = 0;

    /** @var array<int, string> */
    public array $userAnswers = [];

    public ?int $timeRemaining = null;

    public bool $showSubmitModal = false;

    public function mount(int $userTryoutId): void
    {
        $this->userTryout = UserTryout::with([
            'tryoutPackage.questions' => fn ($query) => $query->orderBy('tryout_package_question.order'),
            'userTryoutAnswers',
        ])
            ->where('id', $userTryoutId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        abort_if($this->userTryout->status !== 'ongoing', 403, 'Tryout tidak aktif.');

        $this->questions = $this->userTryout->tryoutPackage->questions->values();
        $this->userAnswers = $this->userTryout->userTryoutAnswers
            ->pluck('user_answer', 'question_id')
            ->toArray();

        $this->currentQuestionIndex = 0;
        $this->timeRemaining = $this->calculateTimeRemaining();
    }

    public function getCurrentQuestionProperty(): ?Question
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < $this->questions->count() - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function prevQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < $this->questions->count()) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function selectAnswer(int $questionId, string $answer): void
    {
        if (!$questionId || !in_array($answer, ['a', 'b', 'c', 'd', 'e'], true)) {
            return;
        }

        $questionExists = $this->questions->firstWhere('id', $questionId);

        if (!$questionExists) {
            return;
        }

        $this->userAnswers[$questionId] = $answer;

        UserTryoutAnswer::updateOrCreate(
            [
                'user_tryout_id' => $this->userTryout->id,
                'question_id' => $questionId,
            ],
            [
                'user_answer' => $answer,
            ]
        );

        $this->dispatch('answer-selected', questionId: $questionId, answer: $answer);
    }

    public function toggleSubmitModal(): void
    {
        $this->showSubmitModal = !$this->showSubmitModal;
    }

    public function submitTryout(): mixed
    {
        $questionIds = $this->questions->pluck('id');

        $answers = $this->userTryout->userTryoutAnswers()
            ->whereIn('question_id', $questionIds)
            ->with('question')
            ->get();

        $correctCount = 0;

        foreach ($answers as $answer) {
            $isCorrect = $answer->question && $answer->user_answer === $answer->question->correct_answer;
            $answer->update(['is_correct' => $isCorrect]);

            if ($isCorrect) {
                $correctCount++;
            }
        }

        $totalQuestions = $this->questions->count();
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 1000, 2) : 0;

        $this->userTryout->update([
            'status' => 'completed',
            'end_time' => now(),
            'score' => $score,
        ]);

        session()->flash('success', 'Tryout selesai. Skor Anda: ' . $score);

        return $this->redirectRoute('tryout.result', ['userTryout' => $this->userTryout->id]);
    }

    public function render()
    {
        return view('livewire.conduct-tryout', [
            'currentQuestion' => $this->currentQuestion,
            'totalQuestions' => $this->questions->count(),
        ]);
    }

    private function calculateTimeRemaining(): ?int
    {
        $durationMinutes = (int) ($this->userTryout->tryoutPackage->duration_minutes ?? 0);
        $totalSeconds = max($durationMinutes * 60, 0);

        if (!$totalSeconds) {
            return null;
        }

        if (!$this->userTryout->start_time) {
            return $totalSeconds;
        }

        $elapsedSeconds = $this->userTryout->start_time->diffInSeconds(now());

        return max($totalSeconds - $elapsedSeconds, 0);
    }
}
