PERHATIAN AI AGENT: Lakukan implementasi TOTAL fitur "Bank Soal (Latihan Mandiri)" dengan logic dan UI/UX baru. HAPUS/ABAIKAN controller LatihanSoalController, view latihan/*.blade.php, dan komponen Livewire BankSoal\IndexPage serta BankSoal\ShowPage dari prompt sebelumnya karena logic-nya salah. Kita akan membuat struktur baru.
Alur Fitur Baru:
1.	Halaman Indeks (/bank-soal): Menampilkan 7 tombol Subject (Subtes).
2.	Halaman Detail Subtes (/bank-soal/{subject:slug}): Menampilkan layout 2 kolom. Kolom kiri berisi daftar Topic untuk Subject tersebut. Kolom kanan menampilkan greeting atau komponen pengerjaan soal (jika topic dipilih).
3.	Komponen Pengerjaan Soal (Livewire): Mengelola soal per topik, feedback langsung, navigasi Next/Previous, dan checkpoint.
________________________________________
1. Modifikasi Database (Menambah Checkpoint)
Tindakan: Buat tabel user_practice_progress untuk menyimpan checkpoint.
1.	Buat Migration Baru:
o	Jalankan: php artisan make:migration create_user_practice_progress_table
o	Isi file migration:
PHP
<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_practice_progress_table.php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_practice_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->unsignedBigInteger('last_viewed_question_id')->nullable(); // ID soal terakhir yg DILIHAT user
            $table->timestamps();
            $table->unique(['user_id', 'topic_id']); 
        });
    }
    public function down(): void { Schema::dropIfExists('user_practice_progress'); }
};
2.	Buat Model Baru:
o	Buat model: php artisan make:model UserPracticeProgress
o	Isi file app/Models/UserPracticeProgress.php:
PHP
<?php
// app/Models/UserPracticeProgress.php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class UserPracticeProgress extends Model {
    protected $table = 'user_practice_progress';
    protected $fillable = ['user_id', 'topic_id', 'last_viewed_question_id'];
    public function user() { return $this->belongsTo(User::class); }
    public function topic() { return $this->belongsTo(Topic::class); }
    public function lastViewedQuestion() { return $this->belongsTo(Question::class, 'last_viewed_question_id'); }
}
3.	Update Model User (app/Models/User.php):
o	Tambahkan relasi:
PHP
// app/Models/User.php
public function practiceProgresses() { return $this->hasMany(UserPracticeProgress::class); }
4.	Jalankan Migration: php artisan migrate
________________________________________
2. Modifikasi Route (routes/web.php)
Tindakan: Definisikan route baru untuk Bank Soal.
1.	Hapus/Ganti Route Lama: Hapus route Bank Soal yang mungkin sudah ada.
2.	Tambahkan Route Baru:
PHP
// routes/web.php
use App\Http\Controllers\BankSoalController; // Controller BARU

Route::middleware('auth')->prefix('bank-soal')->name('bank-soal.')->group(function () {
    Route::get('/', [BankSoalController::class, 'index'])->name('index'); // Hal. Utama (7 Subtes)
    Route::get('/{subject:slug}', [BankSoalController::class, 'showSubject'])->name('subject.show'); // Hal. Detail Subtes (2 kolom)
});
________________________________________
3. Buat Controller Baru (BankSoalController)
Tindakan: Buat controller untuk menangani halaman indeks dan detail subtes.
1.	Buat Controller: php artisan make:controller BankSoalController
2.	Isi app/Http/Controllers/BankSoalController.php:
PHP
<?php
// app/Http/Controllers/BankSoalController.php
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
        return view('bank-soal.index', compact('subjects'));
    }

    // Menampilkan halaman detail Subtes (Layout 2 kolom)
    public function showSubject(Subject $subject): View
    {
        // Load topik untuk sidebar
        $subject->load(['topics' => fn($q) => $q->orderBy('name')]); 

        // View ini akan berisi komponen Livewire untuk pengerjaan soal
        return view('bank-soal.show-subject', compact('subject'));
    }
}
________________________________________
4. Buat View Halaman Indeks (resources/views/bank-soal/index.blade.php)
Tindakan: Buat view Blade biasa untuk menampilkan 7 tombol Subject.
Blade
{{-- resources/views/bank-soal/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Bank Soal Latihan - SNBTKU')

@section('content')
<div class="py-12 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 px-4 sm:px-0 text-center">🏦 Bank Soal SNBTKU</h1>
        <p class="text-center text-gray-600 mb-10 -mt-4">Pilih subtes untuk memulai latihan soal per topik.</p>

        @if($subjects->isEmpty())
            <p class="text-center text-gray-500">Belum ada subtes yang tersedia.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @php
                    $tpsSubjects = $subjects->whereIn('subtest_order', [1, 2, 3, 4])->sortBy('subtest_order'); 
                    $literasiSubjects = $subjects->whereIn('subtest_order', [5, 6, 7])->sortBy('subtest_order');
                @endphp

                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 transform hover:scale-[1.02] transition duration-300">
                    <h2 class="text-xl font-semibold text-center text-blue-700 mb-6 border-b pb-3">Tes Potensi Skolastik (TPS)</h2>
                    <div class="space-y-4">
                        @forelse($tpsSubjects as $subject)
                            <a href="{{ route('bank-soal.subject.show', $subject->slug) }}" 
                               class="block w-full text-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg text-blue-800 font-medium transition duration-150 shadow-sm hover:shadow-md">
                                {{ $subject->name }}
                            </a>
                        @empty <p class="text-sm text-gray-500 text-center">Subtes TPS belum tersedia.</p> @endforelse
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 transform hover:scale-[1.02] transition duration-300">
                     <h2 class="text-xl font-semibold text-center text-indigo-700 mb-6 border-b pb-3">Tes Literasi & Penalaran Matematika</h2>
                     <div class="space-y-4">
                        @forelse($literasiSubjects as $subject)
                            <a href="{{ route('bank-soal.subject.show', $subject->slug) }}"
                               class="block w-full text-center p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg text-indigo-800 font-medium transition duration-150 shadow-sm hover:shadow-md">
                                {{ $subject->name }}
                            </a>
                        @empty <p class="text-sm text-gray-500 text-center">Subtes Literasi & Penalaran belum tersedia.</p> @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
________________________________________
5. Buat View Halaman Detail Subtes (resources/views/bank-soal/show-subject.blade.php)
Tindakan: Buat view Blade yang berisi layout 2 kolom dan memanggil komponen Livewire untuk pengerjaan soal.
Blade
{{-- resources/views/bank-soal/show-subject.blade.php --}}
@extends('layouts.app')

@section('title', 'Latihan: ' . $subject->name . ' - SNBTKU')

@section('content')
<div class="flex flex-col md:flex-row min-h-screen bg-gray-50">

    <aside class="w-full md:w-4/12 lg:w-3/12 xl:w-2/12 p-4 bg-white border-r shadow-md sticky top-0 h-screen-nav-mobile md:h-screen overflow-y-auto order-last md:order-first">
        {{-- Header Subtes --}}
        <div class="text-center mb-4 pb-4 border-b">
            <a href="{{ route('bank-soal.index') }}" class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali ke Bank Soal</a>
            <h3 class="font-bold text-lg text-gray-800 mt-2">{{ $subject->name }}</h3>
        </div>

        {{-- Daftar Topik --}}
        <h4 class="font-semibold text-xs text-gray-500 mb-2 uppercase tracking-wider px-3">Pilih Topik Latihan</h4>
        <nav class="space-y-1">
            @forelse($subject->topics as $topic)
                {{-- Tombol ini akan memicu event di komponen Livewire --}}
                <button 
                    onclick="Livewire.dispatch('topicSelected', { topicId: {{ $topic->id }} })" 
                    type="button"
                    class="w-full text-left px-3 py-2 rounded-md text-sm transition duration-150 ease-in-out flex justify-between items-center topic-button"
                    data-topic-id="{{ $topic->id }}"> {{-- Tambahkan data-topic-id --}}
                    <span>{{ $topic->name }}</span>
                    {{-- Icon akan di-handle oleh Livewire/JS --}}
                     <svg class="w-4 h-4 text-gray-400 opacity-0 topic-arrow" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </button>
            @empty
                <p class="px-3 text-xs text-gray-400">Belum ada topik.</p>
            @endforelse
        </nav>
    </aside>

    <main class="w-full md:w-8/12 lg:w-9/12 xl:w-10/12 p-6 md:p-10 order-first md:order-last">
        {{-- Panggil komponen Livewire BARU di sini --}}
        @livewire('bank-soal.practice-area', ['subject' => $subject]) 
    </main>
</div>

{{-- CSS & JS Tambahan --}}
@push('styles')
<style>
    @media (max-width: 767px) { .h-screen-nav-mobile { height: auto; position: static; } }
    .topic-button.active {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
        --tw-gradient-from: #E0E7FF; /* indigo-100 */
        --tw-gradient-to: #DBEAFE; /* blue-100 */
        color: #3B82F6; /* blue-600 */
        font-weight: 600;
        box-shadow: inset 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
     .topic-button.active .topic-arrow { opacity: 1; color: #3B82F6; }
</style>
@endpush
@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('topicChanged', (event) => {
            // Hapus class active dari semua tombol
            document.querySelectorAll('.topic-button').forEach(button => {
                button.classList.remove('active', 'bg-gradient-to-r', 'from-indigo-100', 'to-blue-100', 'text-blue-800', 'font-semibold', 'shadow-inner');
                button.classList.add('text-gray-700', 'hover:bg-gray-100');
                button.querySelector('.topic-arrow').style.opacity = '0';
            });
            // Tambahkan class active ke tombol yang diklik
            const activeButton = document.querySelector(`.topic-button[data-topic-id="${event.topicId}"]`);
            if (activeButton) {
                activeButton.classList.add('active', 'bg-gradient-to-r', 'from-indigo-100', 'to-blue-100', 'text-blue-800', 'font-semibold', 'shadow-inner');
                 activeButton.classList.remove('text-gray-700', 'hover:bg-gray-100');
                 activeButton.querySelector('.topic-arrow').style.opacity = '1';

            }
        });
    });
