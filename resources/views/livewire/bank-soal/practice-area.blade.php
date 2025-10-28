{{-- resources/views/livewire/bank-soal/practice-area.blade.php --}}
<div class="h-full">
    @if(!$currentTopic)
        {{-- State awal: belum ada topik yang dipilih --}}
        <div class="flex flex-col items-center justify-center h-full text-center">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-8 max-w-md">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Pilih Topik untuk Berlatih</h3>
                <p class="text-gray-600 mb-4">Silakan pilih salah satu topik untuk memulai latihan soal {{ $subject->name }}.</p>
                
                {{-- Tombol untuk menampilkan daftar topik --}}
                <button wire:click="toggleTopicList" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 mb-4">
                    {{ $showTopicList ? 'Sembunyikan Topik' : 'Pilih Topik' }}
                </button>

                {{-- Daftar topik --}}
                @if($showTopicList)
                    <div class="mt-4 space-y-2">
                        @foreach($subject->topics as $topic)
                            <button wire:click="selectTopic({{ $topic->id }})"
                                    class="w-full text-left px-4 py-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-blue-300 transition duration-200">
                                <div class="font-medium text-gray-800">{{ $topic->name }}</div>
                                <div class="text-sm text-gray-500">{{ $topic->questions_count ?? 0 }} soal</div>
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center justify-center text-sm text-gray-500 mt-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Atau gunakan sidebar di sebelah kiri untuk navigasi
                </div>
            </div>
        </div>
    @elseif($totalQuestions === 0)
        {{-- State: topik dipilih tapi tidak ada soal --}}
        <div class="flex items-center justify-center h-full">
            <div class="text-center">
                <div class="mb-6">
                    <svg class="mx-auto h-16 w-16 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Soal</h3>
                <p class="text-gray-500">Topik <strong>{{ $currentTopic->name }}</strong> belum memiliki soal latihan.</p>
            </div>
        </div>
    @else
        {{-- State: ada soal, tampilkan interface latihan --}}
        <div class="flex flex-col h-full">
            {{-- Header dengan info progress dan navigasi --}}
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $currentTopic->name }}</h2>
                        <p class="text-sm text-gray-500">
                            Soal {{ $currentQuestionIndex + 1 }} dari {{ $totalQuestions }} 
                            • {{ count($completedQuestions) }} terjawab ({{ $this->getProgressPercentage() }}%)
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if(count($completedQuestions) > 0)
                            <button wire:click="resetProgress" 
                                    onclick="return confirm('Yakin ingin mereset progress latihan?')"
                                    class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
                                Reset Progress
                            </button>
                        @endif
                    </div>
                </div>
                
                {{-- Progress bar --}}
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $this->getProgressPercentage() }}%"></div>
                </div>
            </div>

            {{-- Konten utama --}}
            <div class="flex-1 overflow-y-auto">
                @if($currentQuestion)
                    <div class="p-6">
                        {{-- Soal --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                            <div class="prose prose-sm max-w-none">
                                {!! nl2br(e($currentQuestion->question_text)) !!}
                            </div>
                        </div>

                        {{-- Pilihan jawaban --}}
                        <div class="space-y-3 mb-6">
                            @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                @php
                                    $optionText = $currentQuestion->{'option_' . $option};
                                    $isSelected = $userAnswer === $option;
                                    $isCorrect = $currentQuestion->correct_answer === $option;
                                    $showResult = $showExplanation;
                                @endphp
                                
                                @if($optionText)
                                    <button 
                                        wire:click="selectAnswer('{{ $option }}')"
                                        @if($isAnswered) disabled @endif
                                        class="w-full text-left p-4 rounded-lg border-2 transition-all duration-200 
                                               @if($showResult && $isCorrect) 
                                                   border-green-500 bg-green-50 
                                               @elseif($showResult && $isSelected && !$isCorrect) 
                                                   border-red-500 bg-red-50 
                                               @elseif($isSelected && !$showResult) 
                                                   border-blue-500 bg-blue-50 
                                               @else 
                                                   border-gray-300 hover:border-gray-400 hover:bg-gray-50 
                                               @endif
                                               @if($isAnswered) cursor-not-allowed @else cursor-pointer @endif">
                                        
                                        <div class="flex items-start">
                                            <span class="flex-shrink-0 w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-medium mr-3
                                                         @if($showResult && $isCorrect) 
                                                             border-green-500 bg-green-500 text-white 
                                                         @elseif($showResult && $isSelected && !$isCorrect) 
                                                             border-red-500 bg-red-500 text-white 
                                                         @elseif($isSelected && !$showResult) 
                                                             border-blue-500 bg-blue-500 text-white 
                                                         @else 
                                                             border-gray-400 text-gray-600 
                                                         @endif">
                                                {{ strtoupper($option) }}
                                            </span>
                                            <span class="prose prose-sm flex-1">{{ $optionText }}</span>
                                            
                                            @if($showResult && $isCorrect)
                                                <svg class="w-5 h-5 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif($showResult && $isSelected && !$isCorrect)
                                                <svg class="w-5 h-5 text-red-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </button>
                                @endif
                            @endforeach
                        </div>

                        {{-- Tombol Lihat Pembahasan (jika belum menjawab) --}}
                        @if(!$showExplanation && $currentQuestion)
                            <div class="mb-6">
                                <button wire:click="$set('showExplanation', true)" 
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm">
                                    👁️ Lihat Pembahasan
                                </button>
                            </div>
                        @endif

                        {{-- Penjelasan (muncul setelah jawab atau klik lihat pembahasan) --}}
                        @if($showExplanation && $currentQuestion->explanation)
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 mb-6">
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pembahasan
                                </h4>
                                <div class="prose prose-sm max-w-none text-gray-700">
                                    {!! nl2br(e($currentQuestion->explanation)) !!}
                                </div>
                            </div>
                        @endif

                        {{-- Feedback setelah menjawab --}}
                        @if($showExplanation)
                            <div class="mb-6">
                                <div class="p-4 rounded-lg border 
                                    @if($isCorrect === true) bg-green-50 border-green-300 text-green-800 
                                    @elseif($isCorrect === false) bg-red-50 border-red-300 text-red-800 
                                    @else bg-gray-50 border-gray-300 text-gray-800 @endif">

                                    {{-- PERBAIKAN LOGIC FEEDBACK --}}
                                    @if($isCorrect === true)
                                        <p class="font-semibold">
                                            <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg> 
                                            Jawaban Kamu Benar!
                                        </p>
                                    @elseif($isCorrect === false)
                                        <p class="font-semibold">
                                            <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg> 
                                            Jawaban Kamu Salah.
                                        </p>
                                        <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion?->correct_answer }}</span>.</p>
                                    @elseif($isCorrect === null && $showExplanation)
                                        <p class="font-semibold">
                                            <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg> 
                                            Kamu tidak memilih jawaban.
                                        </p>
                                        <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion?->correct_answer }}</span>.</p>
                                    @endif
                                    {{-- AKHIR PERBAIKAN LOGIC --}}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Footer navigasi --}}
            <div class="bg-white border-t border-gray-200 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <button wire:click="previousQuestion" 
                            @if($currentQuestionIndex === 0) disabled @endif
                            class="px-4 py-2 text-sm font-medium rounded-md border 
                                   @if($currentQuestionIndex === 0) 
                                       border-gray-300 text-gray-400 cursor-not-allowed 
                                   @else 
                                       border-gray-300 text-gray-700 hover:bg-gray-50 
                                   @endif">
                        ← Sebelumnya
                    </button>

                    {{-- Navigasi nomor soal --}}
                    <div class="flex items-center space-x-1 overflow-x-auto max-w-md">
                        @for($i = 0; $i < $totalQuestions; $i++)
                            @php
                                $questionId = $questions[$i]['id'] ?? null;
                                $isCompleted = $questionId && in_array($questionId, $completedQuestions);
                                $isCurrent = $i === $currentQuestionIndex;
                            @endphp
                            <button wire:click="goToQuestion({{ $i }})"
                                    class="w-8 h-8 text-xs font-medium rounded-md transition-colors
                                           @if($isCurrent) 
                                               bg-blue-600 text-white 
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
                            class="px-4 py-2 text-sm font-medium rounded-md border 
                                   @if($currentQuestionIndex === $totalQuestions - 1) 
                                       border-gray-300 text-gray-400 cursor-not-allowed 
                                   @else 
                                       border-gray-300 text-gray-700 hover:bg-gray-50 
                                   @endif">
                        Selanjutnya →
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Listen for custom event from sidebar topic selection
    window.addEventListener('topic-selected-from-sidebar', function(event) {
        console.log('Received topic-selected-from-sidebar event:', event.detail);
        const topicId = event.detail.topicId;
        
        // Call the Livewire method
        @this.call('selectTopicFromSidebar', topicId);
    });
});
</script>