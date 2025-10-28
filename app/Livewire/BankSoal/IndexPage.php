<?php

namespace App\Livewire\BankSoal;

use Livewire\Component;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class IndexPage extends Component
{
    public $subjects;

    public function mount()
    {
        $this->subjects = Subject::whereNotNull('subtest_order')
                                ->orderBy('subtest_order')
                                ->get();
    }

    public function render(): View
    {
        return view('livewire.bank-soal.index-page')
                ->layout('layouts.app', ['title' => 'Bank Soal Latihan']);
    }
}