</script>
@endpush
@endsection
________________________________________
6. Buat Komponen Livewire Pengerjaan Soal (PracticeArea)
Tindakan: Buat komponen Livewire baru yang akan menangani logic pengerjaan soal di Kolom Kanan.
1.	Buat Komponen: php artisan make:livewire BankSoal/PracticeArea
2.	Isi app/Livewire/BankSoal/PracticeArea.php (Logic Inti):
PHP
<?php
// app/Livewire/BankSoal/PracticeArea.php
namespace App\Livewire\BankSoal;

use Livewire\Component;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\UserPracticeProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On; // Import On attribute

class PracticeArea extends Component
{
    public Subject $subject; // Diterima dari view Blade
    public ?Topic $activeTopic = null;
    public ?Question $currentQuestion = null;
    public ?string $selectedAnswer = null;
    public bool $showExplanation = false;
    public ?bool $isCorrect = null;
    public int $questionCounter = 0; // Untuk nomor urut soal
    public int $totalQuestionsInTopic = 0;

    // Listener untuk event 'topicSelected' dari tombol di sidebar
    #[On('topicSelected')] 
    public function selectTopic($topicId)
    {
        $this->activeTopic = Topic::find($topicId);
        $this->resetState(); // Reset pengerjaan

        if ($this->activeTopic) {
             // Hitung total soal
            $this->totalQuestionsInTopic = $this->activeTopic->questions()->count();

            // Cari checkpoint
            $progress = $this->getProgress();
            $lastQuestionId = $progress?->last_viewed_question_id;

            // Load soal (mulai dari checkpoint atau soal pertama)
            $this->loadQuestion($lastQuestionId);

            // Kirim event untuk highlight sidebar (opsional)
            $this->dispatch('topicChanged', topicId: $topicId);
        }
    }

