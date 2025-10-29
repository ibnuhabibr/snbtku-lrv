{{-- resources/views/livewire/bank-soal/practice-area.blade.php --}}
<div class="h-full">
    @if(!$currentTopic)
        {{-- State awal: belum ada topik yang dipilih --}}
        <div class="flex flex-col items-center justify-center h-full px-4 py-10 text-center">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-sm p-8 md:p-10 max-w-lg w-full space-y-5">
                <div class="mx-auto w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <h3 class="text-xl font-semibold text-gray-900">Pilih Topik Latihan</h3>
                    <p class="text-gray-600">Silakan pilih salah satu topik untuk mulai mengerjakan soal {{ $subject->name }}.</p>
                </div>

                <button wire:click="toggleTopicList"
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                    {{ $showTopicList ? 'Sembunyikan Topik' : 'Pilih Topik' }}
                </button>

                @if($showTopicList)
                    <div class="mt-4 space-y-2 text-left">
                        @foreach($subject->topics as $topic)
                            <button wire:click="selectTopic({{ $topic->id }})"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-blue-300 transition">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-800">{{ $topic->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $topic->questions_count ?? 0 }} soal</div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center justify-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Atau gunakan sidebar di sebelah kiri untuk navigasi
                </div>
            </div>
        </div>
    @elseif($totalQuestions === 0)
        {{-- State: topik dipilih tapi tidak ada soal --}}
        <div class="flex items-center justify-center h-full px-4">
            <div class="text-center space-y-4">
                <svg class="mx-auto h-16 w-16 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold text-gray-900">Belum Ada Soal</h3>
                    <p class="text-gray-500">Topik <strong>{{ $currentTopic->name }}</strong> belum memiliki soal latihan.</p>
                </div>
            </div>
        </div>
    @else
        {{-- State: ada soal --}}
        <div class="flex flex-col gap-6 md:gap-8 h-full pb-28 md:pb-0">
            {{-- Header --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm px-5 py-4 md:px-6 md:py-5 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-lg md:text-xl font-semibold text-gray-900">{{ $currentTopic->name }}</h2>
                        <p class="text-sm text-gray-500">
                            Soal {{ $currentQuestionIndex + 1 }} dari {{ $totalQuestions }} | {{ count($completedQuestions) }} terjawab ({{ $this->getProgressPercentage() }}%)
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">
                            Benar {{ $correctCount }} / Salah {{ $incorrectCount }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-100">
                            Akurasi {{ $this->accuracy !== null ? $this->accuracy.'%' : '—' }}
                        </span>
                        <button wire:click="requestReset"
                                class="inline-flex items-center px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition">
                            Reset Progress
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                        <p class="text-xs text-gray-500">Soal Dikerjakan</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $this->attemptedCount }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <p class="text-xs text-gray-500">Jawaban Benar</p>
                        <p class="text-lg font-semibold text-emerald-700">{{ $correctCount }}</p>
                    </div>
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                        <p class="text-xs text-gray-500">Jawaban Salah</p>
                        <p class="text-lg font-semibold text-red-600">{{ $incorrectCount }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3">
                        <p class="text-xs text-gray-500">Ditandai Review</p>
                        <p class="text-lg font-semibold text-blue-700">{{ count($flaggedQuestions) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse lg:grid lg:grid-cols-[minmax(0,3.5fr)_minmax(0,2fr)] gap-6 md:gap-8">
                {{-- Question area --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <div class="px-5 py-4 md:px-6 md:py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-gray-700">Soal {{ $currentQuestionIndex + 1 }}</p>
                            <p class="text-xs text-gray-500">{{ $currentTopic->name }}</p>
                        </div>
                        @if($currentQuestionId = $currentQuestion?->id)
                            <button wire:click="toggleFlag({{ $currentQuestionId }})"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs font-medium transition
                                        @if($this->isQuestionFlagged($currentQuestionId))
                                            border-amber-300 bg-amber-50 text-amber-700
                                        @else
                                            border-gray-200 bg-white text-gray-600 hover:bg-gray-50
                                        @endif">
                                <svg class="w-4 h-4" fill="@if($this->isQuestionFlagged($currentQuestionId)) currentColor @else none @endif" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M5 3v17l7-4 7 4V3H5z" />
                                </svg>
                                {{ $this->isQuestionFlagged($currentQuestionId) ? 'Ditandai' : 'Tandai Soal' }}
                            </button>
                        @endif
                    </div>

                    <div class="px-5 py-6 md:px-6 space-y-6">
                        <div class="prose prose-sm md:prose-base max-w-none text-gray-800">
                            {!! nl2br(e($currentQuestion->question_text ?? '')) !!}
                        </div>

                        @if($currentQuestion)
                            <div class="space-y-3">
                                @foreach($currentQuestion->options as $optionKey => $optionText)
                                    <button
                                        type="button"
                                        wire:key="option-{{ $currentQuestion->id }}-{{ $optionKey }}"
                                        wire:click="selectAnswer('{{ $optionKey }}')"
                                        class="w-full text-left px-4 py-3 rounded-xl border transition focus:outline-none focus:ring-2 focus:ring-indigo-400
                                            @if($userAnswer === $optionKey)
                                                border-indigo-500 bg-indigo-50 text-indigo-700 shadow-inner
                                            @elseif(isset($questionAnswers[$currentQuestion->id]) && $questionAnswers[$currentQuestion->id]['correct'] === true && $currentQuestion->correct_answer === $optionKey)
                                                border-emerald-500 bg-emerald-50 text-emerald-700
                                            @else
                                                border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 text-gray-700
                                            @endif">
                                        <div class="flex items-start gap-3">
                                            <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full border text-sm font-semibold
                                                @if($userAnswer === $optionKey)
                                                    border-indigo-500 bg-indigo-500 text-white
                                                @elseif(isset($questionAnswers[$currentQuestion->id]) && $questionAnswers[$currentQuestion->id]['correct'] === true && $currentQuestion->correct_answer === $optionKey)
                                                    border-emerald-500 bg-emerald-500 text-white
                                                @else
                                                    border-gray-300 text-gray-500
                                                @endif">
                                                {{ strtoupper($optionKey) }}
                                            </span>
                                            <span class="text-sm md:text-base text-gray-700 leading-relaxed">
                                                {!! nl2br(e($optionText)) !!}
                                            </span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <div>
                            @if($isAnswered)
                                <div class="rounded-xl border
                                        @if($isCorrect)
                                            border-emerald-200 bg-emerald-50 text-emerald-700
                                        @else
                                            border-red-200 bg-red-50 text-red-700
                                        @endif">
                                    <div class="px-5 py-4">
                                        <p class="font-semibold flex items-center gap-2">
                                            @if($isCorrect)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Jawabanmu benar!
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Jawabanmu belum tepat.
                                            @endif
                                        </p>
                                        <p class="text-sm mt-2">
                                            Jawaban yang benar: <span class="uppercase font-semibold">{{ $currentQuestion->correct_answer }}</span>
                                        </p>
                                    </div>
                                    <div class="border-t border-current/10 px-5 py-4 bg-white/80 text-sm text-gray-700 rounded-b-xl">
                                        <p class="font-semibold text-gray-900 mb-2">Pembahasan</p>
                                        <p>{{ $currentQuestion->explanation }}</p>
                                    </div>
                                </div>
                            @elseif($showExplanation)
                                <div class="rounded-xl border border-yellow-200 bg-yellow-50 text-yellow-800 px-5 py-4">
                                    <p class="font-semibold flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18h.01"></path>
                                        </svg>
                                        Kamu belum memilih jawaban.
                                    </p>
                                    <p class="text-sm mt-2">
                                        Jawaban yang benar: <span class="uppercase font-semibold">{{ $currentQuestion->correct_answer }}</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-5 lg:space-y-6">
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-6 space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach(['all' => 'Semua', 'unanswered' => 'Belum', 'incorrect' => 'Salah', 'flagged' => 'Ditandai'] as $mode => $label)
                                <button wire:click="setFilterMode('{{ $mode }}')"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium border transition
                                            @if($filterMode === $mode)
                                                border-blue-500 bg-blue-50 text-blue-700
                                            @else
                                                border-gray-200 text-gray-600 hover:bg-gray-50
                                            @endif">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-900">Navigasi Soal</h3>
                                <span class="text-xs text-gray-400">Klik untuk lompat</span>
                            </div>
                            <div class="grid grid-cols-5 sm:grid-cols-6 gap-2">
                                @for($i = 0; $i < $totalQuestions; $i++)
                                    @php
                                        $questionId = $questionIds[$i] ?? null;
                                    @endphp
                                    @if(!$questionId || !$this->shouldShowQuestion($questionId))
                                        @continue
                                    @endif
                                    <button
                                        wire:click="goToQuestion({{ $i }})"
                                        class="relative h-10 rounded-lg text-xs font-medium transition-colors flex items-center justify-center
                                            @if($i === $currentQuestionIndex)
                                                bg-blue-600 text-white shadow-sm
                                            @elseif(isset($questionAnswers[$questionId]))
                                                @if($questionAnswers[$questionId]['correct'] === true)
                                                    bg-emerald-100 text-emerald-800 border border-emerald-200
                                                @else
                                                    bg-red-100 text-red-700 border border-red-200
                                                @endif
                                            @else
                                                bg-gray-100 text-gray-600 hover:bg-gray-200
                                            @endif">
                                        {{ $i + 1 }}
                                        @if($this->isQuestionFlagged($questionId))
                                            <span class="absolute -top-1 -right-1 text-amber-500">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 2l2.39 4.84 5.34.78-3.86 3.77.91 5.32L10 14.77l-4.78 2.51.91-5.32-3.86-3.77 5.34-.78L10 2z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </button>
                                @endfor
                            </div>
                        </div>

                        @if(count($flaggedQuestions) > 0)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm font-semibold text-gray-900">
                                    <span>Ditandai</span>
                                    <span class="text-xs text-gray-400">{{ count($flaggedQuestions) }} soal</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($flaggedQuestions as $flaggedId)
                                        @php $index = $questionIndexMap[$flaggedId] ?? null; @endphp
                                        @if($index === null)
                                            @continue
                                        @endif
                                        <button wire:click="goToQuestion({{ $index }})"
                                                class="px-3 py-1 rounded-full text-xs font-medium border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100">
                                            Soal {{ $index + 1 }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    @endif

    {{-- Modal reset progress --}}
    @if($showResetModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h4 class="text-lg font-semibold text-gray-900">Reset Progress Latihan?</h4>
                    <p class="text-sm text-gray-500 mt-1">Aksi ini akan menghapus semua jawaban dan statistik latihan untuk topik ini.</p>
                </div>
                <div class="px-6 py-5 space-y-3 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Total soal terjawab</span>
                        <span class="font-semibold text-gray-900">{{ count($completedQuestions) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Jawaban benar</span>
                        <span class="font-semibold text-emerald-600">{{ $correctCount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Jawaban salah</span>
                        <span class="font-semibold text-red-600">{{ $incorrectCount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Akurasi</span>
                        <span class="font-semibold text-gray-900">{{ $this->accuracy !== null ? $this->accuracy.'%' : '—' }}</span>
                    </div>
                </div>
                <div class="px-6 py-5 flex items-center justify-end gap-3">
                    <button type="button"
                            class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50"
                            wire:click="cancelReset">
                        Batal
                    </button>
                    <button type="button"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-500 transition"
                            wire:click="confirmReset">
                        Hapus Progress
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl shadow z-40"
             x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3500)">
            {{ session('message') }}
        </div>
    @endif
</div>
