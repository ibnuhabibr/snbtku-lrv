<?php

namespace App\Http\Controllers;

use App\Models\TryoutPackage;
use App\Models\UserTryout;
use App\Models\UserSubtestProgress;
use App\Models\UserTryoutAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TryoutController extends Controller
{
    /**
     * Display a listing of tryout packages.
     */
    public function index()
    {
        return view('tryouts.index');
    }

    /**
     * Show the conduct tryout page.
     */
    public function conduct(UserTryout $userTryout)
    {
        // Check if user owns this tryout
        if ($userTryout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this tryout.');
        }

        // Check if tryout is still ongoing
        if ($userTryout->status !== 'ongoing') {
            return redirect()->route('tryout.result', $userTryout->id);
        }

        return view('tryouts.conduct', compact('userTryout'));
    }

    /**
     * Show the tryout result page.
     */
    public function result(UserTryout $userTryout)
    {
        // Check if user owns this tryout
        if ($userTryout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this tryout result.');
        }

        // Check if tryout is completed
        if ($userTryout->status !== 'completed') {
            return redirect()->route('tryout.conduct', $userTryout->id);
        }

        return view('tryouts.result', compact('userTryout'));
    }

    /**
     * Show the tryout detail page (Hub 7 Subtes).
     */
    public function detail(UserTryout $userTryout)
    {
        // Check if user owns this tryout
        if ($userTryout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this tryout.');
        }

        // Load subtest progresses with subjects
        $userTryout->load(['subtestProgresses.subject']);

        return view('tryouts.detail', compact('userTryout'));
    }

    /**
     * Show the conduct subtest page (Livewire component wrapper).
     */
    public function conductSubtest(UserSubtestProgress $subtestProgress): View|RedirectResponse
    {
        // Load the userTryout relation to ensure it's available
        $subtestProgress->load('userTryout');
        
        // 1. Check user ownership (INI PENGECEKAN YANG BENAR)
        // Kita cek apakah user_id di UserTryout INDUKNYA sama dengan user yang login
        if ($subtestProgress->userTryout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // 2. Check subtest status (Hanya unlocked atau ongoing yang boleh) - Biarkan ini
        if (!in_array($subtestProgress->status, ['unlocked', 'ongoing'])) {
            session()->flash('error', 'Subtes ini tidak dapat dikerjakan saat ini.');
            return redirect()->route('tryout.detail', $subtestProgress->user_tryout_id);
        }

        // 3. Jika status 'unlocked', ubah jadi 'ongoing' dan set start time - Biarkan ini
        if ($subtestProgress->status === 'unlocked') {
            $subtestProgress->update([
                'status' => 'ongoing',
                'started_at' => now(),
            ]);
        }

        // 4. Return the view - Biarkan ini
        return view('tryouts.conduct-subtest', compact('subtestProgress'));
    }

    /**
     * Show the subtest review page with answers and explanations.
     */
    public function reviewSubtest(UserSubtestProgress $subtestProgress): View|RedirectResponse
    {
        // 1. Check user ownership
        if ($subtestProgress->userTryout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // 2. Check if subtest is completed
        if ($subtestProgress->status !== 'completed') {
            session()->flash('error', 'Pembahasan hanya tersedia untuk subtes yang sudah selesai.');
            return redirect()->route('tryout.detail', $subtestProgress->user_tryout_id);
        }

        // 3. Load necessary data
        $subtestProgress->load(['subject', 'userTryout.tryoutPackage']);

        // 4. Get all questions for this subtest in this package, ordered correctly
        $questions = Question::whereHas('topic.subject', fn($q) => 
            $q->where('id', $subtestProgress->subject_id)
        )
        ->whereHas('tryoutPackages', fn($q) => 
            $q->where('tryout_package_id', $subtestProgress->userTryout->tryout_package_id)
        )
        ->with(['tryoutPackages' => fn($q) => 
            $q->where('tryout_package_id', $subtestProgress->userTryout->tryout_package_id)
        ])
        ->get()
        ->sortBy(function($question) use ($subtestProgress) { // Pastikan sorting by pivot order
             // Cari pivot data yang sesuai
             $pivot = $question->tryoutPackages->firstWhere('id', $subtestProgress->userTryout->tryout_package_id);
             return $pivot ? $pivot->pivot->order : 999;
        })->values(); // Reset keys

        // 5. Get user answers for these questions in this specific tryout session
        $userAnswers = UserTryoutAnswer::where('user_tryout_id', $subtestProgress->user_tryout_id)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id'); // Key by question_id for easy lookup in view

        // 6. Return the review view (akan dibuat di prompt berikutnya)
        return view('tryouts.review', compact('subtestProgress', 'questions', 'userAnswers'));
    }

    /**
     * Show the overall tryout result page.
     */
    public function overallResult(UserTryout $userTryout): View|RedirectResponse
    {
        // 1. Check user ownership
        if ($userTryout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this tryout result.');
        }

        // 2. Load necessary data 
        $userTryout->load(['subtestProgresses.subject', 'tryoutPackage.questions']);

        // 3. Check completion
        $completedSubtestsCount = $userTryout->subtestProgresses->where('status', 'completed')->count();
        if ($completedSubtestsCount < $userTryout->subtestProgresses->count()) {
            session()->flash('error', 'Tryout ini belum selesai sepenuhnya.');
            return redirect()->route('tryout.detail', $userTryout->id);
        }

        // --- PERBAIKAN LOGIC SKOR & DATA ---
        $allPackageQuestions = $userTryout->tryoutPackage->questions;
        $totalOverallQuestions = $allPackageQuestions->count();

        // Ambil SEMUA jawaban user untuk tryout ini
        $allUserAnswers = UserTryoutAnswer::where('user_tryout_id', $userTryout->id)
                                ->where('is_correct', true) // Hanya ambil yg benar
                                ->pluck('question_id'); // Ambil ID soal yg benar
        $totalOverallCorrect = $allUserAnswers->count();

        // Hitung Skor Keseluruhan baru (skor = (benar/total) * 1000)
        $overallScore = $totalOverallQuestions > 0 ? round(($totalOverallCorrect / $totalOverallQuestions) * 1000, 2) : 0;

        // Siapkan data detail per subtes
        $subtestDetails = collect();
        foreach ($userTryout->subtestProgresses as $progress) {
            // Hitung total soal untuk subtes ini dalam paket
            $questionsInSubtest = $allPackageQuestions->filter(function ($question) use ($progress) {
                return $question->topic->subject_id == $progress->subject_id;
            });
            $totalQuestionsInSubtest = $questionsInSubtest->count();

            // Hitung total jawaban benar user untuk subtes ini
            $correctAnswersInSubtest = $allUserAnswers->intersect($questionsInSubtest->pluck('id'))->count();

            // Hitung durasi (jika ada start & completed time) dengan pembulatan
            $duration = null;
            if ($progress->started_at && $progress->completed_at) {
                $durationMinutes = $progress->started_at->diffInMinutes($progress->completed_at);
                $duration = round($durationMinutes, 1); // Pembulatan 1 desimal
            }

            // Hitung skor subtes (skor = (benar/total) * 1000)
            $subtestScore = $totalQuestionsInSubtest > 0 ? round(($correctAnswersInSubtest / $totalQuestionsInSubtest) * 1000, 2) : 0;

            $subtestDetails->push([
                'name' => $progress->subject->name,
                'total_questions' => $totalQuestionsInSubtest, // <-- Total soal subtes
                'correct_answers' => $correctAnswersInSubtest, // <-- Jumlah benar subtes
                'score' => $subtestScore, // Format baru: jumlah benar : total soal
                'duration' => $duration, // Durasi pengerjaan dengan pembulatan
                'status' => 'Selesai',
                'review_link' => route('tryout.review', $progress->id)
            ]);
        }

        // Hitung Statistik Ringkasan (jika perlu)
        $totalIncorrect = UserTryoutAnswer::where('user_tryout_id', $userTryout->id)->where('is_correct', false)->count();
        $totalUnanswered = $totalOverallQuestions - $totalOverallCorrect - $totalIncorrect;
        $totalDurationMinutes = $subtestDetails->sum('duration');

        // (Opsional) Update skor & status UserTryout - simpan skor dalam format string
        if ($userTryout->score === null || $userTryout->score != $overallScore) {
            $userTryout->update(['score' => $overallScore]);
        }
        if ($userTryout->status !== 'completed') {
             $userTryout->update(['status' => 'completed', 'end_time' => now()]);
        }
        // --- AKHIR PERBAIKAN LOGIC SKOR & DATA ---

        // Ambil tryoutPackage untuk dikirim ke view
        $tryoutPackage = $userTryout->tryoutPackage;

        // 5. Return the result view dengan data BARU
        return view('tryouts.overall-result', compact(
            'userTryout', 
            'tryoutPackage', // Tambahkan variabel tryoutPackage
            'overallScore', 
            'subtestDetails', // Kirim data detail per subtes
            'totalOverallQuestions', // Kirim total soal keseluruhan
            'totalOverallCorrect', // Kirim total benar keseluruhan
            'totalIncorrect', // Kirim total salah keseluruhan
            'totalUnanswered', // Kirim total tak dijawab
            'totalDurationMinutes' // Kirim total durasi
        ));
    }
}