    // Method untuk PINDAH SOAL (Next)
    public function nextQuestion()
    {
        if (!$this->activeTopic || !$this->currentQuestion) return;

        // Logic #4: Koreksi Langsung
        if (!$this->showExplanation) {
            $this->checkAnswer();
            $this->showExplanation = true;
            return; // Berhenti di sini, user harus klik "Next" lagi
        }

        // Logic #3: Soal Unlimit (Next terus)
        $this->loadQuestion($this->currentQuestion->id); // Load soal berikutnya
        $this->resetState(); // Bersihkan jawaban
    }

    // Method untuk PINDAH SOAL (Previous)
    public function prevQuestion()
    {
        if (!$this->activeTopic || !$this->currentQuestion) return;

        // Cari soal sebelumnya
        $prevQuestion = $this->activeTopic->questions()
                            ->where('id', '<', $this->currentQuestion->id)
                            ->orderBy('id', 'desc')
                            ->first();

        if ($prevQuestion) {
            $this->currentQuestion = $prevQuestion;
            $this->updateProgress($prevQuestion->id); // Update checkpoint
            $this->resetState(); // Bersihkan jawaban
            $this->questionCounter--; // Mundurkan counter
        }
        // Jika tidak ada soal sebelumnya, user tetap di soal ini
    }

