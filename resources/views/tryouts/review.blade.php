{{-- resources/views/tryouts/review.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembahasan: ' . $subtestProgress->subject->name . ' - ' . $subtestProgress->userTryout->tryoutPackage->title . ' - SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-lg shadow-md border p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">Pembahasan: {{ $subtestProgress->subject->name }}</h1>
                    <p class="text-sm text-gray-600">{{ $subtestProgress->userTryout->tryoutPackage->title }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Skor Subtes</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($subtestProgress->score, 1) }}</p>
                </div>
            </div>
             <div class="mt-4 text-sm">
                 <a href="{{ route('tryout.detail', $subtestProgress->user_tryout_id) }}"
                    class="text-gray-600 hover:text-gray-800 font-medium">
                    &larr; Kembali ke Detail Try Out
                 </a>
             </div>
        </div>

        <div class="space-y-6">
            @foreach($questions as $index => $question)
                @php
                    $userAnswerData = $userAnswers->get($question->id);
                    $userAnswer = $userAnswerData?->user_answer;
                    $isCorrect = $userAnswerData?->is_correct;
                @endphp
                <div class="bg-white rounded-lg shadow-sm border p-6" id="question-{{ $index + 1 }}">
                    
                    {{-- Nomor dan Teks Soal --}}
                    <div class="prose max-w-none text-base mb-4">
                        <p class="font-semibold text-gray-800">Soal Nomor {{ $index + 1 }}</p>
                        <p>{!! $question->question_text !!}</p>
                    </div>

                    {{-- Opsi Jawaban --}}
                    <div class="space-y-3">
                        @php $options = ['a', 'b', 'c', 'd', 'e']; @endphp
                        @foreach($options as $option)
                            @php
                                $optionText = 'option_' . $option;
                                $isUserAnswer = ($userAnswer === $option);
                                $isCorrectAnswer = ($question->correct_answer === $option);
                                $optionClass = 'border-gray-300 bg-white'; // Default

                                if ($isUserAnswer && $isCorrect) {
                                    $optionClass = 'border-green-500 bg-green-50 text-green-800 ring-2 ring-green-200'; // Jawaban User Benar
                                } elseif ($isUserAnswer && !$isCorrect) {
                                    $optionClass = 'border-red-500 bg-red-50 text-red-800 ring-2 ring-red-200'; // Jawaban User Salah
                                } elseif ($isCorrectAnswer) {
                                    $optionClass = 'border-green-500 bg-green-50 text-green-800'; // Kunci Jawaban (jika tidak dipilih user)
                                }
                            @endphp
                            <div class="p-4 border rounded {{ $optionClass }}">
                                {{ strtoupper($option) }}. {!! $question->$optionText !!}
                                {{-- Tambahan Indikator --}}
                                @if($isUserAnswer && $isCorrect) <span class="ml-2 text-xs font-semibold">(Jawaban Kamu - Benar)</span> @endif
                                @if($isUserAnswer && !$isCorrect) <span class="ml-2 text-xs font-semibold">(Jawaban Kamu - Salah)</span> @endif
                                @if($isCorrectAnswer && !$isUserAnswer) <span class="ml-2 text-xs font-semibold text-green-700">(Kunci Jawaban)</span> @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Pembahasan --}}
                    @if($question->explanation)
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-700 mb-2">Pembahasan:</h4>
                        <div class="prose prose-sm max-w-none text-gray-600">
                           {!! $question->explanation !!}
                        </div>
                    </div>
                    @endif

                </div>
            @endforeach
        </div>

        <div class="mt-8 text-center">
             <a href="{{ route('tryout.detail', $subtestProgress->user_tryout_id) }}"
                class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                &larr; Kembali ke Detail Try Out
             </a>
        </div>
    </div>
</div>
@endsection