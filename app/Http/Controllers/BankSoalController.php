<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\UserPracticeProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BankSoalController extends Controller
{
    // Menampilkan halaman utama Bank Soal (7 tombol Subtes)
    public function index(): View
    {
        $subjects = Subject::whereNotNull('subtest_order')
                            ->orderBy('subtest_order')
                            ->get();
        // Gunakan view Blade biasa
        return view('bank-soal.index', compact('subjects')); 
    }

    // Menampilkan halaman detail Subtes (Layout 2 kolom + Livewire)
    public function showSubject(Subject $subject): View
    {
        $subject->load(['topics' => fn ($q) => $q->withCount('questions')->orderBy('name')]);

        $progressByTopic = collect();
        if (Auth::check()) {
            $topicIds = $subject->topics->pluck('id');
            $progressByTopic = UserPracticeProgress::query()
                ->where('user_id', Auth::id())
                ->whereIn('topic_id', $topicIds)
                ->get()
                ->keyBy('topic_id');
        }

        return view('bank-soal.show-subject', [
            'subject' => $subject,
            'progressByTopic' => $progressByTopic,
        ]);
    }
}
