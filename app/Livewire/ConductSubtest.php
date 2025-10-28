<?php
// app/Livewire/ConductSubtest.php

namespace App\Livewire;

use App\Models\UserSubtestProgress;
use App\Models\UserTryoutAnswer;
use App\Models\Question;
use App\Models\Subject;
use Livewire\Component;
use Livewire\Attributes\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ConductSubtest extends Component
{
    public UserSubtestProgress $subtestProgress;
    public Collection $questions;
    public int $currentQuestionIndex = 0;
    public ?Question $currentQuestion = null;
    public int $totalQuestions;
    public array $userAnswers = [];
    
    #[Session]
    public array $markedQuestions = [];
    
    public ?int $timeRemaining = null;
    public bool $showConfirmModal = false;

    // Waktu spesifik untuk setiap subtes (sesuai urutan SNBT yang benar)
    public array $subtestDurations = [
        // subject_id => duration_in_minutes
        1 => 30,    // PU (Penalaran Umum)
        2 => 15,    // KMBM (Pemahaman Bacaan dan Menulis) 
        3 => 25,    // PPU (Pengetahuan dan Pemahaman Umum)
        4 => 20,    // PK (Pengetahuan Kuantitatif)
        5 => 42.5,  // Lit Indo (Literasi dalam Bahasa Indonesia)
        6 => 30,    // Lit Ing (Literasi dalam Bahasa Inggris)
        7 => 42.5,  // PM (Penalaran Matematika)
    ];

    public function mount($subtestProgressId)
    {
        // Load UserSubtestProgress beserta relasinya
        $this->subtestProgress = UserSubtestProgress::with(['userTryout.tryoutPackage', 'subject'])
            ->where('id', $subtestProgressId)
            ->whereHas('userTryout', fn($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        // Validasi status lagi (double check)
        if (!in_array($this->subtestProgress->status, ['ongoing'])) {
            session()->flash('error', 'Sesi subtes ini tidak aktif.');
            return $this->redirectRoute('tryout.detail', ['userTryout' => $this->subtestProgress->user_tryout_id]);
        }

        // Load soal HANYA untuk subtes ini
        $this->questions = Question::whereHas('topic.subject', fn($q) =>
            $q->where('id', $this->subtestProgress->subject_id)
        )
        ->whereHas('tryoutPackages', fn($q) => // Pastikan soal ada di paket ini
            $q->where('tryout_package_id', $this->subtestProgress->userTryout->tryout_package_id)
        )
        ->with(['tryoutPackages' => fn($q) => // Eager load pivot order
            $q->where('tryout_package_id', $this->subtestProgress->userTryout->tryout_package_id)
        ])
        ->get()
        ->sortBy(function($question) { // Urutkan berdasarkan pivot order
            return $question->tryoutPackages->first()->pivot->order;
        })->values(); // Reset keys to 0, 1, 2...

        $this->totalQuestions = $this->questions->count();

        // Load jawaban HANYA untuk sesi tryout ini
        $answers = UserTryoutAnswer::where('user_tryout_id', $this->subtestProgress->user_tryout_id)
            ->whereIn('question_id', $this->questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        $this->userAnswers = $answers->map(fn ($answer) => $answer->user_answer)->toArray();

        // Set state awal
        $this->currentQuestionIndex = 0; // Mulai dari soal pertama subtes
        $this->setCurrentQuestion();
        $this->calculateTimeRemaining(); // Panggil kalkulasi waktu
    }

    public function calculateTimeRemaining()
    {
        // Pastikan subtes sudah dimulai
        if (!$this->subtestProgress->started_at) {
            $fullDurationSeconds = floor(($this->subtestDurations[$this->subtestProgress->subject_id] ?? 0) * 60);
            $this->timeRemaining = $fullDurationSeconds;
            return;
        }

        // --- LOGIC BARU (FOKUS AKURASI) ---
        $fullDurationSeconds = floor(($this->subtestDurations[$this->subtestProgress->subject_id] ?? 0) * 60);
        $startTime = $this->subtestProgress->started_at->timestamp; // Gunakan timestamp
        $now = now()->timestamp;
        $elapsedSeconds = $now - $startTime;

        // Jika ada sisa waktu di DB (resume), gunakan itu sebagai batas ATAS
        $remainingFromDb = $this->subtestProgress->time_remaining_seconds;

        if ($remainingFromDb !== null && $this->timeRemaining === null) { // Hanya saat mount awal
             // Hitung sisa waktu berdasarkan start time, TAPI tidak boleh lebih dari sisa di DB
             $calculatedRemaining = max(0, $fullDurationSeconds - $elapsedSeconds);
             $this->timeRemaining = min($remainingFromDb, $calculatedRemaining); // Ambil yg lebih kecil

             // Hapus sisa waktu DB setelah digunakan
             $this->subtestProgress->update(['time_remaining_seconds' => null]);

        } else {
             // Hitung normal berdasarkan start time
             $this->timeRemaining = max(0, $fullDurationSeconds - $elapsedSeconds);
        }
        // --- AKHIR LOGIC BARU ---

        if ($this->timeRemaining !== null && $this->timeRemaining <= 0) {
            if ($this->subtestProgress->status === 'ongoing') {
                $this->submitSubtest();
            }
        }
    }

    // Fungsi Timer yang dipanggil per detik oleh wire:poll
    public function decrementTime()
    {
        // Panggil calculateTimeRemaining untuk update $this->timeRemaining secara akurat
        // calculateTimeRemaining sudah include logic submit otomatis jika waktu habis
        $this->calculateTimeRemaining(); 
    }

    public function setCurrentQuestion()
    {
        if ($this->questions->has($this->currentQuestionIndex)) {
            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        } else {
            $this->currentQuestion = null; // Handle jika index tidak valid
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

    public function goToQuestion(int $index)
    {
        if ($index >= 0 && $index < $this->totalQuestions) {
            $this->currentQuestionIndex = $index;
            $this->setCurrentQuestion();
        }
    }

    public function selectAnswer(string $answer) // Hanya terima $answer, ID didapat dari $currentQuestion
    {
        if (!$this->currentQuestion) return;

        $questionId = $this->currentQuestion->id;
        $this->userAnswers[$questionId] = $answer;

        // Simpan atau update jawaban di database
        UserTryoutAnswer::updateOrCreate(
            [
                'user_tryout_id' => $this->subtestProgress->user_tryout_id,
                'question_id' => $questionId,
            ],
            [
                'user_answer' => $answer,
            ]
        );
    }

    public function toggleMark()
    {
        if (!$this->currentQuestion) return;

        $questionId = $this->currentQuestion->id;
        if (in_array($questionId, $this->markedQuestions)) {
            $this->markedQuestions = array_diff($this->markedQuestions, [$questionId]);
        } else {
            $this->markedQuestions[] = $questionId;
        }
        // Note: Penanda 'ragu-ragu' tidak disimpan di DB, hanya state sementara di UI
    }

    // Dipanggil saat user klik "Selesai Subtes"
    public function submitSubtest()
    {
        // 1. Hitung skor untuk subtes ini
        $correctAnswers = 0;
        $subtestQuestionIds = $this->questions->pluck('id');
        $submittedAnswers = UserTryoutAnswer::where('user_tryout_id', $this->subtestProgress->user_tryout_id)
            ->whereIn('question_id', $subtestQuestionIds)
            ->with('question') // Eager load soal untuk cek jawaban benar
            ->get();

        foreach ($submittedAnswers as $answer) {
            $isCorrect = $answer->question && ($answer->user_answer === $answer->question->correct_answer);
            $answer->update(['is_correct' => $isCorrect]); // Update is_correct di DB
            if ($isCorrect) {
                $correctAnswers++;
            }
        }
        // Hitung skor subtes: (benar/total) * 1000
        $score = $this->totalQuestions > 0 ? round(($correctAnswers / $this->totalQuestions) * 1000, 2) : 0;

        // 2. Update status subtes menjadi 'completed'
        $this->subtestProgress->update([
            'status' => 'completed',
            'score' => $score,
            'time_remaining_seconds' => null, // Waktu habis atau sudah selesai
            'completed_at' => now(),
        ]);

        // 3. Unlock subtes berikutnya
        $currentOrder = $this->subtestProgress->subject->subtest_order;
        $nextSubtestProgress = UserSubtestProgress::where('user_tryout_id', $this->subtestProgress->user_tryout_id)
            ->whereHas('subject', fn($q) => $q->where('subtest_order', $currentOrder + 1))
            ->first();

        if ($nextSubtestProgress && $nextSubtestProgress->status === 'locked') {
            $nextSubtestProgress->update(['status' => 'unlocked']);
        }

        // 4. Reset session marked questions
        $this->reset('markedQuestions');

        // 5. Redirect kembali ke halaman detail
        session()->flash('success', 'Subtes ' . $this->subtestProgress->subject->name . ' telah selesai.');
        return $this->redirectRoute('tryout.detail', ['userTryout' => $this->subtestProgress->user_tryout_id]);
    }

    // Dipanggil saat user keluar halaman (implementasi Poin 4)
    public function saveProgressAndExit()
    {
        if ($this->subtestProgress->status === 'ongoing') {
            // Hitung sisa waktu TERAKHIR sebelum keluar
            $this->calculateTimeRemaining(); 

            // Simpan sisa waktu ke DB
            $this->subtestProgress->update([
                'time_remaining_seconds' => $this->timeRemaining > 0 ? $this->timeRemaining : 0,
            ]);
        }
        // Redirect ke halaman detail (tanpa flash message)
        // Penting: Redirect ini harus terjadi di sisi klien setelah konfirmasi
        $this->dispatch('redirectToDetail', route('tryout.detail', ['userTryout' => $this->subtestProgress->user_tryout_id]));
    }

    public function render()
    {
        // Panggil view baru 'livewire.conduct-subtest'
        return view('livewire.conduct-subtest');
    }
}