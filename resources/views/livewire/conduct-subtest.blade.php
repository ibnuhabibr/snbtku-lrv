{{-- resources/views/livewire/conduct-subtest.blade.php --}}
<div wire:poll.1s="decrementTime" class="flex flex-col md:flex-row min-h-screen bg-gray-100">

    {{-- SIDEBAR KIRI: Timer + Navigasi Soal --}}
    <div class="w-full md:w-3/12 lg:w-2/12 p-4 bg-white border-r sticky top-0 h-screen overflow-y-auto order-last md:order-first">
        
        {{-- HEADER SUBTES --}}
        <div class="text-center mb-4 border-b pb-3">
            <h3 class="font-bold text-lg text-gray-800">{{ $subtestProgress->subject->name }}</h3>
            <p class="text-sm text-gray-600">Subtes {{ $subtestProgress->subject->subtest_order }}/7</p>
        </div>

        {{-- TIMER --}}
        <div x-data="{ serverTime: @entangle('timeRemaining'), localTime: null, interval: null }"
             x-init="
                localTime = serverTime;
                if (interval) clearInterval(interval); // Hapus interval lama jika ada
                if (localTime > 0) {
                    interval = setInterval(() => {
                        if (localTime > 0) {
                            localTime--;
                        } else {
                            clearInterval(interval);
                            // Opsional: bisa panggil livewire submit di sini jika perlu fallback
                        }
                    }, 1000);
                }
                // Hentikan interval saat komponen dihancurkan
                $watch('serverTime', value => { 
                     // Sinkronkan ulang jika server kirim update (misal karena waktu habis)
                     localTime = value; 
                     if (interval && localTime <= 0) clearInterval(interval);
                 });
                $el.addEventListener('livewire:navigating', () => clearInterval(interval)); // Clear saat pindah halaman
             "
             class="text-center mb-4 p-3 bg-red-50 rounded-lg border border-red-200">
            <h4 class="font-bold text-sm text-gray-700 mb-1">Waktu Tersisa</h4>
            <span class="text-xl font-mono"
                  :class="localTime !== null && localTime < 300 ? 'text-red-500' : 'text-blue-600'"
                  x-text="localTime !== null ? new Date(localTime * 1000).toISOString().substr(11, 8) : '00:00:00'">
                {{-- Teks awal sebelum JS jalan --}}
                {{ gmdate('H:i:s', $timeRemaining ?? 0) }}
            </span>
            <div x-show="localTime !== null && localTime <= 300 && localTime > 0">
                <p class="text-xs text-red-600 mt-1">⚠️ Waktu hampir habis!</p>
            </div>
        </div>

        {{-- PROGRESS BAR --}}
        <div class="mb-4">
            @php
                $answeredCount = count(array_filter($userAnswers));
                $progressPercent = $totalQuestions > 0 ? ($answeredCount / $totalQuestions) * 100 : 0;
            @endphp
            <div class="flex justify-between text-xs text-gray-600 mb-1">
                <span>Progress</span>
                <span>{{ $answeredCount }}/{{ $totalQuestions }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>

        {{-- GRID NAVIGASI SOAL --}}
        <h4 class="font-bold mb-2 text-sm text-gray-700">Navigasi Soal</h4>
        <div class="grid grid-cols-5 gap-2 mb-4">
            @foreach($questions as $index => $question)
                @php
                    $qId = $question->id;
                    $isAnswered = isset($userAnswers[$qId]) && $userAnswers[$qId] !== null;
                    $isActive = ($index == $currentQuestionIndex);
                    $isMarked = in_array($qId, $markedQuestions);

                    // Prioritas styling: Active > Marked > Answered > Default
                    if ($isActive) {
                        $class = 'bg-blue-600 text-white border-blue-700 ring-2 ring-blue-300';
                    } elseif ($isMarked) {
                        $class = 'bg-yellow-400 text-gray-900 border-yellow-500';
                    } elseif ($isAnswered) {
                        $class = 'bg-green-500 text-white border-green-600';
                    } else {
                        $class = 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50';
                    }
                @endphp
                
                <button wire:click="goToQuestion({{ $index }})"
                        class="h-9 w-9 flex items-center justify-center rounded border cursor-pointer text-sm font-medium transition-all duration-200 {{ $class }}">
                    {{ $index + 1 }}
                </button>
            @endforeach
        </div>

        {{-- LEGEND --}}
        <div class="text-xs text-gray-600 mb-4 space-y-1">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-blue-600 rounded"></div>
                <span>Aktif</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-500 rounded"></div>
                <span>Dijawab</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-yellow-400 rounded"></div>
                <span>Ragu-ragu</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-white border border-gray-300 rounded"></div>
                <span>Belum dijawab</span>
            </div>
        </div>

        {{-- TOMBOL SELESAI --}}
        <button wire:click="$toggle('showConfirmModal')"
                class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200 shadow-md">
            Selesai Subtes
        </button>
    </div>

    {{-- KONTEN UTAMA: Soal + Opsi Jawaban --}}
    <div wire:key="subtest-{{ $subtestProgress->id }}-question-{{ $currentQuestionIndex }}" 
         class="w-full md:w-9/12 lg:w-10/12 p-6 md:p-8 order-first md:order-last">
        
        @if($currentQuestion)
            {{-- HEADER SOAL --}}
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">
                        Soal {{ $currentQuestionIndex + 1 }} dari {{ $totalQuestions }}
                    </h2>
                    <button wire:click="toggleMark"
                            class="flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium transition-colors duration-200
                                   {{ in_array($currentQuestion->id, $markedQuestions) 
                                      ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' 
                                      : 'bg-gray-100 text-gray-600 border border-gray-300 hover:bg-yellow-50' }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        {{ in_array($currentQuestion->id, $markedQuestions) ? 'Batal Tandai' : 'Tandai Ragu' }}
                    </button>
                </div>

                {{-- TEKS SOAL --}}
                <div class="prose max-w-none text-gray-800 leading-relaxed">
                    {!! nl2br(e($currentQuestion->question_text)) !!}
                </div>
            </div>

            {{-- OPSI JAWABAN --}}
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4">Pilih Jawaban:</h3>
                <div class="space-y-3">
                    @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                        @php
                            $optionText = $currentQuestion->{'option_' . $option};
                            $isSelected = isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option;
                        @endphp
                        
                        @if($optionText)
                            <label class="flex items-start gap-3 p-4 rounded-lg border cursor-pointer transition-all duration-200
                                          {{ $isSelected 
                                             ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200' 
                                             : 'bg-gray-50 border-gray-200 hover:bg-blue-25 hover:border-blue-200' }}">
                                <input type="radio" 
                                       name="question_{{ $currentQuestion->id }}" 
                                       value="{{ $option }}"
                                       wire:click="selectAnswer('{{ $option }}')"
                                       {{ $isSelected ? 'checked' : '' }}
                                       class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="font-medium text-gray-800 mr-2">{{ strtoupper($option) }}.</span>
                                    <span class="text-gray-700">{{ $optionText }}</span>
                                </div>
                            </label>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- NAVIGASI SOAL --}}
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex justify-between items-center">
                    <button wire:click="prevQuestion" 
                            {{ $currentQuestionIndex <= 0 ? 'disabled' : '' }}
                            class="flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg font-medium transition-colors duration-200
                                   {{ $currentQuestionIndex <= 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Sebelumnya
                    </button>

                    <span class="text-gray-600 font-medium">
                        {{ $currentQuestionIndex + 1 }} / {{ $totalQuestions }}
                    </span>

                    <button wire:click="nextQuestion" 
                            {{ $currentQuestionIndex >= $totalQuestions - 1 ? 'disabled' : '' }}
                            class="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg font-medium transition-colors duration-200
                                   {{ $currentQuestionIndex >= $totalQuestions - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600' }}">
                        Selanjutnya
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

        @else
            {{-- FALLBACK JIKA TIDAK ADA SOAL --}}
            <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
                <div class="text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada soal tersedia</h3>
                    <p class="text-gray-600">Silakan hubungi administrator jika masalah ini terus berlanjut.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL KONFIRMASI SELESAI --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-md w-full">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Selesaikan Subtes?</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Anda telah menjawab <strong>{{ count(array_filter($userAnswers)) }}</strong> dari <strong>{{ $totalQuestions }}</strong> soal.
                        <br>Apakah Anda yakin ingin menyelesaikan subtes ini?
                    </p>
                    <div class="flex gap-3 justify-center">
                        <button wire:click="$toggle('showConfirmModal')"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition-colors duration-200">
                            Batal
                        </button>
                        <button onclick="preventExit = false;" wire:click="submitSubtest"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-colors duration-200">
                            Ya, Selesaikan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- JAVASCRIPT UNTUK HANDLING EXIT --}}
<script>
    // Flag untuk mengontrol peringatan exit
    let preventExit = true;
    
    // Prevent accidental page refresh/close
    window.addEventListener('beforeunload', function(e) {
        if (preventExit) {
            e.preventDefault();
            e.returnValue = 'Anda yakin ingin meninggalkan halaman? Progress akan disimpan.';
            return 'Anda yakin ingin meninggalkan halaman? Progress akan disimpan.';
        }
    });

    // Listen untuk event redirect dari Livewire
    document.addEventListener('livewire:init', () => {
        Livewire.on('redirectToDetail', (url) => {
            window.removeEventListener('beforeunload', arguments.callee); // Remove warning
            window.location.href = url;
        });
    });

    // Auto-save progress setiap 30 detik (opsional)
    setInterval(() => {
        if (typeof Livewire !== 'undefined') {
            // Trigger save progress tanpa redirect
            // Livewire.dispatch('saveProgress'); // Bisa ditambahkan method ini di component
        }
    }, 30000);
</script>