<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Hasil Try Out</h1>
                <h2 class="text-xl text-gray-600 mb-4">{{ $userTryout->tryoutPackage->title }}</h2>
                
                <!-- Score Display -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-6 mb-6">
                    <div class="text-4xl font-bold mb-2">{{ number_format($userTryout->score, 1) }}</div>
                    <div class="text-lg">Skor Anda</div>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600">{{ $correctAnswers->count() }}</div>
                        <div class="text-sm text-green-800">Benar</div>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-red-600">{{ $incorrectAnswers->count() }}</div>
                        <div class="text-sm text-red-800">Salah</div>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-gray-600">{{ $unansweredQuestions->count() }}</div>
                        <div class="text-sm text-gray-800">Tidak Dijawab</div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600">{{ $questions->count() }}</div>
                        <div class="text-sm text-blue-800">Total Soal</div>
                    </div>
                </div>

                <!-- Time Information -->
                <div class="text-sm text-gray-600 mb-4">
                    <p>Waktu Mulai: {{ $userTryout->start_time->format('d/m/Y H:i:s') }}</p>
                    <p>Waktu Selesai: {{ $userTryout->end_time->format('d/m/Y H:i:s') }}</p>
                    <p>Durasi: {{ $userTryout->duration }} menit</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <button 
                        wire:click="toggleExplanations"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition duration-150 ease-in-out"
                    >
                        {{ $showExplanations ? 'Sembunyikan' : 'Tampilkan' }} Pembahasan
                    </button>
                    <button 
                        wire:click="backToTryouts"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-md font-medium transition duration-150 ease-in-out"
                    >
                        Kembali ke Daftar Try Out
                    </button>
                </div>
            </div>
        </div>

        <!-- Detailed Results -->
        @if($showExplanations)
            <div class="space-y-6">
                @foreach($questions as $index => $question)
                    @php
                        $userAnswer = $userAnswers->get($question->id);
                        $isCorrect = $userAnswer && $userAnswer->is_correct;
                        $isAnswered = $userAnswer !== null;
                    @endphp

                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                        <!-- Question Header -->
                        <div class="px-6 py-4 border-b 
                            {{ $isCorrect ? 'bg-green-50 border-green-200' : ($isAnswered ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200') }}">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Soal {{ $index + 1 }}
                                </h3>
                                <div class="flex items-center space-x-2">
                                    @if($isCorrect)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Benar
                                        </span>
                                    @elseif($isAnswered)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ✗ Salah
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            - Tidak Dijawab
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Question Text -->
                            <div class="mb-6">
                                <div class="prose max-w-none text-gray-800">
                                    {!! nl2br(e($question->question_text)) !!}
                                </div>
                            </div>

                            <!-- Answer Options -->
                            <div class="space-y-3 mb-6">
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                    @if($question->{'option_' . $option})
                                        @php
                                            $isUserAnswer = $userAnswer && $userAnswer->user_answer === $option;
                                            $isCorrectAnswer = $question->correct_answer === $option;
                                        @endphp

                                        <div class="flex items-start p-3 border rounded-lg
                                            {{ $isCorrectAnswer ? 'border-green-500 bg-green-50' : '' }}
                                            {{ $isUserAnswer && !$isCorrectAnswer ? 'border-red-500 bg-red-50' : '' }}
                                            {{ !$isCorrectAnswer && !$isUserAnswer ? 'border-gray-200' : '' }}">
                                            
                                            <div class="flex items-center mr-3 mt-1">
                                                @if($isCorrectAnswer)
                                                    <div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                @elseif($isUserAnswer)
                                                    <div class="w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-4 h-4 border-2 border-gray-300 rounded-full"></div>
                                                @endif
                                            </div>

                                            <div class="flex-1">
                                                <span class="font-medium text-gray-900 mr-2">{{ strtoupper($option) }}.</span>
                                                <span class="text-gray-800">{{ $question->{'option_' . $option} }}</span>
                                                
                                                @if($isCorrectAnswer)
                                                    <span class="ml-2 text-sm font-medium text-green-600">(Jawaban Benar)</span>
                                                @endif
                                                @if($isUserAnswer && !$isCorrectAnswer)
                                                    <span class="ml-2 text-sm font-medium text-red-600">(Jawaban Anda)</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Explanation -->
                            @if($question->explanation)
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="font-medium text-blue-900 mb-2">Pembahasan:</h4>
                                    <div class="text-blue-800 text-sm">
                                        {!! nl2br(e($question->explanation)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
