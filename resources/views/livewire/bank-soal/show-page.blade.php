{{-- resources/views/livewire/bank-soal/show-page.blade.php --}}
<div class="flex flex-col md:flex-row min-h-screen bg-gray-50">

    <aside class="w-full md:w-4/12 lg:w-3/12 xl:w-2/12 p-4 bg-white border-r shadow-md sticky top-0 h-screen-nav-mobile md:h-screen overflow-y-auto order-last md:order-first">
        {{-- Header Subtes --}}
        <div class="text-center mb-4 pb-4 border-b">
            <a href="{{ route('bank-soal.index') }}" wire:navigate class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali ke Bank Soal</a>
            <h3 class="font-bold text-lg text-gray-800 mt-2">{{ $subject->name }}</h3>
        </div>

        {{-- Daftar Topik --}}
        <h4 class="font-semibold text-xs text-gray-500 mb-2 uppercase tracking-wider px-3">Pilih Topik</h4>
        <nav class="space-y-1">
            @forelse($subject->topics->sortBy('name') as $topic)
                <button
                    wire:click="selectTopic({{ $topic->id }})"
                    type="button"
                    class="w-full text-left px-3 py-2 rounded-md text-sm transition duration-150 ease-in-out flex justify-between items-center
                            {{ $selectedTopic && $selectedTopic->id === $topic->id ? 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 font-semibold shadow-inner' : 'text-gray-700 hover:bg-gray-100' }}">
                        <span class="text-sm">{{ $topic->name }}</span>
                        @if($selectedTopic && $selectedTopic->id === $topic->id)
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    @endif
                </button>
            @empty
                <p class="px-3 text-xs text-gray-400">Belum ada topik.</p>
            @endforelse
        </nav>
    </aside>

    <main class="w-full md:w-8/12 lg:w-9/12 xl:w-10/12 p-6 md:p-10 order-first md:order-last">
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
            <div class="bg-white rounded-lg shadow-md border p-8 text-center h-full flex flex-col justify-center items-center">
                 <svg class="w-16 h-16 text-green-400 mb-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <h1 class="text-xl font-semibold text-gray-700">Latihan Selesai!</h1>
                <p class="mt-2 text-gray-500">Kamu sudah mencapai akhir soal yang tersedia untuk topik **{{ $selectedTopic->name }}**.</p>
                <p class="mt-1 text-sm text-gray-400">(Admin akan menambahkan soal baru secara berkala).</p>
                 <button wire:click="selectTopic({{ $selectedTopic->id }})" {{-- Reload topic --}}
                         class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                         Ulangi Latihan dari Awal
                 </button>
            </div>
        @else
            {{-- Tampilan Pengerjaan Soal --}}
            <div class="bg-white rounded-lg shadow-md border overflow-hidden">
                {{-- Konten Soal --}}
                <div class="p-6">
                    <p class="font-semibold text-gray-600 text-sm mb-3">
                        Topik: {{ $selectedTopic->name }} (Soal ID: {{ $currentQuestion->id }})
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
                                @disabled($showExplanation) {{-- Disable saat mode koreksi --}}
                                class="w-full text-left p-4 border rounded-lg transition duration-150 ease-in-out flex items-start space-x-3 disabled:cursor-not-allowed disabled:opacity-70"
                                x-bind:class="{
                                    'bg-white hover:bg-gray-50 border-gray-300': !$wire.selectedAnswer || $wire.selectedAnswer !== '{{ $option }}',
                                    'bg-blue-50 border-blue-400 ring-2 ring-blue-200 text-blue-800': $wire.selectedAnswer === '{{ $option }}' && !$wire.showExplanation,
                                    'bg-green-50 border-green-400 text-green-800 font-medium': $wire.showExplanation && '{{ $option }}' === '{{ $correctAnswer }}', {{-- Kunci Jawaban --}}
                                    'bg-red-50 border-red-400 text-red-800': $wire.showExplanation && $wire.selectedAnswer === '{{ $option }}' && '{{ $option }}' !== '{{ $correctAnswer }}', {{-- User Salah Pilih Ini --}}
                                    'bg-white border-gray-300 text-gray-600': $wire.showExplanation && $wire.selectedAnswer !== '{{ $option }}' && '{{ $option }}' !== '{{ $correctAnswer }}' {{-- Opsi Lain Saat Koreksi --}}
                                }"
                            >
                                <span class="font-semibold">{{ strtoupper($option) }}.</span>
                                <span class="prose prose-sm max-w-none">{!! $currentQuestion->$optionText !!}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Feedback & Pembahasan --}}
                    {{-- Gunakan @entangle untuk sinkronisasi dengan Alpine --}}
                    <div x-data="{ show: @entangle('showExplanation') }" x-show="show" x-collapse>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="mb-4 p-4 rounded-lg border
                                @if($isCorrect === true) bg-green-50 border-green-300 text-green-800 @elseif($isCorrect === false) bg-red-50 border-red-300 text-red-800 @else bg-gray-50 border-gray-300 text-gray-800 @endif">

                                @if($isCorrect === true)
                                    <p class="font-semibold"><svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Jawaban Kamu Benar!</p>
                                @elseif($isCorrect === false)
                                    <p class="font-semibold"><svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg> Jawaban Kamu Salah.</p>
                                    <p class="text-sm mt-1">Jawaban yang benar: <span class="uppercase font-bold">{{ $currentQuestion->correct_answer }}</span>.</p>
                                @elseif($isCorrect === null && $showExplanation) {{-- Tidak Menjawab --}}
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
</div>

{{-- CSS Tambahan (jika diperlukan) --}}
@push('styles')
<style>
    @media (max-width: 767px) { .h-screen-nav-mobile { height: auto; position: static; } }
    .prose p { margin-top: 0; margin-bottom: 0.5em; }
    button span.prose { display: inline; }
    /* Pastikan prose di penjelasan tidak terlalu besar */
    .prose-sm { font-size: 0.875rem; line-height: 1.5rem; }
     /* Fix prose list styling */
    .prose ul, .prose ol { margin-top: 0.5em; margin-bottom: 0.5em; }
    .prose li p { margin-bottom: 0.2em; }
    /* Style untuk button topik aktif */
     .bg-gradient-to-r.from-blue-100.to-indigo-100 { /* Style yang lebih spesifik */ }
</style>
@endpush