    // Method untuk MENGECEK JAWABAN
    public function checkAnswer()
    {
        if (!$this->currentQuestion) return;
        if ($this->selectedAnswer) {
            $this->isCorrect = ($this->selectedAnswer === $this->currentQuestion->correct_answer);
        } else {
            $this->isCorrect = null; // Tidak menjawab
        }
    }

    // Helper: Load soal (soal pertama, atau soal setelah $lastQuestionId)
    private function loadQuestion($lastQuestionId = null)
    {
        if (!$this->activeTopic) return;

        $query = $this->activeTopic->questions();
        $baseQueryForCounter = $this->activeTopic->questions(); // Query terpisah untuk counter

        if ($lastQuestionId) {
            // Cek apakah $lastQuestionId valid untuk topik ini
            $lastQuestionExists = $this->activeTopic->questions()->where('id', $lastQuestionId)->exists();
            if($lastQuestionExists) {
                $query->where('id', '>', $lastQuestionId); // Cari soal SETELAH checkpoint
                // Hitung counter berdasarkan posisi $lastQuestionId
                 $this->questionCounter = $baseQueryForCounter->where('id', '<=', $lastQuestionId)->count();
            } else {
                 // Jika lastQuestionId tidak valid (misal soal dihapus), mulai dari awal
                 $lastQuestionId = null; // Anggap tidak ada checkpoint
                 $this->questionCounter = 0;
            }
        } else {
            // Jika tidak ada checkpoint, mulai dari awal
             $this->questionCounter = 0;
        }

        $nextQuestion = $query->orderBy('id', 'asc')->first();

        if ($nextQuestion) {
            $this->currentQuestion = $nextQuestion;
            $this->updateProgress($nextQuestion->id); // Logic #5: Simpan Checkpoint
            $this->questionCounter++; // Naikkan counter
        } else {
            // Jika tidak ada soal BARU setelah checkpoint (sudah di akhir)
            // Cek apakah ada soal SEBELUM checkpoint (jika checkpointnya bukan soal pertama)
            if($lastQuestionId && $this->activeTopic->questions()->where('id', '<', $lastQuestionId)->exists()) {
                // Loop kembali ke soal pertama
                 $this->currentQuestion = $this->activeTopic->questions()->orderBy('id', 'asc')->first();
                 $this->updateProgress($this->currentQuestion?->id);
                 $this->questionCounter = 1; // Kembali ke 1
            } else if (!$lastQuestionId && $this->totalQuestionsInTopic > 0) {
                 // Kasus: Hanya ada 1 soal di topik, atau baru mulai tapi tidak ada soal setelahnya
                 // (Seharusnya tidak terjadi jika query awal benar)
                 // Coba load soal pertama lagi
                  $this->currentQuestion = $this->activeTopic->questions()->orderBy('id', 'asc')->first();
                  $this->updateProgress($this->currentQuestion?->id);
                  $this->questionCounter = 1;
            }
             else {
                // Benar-benar tidak ada soal lagi ATAU topik kosong
                $this->currentQuestion = null;
                $this->questionCounter = 0;
            }
        }
    }

