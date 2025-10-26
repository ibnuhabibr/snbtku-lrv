<div class="min-h-screen bg-gray-50" wire:poll.1s="calculateTimeRemaining">
    <!-- Header with Timer -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $userTryout->tryoutPackage->title }}</h1>
                    <p class="text-sm text-gray-600">Soal {{ $currentQuestionIndex + 1 }} dari {{ $totalQuestions }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Waktu Tersisa</div>
                        <div class="text-lg font-mono font-bold {{ $timeRemaining < 300 ? 'text-red-600' : 'text-blue-600' }}">
                            {{ gmdate('H:i:s', max(0, $timeRemaining)) }}
                        </div>
                    </div>
                    <button 
                        wire:click="confirmSubmit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out"
                    >
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Question Navigation Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border p-4 sticky top-6">
                    <h3 class="font-medium text-gray-900 mb-3">Navigasi Soal</h3>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($questions as $index => $question)
                            <button 
                                wire:click="goToQuestion({{ $index }})"
                                class="w-8 h-8 text-xs rounded-md border transition duration-150 ease-in-out
                                    {{ $index === $currentQuestionIndex ? 'bg-blue-600 text-white border-blue-600' : '' }}
                                    {{ isset($userAnswers[$question->id]) ? 'bg-green-100 border-green-300 text-green-800' : 'bg-gray-50 border-gray-300 text-gray-600' }}
                                    {{ $index === $currentQuestionIndex && isset($userAnswers[$question->id]) ? 'bg-blue-600 text-white border-blue-600' : '' }}
                                    hover:bg-blue-100 hover:border-blue-300"
                            >
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                    <div class="mt-4 text-xs text-gray-600">
                        <div class="flex items-center mb-1">
                            <div class="w-3 h-3 bg-green-100 border border-green-300 rounded mr-2"></div>
                            Sudah dijawab
                        </div>
                        <div class="flex items-center mb-1">
                            <div class="w-3 h-3 bg-blue-600 rounded mr-2"></div>
                            Soal aktif
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-50 border border-gray-300 rounded mr-2"></div>
                            Belum dijawab
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Question Area -->
            <div class="lg:col-span-3">
                @if($currentQuestion)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <!-- Question Text -->
                        <div class="mb-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">
                                Soal {{ $currentQuestionIndex + 1 }}
                            </h2>
                            <div class="prose max-w-none text-gray-800">
                                {!! nl2br(e($currentQuestion->question_text)) !!}
                            </div>
                        </div>

                        <!-- Answer Options -->
                        <div class="space-y-3 mb-6">
                            @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                @if($currentQuestion->{'option_' . $option})
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out
                                        {{ isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                        <input 
                                            type="radio" 
                                            name="question_{{ $currentQuestion->id }}" 
                                            value="{{ $option }}"
                                            wire:click="selectAnswer({{ $currentQuestion->id }}, '{{ $option }}')"
                                            {{ isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option ? 'checked' : '' }}
                                            class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                        >
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-900 mr-2">{{ strtoupper($option) }}.</span>
                                            <span class="text-gray-800">{{ $currentQuestion->{'option_' . $option} }}</span>
                                        </div>
                                    </label>
                                @endif
                            @endforeach
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between">
                            <button 
                                wire:click="prevQuestion"
                                {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out"
                            >
                                ← Sebelumnya
                            </button>
                            
                            <button 
                                wire:click="nextQuestion"
                                {{ $currentQuestionIndex === $totalQuestions - 1 ? 'disabled' : '' }}
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out"
                            >
                                Selanjutnya →
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmSubmit)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900">Konfirmasi Selesai</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menyelesaikan try out ini? 
                            Anda tidak dapat mengubah jawaban setelah menyelesaikan.
                        </p>
                        <div class="mt-4">
                            <p class="text-sm text-gray-700">
                                Soal dijawab: {{ count($userAnswers) }} dari {{ $totalQuestions }}
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-center space-x-3 mt-4">
                        <button 
                            wire:click="cancelSubmit"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition duration-150 ease-in-out"
                        >
                            Batal
                        </button>
                        <button 
                            wire:click="submitTryout"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-150 ease-in-out"
                        >
                            Ya, Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
