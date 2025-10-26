@extends('layouts.app')

@section('title', 'Detail Soal - Admin SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Soal</h1>
                <p class="text-gray-600">Lihat detail soal dalam bank soal</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.questions.edit', $question) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                    Edit Soal
                </a>
                <a href="{{ route('admin.questions.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Question Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Soal</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $question->topic->subject->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Topik</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $question->topic->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jawaban Benar</label>
                    <p class="mt-1 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ strtoupper($question->correct_answer) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Soal</label>
                    <p class="mt-1 text-sm text-gray-900">#{{ $question->id }}</p>
                </div>
            </div>
        </div>

        <!-- Question Content -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Teks Soal</h3>
            <div class="prose max-w-none">
                <div class="text-gray-700 whitespace-pre-wrap">{{ $question->question_text }}</div>
            </div>
        </div>

        <!-- Answer Options -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pilihan Jawaban</h3>
            <div class="space-y-4">
                @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                    <div class="flex items-start space-x-3 p-3 rounded-lg {{ $question->correct_answer === $option ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-sm font-medium {{ $question->correct_answer === $option ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                                {{ strtoupper($option) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $question->{'option_'.$option} }}</div>
                            @if($question->correct_answer === $option)
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Jawaban Benar
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Explanation -->
        @if($question->explanation)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pembahasan</h3>
                <div class="prose max-w-none">
                    <div class="text-gray-700 whitespace-pre-wrap">{{ $question->explanation }}</div>
                </div>
            </div>
        @endif

        <!-- Usage in Tryout Packages -->
        @if($question->tryoutPackages->count() > 0)
            <div class="bg-white rounded-lg shadow p-6 mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Digunakan dalam Paket Try Out</h3>
                <div class="space-y-2">
                    @foreach($question->tryoutPackages as $package)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $package->title }}</h4>
                                <p class="text-xs text-gray-500">{{ $package->duration_minutes }} menit</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.tryout-packages.show', $package) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-sm">Lihat Paket</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection