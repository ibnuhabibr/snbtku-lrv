{{-- resources/views/livewire/bank-soal/show-page.blade.php --}}
<div class="min-h-screen bg-gray-50 py-6 md:py-10">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-6 px-4 lg:px-8">
        <aside class="w-full md:max-w-sm lg:max-w-xs md:flex-shrink-0 bg-white border border-gray-200 rounded-2xl shadow-sm p-4 md:p-5 order-last md:order-first">
            {{-- Header Subtes --}}
            <div class="text-center mb-4 pb-4 border-b">
                <a href="{{ route('bank-soal.index') }}" wire:navigate class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali ke Bank Soal</a>
                <h3 class="font-bold text-lg text-gray-800 mt-2">{{ $subject->name }}</h3>
            </div>

            {{-- Daftar Topik --}}
            <div class="flex items-center justify-between mb-2 px-3">
                <h4 class="font-semibold text-xs text-gray-500 uppercase tracking-wider">Pilih Topik</h4>
                @if($selectedTopic)
                    <span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $questionCount }} Soal</span>
                @endif
            </div>
            <nav class="space-y-1 max-h-[60vh] overflow-y-auto pr-1">
                @forelse($topics as $topic)
                    <button
                        wire:click="selectTopic({{ $topic->id }})"
                        type="button"
                        class="w-full text-left px-3 py-2 rounded-xl text-sm transition duration-150 ease-in-out flex justify-between items-center border
                                {{ $selectedTopic && $selectedTopic->id === $topic->id ? 'bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 text-blue-800 border-blue-200 font-semibold shadow-inner' : 'text-gray-700 border-transparent hover:border-gray-200 hover:bg-gray-50' }}">
                        <span class="text-sm leading-tight">{{ $topic->name }}</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-[11px] text-gray-400">{{ $topic->questions_count }} Soal</span>
                            @if($selectedTopic && $selectedTopic->id === $topic->id)
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            @endif
                        </div>
                    </button>
                @empty
                    <p class="px-3 text-xs text-gray-400">Belum ada topik.</p>
                @endforelse
            </nav>
        </aside>

        <main class="flex-1 order-first md:order-last">
            {{-- Loading Indicator --}}
            <div wire:loading.flex wire:target="selectTopic, nextQuestion, prevQuestion"
                 class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-3 text-gray-700">Memuat...</span>
            </div>

            @if (!$selectedTopic)
                {{-- Tampilan Awal --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-8 text-center shadow-sm">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Selamat Datang di Latihan {{ $subject->name }}!</h3>
                    <p class="text-gray-600 mb-4">Pilih topik di sidebar untuk memulai latihan soal.</p>
                    <div class="text-sm text-gray-500">
                        <p>💡 <strong>Tips:</strong> Kerjakan soal secara bertahap dan pahami setiap pembahasan.</p>
                    </div>
                </div>
            @elseif (!$currentQuestion)
                 {{-- Tampilan Jika Topik Kosong/Selesai --}}
                <div class="bg-white rounded-2xl shadow-sm border p-8 text-center h-full flex flex-col justify-center items-center">
                     <svg class="w-16 h-16 text-green-400 mb-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <h1 class="text-xl font-semibold text-gray-700">Latihan Selesai!</h1>
                    <p class="mt-2 text-gray-500">Kamu sudah mencapai akhir soal yang tersedia untuk topik <strong>{{ $selectedTopic->name }}</strong>.</p>
                    <p class="mt-1 text-sm text-gray-400">(Admin akan menambahkan soal baru secara berkala).</p>
                     <button wire:click="selectTopic({{ $selectedTopic->id }})"
                             class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                             Ulangi Latihan dari Awal
                     </button>
                </div>
            @else
                {{-- Tampilan Pengerjaan Soal --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        {{-- Konten Soal --}}
                        <div class="p-6 md:p-8">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                                <div>
                                    <p class="font-semibold text-gray-700 text-sm uppercase tracking-wide">{{ $selectedTopic->name }}</p>
                                    <p class="text-xs text-gray-400">Soal ke {{ $currentQuestionIndex + 1 }} dari {{ $questionCount }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 font-semibold">Benar: {{ $correctCount }}</span>
                                    <span class="px-3 py-1 rounded-full bg-red-50 text-red-500 font-semibold">Salah: {{ $incorrectCount }}</span>
                                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600">Selesai: {{ count($completedQuestionIds) }} Soal</span>
                                </div>
                            </div>
                            <div class="prose max-w-none text-base text-gray-800 mb-5">
                                {!! $currentQuestion->question_text !!}
                            </div>
                            <div class="space-y-3 mb-6">
                                @php $options = ['a', 'b', 'c', 'd', 'e']; @endphp
                                @foreach($options as $option)
                                    @php
                                        $optionText = 'option_' . $option;
                                        $correctAnswer = $currentQuestion->correct_answer;
                                        $isSelected = $selectedAnswer === $option;
                                    @endphp
                                    <button
                                        type="button"
                                        wire:click="selectAnswer('{{ $option }}')"
                                        @disabled($showExplanation)
                                        wire:key="option-{{ $currentQuestion->id }}-{{ $option }}"
                                        class="w-full text-left p-4 border rounded-xl transition duration-150 ease-in-out flex items-start gap-3 disabled:cursor-not-allowed disabled:opacity-80"
                                        @class([
                                            'bg-white hover:bg-gray-50 border-gray-200 text-gray-700' => ! $isSelected && ! $showExplanation,
                                            'bg-blue-50 border-blue-400 ring-2 ring-blue-200 text-blue-800 font-medium' => $isSelected && ! $showExplanation,
                                            'bg-green-50 border-green-400 text-green-800 font-medium' => $showExplanation && $option === $correctAnswer,
                                            'bg-red-50 border-red-400 text-red-800' => $showExplanation && $isSelected && $option !== $correctAnswer,
                                            'bg-white border-gray-200 text-gray-500' => $showExplanation && ! $isSelected && $option !== $correctAnswer,
                                        ])
                                    >
                                        <span class="font-semibold">{{ strtoupper($option) }}.</span>
                                        <span class="prose prose-sm max-w-none">{!! $currentQuestion->$optionText !!}</span>
                                    </button>
                                @endforeach
                            </div>

                            {{-- Feedback & Pembahasan --}}
                            <div x-data="{ show: @entangle('showExplanation') }" x-show="show" x-collapse>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="mb-4 p-4 rounded-xl border"
                                        @class([
                                            'bg-green-50 border-green-300 text-green-800' => $isCorrect === true,
                                            'bg-red-50 border-red-300 text-red-800' => $isCorrect === false,
                                            'bg-gray-50 border-gray-300 text-gray-800' => is_null($isCorrect) && $showExplanation,
                                        ])
                                    >
                                        @if($isCorrect === true)
                                            <p class="font-semibold flex items-center gap-2"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Jawaban Kamu Benar!</p>
                                        @elseif($isCorrect === false)
                                            <div>
                                                <p class="font-semibold flex items-center gap-2"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg> Jawaban Kamu Salah.</p>
                                                <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion->correct_answer }}</span>.</p>
                                            </div>
                                        @elseif(is_null($isCorrect) && $showExplanation)
                                            <div>
                                                <p class="font-semibold flex items-center gap-2"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg> Kamu tidak memilih jawaban.</p>
                                                <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion->correct_answer }}</span>.</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($currentQuestion->explanation)
                                        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-xl">
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
                        <div class="px-6 md:px-8 py-4 bg-gray-50 border-t flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                            <button wire:click="prevQuestion"
                                    wire:loading.attr="disabled" wire:target="prevQuestion"
                                    class="w-full sm:w-auto px-6 py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 disabled:opacity-60 disabled:cursor-not-allowed">
                                <span wire:loading wire:target="prevQuestion">Memuat...</span>
                                <span wire:loading.remove wire:target="prevQuestion">&larr; Sebelumnya</span>
                            </button>
                            <button wire:click="nextQuestion"
                                    wire:loading.attr="disabled" wire:target="nextQuestion"
                                    class="w-full sm:w-auto px-6 py-2 text-white rounded-lg font-semibold transition disabled:opacity-60 disabled:cursor-not-allowed {{ $showExplanation ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-500 hover:bg-blue-600' }}">
                                 <span wire:loading wire:target="nextQuestion">Memuat...</span>
                                 <span wire:loading.remove wire:target="nextQuestion">
                                    {{ $showExplanation ? 'Soal Selanjutnya' : 'Koreksi Jawaban' }} &rarr;
                                 </span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                        <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Navigasi Soal</h4>
                        <div class="grid grid-cols-8 sm:grid-cols-10 md:grid-cols-12 gap-2">
                            @foreach($questionIds as $index => $questionId)
                                @php
                                    $isActive = $index === $currentQuestionIndex;
                                    $isDone = in_array($questionId, $completedQuestionIds ?? [], true);
                                    $isCorrectAnswer = isset($questionAnswers[$questionId]['is_correct']) ? $questionAnswers[$questionId]['is_correct'] : null;
                                @endphp
                                <button
                                    wire:click="goToQuestion({{ $index }})"
                                    class="text-xs font-semibold rounded-lg px-2 py-2 border transition focus:outline-none focus:ring-2 focus:ring-blue-300"
                                    @class([
                                        'bg-blue-500 text-white border-blue-500 shadow' => $isActive,
                                        'bg-green-50 text-green-700 border-green-200' => ! $isActive && $isCorrectAnswer === true,
                                        'bg-red-50 text-red-600 border-red-200' => ! $isActive && $isCorrectAnswer === false,
                                        'bg-gray-100 text-gray-600 border-gray-200' => ! $isActive && $isDone && $isCorrectAnswer === null,
                                        'bg-white text-gray-500 border-gray-200 hover:border-blue-200 hover:text-blue-600' => ! $isActive && ! $isDone,
                                    ])
                                >
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3 text-[11px] text-gray-500">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-blue-500"></span> Soal Aktif</span>
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-green-500"></span> Benar</span>
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-red-500"></span> Salah</span>
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-gray-300"></span> Dijawab</span>
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>

{{-- CSS Tambahan (jika diperlukan) --}}
@push('styles')
<style>
    .prose p { margin-top: 0; margin-bottom: 0.75em; }
    button span.prose { display: inline; }
    .prose-sm { font-size: 0.9rem; line-height: 1.5rem; }
    .prose ul, .prose ol { margin-top: 0.5em; margin-bottom: 0.5em; padding-left: 1.25rem; }
    .prose li p { margin-bottom: 0.3em; }
    @media (max-width: 768px) {
        nav.space-y-1 { max-height: none; }
    }
</style>
@endpush
