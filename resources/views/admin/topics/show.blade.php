@extends('layouts.app')

@section('title', 'Detail Topik: ' . $topic->name . ' - Admin SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $topic->name }}</h1>
                <p class="text-gray-600">Detail topik dan soal-soalnya</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.topics.edit', $topic) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                    Edit Topik
                </a>
                <a href="{{ route('admin.topics.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Topic Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Topik</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Topik</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $topic->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $topic->subject->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Slug</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $topic->slug }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Soal</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $topic->questions->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Soal dalam Topik Ini</h3>
                <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                    Tambah Soal
                </a>
            </div>
            
            @if($topic->questions->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($topic->questions as $index => $question)
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">
                                        Soal #{{ $index + 1 }}
                                    </h4>
                                    <div class="text-sm text-gray-700 mb-3">
                                        {!! Str::limit(strip_tags($question->question_text), 150) !!}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Jawaban benar: {{ strtoupper($question->correct_answer) }}
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
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada soal</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan soal pertama untuk topik ini.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Tambah Soal Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection