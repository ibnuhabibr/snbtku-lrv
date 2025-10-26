@extends('layouts.app')

@section('title', 'Tambah Soal - Admin SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Tambah Soal Baru</h1>
            <p class="text-gray-600">Buat soal baru untuk bank soal</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.questions.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <!-- Topic Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">
                                Mata Pelajaran
                            </label>
                            <select name="subject_id" 
                                    id="subject_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('subject_id') border-red-300 @enderror"
                                    required>
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            {{ old('subject_id', request('subject_id')) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="topic_id" class="block text-sm font-medium text-gray-700">
                                Topik
                            </label>
                            <select name="topic_id" 
                                    id="topic_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('topic_id') border-red-300 @enderror"
                                    required>
                                <option value="">Pilih Topik</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" 
                                            data-subject="{{ $topic->subject_id }}"
                                            {{ old('topic_id', request('topic_id')) == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('topic_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Question Text -->
                    <div>
                        <label for="question_text" class="block text-sm font-medium text-gray-700">
                            Teks Soal
                        </label>
                        <textarea name="question_text" 
                                  id="question_text" 
                                  rows="6"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('question_text') border-red-300 @enderror"
                                  placeholder="Masukkan teks soal di sini..."
                                  required>{{ old('question_text') }}</textarea>
                        @error('question_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Answer Options -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Pilihan Jawaban
                        </label>
                        <div class="space-y-3">
                            @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                <div>
                                    <label for="option_{{ $option }}" class="block text-sm font-medium text-gray-600 mb-1">
                                        Pilihan {{ strtoupper($option) }}
                                    </label>
                                    <textarea name="option_{{ $option }}" 
                                              id="option_{{ $option }}" 
                                              rows="2"
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('option_'.$option) border-red-300 @enderror"
                                              placeholder="Masukkan pilihan {{ strtoupper($option) }}"
                                              required>{{ old('option_'.$option) }}</textarea>
                                    @error('option_'.$option)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Correct Answer -->
                    <div>
                        <label for="correct_answer" class="block text-sm font-medium text-gray-700">
                            Jawaban Benar
                        </label>
                        <select name="correct_answer" 
                                id="correct_answer" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('correct_answer') border-red-300 @enderror"
                                required>
                            <option value="">Pilih Jawaban Benar</option>
                            @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                <option value="{{ $option }}" 
                                        {{ old('correct_answer') == $option ? 'selected' : '' }}>
                                    {{ strtoupper($option) }}
                                </option>
                            @endforeach
                        </select>
                        @error('correct_answer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Explanation -->
                    <div>
                        <label for="explanation" class="block text-sm font-medium text-gray-700">
                            Pembahasan (Opsional)
                        </label>
                        <textarea name="explanation" 
                                  id="explanation" 
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('explanation') border-red-300 @enderror"
                                  placeholder="Masukkan pembahasan soal (opsional)">{{ old('explanation') }}</textarea>
                        @error('explanation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 mt-8">
                    <a href="{{ route('admin.questions.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                        Batal
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Filter topics based on selected subject
    document.getElementById('subject_id').addEventListener('change', function() {
        const selectedSubjectId = this.value;
        const topicSelect = document.getElementById('topic_id');
        const topicOptions = topicSelect.querySelectorAll('option[data-subject]');
        
        // Reset topic selection
        topicSelect.value = '';
        
        // Show/hide topic options based on selected subject
        topicOptions.forEach(option => {
            if (selectedSubjectId === '' || option.dataset.subject === selectedSubjectId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });

    // Trigger the filter on page load if subject is pre-selected
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject_id');
        if (subjectSelect.value) {
            subjectSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection