@php
    $currentQuestionId = $currentQuestion?->id ?? null;
    $currentUserAnswer = $currentQuestionId ? ($userAnswers[$currentQuestionId] ?? null) : null;
@endphp

<div class="max-w-6xl mx-auto px-4 py-6 sm:py-8 space-y-6 md:space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl md:text-3xl font-semibold text-gray-900">Pengerjaan Try Out</h1>
            <p class="text-sm text-gray-500">Paket: {{ $userTryout->tryoutPackage->title ?? '-' }}</p>
        </div>

        @if(!is_null($timeRemaining))
            @php
                $minutes = floor($timeRemaining / 60);
                $seconds = $timeRemaining % 60;
            @endphp
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l3 3" />
                </svg>
                Sisa Waktu: {{ sprintf('%02d:%02d', $minutes, $seconds) }}
            </div>
        @endif
    </div>

    <div class="flex flex-col-reverse lg:grid lg:grid-cols-[minmax(0,3.5fr)_minmax(0,2fr)] gap-6 md:gap-8">
        {{-- Question Area --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4 md:px-6 md:py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-500">Soal {{ $currentQuestionIndex + 1 }} dari {{ $totalQuestions }}</p>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $currentQuestion?->topic?->name ?? 'Tanpa Topik' }}</h2>
                </div>
                <div class="text-xs text-gray-400">ID Soal: {{ $currentQuestionId ?? '-' }}</div>
            </div>

            <div class="px-5 py-6 md:px-6 space-y-6">
                @if($currentQuestion)
                    <div class="prose prose-sm md:prose-base max-w-none text-gray-800">
                        {!! nl2br(e($currentQuestion->question_text)) !!}
                    </div>

                    <div class="space-y-3">
                        @foreach($currentQuestion->options as $optionKey => $optionText)
                            <button
                                type="button"
                                class="w-full text-left px-4 py-3 rounded-xl border transition focus:outline-none focus:ring-2 focus:ring-indigo-400
                                    @if($currentUserAnswer === $optionKey)
                                        border-indigo-500 bg-indigo-50 text-indigo-700 shadow-inner
                                    @else
                                        border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/60 text-gray-700
                                    @endif"
                                wire:key="option-{{ $currentQuestionId }}-{{ $optionKey }}"
                                wire:click="selectAnswer({{ $currentQuestionId }}, '{{ $optionKey }}')"
                            >
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full border text-sm font-semibold
                                        @if($currentUserAnswer === $optionKey)
                                            border-indigo-500 bg-indigo-500 text-white
                                        @else
                                            border-gray-300 text-gray-500
                                        @endif
                                    ">
                                        {{ strtoupper($optionKey) }}
                                    </span>
                                    <span class="text-sm md:text-base text-gray-700 leading-relaxed">
                                        {!! nl2br(e($optionText)) !!}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Soal tidak tersedia. Silakan kembali ke daftar try out.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-4 lg:space-y-6">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-6 space-y-5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                        {{ $currentQuestionIndex + 1 }}/{{ $totalQuestions }} Soal
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                        {{ count($userAnswers) }} Jawaban
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Navigasi Soal</h3>
                    <div class="grid grid-cols-5 sm:grid-cols-6 gap-2">
                        @for($i = 0; $i < $totalQuestions; $i++)
                            @php
                                $questionId = $questions[$i]['id'] ?? null;
                                $isAnswered = $questionId && isset($userAnswers[$questionId]);
                                $isActive = $i === $currentQuestionIndex;
                            @endphp
                            <button
                                wire:click="goToQuestion({{ $i }})"
                                class="h-10 rounded-lg text-xs font-medium transition-colors flex items-center justify-center
                                    @if($isActive)
                                        bg-blue-600 text-white shadow-sm
                                    @elseif($isAnswered)
                                        bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @else
                                        bg-gray-100 text-gray-600 hover:bg-indigo-50
                                    @endif">
                                {{ $i + 1 }}
                            </button>
                        @endfor
                    </div>
                </div>

                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex w-3 h-3 rounded-full bg-blue-500"></span> Soal aktif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex w-3 h-3 rounded-full bg-emerald-500"></span> Sudah dijawab
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex w-3 h-3 rounded-full bg-gray-300"></span> Belum dijawab
                    </div>
                </div>

                <button
                    wire:click="toggleSubmitModal"
                    class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                    Selesai Try Out
                </button>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-6 space-y-3 text-sm text-gray-600">
                <h3 class="text-sm font-semibold text-gray-900">Catatan</h3>
                <p>Pastikan semua jawaban sudah terisi. Kamu dapat kembali ke soal sebelumnya kapan saja sebelum menyelesaikan try out.</p>
            </div>
        </aside>
    </div>
</div>

@if($showSubmitModal)
    <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-5 border-b border-gray-100">
                <h4 class="text-lg font-semibold text-gray-900">Selesaikan Try Out?</h4>
                <p class="text-sm text-gray-500 mt-1">Pastikan semua jawaban sudah diperiksa. Setelah diselesaikan, kamu tidak bisa mengubah jawaban lagi.</p>
            </div>
            <div class="px-6 py-5 flex items-center justify-end gap-3">
                <button type="button"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50"
                        wire:click="toggleSubmitModal">
                    Kembali
                </button>
                <button type="button"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-500 transition"
                        wire:click="submitTryout">
                    Ya, Selesaikan
                </button>
            </div>
        </div>
    </div>
@endif