    // Helper: Reset state per soal
    private function resetState()
    {
        $this->selectedAnswer = null;
        $this->showExplanation = false;
        $this->isCorrect = null;
    }

    // Helper: Ambil progress user
    private function getProgress()
    {
        if (!$this->activeTopic) return null;
        return UserPracticeProgress::firstWhere([
            'user_id' => Auth::id(),
            'topic_id' => $this->activeTopic->id,
        ]);
    }

    // Helper: Update checkpoint di DB
    private function updateProgress($questionId)
    {
        if (!$this->activeTopic || !$questionId) return;
        UserPracticeProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'topic_id' => $this->activeTopic->id],
            ['last_viewed_question_id' => $questionId]
        );
    }

    public function render(): View
    {
        // View ini hanya merender bagian Kolom Kanan
        return view('livewire.bank-soal.practice-area'); 
    }
}
________________________________________
7. Buat View Komponen Pengerjaan (resources/views/livewire/bank-soal/practice-area.blade.php)
Tindakan: Buat file view ini yang akan dirender di Kolom Kanan.
Blade
{{-- resources/views/livewire/bank-soal/practice-area.blade.php --}}
<div>
    {{-- Loading Indicator --}}
    <div wire:loading.flex wire:target="selectTopic, nextQuestion, prevQuestion" 
         class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="ml-3 text-gray-700">Memuat...</span>
    </div>

    @if (!$activeTopic)
        {{-- Tampilan Awal --}}
        <div class="bg-white rounded-lg shadow-md border p-8 text-center h-full flex flex-col justify-center items-center min-h-[500px]">
            <svg class="w-16 h-16 text-indigo-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <h1 class="text-xl font-semibold text-gray-700">Selamat Datang di Latihan {{ $subject->name }}</h1>
            <p class="mt-2 text-gray-500">Silakan pilih topik di sidebar kiri untuk memulai.</p>
        </div>
    @elseif (!$currentQuestion)
         {{-- Tampilan Jika Topik Kosong/Selesai --}}
        <div class="bg-white rounded-lg shadow-md border p-8 text-center h-full flex flex-col justify-center items-center min-h-[500px]">
             <svg class="w-16 h-16 text-green-400 mb-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <h1 class="text-xl font-semibold text-gray-700">Latihan Selesai!</h1>
            <p class="mt-2 text-gray-500">Kamu sudah mencapai akhir soal yang tersedia untuk topik **{{ $activeTopic->name }}**.</p>
            <p class="mt-1 text-sm text-gray-400">(Soal akan dimulai lagi dari awal jika kamu melanjutkan).</p>
             <button wire:click="selectTopic({{ $activeTopic->id }})" {{-- Reload topic --}}
                     class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                     Ulangi Latihan dari Awal
             </button>
        </div>
    @else
        {{-- Tampilan Pengerjaan Soal --}}
        <div class="bg-white rounded-lg shadow-md border overflow-hidden">
            {{-- Konten Soal --}}
            <div class="p-6">
                <p class="font-semibold text-gray-600 text-sm mb-3 flex justify-between">
                    <span>Topik: {{ $activeTopic->name }}</span>
                    <span>Soal {{ $questionCounter }} dari {{ $totalQuestionsInTopic }}</span> 
                </p>
                <div class="prose max-w-none text-base text-gray-800 mb-5">
                    {!! $currentQuestion->question_text !!}
                </div>
                <div class="space-y-3 mb-5">
                    @php $options = ['a', 'b', 'c', 'd', 'e']; @endphp
                    @foreach($options as $option)
                        @php 
                            $optionText = 'option_' . $option; 
                            $correctAnswer = $currentQuestion->correct_answer;
                        @endphp
                        <button 
                            type="button"
                            wire:click="$set('selectedAnswer', '{{ $option }}')"
                            @disabled($showExplanation) 
                            class="w-full text-left p-4 border rounded-lg transition duration-150 ease-in-out flex items-start space-x-3 disabled:cursor-not-allowed disabled:opacity-70"
                            x-bind:class="{
                                'bg-white hover:bg-gray-50 border-gray-300': !$wire.selectedAnswer || $wire.selectedAnswer !== '{{ $option }}',
                                'bg-blue-50 border-blue-400 ring-2 ring-blue-200 text-blue-800': $wire.selectedAnswer === '{{ $option }}' && !$wire.showExplanation,
                                'bg-green-50 border-green-400 text-green-800 font-medium': $wire.showExplanation && '{{ $option }}' === '{{ $correctAnswer }}',
                                'bg-red-50 border-red-400 text-red-800': $wire.showExplanation && $wire.selectedAnswer === '{{ $option }}' && '{{ $option }}' !== '{{ $correctAnswer }}',
                                'bg-white border-gray-300 text-gray-600': $wire.showExplanation && $wire.selectedAnswer !== '{{ $option }}' && '{{ $option }}' !== '{{ $correctAnswer }}'
                            }"
                        >
                            <span class="font-semibold">{{ strtoupper($option) }}.</span> 
                            <span class="prose prose-sm max-w-none">{!! $currentQuestion->$optionText !!}</span>
                        </button>
                    @endforeach
                </div>
                
                {{-- Feedback & Pembahasan --}}
                <div x-data="{ show: @entangle('showExplanation') }" x-show="show" x-collapse>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="mb-4 p-4 rounded-lg border 
                            @if($isCorrect === true) bg-green-50 border-green-300 text-green-800 @elseif($isCorrect === false) bg-red-50 border-red-300 text-red-800 @else bg-gray-50 border-gray-300 text-gray-800 @endif">
                            
                            @if($isCorrect === true)
                                <p class="font-semibold"><svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Jawaban Kamu Benar!</p>
                            @elseif($isCorrect === false)
                                <p class="font-semibold"><svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg> Jawaban Kamu Salah.</p>
                                <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion->correct_answer }}</span>.</p>
                            @elseif($isCorrect === null && $showExplanation)
                                 <p class="font-semibold"><svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg> Kamu tidak memilih jawaban.</p>
                                 <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion->correct_answer }}</span>.</p>
                            @endif
                        </div>
                        
                        @if($currentQuestion->explanation)
                            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded">
                                <h4 class="font-semibold text-indigo-800 text-sm mb-1">Penjelasan:</h4>
                                <div class="prose prose-sm max-w-none text-indigo-700">
                                    {!! $currentQuestion->explanation !!}
                                </div>
                            </div>
                        @else
                             <p class="text-sm text-indigo-500 italic">Pembahasan belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tombol Navigasi Bawah --}}
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
                <button wire:click="prevQuestion"
                        wire:loading.attr="disabled" wire:target="prevQuestion"
                        class="px-6 py-2 bg-gray-300 text-gray-800 rounded font-medium hover:bg-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
                        @disabled($showExplanation)> {{-- Disable pas mode koreksi --}}
                    <span wire:loading wire:target="prevQuestion">Memuat...</span>
                    <span wire:loading.remove wire:target="prevQuestion">&larr; Previous</span>
                </button>
                <button wire:click="nextQuestion"
                        wire:loading.attr="disabled" wire:target="nextQuestion"
                        class="px-6 py-2 text-white rounded font-medium disabled:opacity-50 disabled:cursor-not-allowed
                               {{ $showExplanation ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-500 hover:bg-blue-600' }}">
                     <span wire:loading wire:target="nextQuestion">Memuat...</span>
                     <span wire:loading.remove wire:target="nextQuestion">
                        {{ $showExplanation ? 'Soal Selanjutnya' : 'Koreksi Jawaban' }} &rarr;
                     </span>
                </button>
            </div>
        </div>
    @endif
</main>

{{-- CSS Tambahan (jika diperlukan) --}}
@push('styles')
<style>
    @media (max-width: 767px) { .h-screen-nav-mobile { height: auto; position: static; } }
    .prose p { margin-top: 0; margin-bottom: 0.5em; } 
    button span.prose { display: inline; } 
    .prose-sm { font-size: 0.875rem; line-height: 1.5rem; }
     .prose ul, .prose ol { margin-top: 0.5em; margin-bottom: 0.5em; }
     .prose li p { margin-bottom: 0.2em; }
</style>
@endpush

</div>

