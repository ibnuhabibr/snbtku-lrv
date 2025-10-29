<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
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
        // Load topik untuk sidebar
        $subject->load(['topics' => fn($q) => $q->withCount('questions')->orderBy('name')]); 

        // View Blade ini akan berisi komponen Livewire untuk area kanan
        return view('bank-soal.show-subject', compact('subject')); 
    }
}
