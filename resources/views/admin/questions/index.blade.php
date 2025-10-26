@extends('layouts.app')

@section('title', 'Kelola Soal - Admin SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Soal</h1>
                <p class="text-gray-600">Kelola semua soal dalam bank soal</p>
            </div>
            <a href="{{ route('admin.questions.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                Tambah Soal
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('admin.questions.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Mata Pelajaran
                    </label>
                    <select name="subject_id" id="subject_id" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" 
                                    {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex-1 min-w-64">
                    <label for="topic_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Topik
                    </label>
                    <select name="topic_id" id="topic_id" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Topik</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" 
                                    {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                {{ $topic->subject->name }} - {{ $topic->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Questions List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($questions->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($questions as $index => $question)
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            Soal #{{ ($questions->currentPage() - 1) * $questions->perPage() + $index + 1 }}
                                        </h4>
                                        <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                            {{ $question->topic->subject->name }}
                                        </span>
                                        <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                            {{ $question->topic->name }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-700 mb-3">
                                        {!! Str::limit(strip_tags($question->question_text), 200) !!}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Jawaban benar: <span class="font-medium">{{ strtoupper($question->correct_answer) }}</span>
                                    </div>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <a href="{{ route('admin.questions.show', $question) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-sm">Lihat</a>
                                    <a href="{{ route('admin.questions.edit', $question) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                                    <form action="{{ route('admin.questions.destroy', $question) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($questions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $questions->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada soal</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan soal pertama.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.questions.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Tambah Soal Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection