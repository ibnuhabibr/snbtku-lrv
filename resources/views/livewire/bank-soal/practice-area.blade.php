{{-- resources/views/livewire/bank-soal/practice-area.blade.php --}}
<div class="h-full">
    @if(!$currentTopic)
        {{-- State awal: belum ada topik yang dipilih --}}
        <div class="flex flex-col items-center justify-center h-full px-4 py-8 text-center">
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
                                <div class="font-medium text-gray-800">{{ $topic->name }}</div>
                                <div class="text-sm text-gray-500">{{ $topic->questions_count ?? 0 }} soal</div>
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
        {{-- State: ada soal, tampilkan interface latihan --}}
        <div class="flex flex-col gap-6 md:gap-8 h-full pb-28 md:pb-0">
            {{-- Header --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm px-5 py-4 md:px-6 md:py-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-lg md:text-xl font-semibold text-gray-900">{{ $currentTopic->name }}</h2>
                        <p class="text-sm text-gray-500">
                            Soal {{ $currentQuestionIndex + 1 }} dari {{ $totalQuestions }} | {{ count($completedQuestions) }} terjawab ({{ $this->getProgressPercentage() }}%)
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">
                            Progress {{ $this->getProgressPercentage() }}%
                        </span>
                        @if(count($completedQuestions) > 0)
                            <button wire:click="resetProgress"
                                    onclick="return confirm('Yakin ingin mereset progress latihan?')"
                                    class="text-xs md:text-sm px-3 py-1.5 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition font-medium">
                                Reset Progress
                            </button>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mt-3">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Soal aktif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span> Sudah dijawab
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> Belum dijawab
                    </div>
                </div>
            </div>

            {{-- Konten soal --}}
            <div class="flex-grow">
                <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-5 md:p-6 space-y-6">
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-700">Soal {{ $currentQuestionIndex + 1 }}</span>
                            <span class="text-gray-300">|</span>
                            <span>{{ count($completedQuestions) }} dari {{ $totalQuestions }} soal sudah dijawab</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">
                            Topik: {{ $currentTopic->name }}
                        </span>
                    </div>

                    <div class="space-y-5">
                        <div class="prose prose-sm md:prose-base max-w-none text-gray-800">
                            {!! nl2br(e($currentQuestion->question_text)) !!}
                        </div>

                        <div class="space-y-3">
                            @foreach($currentQuestion->options as $optionKey => $optionText)
                                <button type="button"
                                        wire:key="option-{{ $currentQuestion->id }}-{{ $optionKey }}"
                                        wire:click="selectAnswer('{{ $optionKey }}')"
                                        class="w-full text-left px-4 py-3 rounded-xl border transition focus:outline-none focus:ring-2 focus:ring-indigo-400
                                            @if($userAnswer === $optionKey)
                                                border-indigo-500 bg-indigo-50 text-indigo-700 shadow-inner
                                            @elseif(in_array($currentQuestion->id, $completedQuestions) && $currentQuestion->correct_answer === $optionKey)
                                                border-green-500 bg-green-50 text-green-700
                                            @else
                                                border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/60 text-gray-700
                                            @endif">
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full border text-sm font-semibold
                                            @if($userAnswer === $optionKey)
                                                border-indigo-500 bg-indigo-500 text-white
                                            @elseif(in_array($currentQuestion->id, $completedQuestions) && $currentQuestion->correct_answer === $optionKey)
                                                border-green-500 bg-green-500 text-white
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
                    </div>

                    <div class="mt-4">
                        @if($isAnswered)
                            <div class="rounded-xl border
                                    @if($isCorrect)
                                        border-green-200 bg-green-50 text-green-700
                                    @else
                                        border-red-200 bg-red-50 text-red-700
                                    @endif">
                                <div class="px-4 py-3 md:px-5 md:py-4">
                                    @if($isCorrect)
                                        <p class="font-semibold flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Jawabanmu benar!
                                        </p>
                                    @else
                                        <p class="font-semibold flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Jawabanmu belum tepat.
                                        </p>
                                        <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion->correct_answer }}</span>.</p>
                                    @endif
                                </div>
                                <div class="border-t border-current/10 px-4 py-3 md:px-5 md:py-4 bg-white/80 text-sm text-gray-700 rounded-b-xl">
                                    <p class="font-semibold text-gray-900 mb-2">Pembahasan</p>
                                    <p>{{ $currentQuestion->explanation }}</p>
                                </div>
                            </div>
                        @elseif($showExplanation)
                            <div class="rounded-xl border border-yellow-200 bg-yellow-50 text-yellow-800 px-4 py-3 md:px-5 md:py-4">
                                <p class="font-semibold flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18h.01" />
                                    </svg>
                                    Kamu belum memilih jawaban.
                                </p>
                                <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion->correct_answer }}</span>.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Footer navigasi --}}
            <div class="bg-white border border-gray-200 shadow-lg md:shadow-sm rounded-t-2xl md:rounded-2xl px-5 py-4 md:px-6 md:py-5 sticky bottom-0 left-0 right-0 md:static">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <button wire:click="previousQuestion"
                            @if($currentQuestionIndex === 0) disabled @endif
                            class="w-full md:w-auto px-4 py-2 text-sm font-medium rounded-lg border transition
                                   @if($currentQuestionIndex === 0)
                                       border-gray-300 text-gray-400 cursor-not-allowed bg-gray-100
                                   @else
                                       border-gray-300 text-gray-700 hover:bg-gray-50
                                   @endif">
                        < Sebelumnya
                    </button>

                    <div class="flex items-center gap-1 overflow-x-auto mx-[-1.25rem] px-5 md:mx-0 md:px-0 md:justify-center">
                        @for($i = 0; $i < $totalQuestions; $i++)
                            @php
                                $questionId = $questions[$i]['id'] ?? null;
                                $isCompleted = $questionId && in_array($questionId, $completedQuestions);
                                $isCurrent = $i === $currentQuestionIndex;
                            @endphp
                            <button wire:click="goToQuestion({{ $i }})"
                                    class="w-9 h-9 text-xs font-medium rounded-lg transition-colors flex-shrink-0
                                           @if($isCurrent)
                                               bg-blue-600 text-white shadow-sm
                                           @elseif($isCompleted)
                                               bg-green-100 text-green-800 border border-green-300
                                           @else
                                               bg-gray-100 text-gray-600 hover:bg-gray-200
                                           @endif">
                                {{ $i + 1 }}
                            </button>
                        @endfor
                    </div>

                    <button wire:click="nextQuestion"
                            @if($currentQuestionIndex === $totalQuestions - 1) disabled @endif
                            class="w-full md:w-auto px-4 py-2 text-sm font-medium rounded-lg border transition
                                   @if($currentQuestionIndex === $totalQuestions - 1)
                                       border-gray-300 text-gray-400 cursor-not-allowed bg-gray-100
                                   @else
                                       border-gray-300 text-gray-700 hover:bg-gray-50
                                   @endif">
                        Selanjutnya >
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50"
             x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('message') }}
        </div>
    @endif
</div>
